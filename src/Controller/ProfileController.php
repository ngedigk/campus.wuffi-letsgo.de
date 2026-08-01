<?php

namespace App\Controller;

use App\Services\AuthService;
use App\Services\CsrfService;
use App\Services\UserService;
use App\Helpers\ViewRenderer;
use App\Dto\User;
use Exception;
use Throwable;

class ProfileController
{
    public function __construct(
        private AuthService $authService,
        private CsrfService $csrfService,
        private UserService $userService,
        private ViewRenderer $viewRenderer
    ) {}

    public function index(): void
    {
        if (!$this->authService->isLoggedIn()) {
            header('Location: index.php');
            exit;
        }

        $user = $this->authService->currentUser();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['action']) && $_POST['action'] === 'change_password') {
                $this->handleChangePassword($user);
                return;
            }
            $this->handleProfileUpdate($user);
            return;
        }

        $this->renderForm($user, []);
    }

    private function handleChangePassword(User $user): void
    {
        try {
            $this->csrfService->validateToken($_POST['csrf_token'] ?? '');
        } catch (Exception $e) {
            $this->renderForm($user, ['error' => $e->getMessage()]);
            return;
        }

        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        if ($password !== $passwordConfirm) {
            $this->renderForm($user, ['error' => 'Passwörter stimmen nicht überein.']);
            return;
        }

        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $this->userService->setPassword($user->id, $hash);
            $this->renderForm($user, ['success' => 'Passwort erfolgreich aktualisiert.']);
        } catch (Throwable $e) {
            error_log("Password change failed: " . $e->getMessage());
            $this->renderForm($user, ['error' => 'Bei der Aktualisierung des Passworts ist ein Fehler aufgetreten.']);
        }
    }

    private function handleProfileUpdate(User $user): void
    {
        try {
            $this->csrfService->validateToken($_POST['csrf_token'] ?? '');
        } catch (Exception $e) {
            $this->renderForm($user, ['error' => $e->getMessage()]);
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->renderForm($user, ['error' => 'Ungültige E-Mail-Adresse.']);
            return;
        }

        $existingUser = $this->userService->findByEmail($email);
        if ($existingUser && $existingUser->id !== $user->id) {
            $this->renderForm($user, ['error' => 'Diese E-Mail-Adresse wird bereits verwendet.']);
            return;
        }

        try {
            $this->userService->update($user->id, $email, $name);
            $updatedUser = $this->userService->findByEmail($email);
            $this->renderForm($updatedUser, ['success' => 'Profil erfolgreich aktualisiert.']);
        } catch (\Exception $e) {
            error_log("Profile update failed: " . $e->getMessage());
            $this->renderForm($user, ['error' => 'Fehler beim Aktualisieren des Profils.']);
        }
    }

    private function renderForm(User $user, array $context): void
    {
        $context['csrfToken'] = $this->csrfService->generateToken();
        $context['pageTitle'] = 'Profil';
        $context['isLoggedIn'] = true;
        $context['isAdmin'] = $user->isAdmin;
        $context['user'] = $user;
        $context['additionalJs'] = ['/assets/js/password-meter.js'];
        $context['additionalCss'] = ['/assets/css/register.css'];

        $this->viewRenderer->renderWithTemplate('profile', $context);
    }
}