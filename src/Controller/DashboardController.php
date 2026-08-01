<?php

namespace App\Controller;

use App\Services\DashboardService;
use App\Services\RedeemService;
use App\Services\AuthService;
use App\Services\CsrfService;
use App\Helpers\ViewRenderer;
use App\Exceptions\RedeemException;
use Exception;
use Throwable;

class DashboardController
{
    public function __construct(
        private DashboardService $dashboardService,
        private ViewRenderer $viewRenderer,
        private RedeemService $redeemService,
        private AuthService $authService,
        private CsrfService $csrfService
    ) {}

    public function index(array $context): void
    {
        $courses = $this->dashboardService->getUserDashboardData($context['user']->id);

        $viewData = [
            'pageTitle' => 'Dashboard',
            ...$context,
            'courses' => $courses,
            'additionalCss' => [...$context['additionalCss'], '/assets/css/dashboard.css']
        ];

        $this->viewRenderer->renderWithTemplate('dashboard', $viewData);
    }

    public function redeem(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php");
            exit;
        }

        if (!$this->authService->isLoggedIn()) {
            header("Location: index.php");
            exit;
        }

        try {
            $this->csrfService->validateToken($_POST['csrf_token']);
        } catch (Exception $e) {
            $_SESSION['redeem_error'] = $e->getMessage();
            header("Location: index.php");
            return;
        }

        $code = trim($_POST['code'] ?? '');

        if ($code === '') {
            $_SESSION['redeem_error'] = "Ungültiger Code.";
            header("Location: index.php");
            exit;
        }

        try {
            $this->redeemService->redeem($this->authService->getCurrentUserId(), $code);
            $_SESSION['redeem_success'] = "Kurs erfolgreich eingelöst.";
        } catch (RedeemException $e) {
            $_SESSION['redeem_error'] = $e->getMessage();
        } catch (Throwable $e) {
            error_log($e);
            $_SESSION['redeem_error'] = "Etwas ist schief gelaufen. Bitte versuchen Sie es später erneut.";
        }

        header("Location: index.php");
        exit;
    }
}