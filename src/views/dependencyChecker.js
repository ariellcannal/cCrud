/**
 * Validador de Dependências do Pacote cCrud
 * Verifica se as bibliotecas listadas em peerDependencies foram carregadas
 * pela aplicação consumidora antes da execução deste pacote.
 */
window.addEventListener('load', function() {
  const packageName = 'Pacote cCrud';

  // Função para checar se o CSS do Font Awesome foi carregado
  const checkFontAwesome = () => {
    // Verifica se há algum link com Font Awesome
    const links = Array.from(document.querySelectorAll('link[rel="stylesheet"]'));
    const hasFontAwesomeLink = links.some(link => 
      link.href.includes('font-awesome') || link.href.includes('fontawesome')
    );
    
    if (hasFontAwesomeLink) {
      return true;
    }
    
    // Fallback: testa renderização
    const testElement = document.createElement('i');
    testElement.className = 'fa fa-check'; // Classe compatível com v5 e v6
    testElement.style.display = 'none';
    document.body.appendChild(testElement);
    const fontFamily = window.getComputedStyle(testElement).getPropertyValue('font-family');
    document.body.removeChild(testElement);
    return fontFamily.includes('Font Awesome') || fontFamily.includes('FontAwesome');
  };

  const dependencies = [
    { name: 'jQuery', check: () => window.jQuery },
    { name: 'Bootstrap 5', check: () => window.bootstrap },
    { name: 'jQuery UI', check: () => window.jQuery && window.jQuery.ui },
    { name: 'jQuery UI Timepicker', check: () => window.jQuery && window.jQuery.fn.datetimepicker },
    { name: 'Select2', check: () => window.jQuery && window.jQuery.fn.select2 },
    { name: 'jQuery Mask', check: () => window.jQuery && window.jQuery.fn.mask },
    { name: 'AlertifyJS', check: () => window.alertify },
    { name: 'CKEditor 4', check: () => window.CKEDITOR },
    { name: 'Cropper.js', check: () => window.Cropper },
    { name: 'Font Awesome', check: checkFontAwesome },
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
});