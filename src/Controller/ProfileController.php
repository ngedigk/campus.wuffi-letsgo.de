<?php

namespace App\Controller;

use App\Contracts\Services\UserServiceInterface;

use App\Services\AuthService;
use App\Services\CsrfService;

use App\Helpers\ViewRenderer;

use App\Dto\User;

use App\Exceptions\CsrfException;

class ProfileController
{
    public function __construct(
        private AuthService $authService,
        private CsrfService $csrfService,
        private UserServiceInterface $userService,
        private ViewRenderer $viewRenderer
    ) {}

    public function index(): void
    {
        $user = $this->authService->currentUser();

        $this->renderForm($user, []);
    }

    public function update(): void
    {
        $user = $this->authService->currentUser();

        try {
            $this->csrfService->validateToken($_POST['csrf_token'] ?? '');
        } catch (CsrfException $e) {
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

        $this->userService->update($user->id, $email, $name);
        $updatedUser = $this->userService->findByEmail($email);
        $this->renderForm($updatedUser, ['success' => 'Profil erfolgreich aktualisiert.']);
    }

    public function changePassword(): void
    {
        $user = $this->authService->currentUser();

        try {
            $this->csrfService->validateToken($_POST['csrf_token'] ?? '');
        } catch (CsrfException $e) {
            $this->renderForm($user, ['error' => $e->getMessage()]);
            return;
        }

        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        if ($password !== $passwordConfirm) {
            $this->renderForm($user, ['error' => 'Passwörter stimmen nicht überein.']);
            return;
        }

        $this->userService->setPassword($user->id, password_hash($password, PASSWORD_DEFAULT));
        $this->renderForm($user, ['success' => 'Passwort erfolgreich aktualisiert.']);
        
    }

    private function renderForm(User $user, array $context): void
    {
        $context = array_merge(
            $context,
            [
                'csrfToken' => $this->csrfService->generateToken(),
                'pageTitle' => 'Profil',
                'isLoggedIn' => true,
                'isAdmin' => $user->isAdmin,
                'user' => $user,
                'additionalJs' => [
                    ['src' => '/assets/js/password-toggle.js'],
                    ['src' => '/assets/js/password-meter.js']
                ],
                'additionalCss' => [
                    '/assets/css/password-meter.css',
                    '/assets/css/profile.css'
                ],
                'breadcrumb' => [
                    [
                        'url' => "/",
                        'title' => "Startseite"
                    ], [
                        'title' => 'Profil'
                    ]
                ]
            ]
        );
        

        $this->viewRenderer->renderWithTemplate('profile', $context);
    }
}