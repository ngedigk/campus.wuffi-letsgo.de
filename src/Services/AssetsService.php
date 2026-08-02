<?php

namespace App\Services;

use \Exception;

class AssetsService
{
    public function __construct(
        private AuthService $authService,
        private CsrfService $csrfService,
        private string $assetsPath,
        private string $assetsUrl
    ) {}

    public function getAudioFiles(): array
    {
        $audioDir = $this->assetsPath . '/audio/';
        $audioFiles = [];
        if (is_dir($audioDir)) {
            foreach (scandir($audioDir) as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                $path = $audioDir . $file;
                if (is_file($path)) {
                    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    if (in_array($extension, ['mp3', 'wav', 'ogg', 'm4a', 'webm', 'aac', 'flac'])) {
                        $audioFiles[] = $file;
                    }
                }
            }
        }
        return $audioFiles;
    }

    public function getSlideAssets(): array
    {
        $assetsDir = $this->assetsPath . '/images/slides/';
        $assetsUrl = $this->assetsUrl . '/images/slides/';
        $slideAssets = [];

        if (is_dir($assetsDir)) {
            foreach (scandir($assetsDir) as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                $path = $assetsDir . $file;
                if (is_file($path)) {
                    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        $slideAssets[] = ['src' => $assetsUrl . $file];
                    }
                }
            }
        }
        return $slideAssets;
    }

    public function handleAudioUpload(array $files): ?string
    {
        if (!isset($files['audio_file']) || $files['audio_file']['error'] !== UPLOAD_ERR_OK) {
            if (isset($files['audio_file']['error']) && $files['audio_file']['error'] === UPLOAD_ERR_INI_SIZE) {
                throw new Exception('Die hochgeladene Datei ist zu groß. Bitte erhöhen Sie die "upload_max_filesize" in Ihrer php.ini.');
            }
            return null;
        }

        $maxSize = 10 * 1024 * 1024;
        if ($files['audio_file']['size'] > $maxSize) {
            throw new Exception('Die hochgeladene Datei ist zu groß. Maximal 10MB erlaubt.');
        }

        $allowedTypes = ['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg', 'audio/mp4', 'audio/webm'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $files['audio_file']['tmp_name']);
        if (!in_array($mime, $allowedTypes, true)) {
            throw new Exception('Ungültiger Dateityp. Nur Audio-Dateien sind erlaubt.');
        }

        $uploadDir = $this->assetsPath . '/audio/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $originalName = $files['audio_file']['name'];
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);

        $baseName = str_replace(['ä', 'ö', 'ü', 'Ä', 'Ö', 'Ü', 'ß'], ['ae', 'oe', 'ue', 'Ae', 'Oe', 'Ue', 'ss'], $baseName);

        $baseName = strtolower($baseName);
        $baseName = str_replace(' ', '_', $baseName);
        $filename = $baseName . '.' . $extension;
        $target = $uploadDir . $filename;

        if (file_exists($target)) {
            $i = 1;
            while (file_exists($uploadDir . $baseName . '_' . $i . '.' . $extension)) {
                $i++;
            }
            $filename = $baseName . '_' . $i . '.' . $extension;
            $target = $uploadDir . $filename;
        }

        if (!move_uploaded_file($files['audio_file']['tmp_name'], $target)) {
            throw new Exception('Fehler beim Hochladen der Audio-Datei.');
        }

        return $filename;
    }

    public function handleUploadImage(): string
    {
        if (!$this->authService->isAdmin()) {
            http_response_code(403);
            return json_encode(['error' => 'Nicht autorisiert']);
        }

        try {
            $this->csrfService->validateToken($_POST['csrf_token']);
        } catch (\Exception $e) {
            http_response_code(403);
            return json_encode(['error' => 'CSRF token ungültig']);
        }

        if (!isset($_FILES['files'])) {
            http_response_code(400);
            return json_encode(['error' => 'Keine Datei hochgeladen']);
        }

        $uploadDir = $this->assetsPath . '/images/slides/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $response = [];

        foreach ($_FILES['files']['name'] as $index => $originalName) {
            $tmpName = $_FILES['files']['tmp_name'][$index];
            $filename = uniqid() . '-' . basename($originalName);
            $target = $uploadDir . $filename;

            if (move_uploaded_file($tmpName, $target)) {
                $response[] = [
                    'src' => '/assets/images/slides/' . $filename
                ];
            }
        }

        return json_encode([
            'data' => $response
        ]);
    }

    public function handleDeleteImage(): string
    {
        if (!$this->authService->isAdmin()) {
            http_response_code(403);
            return json_encode(['error' => 'Nicht autorisiert']);
        }

        try {
            $this->csrfService->validateToken($_POST['csrf_token']);
        } catch (\Exception $e) {
            http_response_code(403);
            return json_encode(['error' => 'CSRF token ungültig']);
        }

        $src = $_POST['src'] ?? '';

        if (!$src) {
            http_response_code(400);

            return json_encode(['error' => 'Missing image source']);
        }

        $prefix = $this->assetsUrl . '/images/slides/';

        if (!str_starts_with($src, $prefix)) {
            http_response_code(400);

            return json_encode(['error' => 'Invalid image path']);
        }

        $filename = basename($src);

        $uploadDir = $this->assetsPath . '/images/slides/';
        $file = $uploadDir . $filename;

        if (!is_file($file)) {
            http_response_code(404);

            return json_encode(['error' => 'File not found']);
        }

        if (!unlink($file)) {
            http_response_code(500);

            return json_encode(['error' => 'Could not delete file']);
        }

        return json_encode(['success' => true]);
    }

    public function deleteAudioAsset(string $filename): void
    {
        $uploadDir = $this->assetsPath . '/audio/';
        $file = $uploadDir . $filename;

        if (!is_file($file)) {
            throw new Exception('Audio-Datei existiert nicht.');
        }

        if (!unlink($file)) {
            throw new Exception('Audio-Datei konnte nicht gelöscht werden.');
        }
    }
}