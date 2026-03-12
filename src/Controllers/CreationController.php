<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Creation;
use App\Models\CreationModel;

final class CreationController extends Controller
{
    private CreationModel $model;

    public function __construct()
    {
        $this->model = new CreationModel();
    }

    public function index(): void
    {
        $creations = $this->model->findAll();

        $this->render('creation/index', [
            'pageTitle' => 'Créations',
            'creations' => $creations,
        ]);
    }

    public function showById(int $id): void
    {
        $creation = $this->model->find($id);

        if ($creation === null) {
            $this->abort(404, "Création #{$id} introuvable.");
        }

        $this->render('creation/show', [
            'pageTitle' => 'Détail',
            'creation' => $creation,
        ]);
    }

    public function create(): void
    {
        // GET : affiche le formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->render('creation/create', [
                'pageTitle' => 'Créer',
                'old' => ['title' => '', 'description' => '', 'picture' => ''],
                'error' => null,
            ]);
            return;
        }

        // POST : traite le formulaire
        $this->requirePost();
        $this->requireCsrf('create_creation');

        [$title, $description, $picture] = $this->getPostedData();

        if ($title === '' || $description === '') {
            $this->render('creation/create', [
                'pageTitle' => 'Créer',
                'error' => 'Titre et description obligatoires.',
                'old' => ['title' => $title, 'description' => $description, 'picture' => $picture ?? ''],
            ]);
            return;
        }

        $creation = $this->buildEntity($title, $description, $picture);

        $created = $this->model->insert($creation);

        $this->setFlash('success', 'Création ajoutée ✅');
        $this->redirect('/creations/' . $created->getIdCreation());
    }

    public function edit(int $id): void
    {
        $creation = $this->model->find($id);
        if ($creation === null) {
            $this->abort(404, "Création #{$id} introuvable.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->render('creation/edit', [
                'pageTitle' => 'Modifier',
                'creation' => $creation,
                'old' => [
                    'title' => $creation->getTitle(),
                    'description' => $creation->getDescription(),
                    'picture' => $creation->getPicture() ?? '',
                ],
                'error' => null,
            ]);
            return;
        }

        $this->requirePost();
        $this->requireCsrf('edit_creation_' . $id);

        [$title, $description, $picture] = $this->getPostedData();

        if ($title === '' || $description === '') {
            $this->render('creation/edit', [
                'pageTitle' => 'Modifier',
                'creation' => $creation,
                'error' => 'Titre et description obligatoires.',
                'old' => ['title' => $title, 'description' => $description, 'picture' => $picture ?? ''],
            ]);
            return;
        }

        $toUpdate = $this->buildEntity($title, $description, $picture);
        $updated = $this->model->update($id, $toUpdate);

        if ($updated === null) {
            $this->abort(500, "Update OK mais relecture impossible pour #{$id}");
        }
        $this->setFlash('success', 'Création modifiée ✅');
        $this->redirect('/creations/' . $id);
    }

    public function delete(int $id): void
    {
        $this->requirePost();
        $this->requireCsrf('delete_creation_' . $id);

        $ok = $this->model->delete($id);

        $this->setFlash($ok ? 'success' : 'warning', $ok ? 'Création supprimée ✅' : 'Suppression impossible.');
        $this->redirect('/creations');
    }

    private function getPostedData(): array
    {
        $title = trim((string)($_POST['title'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        $picture = trim((string)($_POST['picture'] ?? ''));
        $picture = $picture === '' ? null : $picture;

        return [$title, $description, $picture];
    }

    private function buildEntity(string $title, string $description, ?string $picture): Creation
    {
        $c = new Creation();
        $c->setTitle($title);
        $c->setDescription($description);
        $c->setPicture($picture);
        return $c;
    }
}
