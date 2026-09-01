<?php

declare(strict_types=1);
use Illuminate\Foundation\Application;

require_once __DIR__.'/vendor/autoload.php';

if (! defined('LARAVEL_VERSION')) {
    define('LARAVEL_VERSION', Application::VERSION);
}

if (! defined('Larastan\\Larastan\\LARAVEL_VERSION')) {
    define('Larastan\\Larastan\\LARAVEL_VERSION', LARAVEL_VERSION);
}
