<?php
namespace cCrud;

use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
if (! defined('CCRUD_PATH')) {
    define('CCRUD_PATH', str_replace('\\', '/', __DIR__));
}

/**
 * Responsável por rotear as requisições do cCrud.
 */
class Route
{

    public function initController()
    {
        return;
    }

    /**
     * Encaminha a requisição para o manipulador correspondente.
     *
     * @return ResponseInterface Resposta HTTP.
     */
    public function router(): ResponseInterface
    {
        $segment = Services::uri()->getSegment(2);

        return match ($segment) {
            'ajax' => $this->ajax(),
            'css' => $this->css(),
            'js' => $this->js(),
            default => Services::response()->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)
        };
    }

    /**
     * Processa requisições Ajax encaminhadas ao cCrud.
     *
     * @return ResponseInterface Resultado da operação.
     */
    public function ajax(): ResponseInterface
    {
        $logger = Services::logger();
        $output = cCrud::getRequestedInstance($logger);

        return $output instanceof ResponseInterface ? $output : Services::response()->setBody($output);
    }

    /**
     * Retorna o conteúdo CSS do cCrud.
     *
     * @return ResponseInterface Resposta contendo o CSS.
     */
    public function css(): ResponseInterface
    {
        $content = file_get_contents(CCRUD_PATH . '/views/cCrud.css');
        return Services::response()->setContentType('text/css')->setBody($content);
    }
    
    /**
     * Retorna o conteúdo JavaScript do cCrud.
     *
     * @return ResponseInterface Resposta contendo o JavaScript.
     */
    public function js(): ResponseInterface
    {
        $content = file_get_contents(CCRUD_PATH . '/views/cCrud.js');
        return Services::response()->setContentType('application/javascript')->setBody($content);
    }
}

