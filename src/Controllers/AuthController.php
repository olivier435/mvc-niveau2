<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Entities\User;
use App\Models\UserModel;
use App\Service\AuthService;
use App\Service\FormValidator;
use App\Service\PasswordService;
use App\Service\PasswordValidator;

final class AuthController extends Controller
{
    private UserModel $model;
    private PasswordService $passwordService;
    private AuthService $authService;

    public function __construct()
    {
        $this->model = new UserModel();
        $this->passwordService = new PasswordService();
        $this->authService = new AuthService();
    }

    public function register(): void
    {
        $this->redirectIfAuthenticated('/');

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->render('auth/register', [
                'pageTitle' => 'Inscription',
                'old' => [
                    'firstname' => '',
                    'lastname' => '',
                    'email' => '',
                    'address' => '',
                    'postal_code' => '',
                    'city' => '',
                    'phone' => '',
                ],
                'errors' => [],
            ]);
            return;
        }

        $this->requirePost();
        $this->requireCsrf('register');

        $form = $this->validateRegisterForm();

        $errors = array_merge(
            $form['validator']->getErrors(),
            $form['extraErrors']
        );

        if (!empty($errors)) {
            $this->render('auth/register', [
                'pageTitle' => 'Inscription',
                'errors' => $errors,
                'old' => $form['old'],
            ]);
            return;
        }

        if ($this->model->emailExists($form['email'])) {
            $this->render('auth/register', [
                'pageTitle' => 'Inscription',
                'errors' => ['email' => 'Un compte existe déjà avec cet email.'],
                'old' => $form['old'],
            ]);
            return;
        }

        $user = $this->buildUserEntity(
            firstname: $form['firstname'],
            lastname: $form['lastname'],
            email: $form['email'],
            passwordHash: $this->passwordService->hash($form['password']),
            address: $form['address'] !== '' ? $form['address'] : null,
            postalCode: $form['postal_code'] !== '' ? $form['postal_code'] : null,
            city: $form['city'] !== '' ? $form['city'] : null,
            phone: $form['phone'] !== '' ? $form['phone'] : null,
        );

        $userId = $this->model->create($user);
        $user->setId($userId);

        $this->authService->login($user);
        
        $redirectTo = $this->authService->pullTargetUrl('/');
        $this->setFlash('success', 'Compte créé, bienvenue ✅');
        $this->redirect($redirectTo);
    }

    public function login(): void
    {
        $this->redirectIfAuthenticated('/');

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->render('auth/login', [
                'pageTitle' => 'Connexion',
                'old' => [
                    'email' => '',
                ],
                'errors' => [],
            ]);
            return;
        }

        $this->requirePost();
        $this->requireCsrf('login');

        $form = $this->validateLoginForm();

        if ($form['validator']->hasErrors()) {
            $this->render('auth/login', [
                'pageTitle' => 'Connexion',
                'errors' => $form['validator']->getErrors(),
                'old' => $form['old'],
            ]);
            return;
        }

        $user = $this->model->findByEmail($form['email']);

        if (
            $user === null ||
            !$this->passwordService->verify($form['password'], $user->getPasswordHash())
        ) {
            $this->render('auth/login', [
                'pageTitle' => 'Connexion',
                'errors' => ['auth' => 'Email ou mot de passe incorrect.'],
                'old' => $form['old'],
            ]);
            return;
        }

        if ($this->passwordService->needsRehash($user->getPasswordHash())) {
            $newHash = $this->passwordService->hash($form['password']);
            $this->model->updatePasswordHash($user->getId(), $newHash);
            $user->setPasswordHash($newHash);
        }

        $this->model->updateLastLogin($user->getId());

        $this->authService->login($user);

        $redirectTo = $this->authService->pullTargetUrl('/');
        $this->setFlash('success', 'Connexion réussie ✅');
        $this->redirect($redirectTo);
    }

    public function logout(): void
    {
        $this->requirePost();
        $this->requireCsrf('logout');

        $this->authService->logout();

        $this->setFlash('success', 'Déconnexion réussie.');
        $this->redirect('/login');
    }

    private function validateRegisterForm(): array
    {
        $validator = new FormValidator($_POST);

        $validator
            ->required('firstname', 'Le prénom est obligatoire.')
            ->minLength('firstname', 2, 'Le prénom doit contenir au moins 2 caractères.')
            ->maxLength('firstname', 100, 'Le prénom ne doit pas dépasser 100 caractères.')
            ->required('lastname', 'Le nom est obligatoire.')
            ->minLength('lastname', 2, 'Le nom doit contenir au moins 2 caractères.')
            ->maxLength('lastname', 100, 'Le nom ne doit pas dépasser 100 caractères.')
            ->required('email', 'L\'email est obligatoire.')
            ->email('email', 'Le format de l\'email est invalide.')
            ->maxLength('email', 180, 'L\'email ne doit pas dépasser 180 caractères.');

        $firstname = trim((string) ($_POST['firstname'] ?? ''));
        $lastname = trim((string) ($_POST['lastname'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $address = trim((string) ($_POST['address'] ?? ''));
        $postalCode = trim((string) ($_POST['postal_code'] ?? ''));
        $city = trim((string) ($_POST['city'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $passwordConfirm = (string) ($_POST['password_confirm'] ?? '');

        $extraErrors = [];

        if ($password === '') {
            $extraErrors['password'] = 'Le mot de passe est obligatoire.';
        }

        if ($passwordConfirm === '') {
            $extraErrors['password_confirm'] = 'La confirmation du mot de passe est obligatoire.';
        }

        if ($password !== '' && $passwordConfirm !== '' && $password !== $passwordConfirm) {
            $extraErrors['password_confirm'] = 'Les mots de passe ne correspondent pas.';
        }

        if ($password !== '') {
            $passwordErrors = PasswordValidator::validate(
                $password,
                $email,
                $firstname,
                $lastname
            );
            if ($passwordErrors !== []) {
                $extraErrors['password'] = $passwordErrors[0];
            }
        }
        return [
            'validator' => $validator,
            'extraErrors' => $extraErrors,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'address' => $address,
            'postal_code' => $postalCode,
            'city' => $city,
            'phone' => $phone,
            'password' => $password,
            'old' => [
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email,
                'address' => $address,
                'postal_code' => $postalCode,
                'city' => $city,
                'phone' => $phone,
            ],
        ];
    }

    private function validateLoginForm(): array
    {
        $validator = new FormValidator($_POST);
        $validator
            ->required('email', 'L\'email est obligatoire.')
            ->email('email', 'Le format de l\'email est invalide.')
            ->required('password', 'Le mot de passe est obligatoire.');
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        return [
            'validator' => $validator,
            'email' => $email,
            'password' => $password,
            'old' => [
                'email' => $email,
            ],
        ];
    }

    private function buildUserEntity(
        string $firstname,
        string $lastname,
        string $email,
        string $passwordHash,
        ?string $address = null,
        ?string $postalCode = null,
        ?string $city = null,
        ?string $phone = null
    ): User {
        $user = new User();
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setEmail($email);
        $user->setPasswordHash($passwordHash);
        $user->setAddress($address);
        $user->setPostalCode($postalCode);
        $user->setCity($city);
        $user->setPhone($phone);
        $user->setRole('ROLE_USER');

        return $user;
    }
}
