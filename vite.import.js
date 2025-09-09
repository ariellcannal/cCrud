/**
 * Ponto de entrada para o pacote cCrud.
 *
 * Este arquivo importa o validador de dependências e os
 * scripts e estilos personalizados do pacote.
 * As bibliotecas de terceiros são tratadas como externas (peer dependencies).
 */

// 1. Executa o validador para garantir que a página tem o que é preciso
import './src/views/dependencyChecker.js';

// 2. Importa a lógica principal e os estilos do seu pacote
import './src/views/cCrud.js';
import './src/views/cCrud.css';