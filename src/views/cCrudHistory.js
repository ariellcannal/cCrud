/**
 * cCrud History Manager
 * 
 * Gerencia o histórico de navegação usando a History API do navegador.
 * Permite navegação com botões voltar/avançar e deep linking.
 * 
 * @class cCrudHistory
 * @version 2.0.0
 */
class cCrudHistory {
    /**
     * Construtor
     * @param {Object} config - Configurações do gerenciador de histórico
     */
    constructor(config = {}) {
        this.config = {
            baseUrl: config.baseUrl || window.location.origin,
            enableDeepLinking: config.enableDeepLinking !== false,
            enablePopState: config.enablePopState !== false,
            ...config
        };

        this.currentState = null;
        this.initialized = false;

        if (this.config.enablePopState) {
            this.initPopStateListener();
        }

        if (this.config.enableDeepLinking) {
            this.handleDeepLink();
        }
    }

    /**
     * Inicializa o listener de popstate para botões voltar/avançar
     * @private
     */
    initPopStateListener() {
        window.addEventListener('popstate', (event) => {
            if (event.state && event.state.cCrudState) {
                this.handlePopState(event.state.cCrudState);
            }
        });
    }

    /**
     * Manipula o evento popstate (voltar/avançar)
     * @private
     * @param {Object} state - Estado do histórico
     */
    handlePopState(state) {
        $(document).trigger('cCrudHistoryPopState', [state]);

        // Se for uma ação de formulário (create/edit/view), abrir offcanvas
        if (state.action && ['create', 'edit', 'view'].includes(state.action)) {
            this.restoreFormState(state);
        } 
        // Se for listagem, restaurar estado da lista
        else if (state.action === 'list' || !state.action) {
            this.restoreListState(state);
        }
    }

    /**
     * Restaura o estado do formulário (create/edit/view)
     * @private
     * @param {Object} state - Estado a ser restaurado
     */
    restoreFormState(state) {
        const data = {
            task: state.action,
            table: state.table,
            instance: state.instance
        };

        if (state.primary) {
            data.primary = state.primary;
        }

        // Disparar evento para que o cCrud abra o offcanvas
        $(document).trigger('cCrudRestoreFormState', [data, state]);
    }

    /**
     * Restaura o estado da listagem
     * @private
     * @param {Object} state - Estado a ser restaurado
     */
    restoreListState(state) {
        const data = {
            task: 'list',
            table: state.table,
            instance: state.instance
        };

        if (state.page) {
            data.page = state.page;
        }

        if (state.search) {
            data.search = state.search;
        }

        if (state.filters) {
            data.filters = state.filters;
        }

        if (state.orderBy) {
            data.orderBy = state.orderBy;
        }

        if (state.view) {
            data.view = state.view;
        }

        // Disparar evento para que o cCrud atualize a listagem
        $(document).trigger('cCrudRestoreListState', [data, state]);
    }

    /**
     * Manipula deep linking (acesso direto via URL)
     * @private
     */
    handleDeepLink() {
        const path = window.location.pathname;
        const params = new URLSearchParams(window.location.search);

        // Verificar se é uma URL do cCrud
        const cCrudPattern = /\/ccrud\/([^\/]+)(?:\/(\d+))?\/?([^\/]*)?/;
        const match = path.match(cCrudPattern);

        if (match) {
            const [, table, primary, action] = match;
            
            const state = {
                table: table,
                action: action || 'list',
                instance: params.get('instance') || table
            };

            if (primary) {
                state.primary = primary;
            }

            // Extrair outros parâmetros da query string
            for (const [key, value] of params.entries()) {
                if (key !== 'instance') {
                    state[key] = value;
                }
            }

            // Disparar evento de deep link
            $(document).trigger('cCrudDeepLink', [state]);
        }
    }

    /**
     * Adiciona um novo estado ao histórico
     * @param {Object} state - Estado a ser adicionado
     * @param {string} title - Título da página
     * @param {string} url - URL a ser exibida
     */
    pushState(state, title = '', url = null) {
        const cCrudState = {
            cCrudState: state,
            timestamp: Date.now()
        };

        if (!url) {
            url = this.buildUrl(state);
        }

        window.history.pushState(cCrudState, title, url);
        this.currentState = state;

        $(document).trigger('cCrudHistoryPush', [state, url]);
    }

    /**
     * Substitui o estado atual do histórico
     * @param {Object} state - Novo estado
     * @param {string} title - Título da página
     * @param {string} url - URL a ser exibida
     */
    replaceState(state, title = '', url = null) {
        const cCrudState = {
            cCrudState: state,
            timestamp: Date.now()
        };

        if (!url) {
            url = this.buildUrl(state);
        }

        window.history.replaceState(cCrudState, title, url);
        this.currentState = state;

        $(document).trigger('cCrudHistoryReplace', [state, url]);
    }

    /**
     * Constrói a URL baseada no estado
     * @private
     * @param {Object} state - Estado atual
     * @returns {string} URL construída
     */
    buildUrl(state) {
        let url = `/ccrud/${state.table}`;

        // Adicionar primary key se existir (edit/view)
        if (state.primary) {
            url += `/${state.primary}`;
        }

        // Adicionar ação se não for list
        if (state.action && state.action !== 'list') {
            url += state.primary ? `/${state.action}` : `/${state.action}`;
        }

        // Adicionar parâmetros de query string
        const params = new URLSearchParams();

        if (state.instance && state.instance !== state.table) {
            params.append('instance', state.instance);
        }

        if (state.page && state.page > 1) {
            params.append('page', state.page);
        }

        if (state.search) {
            params.append('search', state.search);
        }

        if (state.filters) {
            params.append('filters', JSON.stringify(state.filters));
        }

        if (state.orderBy) {
            params.append('orderBy', state.orderBy);
        }

        if (state.view) {
            params.append('view', state.view);
        }

        const queryString = params.toString();
        if (queryString) {
            url += `?${queryString}`;
        }

        return url;
    }

    /**
     * Retorna o estado atual
     * @returns {Object|null} Estado atual
     */
    getCurrentState() {
        return this.currentState;
    }

    /**
     * Navega para trás no histórico
     */
    back() {
        window.history.back();
    }

    /**
     * Navega para frente no histórico
     */
    forward() {
        window.history.forward();
    }

    /**
     * Vai para uma posição específica no histórico
     * @param {number} delta - Número de páginas para navegar (negativo = voltar, positivo = avançar)
     */
    go(delta) {
        window.history.go(delta);
    }
}

// Exportar para uso global
if (typeof window !== 'undefined') {
    window.cCrudHistory = cCrudHistory;
}
