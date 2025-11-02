# Guia de Uso - cCrud 2.0

Este guia demonstra como utilizar os novos recursos do cCrud 2.0.

## Instalação

```bash
composer require ariellcannal/ccrud
```

O pacote será instalado em `vendor/ariellcannal/ccrud/` e estará pronto para uso sem necessidade de comandos adicionais.

## Configuração Básica

### 1. Uso Simples

```php
use cCrud\cCrud;

$crud = new cCrud('users');
echo $crud->render();
```

### 2. Com Filtros Avançados

```php
use cCrud\cCrud;

$crud = new cCrud('users');

// Adicionar filtro simples
$crud->addFilter('status', 'active', '=');

// Adicionar filtro com OR
$crud->addFilter('role', 'admin', '=', 'OR');
$crud->addFilter('role', 'manager', '=', 'OR');

echo $crud->render();
```

### 3. Com reCAPTCHA

```php
use cCrud\cCrud;

$crud = new cCrud('users');

// Habilitar reCAPTCHA v3
$crud->enableRecaptcha(
    'sua-site-key',
    'sua-secret-key',
    'v3'
);

// Definir score mínimo
$crud->setRecaptchaMinScore(0.5);

// Definir ações que requerem validação
$crud->setRecaptchaActions(['create', 'edit', 'delete']);

echo $crud->render();
```

### 4. Com Rastreamento

```php
use cCrud\cCrud;

$crud = new cCrud('users');

// Habilitar Google Analytics
$crud->enableGoogleAnalytics('G-XXXXXXXXXX');

// Habilitar Facebook Pixel
$crud->enableFacebookPixel('1234567890');

// Habilitar Google Ads
$crud->enableGoogleAds('AW-XXXXXXXXX', 'conversion-label');

echo $crud->render();
```

## Funcionalidades JavaScript

### History API

O cCrud agora gerencia automaticamente o histórico de navegação:

```javascript
// Criar instância do gerenciador de histórico
const history = new cCrudHistory({
    baseUrl: window.location.origin,
    enableDeepLinking: true,
    enablePopState: true
});

// Adicionar estado ao histórico
history.pushState({
    table: 'users',
    action: 'edit',
    primary: 123
}, 'Editar Usuário');

// Navegar para trás
history.back();
```

**URLs geradas automaticamente:**
- Listagem: `/ccrud/users`
- Criar: `/ccrud/users/create`
- Editar: `/ccrud/users/123/edit`
- Visualizar: `/ccrud/users/123/view`

**Navegação com botões voltar/avançar funciona perfeitamente!**

### Filtros Avançados

```javascript
// Criar instância do gerenciador de filtros
const filters = new cCrudFilters('users', {
    defaultOperator: 'AND'
});

// Adicionar filtro
filters.addFilter({
    column: 'status',
    columnType: 'text',
    operator: '=',
    value: 'active',
    logicalOperator: 'AND'
});

// Adicionar filtro com OR
filters.addFilter({
    column: 'role',
    columnType: 'text',
    operator: 'IN',
    value: 'admin,manager',
    logicalOperator: 'OR'
});

// Aplicar filtros
filters.applyFilters();

// Salvar visualização
filters.saveView('Usuários Ativos', {
    columns: ['id', 'name', 'email', 'status'],
    orderBy: 'name ASC',
    limit: 50
});

// Carregar visualização
filters.loadView('Usuários Ativos');
```

### Gerenciamento de Colunas

```javascript
// Criar instância do gerenciador de colunas
const columns = new cCrudColumns('users', [
    { name: 'id', label: 'ID', type: 'int' },
    { name: 'name', label: 'Nome', type: 'text' },
    { name: 'email', label: 'E-mail', type: 'email' },
    { name: 'status', label: 'Status', type: 'text' }
], {
    enableResize: true,
    enableReorder: true,
    enableVisibility: true,
    minWidth: 80,
    maxWidth: 500
});

// Definir visibilidade de coluna
columns.setColumnVisibility('id', false);

// Definir largura de coluna
columns.setColumnWidth('name', 200);

// Resetar preferências
columns.resetPreferences();
```

**Funcionalidades automáticas:**
- **Redimensionar**: Arraste a borda direita do cabeçalho
- **Reordenar**: Arraste e solte os cabeçalhos
- **Visibilidade**: Use o seletor de colunas

## Estilos Notion-like

### Aplicar Estilos

Adicione o CSS Notion no seu HTML:

```html
<link rel="stylesheet" href="/vendor/ariellcannal/ccrud/src/views/cCrudNotion.css">
```

### Customizar Cores

