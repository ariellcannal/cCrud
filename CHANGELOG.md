# Changelog - cCrud

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

## [2.0.0] - 2025-01-XX

### ✨ Adicionado

#### JavaScript OOP e History API
- **cCrudHistory.js**: Gerenciamento de histórico de navegação
  - Suporte completo à History API do navegador
  - Navegação com botões voltar/avançar funcionando perfeitamente
  - Deep linking: URLs diretas para create, edit e view
  - Sincronização automática de estado com URL
  - Eventos customizados para integração

#### Filtros Avançados
- **cCrudFilters.js**: Sistema de filtros avançados
  - Múltiplas colunas com operadores E/OU
  - Operadores suportados: =, !=, >, <, >=, <=, LIKE, IN, BETWEEN, IS NULL
  - Visualizações personalizadas salvas
  - Persistência em localStorage
  - Interface intuitiva para adicionar/remover filtros

- **AdvancedFilters.php**: Trait PHP para filtros
  - Integração com Query Builder do CodeIgniter
  - Parse automático de valores por tipo
  - Suporte a todos os operadores SQL comuns

#### Gerenciamento de Colunas
- **cCrudColumns.js**: Gerenciamento de colunas estilo Notion
  - Redimensionamento de colunas (arrastar borda)
  - Reordenação via drag-and-drop
  - Seletor de visibilidade de colunas
  - Persistência de preferências do usuário
  - Larguras mínima e máxima configuráveis

#### Interface Notion-like
- **cCrudNotion.css**: Estilos inspirados no Notion
  - Design limpo e minimalista
  - Tabelas interativas com hover effects
  - Offcanvas responsivo para formulários
    - 600px em desktop
    - Fullscreen em mobile
  - Componentes modernos (botões, inputs, badges)
  - Variáveis CSS para fácil customização

#### Segurança - reCAPTCHA
- **RecaptchaValidation.php**: Trait para validação reCAPTCHA
  - Suporte a reCAPTCHA v2 e v3
  - Configuração de score mínimo para v3
  - Ações configuráveis (create, edit, delete)
  - Renderização automática de scripts e widgets
  - Validação server-side completa

#### Rastreamento de Eventos
- **EventTracking.php**: Trait para rastreamento
  - **Google Analytics (GA4)**: Rastreamento completo de eventos
  - **Facebook Pixel**: Conversões e eventos customizados
  - **Google Ads**: Rastreamento de conversões
  - **LinkedIn Insight Tag**: Rastreamento profissional
  - **Twitter Pixel**: Eventos do Twitter
  - Mapeamento automático de ações para eventos
  - Renderização automática de scripts

### 🔄 Modificado

#### Arquitetura
- Código JavaScript refatorado para programação orientada a objetos
- Separação de responsabilidades em módulos independentes
- Melhor organização de código com traits PHP

#### Interface
- Migração completa para Bootstrap 5
- Responsividade aprimorada
- Melhor acessibilidade

### 📚 Documentação

- CHANGELOG.md criado
- Documentação inline em todos os módulos
- Comentários JSDoc em JavaScript
- PHPDoc em todos os métodos PHP

## [1.x.x] - Versões Anteriores

Versões anteriores não documentadas neste changelog.

---

## Tipos de Mudanças

- `✨ Adicionado` para novas funcionalidades
- `🔄 Modificado` para mudanças em funcionalidades existentes
- `🗑️ Removido` para funcionalidades removidas
- `🐛 Corrigido` para correções de bugs
- `🔒 Segurança` para correções de vulnerabilidades
- `📚 Documentação` para mudanças na documentação
