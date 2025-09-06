<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>cCrud Teste</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <?php if (! $hasEnv): ?>
        <div class="alert alert-warning" role="alert">
            <h1 class="h4">Configuração necessária</h1>
            <p>Crie um arquivo <code>.env</code> na raiz da aplicação com as informações de conexão com o banco de dados:</p>
            <pre class="mb-0">database.default.hostname=localhost
database.default.database=nome_do_banco
database.default.username=usuario
database.default.password=senha</pre>
        </div>
    <?php else: ?>
        <div class="alert alert-success" role="alert">
            <h1 class="h4">Ambiente pronto</h1>
            <p>A tabela <code>ccrud_testes</code> foi criada e populada com 150 registros.</p>
        </div>
    <?php endif; ?>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
