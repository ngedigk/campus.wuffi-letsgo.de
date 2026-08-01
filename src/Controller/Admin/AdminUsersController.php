<?php

class AdminUsersController extends AdminPageController
{
    public function render(array $context): void
    {
        $viewData = [
            ...$context,
            'activePage' => 'users',
            'breadcrumb' => [
                [
                    'url' => '',
                    'title' => 'Users'
                ],
            ],
            'allUsers' => $this->userService->getAll(),
            'pageTitle' => 'Users'
        ];

        $this->viewRenderer->renderWithAdminTemplate('admin/users', $viewData);
    }

    public function handlePost(string $action): void
    {
        switch ($action) {
            case 'grant_admin':
                $this->handleGrantAdmin();
                break;
            case 'revoke_admin':
                $this->handleRevokeAdmin();
                break;
            case 'manually_verify':
                $this->handleManuallyVerify();
                break;
            default:
                throw new Exception('Unsupported admin action.');
        }
    }

    private function handleGrantAdmin(): void
    {
        $email = strtolower(trim($_POST['email'] ?? ''));
        if ($email === '') {
            throw new Exception('Please provide an email address.');
        }

        $this->userService->grantAdmin($email);
        $_SESSION['admin_success'] = 'Admin permissions granted.';
    }

    private function handleRevokeAdmin(): void
    {
        $email = strtolower(trim($_POST['email'] ?? ''));
        if ($email === '') {
            throw new Exception('Please provide an email address.');
        }

        if ($email === strtolower($this->authService->currentUser()->email)) {
            throw new Exception("Can't remove your own admin.");
        }

        $this->userService->removeAdmin($email);
        $_SESSION['admin_success'] = 'Admin permissions removed.';
    }

    private function handleManuallyVerify(): void
    {
        $email = trim($_POST['email'] ?? '');
        if ($email === '') {
            throw new Exception('Please provide an email address.');
        }

        $this->userService->verify($email);
        $_SESSION['admin_success'] = 'User manually verified.';
    }
}
