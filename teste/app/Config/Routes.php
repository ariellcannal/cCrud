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

/**
 * Rotas de integração com o cCrud.
 */
$routes->group('ccrud', ['namespace' => 'cCrud'], static function (RouteCollection $routes): void {
    $routes->add('(:any)', 'cCrud::router');
});

return $routes;

