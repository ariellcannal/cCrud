/**
 * Validador de Dependências do Pacote cCrud
 * Verifica se as bibliotecas listadas em peerDependencies foram carregadas
 * pela aplicação consumidora antes da execução deste pacote.
 */
(function() {
  const packageName = 'Pacote cCrud';

  // Função para checar se o CSS do Font Awesome foi carregado
  const checkFontAwesome = () => {
    const testElement = document.createElement('i');
    testElement.className = 'fa-solid'; // Classe de teste
    testElement.style.display = 'none';
    document.body.appendChild(testElement);
    const fontFamily = window.getComputedStyle(testElement).getPropertyValue('font-family');
    document.body.removeChild(testElement);
    // Verifica pela string da versão 7 na font-family
    return fontFamily.includes('Font Awesome 7'); // <-- Linha atualizada
  };

  const dependencies = [
    { name: 'jQuery', check: () => window.jQuery },
    { name: 'Bootstrap 5', check: () => window.bootstrap },
    { name: 'jQuery UI', check: () => window.jQuery && window.jQuery.ui },
    { name: 'Select2', check: () => window.jQuery && window.jQuery.fn.select2 },
    { name: 'jQuery Mask', check: () => window.jQuery && window.jQuery.fn.mask },
    { name: 'AlertifyJS', check: () => window.alertify },
    { name: 'CKEditor 4', check: () => window.CKEDITOR },
    { name: 'Cropper.js', check: () => window.Cropper },
    { name: 'Font Awesome 7', check: checkFontAwesome },
  ];

  console.log(`[${packageName}] Verificando dependências...`);
  let allLoaded = true;

  dependencies.forEach(dep => {
    try {
      if (!dep.check()) {
        throw new Error(`Dependência não encontrada: ${dep.name}`);
      }
    } catch (e) {
      allLoaded = false;
      console.error(
        `[${packageName}] ERRO: A biblioteca "${dep.name}" não foi encontrada. ` +
        `Por favor, garanta que ela seja carregada na sua página ANTES do script do cCrud.`
      );
    }
  });

  if (allLoaded) {
    console.log(`[${packageName}] Todas as dependências foram carregadas com sucesso.`);
  } else {
    console.error(
        `[${packageName}] Uma ou mais dependências não foram carregadas. O pacote pode não funcionar como esperado.`
    );
  }
})();