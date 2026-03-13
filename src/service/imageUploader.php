<?php

declare(strict_types=1);

namespace App\Service;

final class ImageUploader
{
    private const MAX_BYTES = 2 * 1024 * 1024; // 2 Mo
    private const MAX_WIDTH = 1200; // largeur max après resize
    private const WEBP_QUALITY = 85;

    public function __construct(
        private readonly string $uploadDir = CREATIONS_UPLOAD_DIR,
        private readonly string $publicPrefix = CREATIONS_PUBLIC_PREFIX
    ) {
        $dir = rtrim($this->uploadDir, '/\\');
        // Crée le dossier s'il n'existe pas
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
    }

    /**
     * Upload une image depuis <input type="file" name="$fieldName">
     * - Convertit en webp
     * - Redimensionne (largeur max)
     * - Nomme le fichier : slug-du-titre-0001.webp
     * - Si $existing est fourni : supprime l'ancienne image (remplacement)
     *
     * Retour : filename (ex: mon-titre-0003.webp) ou $existing si aucun fichier envoyé.
     */
    public function uploadCreationWebp(string $fieldName, string $title, ?string $existing = null): string
    {
        // 1) Aucun fichier envoyé => on garde l'existant
        if (!isset($_FILES[$fieldName]) || ($_FILES[$fieldName]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return $existing ?? '';
        }
        $file = $_FILES[$fieldName];

        // 2) Erreur d'upload (PHP)
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new \RuntimeException("Erreur upload (code " . (int)$file['error'] . ").");
        }

        // 3) Limite de taille : 2 Mo
        if (($file['size'] ?? 0) > self::MAX_BYTES) {
            throw new \RuntimeException("Image trop lourde : 2 Mo maximum.");
        }
        $tmpPath = (string)($file['tmp_name'] ?? '');

        // 4) Vérifie que c'est bien une image + récupère infos
        $info = @getimagesize($tmpPath);
        if (!$info) {
            throw new \RuntimeException("Fichier invalide : ce n'est pas une image.");
        }
        [$width, $height] = $info;
        $mime = (string)($info['mime'] ?? '');

        // 5) Autorise seulement certains formats
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($mime, $allowed, true)) {
            throw new \RuntimeException("Format non supporté. Formats autorisés : JPG, PNG, WebP.");
        }

        // 6) Charge l'image source (GD)
        $src = match ($mime) {
            'image/jpeg' => imagecreatefromjpeg($tmpPath),
            'image/png' => imagecreatefrompng($tmpPath),
            'image/webp' => function_exists('imagecreatefromwebp') ? imagecreatefromwebp($tmpPath) : null,
            default => null,
        };

        if (!$src) {
            throw new \RuntimeException("Impossible de lire l'image (GD).");
        }

        // 7) Redimensionnement : largeur max 1200 (proportions conservées)
        $scale = ($width > self::MAX_WIDTH) ? (self::MAX_WIDTH / $width) : 1.0;
        $newW = (int) round($width * $scale);
        $newH = (int) round($height * $scale);
        $dst = imagecreatetruecolor($newW, $newH);

        // 8) Gestion transparence (utile si PNG)
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefilledrectangle($dst, 0, 0, $newW, $newH, $transparent);

        // 9) Copie + resize
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $width, $height);

        // 10) Génère un nom de fichier "slug-0001.webp"
        $slug = self::slugify($title);
        $filename = $this->nextNumberedFilename($slug);
        $target = rtrim($this->uploadDir, '/\\') . DIRECTORY_SEPARATOR . $filename;

        // 11) Sauvegarde en WebP
        if (!function_exists('imagewebp')) {
            throw new \RuntimeException("Le serveur ne supporte pas l'export WebP.");
        }
        imagewebp($dst, $target, self::WEBP_QUALITY);

        // PHP 8+ : imagedestroy est inutile / déprécié -> GC
        unset($src, $dst);

        // 12) Remplacement : supprime l'ancienne image si une nouvelle a été créée
        if ($existing) {
            $this->delete($existing);
        }
        
        return $filename;
    }
    /**
     * Supprime un fichier image (sécurisé : basename)
     */
    public function delete(?string $filename): void
    {
        if (!$filename) return;
        $path = rtrim($this->uploadDir, '/\\') . DIRECTORY_SEPARATOR . basename($filename);
        if (is_file($path)) {
            @unlink($path);
        }
    }
    /**
     * URL publique d'une image (utile dans les vues)
     */
    public function getPublicUrl(?string $filename): ?string
    {
        if (!$filename) return null;
        return rtrim($this->publicPrefix, '/') . '/' . ltrim(basename($filename), '/');
    }
    /**
     * Transforme un titre en slug (mon-super-titre)
     */
    public static function slugify(string $text): string
    {
        $text = trim($text);
        $text = mb_strtolower($text, 'UTF-8');
        // translit (é -> e) si possible
        $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text;
        $text = preg_replace('~[^a-z0-9]+~', '-', $text) ?? '';
        $text = trim($text, '-');
        return $text !== '' ? $text : 'creation';
    }
    /**
     * Donne le prochain fichier disponible : slug-0001.webp, slug-0002.webp, ...
     * (en regardant ce qui existe déjà dans le dossier)
     */
    private function nextNumberedFilename(string $slug): string
    {
        $dir = rtrim($this->uploadDir, '/\\');
        $files = glob($dir . DIRECTORY_SEPARATOR . $slug . '-*.webp') ?: [];
        $max = 0;
        foreach ($files as $path) {
            $base = basename($path);
            if (preg_match('/^' . preg_quote($slug, '/') . '-(\d{4})\.webp$/', $base, $m)) {
                $n = (int) $m[1];
                if ($n > $max) $max = $n;
            }
        }
        return sprintf('%s-%04d.webp', $slug, $max + 1);
    }
}
