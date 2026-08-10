<?php

namespace App\Contracts\Repositories;

use App\Dto\Slide;
use App\Dto\SlideInput;

interface SlideRepositoryInterface
{
    public function get(int $slideId): Slide;

    public function getByModuleId(int $moduleId): array;

    public function getSlidesByAudioUrl(string $audioUrl): array;

    public function create(SlideInput $slide): Slide;

    public function update(Slide $slide): Slide;

    public function delete(int $slideId): void;
}