# Pacote cCrud

![NPM Version](https://img.shields.io/npm/v/ccrud?style=for-the-badge)
![License](https://img.shields.io/npm/l/ccrud?style=for-the-badge)

Um pacote front-end que fornece scripts, estilos e componentes personalizados para a plataforma cCrud.

## Filosofia

Este pacote adota uma abordagem **mínima e flexível**. Ele intencionalmente **não** inclui bibliotecas de terceiros comuns (como jQuery, Bootstrap, Select2, etc.) em seu arquivo final. Em vez disso, ele as declara como `peerDependencies` (dependências de pares).

Isso oferece duas grandes vantagens para a aplicação que o consome:
1.  **Controle Total:** A aplicação principal tem controle total sobre as versões de cada biblioteca.
2.  **Sem Duplicação:** Garante que cada biblioteca seja carregada apenas uma vez na página, evitando conflitos e melhorando o desempenho.

## Pré-requisitos

Para que o pacote `ccrud` funcione, a sua aplicação **deve** carregar as seguintes bibliotecas. A responsabilidade de incluí-las na página é da sua aplicação.

* **@fortawesome/fontawesome-free**: `^7.0.0` (versão esperada)
* **alertifyjs**: `^1.13.1`
* **bootstrap**: `^5.3.0`
* **ckeditor4**: `^4.20.0`
* **cropperjs**: `^1.6.0`
* **jquery**: `^3.7.0`
* **jquery-mask-plugin**: `^1.14.16`
* **jquery-ui-dist**: `^1.13.2`
* **select2**: `^4.1.0-rc.0`

## Instalação

Você pode integrar o pacote `ccrud` em seu projeto de duas maneiras.

### Método 1: Usando NPM (Recomendado para fluxos de trabalho com Node.js)

Esta é a abordagem padrão para ecossistemas JavaScript modernos.

1.  **Instale o cCrud e suas dependências de pares:**
    ```bash
    npm install ccrud @fortawesome/fontawesome-free alertifyjs bootstrap ckeditor4 cropperjs jquery jquery-mask-plugin jquery-ui-dist select2
    ```
2.  **Configure seu processo de build:** Em sua ferramenta de build (como Vite ou Webpack), importe os arquivos do `ccrud` para que sejam incluídos no seu `app.js` e `app.css` finais.
    ```javascript
    // Exemplo no seu arquivo de entrada JS
    import 'ccrud';
    import 'ccrud/public/style.css';
    ```

### Método 2: Usando Composer (Para fluxos de trabalho PHP/CodeIgniter)

Esta abordagem evita a necessidade de um processo de build com Node.js na sua aplicação CI4.

1.  **Exija o pacote via Composer:**
    ```bash
    composer require ariellcannal/ccrud
    ```

2.  **Execute o Composer:**
    ```bash
    composer install
    ```

## Como Usar (Exemplo para CodeIgniter 4)

Aqui está um exemplo completo de como carregar todas as dependências necessárias e o pacote `ccrud` em um layout principal do CI4 (`app/Views/templates/main_layout.php`), usando CDNs para as dependências de pares.

```php
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Minha Aplicação com cCrud</title>

    <link href="[https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css](https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css)" rel="stylesheet">
    <link rel="stylesheet" href="[https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css](https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css)" />
    <link href="[https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css](https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css)" rel="stylesheet" />
    <link rel="stylesheet" href="[https://cdnjs.cloudflare.com/ajax/libs/AlertifyJS/1.13.1/css/alertify.min.css](https://cdnjs.cloudflare.com/ajax/libs/AlertifyJS/1.13.1/css/alertify.min.css)" />
    <link rel="stylesheet" href="[https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css](https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css)" />
    <link rel="stylesheet" href="[https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css](https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css)" />

    <link rel="stylesheet" href="<?= base_url('assets/ccrud/style.css') ?>">
</head>
<body>

    <?= $this->renderSection('content') ?>

    <script src="[https://code.jquery.com/jquery-3.7.1.min.js](https://code.jquery.com/jquery-3.7.1.min.js)"></script>
    <script src="[https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js](https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js)"></script>
    <script src="[https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js](https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js)"></script>
    <script src="[https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js](https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js)"></script>
    <script src="[https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js](https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js)"></script>
    <script src="[https://cdnjs.cloudflare.com/ajax/libs/AlertifyJS/1.13.1/alertify.min.js](https://cdnjs.cloudflare.com/ajax/libs/AlertifyJS/1.13.1/alertify.min.js)"></script>
    <script src="[https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js](https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js)"></script>
    <script src="[https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js](https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js)"></script>
    
    <script src="<?= base_url('assets/ccrud/ccrud.js') ?>"></script>
</body>
</html>