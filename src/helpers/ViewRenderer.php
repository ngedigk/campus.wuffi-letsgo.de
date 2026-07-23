<?php
class ViewRenderer
{
    public function __construct(private string $basePath) {}

    public function render(string $viewFile, array $data): string
    {
        extract($data, EXTR_SKIP);

        ob_start();
        require $this->basePath . '/views/' . $viewFile . '.php';
        return ob_get_clean();
    }

    public function renderWithTemplate(string $viewFile, array $data): void
    {
        $content = $this->render($viewFile, $data);
        
        extract($data, EXTR_SKIP);

        require $this->basePath . '/views/template.php';
    }

    public function renderWithAdminTemplate(string $viewFile, array $data): void
    {
        $content = $this->render($viewFile, $data);
        
        extract($data, EXTR_SKIP);

        require $this->basePath . '/views/admin/template.php';
    }
}