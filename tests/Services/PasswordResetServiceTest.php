<?php

namespace App\Tests\Services;

use App\Services\PasswordResetService;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Repositories\PasswordResetsRepositoryInterface;
use App\Contracts\Mail\MailerInterface;
use App\Contracts\Database\TransactionManagerInterface;

use Psr\Log\LoggerInterface;

use App\Dto\User;

use App\Exceptions\EmailSendException;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PasswordResetServiceTest extends TestCase
{
    private UserRepositoryInterface&MockObject $userRepositoryMock;

    private PasswordResetsRepositoryInterface&MockObject $passwordResetsRepositoryMock;

    private MailerInterface&MockObject $mailerMock;

    private TransactionManagerInterface&MockObject $transactionManagerMock;
    private LoggerInterface&MockObject $loggerMock;

    private PasswordResetService $passwordResetService;

    protected function setUp(): void
    {
        if (!defined('SITE_URL')) {
            define('SITE_URL', 'http://localhost:8080');
        }

        $this->userRepositoryMock = $this->createMock(UserRepositoryInterface::class);
        $this->passwordResetsRepositoryMock = $this->createMock(PasswordResetsRepositoryInterface::class);
        $this->mailerMock = $this->createMock(MailerInterface::class);
        $this->transactionManagerMock = $this->createMock(TransactionManagerInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->passwordResetService = new PasswordResetService(
            $this->userRepositoryMock,
            $this->passwordResetsRepositoryMock,
            $this->mailerMock,
            $this->transactionManagerMock,
            $this->loggerMock
        );
    }

    private function createUser(
        string $id = 'user-1',
        string $email = 'user@example.com'
    ): User {
        return new User(
            id: $id,
            email: $email,
            isAdmin: false,
            emailVerified: true,
            createdAt: '2024-01-01',
            passwordHash: password_hash('password', PASSWORD_DEFAULT),
            name: 'Test User',
        );
    }

    public function testRequestResetSilentlyReturnsWhenUserNotFound(): void
    {
        $this->userRepositoryMock
            ->expects($this->once())
            ->method('findByEmail')
            ->with('nobody@example.com')
            ->willReturn(null);

        $this->passwordResetsRepositoryMock
            ->expects($this->never())
            ->method('recordReset');

        $this->mailerMock
            ->expects($this->never())
            ->method('send');

        $this->passwordResetService->requestReset('nobody@example.com');
    }

    public function testRequestResetSavesTokenAndSendsEmailWhenUserExists(): void
    {
        $user = $this->createUser(
            id: 'user-reset-1',
            email: 'reset@example.com'
        );

        $this->userRepositoryMock
            ->expects($this->once())
            ->method('findByEmail')
            ->with('reset@example.com')
            ->willReturn($user);

        $this->passwordResetsRepositoryMock
            ->expects($this->once())
            ->method('recordReset')
            ->with('user-reset-1', $this->isType('string'));

        $this->mailerMock
            ->expects($this->once())
            ->method('send')
            ->with('reset@example.com', 'Passwort zurücksetzen', $this->stringContains('reset-password?token='));

        $this->passwordResetService->requestReset('reset@example.com');
    }

    public function testRequestResetGeneratesUniqueTokensOnConsecutiveCalls(): void
    {
        $user = $this->createUser(
            id: 'user-token',
            email: 'tokentest@example.com'
        );

        $this->userRepositoryMock
            ->expects($this->exactly(2))
            ->method('findByEmail')
            ->willReturn($user);

        $tokens = [];

        $this->passwordResetsRepositoryMock
            ->expects($this->exactly(2))
            ->method('recordReset')
            ->willReturnCallback(function (string $userId, string $token) use (&$tokens) {
                $tokens[] = $token;
            });

        $this->mailerMock
            ->expects($this->exactly(2))
            ->method('send');

        $this->passwordResetService->requestReset('tokentest@example.com');
        $this->passwordResetService->requestReset('tokentest@example.com');

        $this->assertCount(2, $tokens);
        $this->assertNotSame($tokens[0], $tokens[1]);
    }

    /**
     * Reset tokens must be 256 bits (64 hex characters).
     *
     * This is the security contract: a sufficiently long, unpredictable token
     * that an attacker cannot guess. The 64-char hex format is an intentional
     * application-level choice (not an incidental consequence of bin2hex).
     */
    public function testRequestResetGenerates256BitHexToken(): void
    {
        $user = $this->createUser(
            id: 'user-token-len',
            email: 'len@example.com'
        );

        $this->userRepositoryMock
            ->expects($this->once())
            ->method('findByEmail')
            ->willReturn($user);

        $capturedToken = '';

        $this->passwordResetsRepositoryMock
            ->expects($this->once())
            ->method('recordReset')
            ->willReturnCallback(function (string $userId, string $token) use (&$capturedToken) {
                $capturedToken = $token;
            });

        $this->mailerMock
            ->expects($this->once())
            ->method('send');

        $this->passwordResetService->requestReset('len@example.com');

        $this->assertSame(64, strlen($capturedToken));
        $this->assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $capturedToken);
    }

    public function testRequestResetUsesSameTokenForPersistenceAndEmail(): void
    {
        $user = $this->createUser(
            id: 'user-encode',
            email: 'encode@example.com'
        );

        $this->userRepositoryMock
            ->expects($this->once())
            ->method('findByEmail')
            ->willReturn($user);

        $capturedToken = '';
        $capturedHtml = '';

        $this->passwordResetsRepositoryMock
            ->expects($this->once())
            ->method('recordReset')
            ->willReturnCallback(function (string $userId, string $token) use (&$capturedToken) {
                $capturedToken = $token;
            });

        $this->mailerMock
            ->expects($this->once())
            ->method('send')
            ->willReturnCallback(function (string $to, string $subject, string $html) use (&$capturedHtml) {
                $capturedHtml = $html;
            });

        $this->passwordResetService->requestReset('encode@example.com');

        $this->assertStringContainsString(
            'token=' . rawurlencode($capturedToken),
            $capturedHtml,
            'The token persisted to the database must match the token embedded in the reset email.'
        );
    }

    public function testRequestResetDoesNotThrowWhenEmailSendFails(): void
    {
        $user = $this->createUser(
            id: 'user-mail-fail',
            email: 'mailfail@example.com'
        );

        $this->userRepositoryMock
            ->expects($this->once())
            ->method('findByEmail')
            ->willReturn($user);

        $this->passwordResetsRepositoryMock
            ->expects($this->once())
            ->method('recordReset');

        $this->mailerMock
            ->expects($this->once())
            ->method('send')
            ->willThrowException(new EmailSendException('SMTP error'));

        $this->passwordResetService->requestReset('mailfail@example.com');
    }

    public function testRequestResetTokenPersistedBeforeEmailSent(): void
    {
        $user = $this->createUser(
            id: 'user-order',
            email: 'order@example.com'
        );

        $this->userRepositoryMock
            ->expects($this->once())
            ->method('findByEmail')
            ->willReturn($user);

        $order = [];

        $this->passwordResetsRepositoryMock
            ->expects($this->once())
            ->method('recordReset')
            ->willReturnCallback(function () use (&$order) {
                $order[] = 'recordReset';
            });

        $this->mailerMock
            ->expects($this->once())
            ->method('send')
            ->willReturnCallback(function () use (&$order) {
                $order[] = 'send';
            });

        $this->passwordResetService->requestReset('order@example.com');

        $this->assertSame(['recordReset', 'send'], $order);
    }

    public function testGetUserUuidByTokenReturnsUuidWhenTokenExists(): void
    {
        $this->passwordResetsRepositoryMock
            ->expects($this->once())
            ->method('getUserUuidByToken')
            ->with('valid-token')
            ->willReturn('user-uuid-123');

        $result = $this->passwordResetService->getUserUuidByToken('valid-token');

        $this->assertSame('user-uuid-123', $result);
    }

    public function testGetUserUuidByTokenReturnsNullForEmptyToken(): void
    {
        $this->passwordResetsRepositoryMock
            ->expects($this->never())
            ->method('getUserUuidByToken');

        $result = $this->passwordResetService->getUserUuidByToken('');

        $this->assertNull($result);
    }

    public function testGetUserUuidByTokenReturnsNullForInvalidToken(): void
    {
        $this->passwordResetsRepositoryMock
            ->expects($this->once())
            ->method('getUserUuidByToken')
            ->with('invalid-token')
            ->willReturn('');

        $result = $this->passwordResetService->getUserUuidByToken('invalid-token');

        $this->assertNull($result);
    }

    public function testResetPasswordUpdatesPasswordAndDeletesResetRecordsInsideTransaction(): void
    {
        $userUuid = 'user-reset-password';
        $newPassword = 'newSecurePassword123!';

        $operations = [];

        $this->transactionManagerMock
            ->expects($this->once())
            ->method('run')
            ->willReturnCallback(function (callable $callback) use (&$operations) {
                $operations[] = 'transaction_start';
                $callback();
                $operations[] = 'transaction_end';
                return null;
            });

        $this->userRepositoryMock
            ->expects($this->once())
            ->method('setPassword')
            ->with(
                $userUuid,
                $this->callback(
                    fn (string $hash) => password_verify($newPassword, $hash)
                )
            );

        $this->passwordResetsRepositoryMock
            ->expects($this->once())
            ->method('deleteRecordsByUserId')
            ->with($userUuid);

        $this->passwordResetService->resetPassword($userUuid, $newPassword);

        $this->assertSame(['transaction_start', 'transaction_end'], $operations);
    }

    public function testResetPasswordPropagatesTransactionFailure(): void
    {
        $userUuid = 'user-tx-fail';
        $newPassword = 'password';

        $this->transactionManagerMock
            ->expects($this->once())
            ->method('run')
            ->willThrowException(new \RuntimeException('Transaction failed'));

        $this->userRepositoryMock
            ->expects($this->never())
            ->method('setPassword');

        $this->passwordResetsRepositoryMock
            ->expects($this->never())
            ->method('deleteRecordsByUserId');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Transaction failed');

        $this->passwordResetService->resetPassword($userUuid, $newPassword);
    }

    public function testResetPasswordPropagatesResetRecordDeletionFailure(): void
    {
        $userUuid = 'user-delete-fail';
        $newPassword = 'password';

        $this->transactionManagerMock
            ->expects($this->once())
            ->method('run')
            ->willReturnCallback(fn (callable $callback) => $callback());

        $this->userRepositoryMock
            ->expects($this->once())
            ->method('setPassword');

        $this->passwordResetsRepositoryMock
            ->expects($this->once())
            ->method('deleteRecordsByUserId')
            ->willThrowException(new \RuntimeException('Delete failed'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Delete failed');

        $this->passwordResetService->resetPassword($userUuid, $newPassword);
    }

}
