<?php

namespace App\Services;

use App\Repositories\SlideRepository;
use App\Dto\Slide;
use App\Dto\SlideInput;

class SlideService {
    public function __construct(
        private SlideRepository $slideRepository
    ) {}

    public function create(
        SlideInput $slide
    ): int {
        try {
            $slideId = $this->slideRepository->create($slide);
        } catch (\Exception $e) {
            throw new \Exception("Folienerstellung fehlgeschlagen: " . $e->getMessage());
        }
        return $slideId;        
    }

    public function update(
        Slide $slide
    ): void {
        $this->slideRepository->update($slide);
    }

    public function delete(
        int $id
    ): void {
        $this->slideRepository->delete($id);
    }
}