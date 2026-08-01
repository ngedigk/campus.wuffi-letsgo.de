<?php

namespace App\Repositories;

use PDO;

class ProgressRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getVisitedSlideIds(string $userId, string $courseUuid): array
    {
        $sql = "SELECT ms.id FROM user_slide_views usv
                JOIN module_slides ms ON usv.slide_id = ms.id
                JOIN course_modules cm ON ms.module_id = cm.id
                WHERE usv.user_id = :userId AND cm.course_id = :courseUuid";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'userId' => $userId,
            'courseUuid' => $courseUuid
        ]);
        
        return array_column($stmt->fetchAll(), 'id');
    }

    public function recordSlideView(string $userId, int $slideId): void
    {
        $sql = "INSERT IGNORE INTO user_slide_views (user_id, slide_id, viewed_at) VALUES (:userId, :slideId, CURRENT_TIMESTAMP)";
        $this->pdo->prepare($sql)->execute([
            'userId' => $userId,
            'slideId' => $slideId
        ]);
    }

    public function isCourseCompleted(string $userId, string $courseUuid): bool
    {
        $sqlTotal = "SELECT COUNT(ms.id) FROM module_slides ms
                     JOIN course_modules cm ON ms.module_id = cm.id
                     WHERE cm.course_id = :courseUuid";
        
        $stmtTotal = $this->pdo->prepare($sqlTotal);
        $stmtTotal->execute(['courseUuid' => $courseUuid]);
        $totalSlides = (int) $stmtTotal->fetchColumn();

        if ($totalSlides === 0) {
            return true;
        }

        $sqlVisited = "SELECT COUNT(DISTINCT usv.slide_id) FROM user_slide_views usv
                       JOIN module_slides ms ON usv.slide_id = ms.id
                       JOIN course_modules cm ON ms.module_id = cm.id
                       WHERE usv.user_id = :userId AND cm.course_id = :courseUuid";
        
        $stmtVisited = $this->pdo->prepare($sqlVisited);
        $stmtVisited->execute([
            'userId' => $userId,
            'courseUuid' => $courseUuid
        ]);
        $visitedCount = (int) $stmtVisited->fetchColumn();

        return $visitedCount >= $totalSlides;
    }
}