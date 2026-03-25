<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Creation;
use App\Models\CreationModel;
use App\Service\FormValidator;
use App\Service\ImageUploader;

final class CreationController extends Controller
{
    private CreationModel $model;
    private ImageUploader $uploader;

    public function __construct()
    {
        $this->model = new CreationModel();
        $this->uploader = new ImageUploader();
    }

    public function index(): void
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));

        $limit = 6;
        $offset = ($page - 1) * $limit;

        $creations = $this->model->findPaginated($limit, $offset);

        $total = $this->model->countAll();
        $pages = (int) ceil($total / $limit);

        $this->render('creation/index', [
            'pageTitle' => 'Créations',
            'creations' => $creations,
            'page' => $page,
            'pages' => $pages,
        ]);
    }
    public function search(): void
    {
        $term = trim((string) ($_GET['q'] ?? ''));

        if (mb_strlen($term) < 2) {
            $this->json([
                'items' => [],
            ]);
        }

        $results = $this->model->searchForAutocomplete($term, 8);

        $items = array_map(
            static function (array $row): array {
                $id = (int) ($row['id_creation'] ?? 0);

                return [
                    'id' => $id,
                    'title' => (string) ($row['title'] ?? ''),
                    'url' => '/creations/' . $id,
                ];
            },
            $results
        );

        $this->json([
            'items' => $items,
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
                'old' => [
                    'title' => '',
                    'description' => '',
                    'picture' => ''
                ],
                'errors' => [],
            ]);
            return;
        }

        // POST : traite le formulaire
        $this->requirePost();
        $this->requireCsrf('create_creation');

        $form = $this->validateCreationForm();

        if ($form['validator']->hasErrors()) {
            $this->render('creation/create', [
                'pageTitle' => 'Créer',
                'errors' => $form['validator']->getErrors(),
                'old' => [
                    'title' => $form['title'],
                    'description' => $form['description'],
                    'picture' => ''
                ],
            ]);
            return;
        }

        // Upload (optionnel) + filename slug-0001.webp
        try {
            $filename = $this->uploader->uploadCreationWebp('picture', $form['title']);
        } catch (\RuntimeException $e) {
            $this->render('creation/create', [
                'pageTitle' => 'Créer',
                'errors' => ['picture' => $e->getMessage()],
                'old' => [
                    'title' => $form['title'],
                    'description' => $form['description'],
                    'picture' => ''
                ],
            ]);
            return;
        }

        $creation = $this->buildEntity($form['title'], $form['description'], $filename !== '' ? $filename : null);

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
                    'title' => $creation->getTitle() ?? '',
                    'description' => $creation->getDescription() ?? '',
                    'picture' => $creation->getPicture() ?? '',
                ],
                'errors' => [],
            ]);
            return;
        }

        $this->requirePost();
        $this->requireCsrf('edit_creation_' . $id);

        $form = $this->validateCreationForm();

        if ($form['validator']->hasErrors()) {
            $this->render('creation/edit', [
                'pageTitle' => 'Modifier',
                'creation' => $creation,
                'errors' => $form['validator']->getErrors(),
                'old' => [
                    'title' => $form['title'],
                    'description' => $form['description'],
                    'picture' => $creation->getPicture() ?? '',
                ],
            ]);
            return;
        }

        // Upload en remplacement : supprime l'ancienne image si une nouvelle image est envoyée
        $existing = $creation->getPicture();

        try {
            $filename = $this->uploader->uploadCreationWebp('picture', $form['title'], $existing);
        } catch (\RuntimeException $e) {
            $this->render('creation/edit', [
                'pageTitle' => 'Modifier',
                'creation' => $creation,
                'errors' => ['picture' => $e->getMessage()],
                'old' => [
                    'title' => $form['title'],
                    'description' => $form['description'],
                    'picture' => $existing ?? '',
                ],
            ]);
            return;
        }

        $toUpdate = $this->buildEntity($form['title'], $form['description'], $filename !== '' ? $filename : null);
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

    private function validateCreationForm(): array
    {
        $validator = new FormValidator($_POST);

        $validator->required('title', 'Le titre est obligatoire')
            ->minLength('title', 3, 'Le titre doit contenir au moins 3 caractères.')
            ->maxLength('title', 120, 'Le titre ne doit pas dépasser 120 caractères.')
            ->required('description', 'La description est obligatoire.')
            ->minLength('description', 10, 'La description doit contenir au moins 10 caractères.');

        return [
            'validator' => $validator,
            'title' => trim((string)($_POST['title'] ?? '')),
            'description' => trim((string)($_POST['description'] ?? '')),
        ];
    }

    private function buildEntity(string $title, string $description, ?string $picture): Creation
    {
        $c = new Creation();
        $c->setTitle(ucwords($title));
        $c->setDescription($description);
        $c->setPicture($picture);
        return $c;
    }
}
