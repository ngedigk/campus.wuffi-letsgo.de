<?php

namespace App\Tests\Services;

use App\Services\RedeemService;

use App\Contracts\Repositories\AccessCodeRepositoryInterface;
use App\Contracts\Repositories\UserCourseRepositoryInterface;
use App\Contracts\Database\TransactionManagerInterface;

use App\Exceptions\RedeemException;
use App\Exceptions\CourseAlreadyAddedException;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RedeemServiceTest extends TestCase
{
    private TransactionManagerInterface&MockObject $transactionManagerMock;
    private AccessCodeRepositoryInterface&MockObject $accessCodeRepositoryMock;
    private UserCourseRepositoryInterface&MockObject $userCourseRepositoryMock;

    private RedeemService $redeemService;

    protected function setUp(): void
    {
        $this->transactionManagerMock = $this->createMock(TransactionManagerInterface::class);
        $this->accessCodeRepositoryMock = $this->createMock(AccessCodeRepositoryInterface::class);
        $this->userCourseRepositoryMock = $this->createMock(UserCourseRepositoryInterface::class);

        $this->redeemService = new RedeemService(
            $this->transactionManagerMock,
            $this->accessCodeRepositoryMock,
            $this->userCourseRepositoryMock
        );
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

    public function testRedeemSuccessfullyAddsCourse(): void
    {
        $this->configureTransactionToExecuteCallback();

        $this->accessCodeRepositoryMock
            ->expects($this->once())
            ->method('findByCodeForUpdate')
            ->with('VALID-CODE')
            ->willReturn([
                'id' => 42,
                'course_id' => 'course-uuid-123',
            ]);

        $this->userCourseRepositoryMock
            ->expects($this->once())
            ->method('userHasCourse')
            ->with('user-uuid-1', 'course-uuid-123')
            ->willReturn(false);

        $this->userCourseRepositoryMock
            ->expects($this->once())
            ->method('addCourse')
            ->with('user-uuid-1', 'course-uuid-123', 42);

        $this->redeemService->redeem('user-uuid-1', 'VALID-CODE');
    }

    public function testRedeemThrowsRedeemExceptionForInvalidCode(): void
    {
        $this->configureTransactionToExecuteCallback();

        $this->accessCodeRepositoryMock
            ->expects($this->once())
            ->method('findByCodeForUpdate')
            ->with('INVALID-CODE')
            ->willReturn(null);

        $this->userCourseRepositoryMock
            ->expects($this->never())
            ->method('userHasCourse');
        $this->userCourseRepositoryMock
            ->expects($this->never())
            ->method('addCourse');

        $this->expectException(RedeemException::class);
        $this->expectExceptionMessage('Ungültiger Code.');

        $this->redeemService->redeem('user-uuid-1', 'INVALID-CODE');
    }

    public function testRedeemThrowsRedeemExceptionWhenUserAlreadyHasCourse(): void
    {
        $this->configureTransactionToExecuteCallback();

        $this->accessCodeRepositoryMock
            ->expects($this->once())
            ->method('findByCodeForUpdate')
            ->with('ALREADY-HAS-COURSE')
            ->willReturn([
                'id' => 43,
                'course_id' => 'course-uuid-456',
            ]);

        $this->userCourseRepositoryMock
            ->expects($this->once())
            ->method('userHasCourse')
            ->with('user-uuid-2', 'course-uuid-456')
            ->willReturn(true);

        $this->userCourseRepositoryMock
            ->expects($this->never())
            ->method('addCourse');

        $this->expectException(RedeemException::class);
        $this->expectExceptionMessage('Sie haben bereits Zugriff auf diesen Kurs.');

        $this->redeemService->redeem('user-uuid-2', 'ALREADY-HAS-COURSE');
    }

    public function testRedeemPropagatesCourseAlreadyAddedExceptionAsRedeemExceptionWithPrevious(): void
    {
        $this->configureTransactionToExecuteCallback();

        $this->accessCodeRepositoryMock
            ->expects($this->once())
            ->method('findByCodeForUpdate')
            ->with('DUPE-CODE')
            ->willReturn([
                'id' => 44,
                'course_id' => 'course-uuid-789',
            ]);

        $this->userCourseRepositoryMock
            ->expects($this->once())
            ->method('userHasCourse')
            ->with('user-uuid-3', 'course-uuid-789')
            ->willReturn(false);

        $this->userCourseRepositoryMock
            ->expects($this->once())
            ->method('addCourse')
            ->with('user-uuid-3', 'course-uuid-789', 44)
            ->willThrowException(new CourseAlreadyAddedException('Already added'));

        $this->expectException(RedeemException::class);
        $this->expectExceptionMessage('Dieser Zugangscode wurde bereits eingelöst.');

        try {
            $this->redeemService->redeem('user-uuid-3', 'DUPE-CODE');
        } catch (RedeemException $e) {
            $this->assertInstanceOf(CourseAlreadyAddedException::class, $e->getPrevious());

            throw $e;
        }
    }
}
