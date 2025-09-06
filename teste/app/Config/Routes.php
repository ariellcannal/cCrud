<?php

namespace Config;

use CodeIgniter\Config\Services;
use CodeIgniter\Router\RouteCollection;

/**
 * Rotas principais da aplicação de testes.
 *
 * @var RouteCollection $routes
 */
$routes = Services::routes();

// Rota padrão para a página inicial
$routes->get('/', 'Home::index');

return $routes;

