<?php

namespace App\Tests;

use App\Services\AuthService;

use App\Contracts\Services\UserServiceInterface;
use App\Contracts\Repositories\AuthRepositoryInterface;

use App\Dto\User;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AuthServiceTest extends TestCase
{
    private UserServiceInterface&MockObject $userServiceMock;

    private AuthRepositoryInterface&MockObject $authRepositoryMock;

    private AuthService $authService;

    protected function setUp(): void
    {
        $this->userServiceMock = $this->createMock(UserServiceInterface::class);
        $this->authRepositoryMock = $this->createMock(AuthRepositoryInterface::class);
        $this->authService = new AuthService(
            $this->userServiceMock,
            $this->authRepositoryMock
        );

        $_SESSION = [];
        $_SERVER = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        $_SERVER = [];
    }

    private function createUser(
        string $id = 'user-123',
        string $email = 'test@example.com',
        bool $isAdmin = false,
        bool $emailVerified = true,
        string $password = 'correctpassword',
        string $name = 'Test User'
    ): User {
        return new User(
            id: $id,
            email: $email,
            isAdmin: $isAdmin,
            emailVerified: $emailVerified,
            createdAt: '2024-01-01',
            passwordHash: password_hash($password, PASSWORD_DEFAULT),
            name: $name,
        );
    }

    /**
     * @return array<string, array{hasUserId: bool, userId: ?string, expected: bool}>
     */
    public static function isLoggedInProvider(): array
    {
        return [
            'missing user_id key' => [false, null, false],
            'null user_id' => [true, null, false],
            'empty string user_id' => [true, '', false],
            'whitespace user_id' => [true, '  ', false],
            'valid user_id' => [true, 'user-123', true],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('isLoggedInProvider')]
    public function testIsLoggedIn(
        bool $hasUserId,
        ?string $userId,
        bool $expected
    ): void {
        if ($hasUserId) {
            $_SESSION['user_id'] = $userId;
        }

        $this->assertSame($expected, $this->authService->isLoggedIn());
    }

    public function testGetCurrentUserIdReturnsSessionUserId(): void
    {
        $_SESSION['user_id'] = 'user-456';
        $this->assertSame('user-456', $this->authService->getCurrentUserId());
    }

    public function testGetCurrentUserIdReturnsNullWhenNotLoggedIn(): void
    {
        $this->assertNull($this->authService->getCurrentUserId());
    }

    public function testCurrentUserReturnsNullWhenNotLoggedIn(): void
    {
        $this->assertNull($this->authService->currentUser());
    }

    public function testCurrentUserCallsUserServiceGetWithSessionUserId(): void
    {
        $_SESSION['user_id'] = 'user-789';

        $expectedUser = $this->createUser(id: 'user-789', email: 'test@example.com');

        $this->userServiceMock
            ->expects($this->once())
            ->method('get')
            ->with('user-789')
            ->willReturn($expectedUser);

        $result = $this->authService->currentUser();

        $this->assertSame($expectedUser, $result);
    }

    public function testCurrentUserReturnsNullWhenUserServiceReturnsNull(): void
    {
        $_SESSION['user_id'] = 'nonexistent';

        $this->userServiceMock
            ->expects($this->once())
            ->method('get')
            ->with('nonexistent')
            ->willReturn(null);

        $this->assertNull($this->authService->currentUser());
    }

    public function testCurrentUserUsesCacheOnSecondCall(): void
    {
        $_SESSION['user_id'] = 'user-cache';

        $user = $this->createUser(id: 'user-cache', email: 'cache@example.com');

        $this->userServiceMock
            ->expects($this->once())
            ->method('get')
            ->willReturn($user);

        $first = $this->authService->currentUser();
        $second = $this->authService->currentUser();

        $this->assertSame($user, $first);
        $this->assertSame($user, $second);
    }

    /**
     * @return array<string, array{isAdmin: bool, expected: bool}>
     */
    public static function isAdminProvider(): array
    {
        return [
            'admin user' => [true, true],
            'regular user' => [false, false],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('isAdminProvider')]
    public function testIsAdmin(bool $isAdmin, bool $expected): void
    {
        $_SESSION['user_id'] = 'admin-user';

        $user = $this->createUser(
            id: 'admin-user',
            email: 'admin@example.com',
            isAdmin: $isAdmin
        );

        $this->userServiceMock
            ->expects($this->once())
            ->method('get')
            ->with('admin-user')
            ->willReturn($user);

        $this->assertSame($expected, $this->authService->isAdmin());
    }

    public function testIsAdminReturnsFalseWhenNotLoggedIn(): void
    {
        $this->assertFalse($this->authService->isAdmin());
    }

    public function testAuthenticateReturnsUserWhenPasswordMatches(): void
    {
        $user = $this->createUser(
            id: 'auth-user',
            email: 'auth@example.com',
            password: 'correctpassword'
        );

        $this->userServiceMock
            ->expects($this->once())
            ->method('findByEmail')
            ->with('auth@example.com')
            ->willReturn($user);

        $result = $this->authService->authenticate('auth@example.com', 'correctpassword');

        $this->assertSame($user, $result);
    }

    public function testAuthenticateReturnsNullWhenUserNotFound(): void
    {
        $this->userServiceMock
            ->expects($this->once())
            ->method('findByEmail')
            ->with('noone@example.com')
            ->willReturn(null);

        $this->assertNull(
            $this->authService->authenticate('noone@example.com', 'anypassword')
        );
    }

    public function testAuthenticateReturnsNullWhenPasswordDoesNotMatch(): void
    {
        $user = $this->createUser(
            id: 'wrong-pass-user',
            email: 'wrong@example.com',
            password: 'correctpassword'
        );

        $this->userServiceMock
            ->expects($this->once())
            ->method('findByEmail')
            ->with('wrong@example.com')
            ->willReturn($user);

        $this->assertNull(
            $this->authService->authenticate('wrong@example.com', 'wrongpassword')
        );
    }

    public function testLoginReturnsBlockedWhenIpIsBlocked(): void
    {
        $_SERVER['REMOTE_ADDR'] = '192.168.1.100';

        $this->authRepositoryMock
            ->expects($this->once())
            ->method('getLoginAttemptAmount')
            ->with('192.168.1.100', 10)
            ->willReturn(5);

        $this->userServiceMock
            ->expects($this->never())
            ->method('findByEmail');

        $result = $this->authService->login('test@example.com', 'password');

        $this->assertFalse($result->success);
        $this->assertSame(
            'Zu viele Anmeldeversuche. Bitte versuchen Sie es später nochmal.',
            $result->error
        );
    }

    public function testLoginReturnsInvalidCredentialsWhenUserNotFound(): void
    {
        $_SERVER['REMOTE_ADDR'] = '192.168.1.100';

        $this->authRepositoryMock
            ->expects($this->once())
            ->method('getLoginAttemptAmount')
            ->with('192.168.1.100', 10)
            ->willReturn(0);

        $this->userServiceMock
            ->expects($this->once())
            ->method('findByEmail')
            ->with('noone@example.com')
            ->willReturn(null);

        $this->authRepositoryMock
            ->expects($this->once())
            ->method('recordFailedLogin')
            ->with('192.168.1.100');

        $result = $this->authService->login('noone@example.com', 'password');

        $this->assertFalse($result->success);
        $this->assertSame('E-Mail oder Passwort ungültig', $result->error);
    }

    public function testLoginReturnsInvalidCredentialsWhenPasswordWrong(): void
    {
        $_SERVER['REMOTE_ADDR'] = '192.168.1.100';

        $this->authRepositoryMock
            ->expects($this->once())
            ->method('getLoginAttemptAmount')
            ->with('192.168.1.100', 10)
            ->willReturn(0);

        $user = $this->createUser(
            id: 'login-user',
            email: 'login@example.com',
            password: 'correctpassword'
        );

        $this->userServiceMock
            ->expects($this->once())
            ->method('findByEmail')
            ->with('login@example.com')
            ->willReturn($user);

        $this->authRepositoryMock
            ->expects($this->once())
            ->method('recordFailedLogin')
            ->with('192.168.1.100');

        $result = $this->authService->login('login@example.com', 'wrongpassword');

        $this->assertFalse($result->success);
        $this->assertSame('E-Mail oder Passwort ungültig', $result->error);
    }

    public function testLoginReturnsEmailNotVerifiedWhenEmailNotVerified(): void
    {
        $_SERVER['REMOTE_ADDR'] = '192.168.1.100';

        $this->authRepositoryMock
            ->expects($this->once())
            ->method('getLoginAttemptAmount')
            ->with('192.168.1.100', 10)
            ->willReturn(0);

        $user = $this->createUser(
            id: 'unverified-user',
            email: 'unverified@example.com',
            emailVerified: false,
            password: 'correctpassword'
        );

        $this->userServiceMock
            ->expects($this->once())
            ->method('findByEmail')
            ->with('unverified@example.com')
            ->willReturn($user);

        $this->authRepositoryMock
            ->expects($this->once())
            ->method('recordFailedLogin')
            ->with('192.168.1.100');

        $result = $this->authService->login('unverified@example.com', 'correctpassword');

        $this->assertFalse($result->success);
        $this->assertSame(
            'Bestätigen Sie bitte erst Ihre E-Mail Adresse.',
            $result->error
        );
    }

    public function testLoginSucceedsAndSetsSessionWhenCredentialsValid(): void
    {
        $_SERVER['REMOTE_ADDR'] = '192.168.1.100';

        $this->authRepositoryMock
            ->expects($this->once())
            ->method('getLoginAttemptAmount')
            ->with('192.168.1.100', 10)
            ->willReturn(0);

        $user = $this->createUser(
            id: 'success-user',
            email: 'success@example.com',
            isAdmin: true,
            password: 'correctpassword'
        );

        $this->userServiceMock
            ->expects($this->once())
            ->method('findByEmail')
            ->with('success@example.com')
            ->willReturn($user);

        $this->authRepositoryMock
            ->expects($this->once())
            ->method('clearOldAttempts')
            ->with(10);

        $this->authRepositoryMock
            ->expects($this->never())
            ->method('recordFailedLogin');

        // AuthService requires an initialized session before login
        $this->authService->start();

        $result = $this->authService->login('success@example.com', 'correctpassword');

        $this->assertTrue($result->success);
        $this->assertNull($result->error);
        $this->assertSame('success-user', $_SESSION['user_id']);
        $this->assertSame(1, $_SESSION['is_admin']);
    }

    public function testRegularUserLoginSetsIsAdminToZero(): void
    {
        $_SERVER['REMOTE_ADDR'] = '192.168.1.100';

        $this->authRepositoryMock
            ->expects($this->once())
            ->method('getLoginAttemptAmount')
            ->with('192.168.1.100', 10)
            ->willReturn(0);

        $user = $this->createUser(
            id: 'user-regular',
            email: 'regular@example.com',
            isAdmin: false,
            password: 'correctpassword'
        );

        $this->userServiceMock
            ->expects($this->once())
            ->method('findByEmail')
            ->with('regular@example.com')
            ->willReturn($user);

        $this->authRepositoryMock
            ->expects($this->once())
            ->method('clearOldAttempts')
            ->with(10);

        $this->authRepositoryMock
            ->expects($this->never())
            ->method('recordFailedLogin');

        $this->authService->start();

        $result = $this->authService->login('regular@example.com', 'correctpassword');

        $this->assertTrue($result->success);
        $this->assertSame('user-regular', $_SESSION['user_id']);
        $this->assertSame(0, $_SESSION['is_admin']);
    }

    /**
     * @return array<string, array{attempts: int, expected: bool}>
     */
    public static function ipBlockedProvider(): array
    {
        return [
            'below limit (4)' => [4, false],
            'at limit (5)' => [5, true],
            'above limit (6)' => [6, true],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('ipBlockedProvider')]
    public function testIsIpBlocked(int $attempts, bool $expected): void
    {
        $_SERVER['REMOTE_ADDR'] = '10.0.0.1';

        $this->authRepositoryMock
            ->expects($this->once())
            ->method('getLoginAttemptAmount')
            ->with('10.0.0.1', 10)
            ->willReturn($attempts);

        $this->assertSame($expected, $this->authService->isIpBlocked());
    }

    public function testRecordFailedLoginCallsRepositoryWithClientIp(): void
    {
        $_SERVER['REMOTE_ADDR'] = '192.168.1.200';

        $this->authRepositoryMock
            ->expects($this->once())
            ->method('recordFailedLogin')
            ->with('192.168.1.200');

        $this->authService->recordFailedLogin();
    }

    public function testClearOldAttemptsCallsRepository(): void
    {
        $this->authRepositoryMock
            ->expects($this->once())
            ->method('clearOldAttempts')
            ->with(10);

        $this->authService->clearOldAttempts();
    }

    public function testClearOldAttemptsPassesCustomWindow(): void
    {
        $this->authRepositoryMock
            ->expects($this->once())
            ->method('clearOldAttempts')
            ->with(30);

        $this->authService->clearOldAttempts(30);
    }

    public function testGetClientIpReturnsRemoteAddr(): void
    {
        $_SERVER['REMOTE_ADDR'] = '203.0.113.42';
        $this->assertSame('203.0.113.42', $this->authService->getClientIp());
    }

    public function testGetClientIpReturnsDefaultWhenNoRemoteAddr(): void
    {
        $this->assertSame('0.0.0.0', $this->authService->getClientIp());
    }

    public function testStartInitializesSession(): void
    {
        $this->authService->start();
        $this->assertTrue(session_status() === PHP_SESSION_ACTIVE);
    }

    public function testStartDoesNotBreakAlreadyStartedSession(): void
    {
        $this->authService->start();
        $_SESSION['existing'] = 'data';

        $this->authService->start();

        $this->assertTrue(session_status() === PHP_SESSION_ACTIVE);
        $this->assertSame('data', $_SESSION['existing']);
    }

    public function testLoginWithMissingRemoteAddrUsesDefaultIp(): void
    {
        unset($_SERVER['REMOTE_ADDR']);

        $this->authRepositoryMock
            ->expects($this->once())
            ->method('getLoginAttemptAmount')
            ->with('0.0.0.0', 10)
            ->willReturn(0);

        $this->userServiceMock
            ->expects($this->once())
            ->method('findByEmail')
            ->with('noone@example.com')
            ->willReturn(null);

        $this->authRepositoryMock
            ->expects($this->once())
            ->method('recordFailedLogin')
            ->with('0.0.0.0');

        $result = $this->authService->login('noone@example.com', 'password');

        $this->assertFalse($result->success);
        $this->assertSame('E-Mail oder Passwort ungültig', $result->error);
    }
}
