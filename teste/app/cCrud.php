<?php

namespace Config;

/**
 * Configurações personalizadas do cCrud para este projeto.
 * 
 * Este arquivo sobrescreve as configurações padrão da biblioteca cCrud.
 * Retorne um array com as propriedades que deseja personalizar.
 */
return [
    /**
     * URI base utilizada pelo cCrud para processar requisições.
     * 
     * Deve corresponder ao grupo de rotas configurado em Routes.php.
     * 
     * Exemplos:
     * - 'ccrud' para http://localhost/projeto/ccrud/
     * - 'admin/crud' para http://localhost/projeto/admin/crud/
     * - 'ccrud' para http://dev.ccrud/ccrud/ (virtual host com subdiretório)
     */
    'request_uri' => 'ccrud',
    
    /**
     * Outras configurações podem ser adicionadas aqui.
     * Veja src/config/cCrud.php da biblioteca para todas as opções disponíveis.
     */
];
