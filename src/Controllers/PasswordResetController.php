<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\UserModel;
use App\Service\FormValidator;
use App\Service\PasswordResetService;
use App\Service\PasswordService;
use App\Service\PasswordValidator;

final class PasswordResetController extends Controller
{
    private UserModel $model;
    private PasswordResetService $resetService;
    private PasswordService $passwordService;

    public function __construct()
    {
        $this->model = new UserModel();
        $this->resetService = new PasswordResetService();
        $this->passwordService = new PasswordService();
    }

    public function forgotPassword(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->render('auth/forgot_password', [
                'pageTitle' => 'Mot de passe oublié',
                'old' => [
                    'email' => '',
                ],
                'errors' => [],
            ]);
            return;
        }

        $this->requirePost();
        $this->requireCsrf('forgot_password');

        $validator = new FormValidator($_POST);
        $validator
            ->required('email', 'L\'email est obligatoire.')
            ->email('email', 'Le format de l\'email est invalide.');

        $email = trim((string) ($_POST['email'] ?? ''));

        if ($validator->hasErrors()) {
            $this->render('auth/forgot_password', [
                'pageTitle' => 'Mot de passe oublié',
                'errors' => $validator->getErrors(),
                'old' => [
                    'email' => $email,
                ],
            ]);
            return;
        }

        $user = $this->model->findByEmail($email);

        if ($user !== null) {
            $resetData = $this->resetService->createResetRequest($user, $this->model);

            $resetLink = '/reset-password?selector='
                . urlencode($resetData['selector'])
                . '&token='
                . urlencode($resetData['token']);

            // Pour ce cours MVC :
            // en production, ce lien serait envoyé par email.
            // En local / en debug, on peut le stocker en flash pour le démontrer.
            if (defined('APP_DEBUG') && APP_DEBUG) {
                $this->setFlash('info', 'Lien de réinitialisation : ' . $resetLink);
            }
        }

        $this->setFlash(
            'success',
            'Si un compte correspond à cet email, un lien de réinitialisation a été généré.'
        );

        $this->redirect('/login');
    }

    public function resetPassword(): void
    {
        $selector = trim((string) ($_REQUEST['selector'] ?? ''));
        $token = trim((string) ($_REQUEST['token'] ?? ''));

        $user = $this->model->findByResetSelector($selector);

        if ($user === null || !$this->resetService->isValidResetToken($user, $token)) {
            $this->denyAccess('Lien de réinitialisation invalide ou expiré.');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->render('auth/reset_password', [
                'pageTitle' => 'Réinitialiser le mot de passe',
                'errors' => [],
                'selector' => $selector,
                'token' => $token,
            ]);
            return;
        }

        $this->requirePost();
        $this->requireCsrf('reset_password');

        $password = (string) ($_POST['password'] ?? '');
        $passwordConfirm = (string) ($_POST['password_confirm'] ?? '');

        $errors = [];

        if ($password === '') {
            $errors['password'] = 'Le mot de passe est obligatoire.';
        }

        if ($passwordConfirm === '') {
            $errors['password_confirm'] = 'La confirmation du mot de passe est obligatoire.';
        }

        if ($password !== '' && $passwordConfirm !== '' && $password !== $passwordConfirm) {
            $errors['password_confirm'] = 'Les mots de passe ne correspondent pas.';
        }

        if ($password !== '' && !isset($errors['password'])) {
            $passwordErrors = PasswordValidator::validate(
                $password,
                $user->getEmail(),
                $user->getFirstname(),
                $user->getLastname(),
                $user->getPasswordHash()
            );

            if ($passwordErrors !== []) {
                $errors['password'] = implode(' ', $passwordErrors);
            }
        }

        if (!empty($errors)) {
            $this->render('auth/reset_password', [
                'pageTitle' => 'Réinitialiser le mot de passe',
                'errors' => $errors,
                'selector' => $selector,
                'token' => $token,
            ]);
            return;
        }

        $newHash = $this->passwordService->hash($password);

        $this->model->updatePasswordHash($user->getId(), $newHash);
        $this->model->clearResetRequest($user->getId());

        $this->setFlash('success', 'Votre mot de passe a été réinitialisé avec succès.');
        $this->redirect('/login');
    }
}
