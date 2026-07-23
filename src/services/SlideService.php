<?php

class SlideService {
    public function __construct(
        private SlideRepository $slideRepository
    ) {}

    public function create(
        CreateSlide $slide
    ): int {
        try {
            $slideId = $this->slideRepository->create($slide);
        } catch (\Exception $e) {
            throw new \Exception("Failed to create slide: " . $e->getMessage());
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