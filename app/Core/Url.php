<?php

namespace App\Core;

class Url
{
    private static ?string $basePath = null;

    public static function basePath(): string
    {
        if (self::$basePath === null) {
            $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
            if (strpos($scriptName, '/public/') !== false) {
                // Projeto em subpasta (ex: /Avaliacao2PHP/public/index.php)
                self::$basePath = substr($scriptName, 0, strpos($scriptName, '/public'));
            } else {
                // Projeto na raiz
                self::$basePath = '';
            }
        }
        return self::$basePath;
    }

    public static function to(string $path = ''): string
    {
        $basePath = self::basePath();
        
        // Trata query string separadamente
        $query = '';
        if (strpos($path, '?') !== false) {
            [$path, $query] = explode('?', $path, 2);
            $query = '?' . $query;
        }
        
        $path = ltrim($path, '/');
        
        if ($path === '') {
            return ($basePath ?: '/') . $query;
        }
        
        return $basePath . '/' . $path . $query;
    }
}

