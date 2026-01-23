# Quick Start - cCrud

Guia rápido para começar a usar o cCrud em seu projeto CodeIgniter 4.

## 📦 Instalação

```bash
composer require ariellcannal/ccrud
```

## 🚀 Uso Básico

### 1. Criar um Model

```php
<?php
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'email', 'phone', 'created_at'];
}
```

### 2. Criar um Controller

```php
<?php
namespace App\Controllers;

use App\Models\UserModel;
use cCrud\cCrud;

class Users extends BaseController
{
    public function index()
    {
        $model = new UserModel();
        $crud = new cCrud($model);
        
        return view('users_crud', [
            'crud' => $crud->render()
        ]);
    }
}
```

### 3. Criar a View

```php
<!-- app/Views/users_crud.php -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários</title>
</head>
<body>
    <div class="container mt-4">
        <h1>Gerenciar Usuários</h1>
        <?= $crud ?>
    </div>
</body>
</html>
```

### 4. Configurar Rotas

```php
// app/Config/Routes.php
<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Suas rotas
$routes->get('/users', 'Users::index');

// Rotas do cCrud (OBRIGATÓRIO)
$routes->group('ccrud', ['namespace' => 'cCrud'], static function (RouteCollection $routes): void {
    $routes->add('', 'Route::router');
    $routes->add('(:any)', 'Route::router');
});
```

## ✅ Pronto!

Acesse `http://seu-site.com/users` e o CRUD estará funcionando com:

- ✅ Listagem com paginação
- ✅ Busca
- ✅ Criação de registros
- ✅ Edição de registros
- ✅ Exclusão de registros
- ✅ Interface Bootstrap 5 responsiva
- ✅ Todas as dependências carregadas automaticamente

## 🎨 Personalização

### Definir Colunas Visíveis

```php
$crud = new cCrud($model);
$crud->columns(['name', 'email', 'phone']);
```

### Definir Labels

```php
$crud->display_as('name', 'Nome Completo');
$crud->display_as('email', 'E-mail');
$crud->display_as('phone', 'Telefone');
```

### Definir Tipos de Campo

```php
$crud->field_type('email', 'email');
$crud->field_type('phone', 'text');
$crud->field_type('created_at', 'datetime');
```

### Campos Obrigatórios

```php
$crud->required_fields(['name', 'email']);
```

### Validação

```php
$crud->set_rules('email', 'E-mail', 'required|valid_email|is_unique[users.email,id,{id}]');
$crud->set_rules('phone', 'Telefone', 'required|min_length[10]');
```

### Callbacks

```php
// Antes de inserir
$crud->callback_before_insert(function($post_array) {
    $post_array['created_at'] = date('Y-m-d H:i:s');
    return $post_array;
});

// Depois de inserir
$crud->callback_after_insert(function($post_array, $primary_key) {
    log_message('info', "Usuário {$primary_key} criado");
    return true;
});

// Antes de atualizar
$crud->callback_before_update(function($post_array, $primary_key) {
    $post_array['updated_at'] = date('Y-m-d H:i:s');
    return $post_array;
});
```

## 🔧 Configuração Avançada

### Desabilitar Operações

```php
$crud->unset_add();        // Desabilita criação
$crud->unset_edit();       // Desabilita edição
$crud->unset_delete();     // Desabilita exclusão
$crud->unset_read();       // Desabilita visualização
```

### Filtros

```php
$crud->where('status', 'active');
$crud->or_where('role', 'admin');
```

### Ordenação

```php
$crud->order_by('name', 'ASC');
```

### Limitar Resultados

```php
$crud->limit(50);
```

## 📚 Documentação Completa

Para mais informações, consulte:
- [README.md](README.md) - Documentação completa
- [USAGE_GUIDE.md](USAGE_GUIDE.md) - Guia de uso detalhado
- [CHANGELOG.md](CHANGELOG.md) - Histórico de mudanças

## 🐛 Problemas Comuns

### Erro 400 (Bad Request) ao clicar em botões

**Causa**: Rotas do cCrud não configuradas

**Solução**: Adicione as rotas do cCrud em `app/Config/Routes.php`:

```php
$routes->group('ccrud', ['namespace' => 'cCrud'], static function (RouteCollection $routes): void {
    $routes->add('', 'Route::router');
    $routes->add('(:any)', 'Route::router');
});
```

### Dependências JavaScript não carregadas

**Causa**: As dependências são carregadas automaticamente, mas pode haver conflito com outras bibliotecas

**Solução**: As dependências são incluídas automaticamente via CDN. Se você já tem jQuery, Bootstrap, etc. carregados, pode haver duplicação. Isso não causa problemas, mas pode ser otimizado no futuro.

### Interface não está responsiva

**Causa**: Bootstrap 5 não está carregando corretamente

**Solução**: Verifique o console do navegador para erros. As dependências são carregadas automaticamente, mas certifique-se de que não há conflitos com outras versões do Bootstrap.

## 💡 Dicas

1. **Use Models**: Sempre use Models do CodeIgniter 4 ao invés de passar o nome da tabela diretamente
2. **Validação**: Configure validações no Model ou no Controller para garantir integridade dos dados
3. **Callbacks**: Use callbacks para lógica de negócio complexa
4. **Personalização**: O cCrud é altamente personalizável, explore todas as opções disponíveis

## 🆘 Suporte

- **Issues**: https://github.com/ariellcannal/ccrud/issues
- **Discussões**: https://github.com/ariellcannal/ccrud/discussions

---

**Versão**: 2.0.0  
**Licença**: MIT  
**Autor**: Ariell Cannal
