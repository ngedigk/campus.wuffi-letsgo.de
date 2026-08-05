<?php

use App\Controller\Admin\AdminCoursesController;

use App\Middleware\AdminMiddleware;
use App\Middleware\AuthMiddleware;

$router->group([
    'prefix' => '/admin/courses',
    'middleware' => [
        AuthMiddleware::class,
        AdminMiddleware::class
    ],
], function ($router) {
    // get
    $router->get(
        '/{courseUuid}',
        [AdminCoursesController::class, 'renderCourse']
    );
    $router->get(
        '/{courseUuid}/modules/{moduleId}',
        [AdminCoursesController::class, 'renderModule']
    );
    $router->get(
        '/{courseUuid}/modules/{moduleId}/slides/{slideId}',
        [AdminCoursesController::class, 'renderSlide']
    );

    // create
    $router->post(
        '',
        [AdminCoursesController::class, 'createCourse']
    );
    $router->post(
        '/{courseUuid}/modules',
        [AdminCoursesController::class, 'createModule']
    );
    $router->post(
        '/{courseUuid}/modules/{moduleId}/slides',
        [AdminCoursesController::class, 'createSlide']
    );
    $router->post(
        '/{courseUuid}/modules/{moduleId}/slides/{slideId}/questions',
        [AdminCoursesController::class, 'createQuestion']
    );

    // update
    $router->post(
        '/{courseUuid}/update',
        [AdminCoursesController::class, 'updateCourse']
    );
    $router->post(
        '/{courseUuid}/modules/{moduleId}/update',
        [AdminCoursesController::class, 'updateModule']
    );
    $router->post(
        '/{courseUuid}/modules/{moduleId}/slides/{slideId}/update',
        [AdminCoursesController::class, 'updateSlide']
    );
    $router->post(
        '/{courseUuid}/modules/{moduleId}/slides/{slideId}/questions/{questionId}/update',
        [AdminCoursesController::class, 'updateQuestion']
    );

    // delete
    $router->post(
        '/{courseUuid}/delete',
        [AdminCoursesController::class, 'deleteCourse']
    );
    $router->post(
        '/{courseUuid}/modules/{moduleId}/delete',
        [AdminCoursesController::class, 'deleteModule']
    );
    $router->post(
        '/{courseUuid}/modules/{moduleId}/slides/{slideId}/delete',
        [AdminCoursesController::class, 'deleteSlide']
    );
    $router->post(
        '/{courseUuid}/modules/{moduleId}/slides/{slideId}/questions/{questionId}/delete',
        [AdminCoursesController::class, 'deleteQuestion']
    );

    // ajax
    $router->post(
        '/upload-image',
        [AdminCoursesController::class, 'uploadImage']
    );
    $router->post(
        '/delete-image',
        [AdminCoursesController::class, 'deleteImage'])
    ; 
});