```css
:root {
    --ccrud-notion-bg: #ffffff;
    --ccrud-notion-bg-hover: #f7f6f3;
    --ccrud-notion-border: #e9e9e7;
    --ccrud-notion-text: #37352f;
    --ccrud-notion-primary: #2383e2;
}
```

### Offcanvas Responsivo

O offcanvas para formulários se adapta automaticamente:

- **Desktop (> 768px)**: Painel lateral de 600px
- **Mobile (≤ 768px)**: Tela cheia

## Eventos Customizados

### JavaScript

```javascript
// Evento quando filtro é adicionado
$(document).on('cCrudFilterAdded', function(e, filter, instance) {
    console.log('Filtro adicionado:', filter);
});

// Evento quando visualização é carregada
$(document).on('cCrudViewLoaded', function(e, view, instance) {
    console.log('Visualização carregada:', view);
});

// Evento quando histórico muda
$(document).on('cCrudHistoryPush', function(e, state, url) {
    console.log('Histórico atualizado:', state, url);
});

// Evento quando coluna é redimensionada
$(document).on('cCrudColumnWidthChanged', function(e, columnName, width, instance) {
    console.log('Coluna redimensionada:', columnName, width);
});
```

## Exemplos Avançados

### Filtro Complexo

```php
$crud = new cCrud('orders');

// Status = 'pending' AND (priority = 'high' OR priority = 'urgent')
$crud->addFilter('status', 'pending', '=', 'AND');
$crud->addFilter('priority', 'high', '=', 'OR');
$crud->addFilter('priority', 'urgent', '=', 'OR');

// created_at BETWEEN '2024-01-01' AND '2024-12-31'
$crud->addFilter('created_at', ['2024-01-01', '2024-12-31'], 'BETWEEN', 'AND');

echo $crud->render();
```

### Rastreamento Completo

```php
$crud = new cCrud('products');

// Configurar todos os pixels
$crud->configureTracking([
    'google_analytics' => [
        'enabled' => true,
        'tracking_id' => 'G-XXXXXXXXXX'
    ],
    'facebook_pixel' => [
        'enabled' => true,
        'pixel_id' => '1234567890'
    ],
    'google_ads' => [
        'enabled' => true,
        'conversion_id' => 'AW-XXXXXXXXX',
        'conversion_label' => 'abc123'
    ],
    'linkedin' => [
        'enabled' => true,
        'partner_id' => '12345'
    ],
    'twitter' => [
        'enabled' => true,
        'pixel_id' => 'o1234'
    ]
]);

echo $crud->render();
```

### Visualizações Personalizadas

```javascript
// Criar visualização "Pedidos Urgentes"
const filters = new cCrudFilters('orders');

filters.addFilter({
    column: 'priority',
    value: 'urgent',
    operator: '=',
    logicalOperator: 'AND'
});

filters.addFilter({
    column: 'status',
    value: 'pending',
    operator: '=',
    logicalOperator: 'AND'
});

filters.saveView('Pedidos Urgentes', {
    columns: ['id', 'customer', 'priority', 'created_at'],
    orderBy: 'created_at DESC',
    limit: 20
});

// Carregar visualização salva
filters.loadView('Pedidos Urgentes');
```

## Diferença entre Busca e Filtro

### Busca
- Busca por **string** nas colunas visíveis da listagem
- Funciona como um "search global"
- Ideal para encontrar rapidamente um registro

```javascript
// Busca automática ao digitar
$('.ccrud-search-input').on('input', function() {
    const searchTerm = $(this).val();
    // Busca em todas as colunas visíveis
});
```

### Filtro
- Filtra por **colunas específicas** do banco de dados
- Permite escolher **operadores** (=, >, <, LIKE, etc.)
- Suporta **múltiplas colunas** com E/OU
- Permite **salvar visualizações**

```javascript
// Filtro avançado
filters.addFilter({
    column: 'created_at',
    value: '2024-01-01',
    operator: '>=',
    logicalOperator: 'AND'
});
```

## Persistência de Dados

Todas as preferências do usuário são salvas automaticamente no `localStorage`:

- **Filtros ativos**: `cCrud_filters_{instance}`
- **Visualizações salvas**: `cCrud_views_{instance}`
- **Preferências de colunas**: `cCrud_columns_{instance}`

Ao acessar a instância novamente, as últimas configurações são restauradas automaticamente.

## Compatibilidade

- **CodeIgniter**: 4.5+
- **PHP**: 7.4+
- **Bootstrap**: 5.3+
- **Navegadores**: Chrome, Firefox, Safari, Edge (últimas 2 versões)

## Suporte

Para dúvidas ou problemas, abra uma issue no GitHub:
https://github.com/ariellcannal/ccrud/issues
