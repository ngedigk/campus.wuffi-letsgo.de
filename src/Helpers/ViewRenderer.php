<?php
class ViewRenderer
{
    public function __construct(private string $basePath) {}

    public function render(string $viewFile, array $data): string
    {
        $viewModel = $data;
        foreach ($data as $key => $value) {
            if (preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $key)) {
                ${$key} = $value;
            }
        }

        ob_start();
        require $this->basePath . '/Views/' . $viewFile . '.php';
        return ob_get_clean();
    }

    public function renderWithTemplate(string $viewFile, array $data): void
    {
        $content = $this->render($viewFile, $data);
        
        $viewModel = $data;
        foreach ($data as $key => $value) {
            if (preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $key)) {
                ${$key} = $value;
            }
        }

        require $this->basePath . '/Views/template.php';
    }

    public function renderWithAdminTemplate(string $viewFile, array $data): void
    {
        $content = $this->render($viewFile, $data);
        
        $viewModel = $data;
        foreach ($data as $key => $value) {
            if (preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $key)) {
                ${$key} = $value;
            }
        }

        require $this->basePath . '/Views/admin/template.php';
    }
}