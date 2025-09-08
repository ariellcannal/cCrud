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
    <?= link_tag('node_modules/bootstrap/dist/css/bootstrap.min.css') ?>
    <?= link_tag('node_modules/jquery-ui-dist/jquery-ui.min.css') ?>
    <?= link_tag('node_modules/jcrop/dist/jcrop.css') ?>
    <?= link_tag('node_modules/select2/dist/css/select2.min.css') ?>
    <?= link_tag('node_modules/alertifyjs/build/css/alertify.min.css') ?>
</head>
<body>
    <?= $crud ?>
    <?= script_tag('node_modules/jquery/dist/jquery.min.js') ?>
    <?= script_tag('node_modules/jquery-ui-dist/jquery-ui.min.js') ?>
    <?= script_tag('node_modules/bootstrap/dist/js/bootstrap.bundle.min.js') ?>
    <?= script_tag('node_modules/alertifyjs/build/alertify.min.js') ?>
    <?= script_tag('node_modules/jcrop/dist/jcrop.js') ?>
    <?= script_tag('node_modules/ckeditor4/ckeditor.js') ?>
    <?= script_tag('node_modules/select2/dist/js/select2.min.js') ?>
    <?= script_tag('node_modules/jquery-mask-plugin/dist/jquery.mask.min.js') ?>
    <?= script_tag('https://maps.googleapis.com/maps/api/js?key=' . getenv('maps_api_key')) ?>
</body>
</html>
