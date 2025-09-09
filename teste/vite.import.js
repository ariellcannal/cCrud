/**
 * Arquivo de entrada para recursos front-end agregados.
 *
 * Responsável por importar dependências do pacote cCrud.
 */
import $ from 'jquery';
import 'jquery-ui-dist/jquery-ui.min.js';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import 'alertifyjs/build/alertify.min.js';
import 'jcrop/dist/jcrop.js';
import 'ckeditor4/ckeditor.js';
import 'select2/dist/js/select2.min.js';
import 'jquery-mask-plugin/dist/jquery.mask.min.js';

// Exposição global para bibliotecas que dependem de jQuery global.
window.$ = window.jQuery = $;

// Importação de estilos.
import 'bootstrap/dist/css/bootstrap.min.css';
import 'jquery-ui-dist/jquery-ui.min.css';
import 'jcrop/dist/jcrop.css';
import 'select2/dist/css/select2.min.css';
import 'alertifyjs/build/css/alertify.min.css';

