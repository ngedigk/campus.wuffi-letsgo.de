<?php

namespace App\Repositories;

use App\Contracts\Repositories\CourseRepositoryInterface;

use App\Dto\Course;
use App\Dto\CourseInput;

use \PDO;

class CourseRepository implements CourseRepositoryInterface
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function get(string $courseUuid): Course
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM courses
            WHERE id = :courseUuid
            ORDER BY sort_order
        ");

        $stmt->execute(['courseUuid' => $courseUuid]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $this->createDto($row);
    }

    public function getCourseForUser(string $userUuid, string $courseUuid): ?Course
    {
        $stmt = $this->pdo->prepare("
            SELECT
                c.id,
                c.title,
                c.description,
                c.prerequisite_course_id,
                c.sort_order
            FROM courses c
            INNER JOIN user_courses uc
                ON uc.course_id = c.id
            WHERE
                uc.user_id = :userUuid
                AND c.id = :courseUuid
            ORDER BY c.sort_order
        ");

        $stmt->execute([
            'userUuid' => $userUuid,
            'courseUuid' => $courseUuid
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        return $this->createDto($row);
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM courses
            ORDER BY sort_order
        ");

        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function($row) {
            return $this->createDto($row);
        }, $rows);
    }

    public function getAllForUser(string $userUuid): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                c.id,
                c.title,
                c.description,
                c.prerequisite_course_id,
                c.sort_order
            FROM courses c
            INNER JOIN user_courses uc
                ON uc.course_id = c.id
            WHERE
                uc.user_id = :userUuid
            ORDER BY c.sort_order
        ");

        $stmt->execute(['userUuid' => $userUuid]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function($row) {
            return $this->createDto($row);
        }, $rows);
    }

    public function create(string $courseUuid, CourseInput $course): Course
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO courses
            (
                id,
                title,
                description,
                prerequisite_course_id,
                sort_order
            )
            VALUES (:uuid, :title, :description, :prerequisiteCourseId, :sortOrder)
        ");

        $stmt->execute(array_merge(
            ['uuid' => $courseUuid],
            $course->toArray()
        ));

        return $this->createDto([
            'id' => $courseUuid,
            'title' => $course->title,
            'description' => $course->description,
            'prerequisite_course_id' => $course->prerequisiteCourseId,
            'sort_order' => $course->sortOrder
        ]);
    }

    public function update(Course $course): Course
    {
        $stmt = $this->pdo->prepare("
            UPDATE courses
            SET title = :title, description = :description, prerequisite_course_id = :prerequisiteCourseId, sort_order = :sortOrder
            WHERE id = :uuid
        ");
        $stmt->execute($course->toArray());

        return $this->createDto([
            'id' => $course->uuid,
            'title' => $course->title,
            'description' => $course->description,
            'prerequisite_course_id' => $course->prerequisiteCourseId,
            'sort_order' => $course->sortOrder
        ]);
    }

    public function delete(string $uuid): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM courses WHERE id = :uuid");
        $stmt->execute(['uuid' => $uuid]);
    }

    private function createDto(array $row): Course
    {
        return new Course(
            uuid: $row['id'],
            title: $row['title'],
            description: $row['description'],
            prerequisiteCourseId: $row['prerequisite_course_id'],
            sortOrder: $row['sort_order'],
            modules: null
        );
    }
}