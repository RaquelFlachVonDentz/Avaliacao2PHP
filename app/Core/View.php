<?php

namespace App\Core;

use League\Plates\Engine;
use App\Core\Url;

class View
{
    private Engine $engine;

    public function __construct()
    {
        $this->engine = new Engine(dirname(__DIR__, 2) . '/views');
        
        // Adiciona função helper para gerar URLs com o caminho base correto
        $this->engine->registerFunction('baseUrl', [Url::class, 'to']);
    }

    public function render(string $template, array $data = []): string
    {
        return $this->engine->render($template, $data);
    }
}
