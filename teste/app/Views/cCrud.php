<?php
/**
 * Estrutura HTML padrão para impressão do cCrud.
 *
 * @var string $crud Conteúdo HTML gerado pelo cCrud
 */
helper('html');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>cCrud</title>
</head>
<body>
    <?= $ccrud ?>
    <?php //script_tag('https://maps.googleapis.com/maps/api/js?key=' . getenv('maps_api_key')) ?>
</body>
</html>
