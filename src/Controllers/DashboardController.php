<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\DashboardModel;
use App\Models\UserModel;

final class DashboardController extends Controller
{
    private DashboardModel $model;
    private UserModel $userModel;

    public function __construct()
    {
        $this->model = new DashboardModel();
        $this->userModel = new UserModel();
    }

    public function index(): void
    {
        // Stats créations
        $stats = $this->model->getCreationStats();
        $latestCreations = $this->model->getLatestCreations(5);

        // Stats utilisateurs
        $userStats = [
            'total' => $this->userModel->countAll(),
            'admins' => $this->userModel->countAdmins(),
            'usersOnly' => $this->userModel->countUsersOnly(),
        ];

        // Derniers utilisateurs
        $latestUsers = $this->userModel->getLatestUsers(5);

        $this->render('dashboard/index', [
            'pageTitle' => 'Dashboard admin',
            'stats' => $stats,
            'latestCreations' => $latestCreations,
            'userStats' => $userStats,
            'latestUsers' => $latestUsers,
        ]);
    }
}
