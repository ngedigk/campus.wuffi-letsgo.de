<?php

namespace App\Tests\Services;

use App\Services\RegistrationService;

use App\Contracts\Repositories\AccessCodeRepositoryInterface;
use App\Contracts\Repositories\EmailVerificationRepositoryInterface;
use App\Contracts\Repositories\RegistrationCodeRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Database\TransactionManagerInterface;
use App\Contracts\Mail\MailerInterface;

use Psr\Log\LoggerInterface;

use App\Exceptions\DuplicateEmailException;
use App\Exceptions\InvalidRegistrationCodeException;
use App\Exceptions\RegistrationCodeAlreadyUsedException;
use App\Exceptions\UserNotFoundException;

use App\Dto\User;

use App\Services\UuidService;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RegistrationServiceTest extends TestCase
{
    private TransactionManagerInterface&MockObject $transactionManagerMock;
    private MailerInterface&MockObject $mailerMock;
    private UserRepositoryInterface&MockObject $userRepositoryMock;
    private EmailVerificationRepositoryInterface&MockObject $emailVerificationRepositoryMock;
    private RegistrationCodeRepositoryInterface&MockObject $registrationCodeRepositoryMock;
    private AccessCodeRepositoryInterface&MockObject $accessCodeRepositoryMock;
    private UuidService&MockObject $uuidServiceMock;
    private LoggerInterface&MockObject $loggerMock;

    private RegistrationService $registrationService;

    private ?string $capturedToken = null;

    protected function setUp(): void
    {
        if (!defined('SITE_URL')) {
            define('SITE_URL', 'http://localhost:8080');
        }

        $this->transactionManagerMock = $this->createMock(TransactionManagerInterface::class);
        $this->mailerMock = $this->createMock(MailerInterface::class);
        $this->userRepositoryMock = $this->createMock(UserRepositoryInterface::class);
        $this->emailVerificationRepositoryMock = $this->createMock(EmailVerificationRepositoryInterface::class);
        $this->registrationCodeRepositoryMock = $this->createMock(RegistrationCodeRepositoryInterface::class);
        $this->accessCodeRepositoryMock = $this->createMock(AccessCodeRepositoryInterface::class);
        $this->uuidServiceMock = $this->createMock(UuidService::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->registrationService = new RegistrationService(
            $this->transactionManagerMock,
            $this->mailerMock,
            $this->userRepositoryMock,
            $this->emailVerificationRepositoryMock,
            $this->registrationCodeRepositoryMock,
            $this->accessCodeRepositoryMock,
            $this->uuidServiceMock,
            $this->loggerMock
        );

        $this->capturedToken = null;
    }

    private function configureSuccessfulRegistrationExpectations(
        string $expectedUserId,
        string $password
    ): void {
        $this->userRepositoryMock
            ->expects($this->once())
            ->method('existsByEmail')
            ->with('test@example.com')
            ->willReturn(false);

        $this->registrationCodeRepositoryMock
            ->expects($this->once())
            ->method('findByCodeForUpdate')
            ->with('VALID-CODE')
            ->willReturn(['id' => 1, 'code' => 'VALID-CODE']);

        $this->registrationCodeRepositoryMock
            ->expects($this->once())
            ->method('isUsed')
            ->with('VALID-CODE')
            ->willReturn(false);

        $this->uuidServiceMock
            ->expects($this->once())
            ->method('generate')
            ->willReturn($expectedUserId);

        $this->userRepositoryMock
            ->expects($this->once())
            ->method('create')
            ->with(
                $expectedUserId,
                'test@example.com',
                $this->callback(
                    fn (string $hash) => password_verify($password, $hash)
                ),
                'Test Name'
            );

        $this->emailVerificationRepositoryMock
            ->expects($this->once())
            ->method('upsert')
            ->with(
                $expectedUserId,
                $this->callback(function (string $token): bool {
                    $this->capturedToken = $token;
                    return strlen($token) === 64;
                })
            );

        $this->registrationCodeRepositoryMock
            ->expects($this->once())
            ->method('markAsUsed')
            ->with(1, $expectedUserId);

        $this->registrationCodeRepositoryMock
            ->expects($this->once())
            ->method('getCourseIds')
            ->with(1)
            ->willReturn(['course-uuid-1', 'course-uuid-2']);

        $this->accessCodeRepositoryMock
            ->expects($this->exactly(2))
            ->method('createForRegistration')
            ->willReturnOnConsecutiveCalls(100, 101);

        $this->userRepositoryMock
            ->expects($this->once())
            ->method('enrollInCourses')
            ->with($expectedUserId, $this->callback(function ($pairs) {
                $this->assertCount(2, $pairs);
                $this->assertEquals('course-uuid-1', $pairs[0]['course_id']);
                $this->assertEquals('course-uuid-2', $pairs[1]['course_id']);
                $this->assertEquals(100, $pairs[0]['access_code_id']);
                $this->assertEquals(101, $pairs[1]['access_code_id']);
                return true;
            }));
    }

    private function configureTransactionToExecuteCallback(): void
    {
        $this->transactionManagerMock
            ->expects($this->once())
            ->method('run')
            ->willReturnCallback(function (callable $callback) {
                $callback();
            });
    }

    public function testRegisterCreatesUserAndEnrollsInCourses(): void
    {
        $this->configureTransactionToExecuteCallback();

        $this->configureSuccessfulRegistrationExpectations(
            'user-uuid-123',
            'securepassword'
        );

        $this->mailerMock
            ->expects($this->once())
            ->method('send')
            ->with(
                'test@example.com',
                'Bestätigen Sie Ihre E-Mail',
                $this->callback(
                    fn (string $body) => str_contains($body, 'register/verify?token=' . $this->capturedToken)
                )
            );

        $this->registrationService->register(
            'test@example.com',
            'securepassword',
            'VALID-CODE',
            'Test Name'
        );

        $this->assertNotNull($this->capturedToken);
        $this->assertSame(64, strlen($this->capturedToken));
    }

    public function testRegisterThrowsDuplicateEmailException(): void
    {
        $this->configureTransactionToExecuteCallback();

        $this->userRepositoryMock
            ->expects($this->once())
            ->method('existsByEmail')
            ->with('existing@example.com')
            ->willReturn(true);

        $this->registrationCodeRepositoryMock
            ->expects($this->never())
            ->method('findByCodeForUpdate');
        $this->registrationCodeRepositoryMock
            ->expects($this->never())
            ->method('isUsed');
        $this->uuidServiceMock
            ->expects($this->never())
            ->method('generate');
        $this->userRepositoryMock
            ->expects($this->never())
            ->method('create');
        $this->emailVerificationRepositoryMock
            ->expects($this->never())
            ->method('upsert');
        $this->mailerMock
            ->expects($this->never())
            ->method('send');

        $this->expectException(DuplicateEmailException::class);
        $this->expectExceptionMessage('E-Mail existiert bereits.');

        $this->registrationService->register(
            'existing@example.com',
            'password',
            'VALID-CODE',
            'Test Name'
        );
    }

    public function testRegisterThrowsInvalidRegistrationCodeException(): void
    {
        $this->configureTransactionToExecuteCallback();

        $this->userRepositoryMock
            ->expects($this->once())
            ->method('existsByEmail')
            ->with('test@example.com')
            ->willReturn(false);

        $this->registrationCodeRepositoryMock
            ->expects($this->once())
            ->method('findByCodeForUpdate')
            ->with('INVALID-CODE')
            ->willReturn(null);

        $this->registrationCodeRepositoryMock
            ->expects($this->never())
            ->method('isUsed');
        $this->registrationCodeRepositoryMock
            ->expects($this->never())
            ->method('getCourseIds');
        $this->registrationCodeRepositoryMock
            ->expects($this->never())
            ->method('markAsUsed');
        $this->uuidServiceMock
            ->expects($this->never())
            ->method('generate');
        $this->userRepositoryMock
            ->expects($this->never())
            ->method('create');
        $this->emailVerificationRepositoryMock
            ->expects($this->never())
            ->method('upsert');
        $this->mailerMock
            ->expects($this->never())
            ->method('send');

        $this->expectException(InvalidRegistrationCodeException::class);
        $this->expectExceptionMessage('Ungültiger Registrierungscode.');

        $this->registrationService->register(
            'test@example.com',
            'password',
            'INVALID-CODE',
            'Test Name'
        );
    }

    public function testRegisterThrowsRegistrationCodeAlreadyUsedException(): void
    {
        $this->configureTransactionToExecuteCallback();

        $this->userRepositoryMock
            ->expects($this->once())
            ->method('existsByEmail')
            ->with('test@example.com')
            ->willReturn(false);

        $this->registrationCodeRepositoryMock
            ->expects($this->once())
            ->method('findByCodeForUpdate')
            ->with('USED-CODE')
            ->willReturn(['id' => 2, 'code' => 'USED-CODE']);

        $this->registrationCodeRepositoryMock
            ->expects($this->once())
            ->method('isUsed')
            ->with('USED-CODE')
            ->willReturn(true);

        $this->registrationCodeRepositoryMock
            ->expects($this->never())
            ->method('getCourseIds');
        $this->registrationCodeRepositoryMock
            ->expects($this->never())
            ->method('markAsUsed');
        $this->uuidServiceMock
            ->expects($this->never())
            ->method('generate');
        $this->userRepositoryMock
            ->expects($this->never())
            ->method('create');
        $this->emailVerificationRepositoryMock
            ->expects($this->never())
            ->method('upsert');
        $this->mailerMock
            ->expects($this->never())
            ->method('send');

        $this->expectException(RegistrationCodeAlreadyUsedException::class);
        $this->expectExceptionMessage('Registrierungscode wurde bereits verwendet.');

        $this->registrationService->register(
            'test@example.com',
            'password',
            'USED-CODE',
            'Test Name'
        );
    }

    public function testRegisterEnrollsUserInNoCoursesWhenCodeHasNoCourses(): void
    {
        $this->configureTransactionToExecuteCallback();

        $this->userRepositoryMock
            ->expects($this->once())
            ->method('existsByEmail')
            ->with('test@example.com')
            ->willReturn(false);

        $this->registrationCodeRepositoryMock
            ->expects($this->once())
            ->method('findByCodeForUpdate')
            ->with('NO-COURSE-CODE')
            ->willReturn(['id' => 3, 'code' => 'NO-COURSE-CODE']);

        $this->registrationCodeRepositoryMock
            ->expects($this->once())
            ->method('isUsed')
            ->with('NO-COURSE-CODE')
            ->willReturn(false);

        $this->uuidServiceMock
            ->expects($this->once())
            ->method('generate')
            ->willReturn('user-uuid-nocourse');

        $this->userRepositoryMock
            ->expects($this->once())
            ->method('create')
            ->with(
                'user-uuid-nocourse',
                'test@example.com',
                $this->callback(
                    fn (string $hash) => password_verify('password', $hash)
                ),
                'Test Name'
            );

        $this->emailVerificationRepositoryMock
            ->expects($this->once())
            ->method('upsert')
            ->with(
                'user-uuid-nocourse',
                $this->callback(function (string $token): bool {
                    $this->capturedToken = $token;
                    return strlen($token) === 64;
                })
            );

        $this->registrationCodeRepositoryMock
            ->expects($this->once())
            ->method('getCourseIds')
            ->with(3)
            ->willReturn([]);

        $this->registrationCodeRepositoryMock
            ->expects($this->once())
            ->method('markAsUsed')
            ->with(3, 'user-uuid-nocourse');

        $this->userRepositoryMock
            ->expects($this->never())
            ->method('enrollInCourses');

        $this->accessCodeRepositoryMock
            ->expects($this->never())
            ->method('createForRegistration');

        $this->mailerMock
            ->expects($this->once())
            ->method('send')
            ->with(
                'test@example.com',
                'Bestätigen Sie Ihre E-Mail',
                $this->callback(
                    fn (string $body) => str_contains($body, 'register/verify?token=' . $this->capturedToken)
                )
            );

        $this->registrationService->register(
            'test@example.com',
            'password',
            'NO-COURSE-CODE',
            'Test Name'
        );

        $this->assertNotNull($this->capturedToken);
    }

    public function testRegisterPropagatesExceptionFromTransaction(): void
    {
        $this->configureTransactionToExecuteCallback();

        $this->userRepositoryMock
            ->expects($this->once())
            ->method('existsByEmail')
            ->with('test@example.com')
            ->willReturn(false);

        $this->registrationCodeRepositoryMock
            ->expects($this->once())
            ->method('findByCodeForUpdate')
            ->with('VALID-CODE')
            ->willReturn(['id' => 1, 'code' => 'VALID-CODE']);

        $this->registrationCodeRepositoryMock
            ->expects($this->once())
            ->method('isUsed')
            ->with('VALID-CODE')
            ->willReturn(false);

        $this->uuidServiceMock
            ->expects($this->once())
            ->method('generate')
            ->willReturn('user-uuid');

        $this->userRepositoryMock
            ->expects($this->once())
            ->method('create')
            ->willThrowException(new \RuntimeException('DB write failed'));

        $this->emailVerificationRepositoryMock
            ->expects($this->never())
            ->method('upsert');
        $this->mailerMock
            ->expects($this->never())
            ->method('send');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('DB write failed');

        $this->registrationService->register(
            'test@example.com',
            'securepassword',
            'VALID-CODE',
            'Test Name'
        );
    }

    public function testResendVerificationEmailUpdatesTokenAndSendsEmail(): void
    {
        $user = new User(
            id: 'user-uuid-456',
            email: 'existing@example.com',
            isAdmin: false,
            emailVerified: true,
            createdAt: '2024-01-01',
            passwordHash: '$2y$10$dummy',
            name: 'Existing User'
        );

        $this->userRepositoryMock
            ->expects($this->once())
            ->method('findByEmail')
            ->with('existing@example.com')
            ->willReturn($user);

        $this->emailVerificationRepositoryMock
            ->expects($this->once())
            ->method('upsert')
            ->with(
                'user-uuid-456',
                $this->callback(function (string $token): bool {
                    $this->capturedToken = $token;
                    return strlen($token) === 64;
                })
            );

        $this->mailerMock
            ->expects($this->once())
            ->method('send')
            ->with(
                'existing@example.com',
                'Bestätigen Sie Ihre E-Mail',
                $this->callback(
                    fn (string $body) => str_contains($body, 'register/verify?token=' . $this->capturedToken)
                )
            );

        $this->registrationService->resendVerificationEmail('existing@example.com');

        $this->assertNotNull($this->capturedToken);
        $this->assertSame(64, strlen($this->capturedToken));
    }

    public function testResendVerificationEmailThrowsWhenUserNotFound(): void
    {
        $this->userRepositoryMock
            ->expects($this->once())
            ->method('findByEmail')
            ->with('nowhere@example.com')
            ->willReturn(null);

        $this->emailVerificationRepositoryMock
            ->expects($this->never())
            ->method('upsert');
        $this->mailerMock
            ->expects($this->never())
            ->method('send');

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('Es wurde kein Benutzer mit dieser E-Mail gefunden.');

        $this->registrationService->resendVerificationEmail('nowhere@example.com');
    }
}
