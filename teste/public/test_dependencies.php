<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Dependências cCrud</title>
</head>
<body>
    <h1>Teste de Carregamento de Dependências</h1>
    
    <h2>1. Teste de Include do dependencies.php</h2>
    <?php
    define('CCRUD_PATH', __DIR__ . '/../../src');
    
    echo "<p>CCRUD_PATH: " . CCRUD_PATH . "</p>";
    echo "<p>Arquivo dependencies.php existe? " . (file_exists(CCRUD_PATH . '/views/dependencies.php') ? 'SIM' : 'NÃO') . "</p>";
    
    if (file_exists(CCRUD_PATH . '/views/dependencies.php')) {
        echo "<h3>Conteúdo do dependencies.php:</h3>";
        echo "<pre>";
        ob_start();
        include CCRUD_PATH . '/views/dependencies.php';
        $content = ob_get_clean();
        echo htmlspecialchars($content);
        echo "</pre>";
        
        echo "<h3>Renderizado:</h3>";
        echo $content;
    }
    ?>
    
    <h2>2. Teste de Verificação de Dependências</h2>
    <div id="test-results"></div>
    
    <script>
        // Aguardar carregamento completo
        window.addEventListener('load', function() {
            const results = document.getElementById('test-results');
            let html = '<ul>';
            
            // Testar jQuery
            html += '<li>jQuery: ' + (typeof jQuery !== 'undefined' ? '✅ Carregado (v' + jQuery.fn.jquery + ')' : '❌ Não carregado') + '</li>';
            
            // Testar Bootstrap
            html += '<li>Bootstrap: ' + (typeof bootstrap !== 'undefined' ? '✅ Carregado' : '❌ Não carregado') + '</li>';
            
            // Testar jQuery UI
            html += '<li>jQuery UI: ' + (typeof jQuery !== 'undefined' && jQuery.ui ? '✅ Carregado (v' + jQuery.ui.version + ')' : '❌ Não carregado') + '</li>';
            
            // Testar jQuery UI Timepicker
            html += '<li>jQuery UI Timepicker: ' + (typeof jQuery !== 'undefined' && jQuery.fn.datetimepicker ? '✅ Carregado' : '❌ Não carregado') + '</li>';
            
            // Testar Select2
            html += '<li>Select2: ' + (typeof jQuery !== 'undefined' && jQuery.fn.select2 ? '✅ Carregado' : '❌ Não carregado') + '</li>';
            
            // Testar jQuery Mask
            html += '<li>jQuery Mask: ' + (typeof jQuery !== 'undefined' && jQuery.fn.mask ? '✅ Carregado' : '❌ Não carregado') + '</li>';
            
            // Testar AlertifyJS
            html += '<li>AlertifyJS: ' + (typeof alertify !== 'undefined' ? '✅ Carregado' : '❌ Não carregado') + '</li>';
            
            // Testar CKEditor
            html += '<li>CKEditor: ' + (typeof CKEDITOR !== 'undefined' ? '✅ Carregado (v' + CKEDITOR.version + ')' : '❌ Não carregado') + '</li>';
            
            // Testar Cropper
            html += '<li>Cropper.js: ' + (typeof Cropper !== 'undefined' ? '✅ Carregado' : '❌ Não carregado') + '</li>';
            
            // Testar Font Awesome
            const links = Array.from(document.querySelectorAll('link[rel="stylesheet"]'));
            const hasFontAwesome = links.some(link => link.href.includes('font-awesome') || link.href.includes('fontawesome'));
            html += '<li>Font Awesome: ' + (hasFontAwesome ? '✅ Link encontrado' : '❌ Link não encontrado') + '</li>';
            
            html += '</ul>';
            results.innerHTML = html;
        });
    </script>
</body>
</html>
