<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\UserModel;
use App\Service\PasswordResetService;

final class AdminUserController extends Controller
{
    private UserModel $userModel;
    private PasswordResetService $passwordResetService;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->passwordResetService = new PasswordResetService();
    }

    public function index(): void
    {
        $this->requireRole('ROLE_ADMIN');

        $users = $this->userModel->findAllOrderedByCreatedAtDesc();

        $this->render('admin_user/index', [
            'pageTitle' => 'Gestion des utilisateurs',
            'users' => $users,
        ]);
    }

    public function edit(int $id): void
    {
        $this->requireRole('ROLE_ADMIN');

        $user = $this->userModel->findById($id);

        if (!$user) {
            $this->setFlash('danger', 'Utilisateur introuvable.');
            $this->redirect('/admin/users');
        }

        if (strtoupper($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            $this->requireCsrf('edit_user_' . $id);

            $firstname = trim((string) ($_POST['firstname'] ?? ''));
            $lastname  = trim((string) ($_POST['lastname'] ?? ''));
            $email     = trim((string) ($_POST['email'] ?? ''));
            $role      = trim((string) ($_POST['role'] ?? 'ROLE_USER'));

            $errors = [];

            if ($firstname === '') {
                $errors[] = 'Le prénom est obligatoire.';
            }

            if ($lastname === '') {
                $errors[] = 'Le nom est obligatoire.';
            }

            if ($email === '') {
                $errors[] = 'L\'email est obligatoire.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'L\'email est invalide.';
            }

            if (!in_array($role, ['ROLE_ADMIN', 'ROLE_USER'], true)) {
                $errors[] = 'Le rôle sélectionné est invalide.';
            }

            $existingUser = $this->userModel->findByEmail($email);
            if ($existingUser && $existingUser->getId() !== $user->getId()) {
                $errors[] = 'Cet email est déjà utilisé par un autre utilisateur.';
            }

            $currentUser = $this->getUser();
            if (
                $currentUser !== null
                && (int) $currentUser['id'] === (int) $user->getId()
                && $role !== 'ROLE_ADMIN'
            ) {
                $errors[] = 'Vous ne pouvez pas retirer votre propre rôle administrateur.';
            }

            if ($errors !== []) {
                foreach ($errors as $error) {
                    $this->setFlash('danger', $error);
                }

                $user
                    ->setFirstname($firstname)
                    ->setLastname($lastname)
                    ->setEmail($email)
                    ->setRole($role);

                $this->render('admin_user/edit', [
                    'pageTitle' => 'Modifier un utilisateur',
                    'user' => $user,
                ]);
                return;
            }

            $user
                ->setFirstname($firstname)
                ->setLastname($lastname)
                ->setEmail($email)
                ->setRole($role);

            $this->userModel->updateAdminFields($user);

            $this->setFlash('success', 'Utilisateur modifié avec succès.');
            $this->redirect('/admin/users');
        }

        $this->render('admin_user/edit', [
            'pageTitle' => 'Modifier un utilisateur',
            'user' => $user,
        ]);
    }

    public function delete(int $id): void
    {
        $this->requireRole('ROLE_ADMIN');
        $this->requirePost();
        $this->requireCsrf('delete_user_' . $id);

        $user = $this->userModel->findById($id);

        if (!$user) {
            $this->setFlash('danger', 'Utilisateur introuvable.');
            $this->redirect('/admin/users');
        }

        $currentUser = $this->getUser();

        if ($currentUser && (int) $currentUser['id'] === (int) $user->getId()) {
            $this->setFlash('danger', 'Vous ne pouvez pas supprimer votre propre compte.');
            $this->redirect('/admin/users');
        }

        if ($user->getRole() === 'ROLE_ADMIN' && $this->userModel->countAdmins() <= 1) {
            $this->setFlash('danger', 'Impossible de supprimer le dernier administrateur.');
            $this->redirect('/admin/users');
        }

        $this->userModel->deleteById($id);

        $this->setFlash('success', 'Utilisateur supprimé avec succès.');
        $this->redirect('/admin/users');
    }

    public function sendResetLink(int $id): void
    {
        $this->requireRole('ROLE_ADMIN');
        $this->requirePost();
        $this->requireCsrf('reset_password_user_' . $id);

        $user = $this->userModel->findById($id);

        if (!$user) {
            $this->setFlash('danger', 'Utilisateur introuvable.');
            $this->redirect('/admin/users');
        }

        $resetData = $this->passwordResetService->createResetRequest($user, $this->userModel);

        $resetUrl = '/reset-password?selector='
            . urlencode($resetData['selector'])
            . '&token='
            . urlencode($resetData['token']);

        // À remplacer ensuite par l'envoi réel d'un email.
        $this->setFlash(
            'success',
            'Lien de réinitialisation généré pour ' . $user->getEmail() . ' : ' . $resetUrl
        );

        $this->redirect('/admin/users');
    }
}
