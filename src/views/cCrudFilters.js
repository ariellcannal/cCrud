/**
 * cCrud Advanced Filters
 * 
 * Gerencia filtros avançados com suporte a múltiplas colunas,
 * operadores E/OU, e visualizações personalizadas salvas.
 * 
 * @class cCrudFilters
 * @version 2.0.0
 */
class cCrudFilters {
    /**
     * Construtor
     * @param {string} instance - Nome da instância do cCrud
     * @param {Object} config - Configurações dos filtros
     */
    constructor(instance, config = {}) {
        this.instance = instance;
        this.config = {
            storageKey: `cCrud_filters_${instance}`,
            viewsStorageKey: `cCrud_views_${instance}`,
            defaultOperator: 'AND',
            ...config
        };

        this.filters = [];
        this.currentView = null;
        this.savedViews = this.loadSavedViews();
        
        this.init();
    }

    /**
     * Inicializa o gerenciador de filtros
     * @private
     */
    init() {
        this.loadFilters();
        this.attachEventListeners();
    }

    /**
     * Anexa event listeners
     * @private
     */
    attachEventListeners() {
        // Listener para adicionar novo filtro
        $(document).on('click', `[data-ccrud-action="add-filter"][data-instance="${this.instance}"]`, (e) => {
            e.preventDefault();
            this.showAddFilterModal();
        });

        // Listener para remover filtro
        $(document).on('click', `[data-ccrud-action="remove-filter"][data-instance="${this.instance}"]`, (e) => {
            e.preventDefault();
            const index = $(e.currentTarget).data('filter-index');
            this.removeFilter(index);
        });

        // Listener para aplicar filtros
        $(document).on('click', `[data-ccrud-action="apply-filters"][data-instance="${this.instance}"]`, (e) => {
            e.preventDefault();
            this.applyFilters();
        });

        // Listener para limpar filtros
        $(document).on('click', `[data-ccrud-action="clear-filters"][data-instance="${this.instance}"]`, (e) => {
            e.preventDefault();
            this.clearFilters();
        });

        // Listener para salvar visualização
        $(document).on('click', `[data-ccrud-action="save-view"][data-instance="${this.instance}"]`, (e) => {
            e.preventDefault();
            this.showSaveViewModal();
        });

        // Listener para carregar visualização
        $(document).on('click', `[data-ccrud-action="load-view"][data-instance="${this.instance}"]`, (e) => {
            e.preventDefault();
            const viewName = $(e.currentTarget).data('view-name');
            this.loadView(viewName);
        });

        // Listener para deletar visualização
        $(document).on('click', `[data-ccrud-action="delete-view"][data-instance="${this.instance}"]`, (e) => {
            e.preventDefault();
            const viewName = $(e.currentTarget).data('view-name');
            this.deleteView(viewName);
        });
    }

    /**
     * Adiciona um novo filtro
     * @param {Object} filter - Configuração do filtro
     * @returns {number} Índice do filtro adicionado
     */
    addFilter(filter) {
        const filterObj = {
            column: filter.column,
            columnType: filter.columnType || 'text',
            operator: filter.operator || '=',
            value: filter.value,
            logicalOperator: filter.logicalOperator || this.config.defaultOperator
        };

        this.filters.push(filterObj);
        this.saveFilters();
        this.renderFilters();

        $(document).trigger('cCrudFilterAdded', [filterObj, this.instance]);

        return this.filters.length - 1;
    }

    /**
     * Remove um filtro
     * @param {number} index - Índice do filtro a ser removido
     */
    removeFilter(index) {
        if (index >= 0 && index < this.filters.length) {
            const removed = this.filters.splice(index, 1)[0];
            this.saveFilters();
            this.renderFilters();

            $(document).trigger('cCrudFilterRemoved', [removed, index, this.instance]);
        }
    }

    /**
     * Atualiza um filtro existente
     * @param {number} index - Índice do filtro
     * @param {Object} updates - Atualizações a serem aplicadas
     */
    updateFilter(index, updates) {
        if (index >= 0 && index < this.filters.length) {
            Object.assign(this.filters[index], updates);
            this.saveFilters();
            this.renderFilters();

            $(document).trigger('cCrudFilterUpdated', [this.filters[index], index, this.instance]);
        }
    }

    /**
     * Limpa todos os filtros
     */
    clearFilters() {
        this.filters = [];
        this.saveFilters();
        this.renderFilters();
        this.applyFilters();

        $(document).trigger('cCrudFiltersClear', [this.instance]);
    }

    /**
     * Aplica os filtros atuais
     */
    applyFilters() {
        const filtersData = this.buildFiltersData();
        
        $(document).trigger('cCrudFiltersApply', [filtersData, this.instance]);

        // Disparar requisição AJAX para atualizar a listagem
        if (typeof cCrud !== 'undefined' && cCrud.request) {
            const container = $(`.cCrud-ajax[data-instance="${this.instance}"]`);
            if (container.length) {
                const data = {
                    task: 'list',
                    instance: this.instance,
                    filters: filtersData
                };

                cCrud.request(container, data);
            }
        }
    }

    /**
     * Constrói os dados dos filtros para envio ao servidor
     * @returns {Object} Dados dos filtros
     */
    buildFiltersData() {
        return {
            conditions: this.filters.map(f => ({
                column: f.column,
                operator: f.operator,
                value: f.value,
                type: f.columnType
            })),
            logic: this.filters.map(f => f.logicalOperator)
        };
    }

    /**
     * Renderiza os filtros na interface
     */
    renderFilters() {
        const container = $(`[data-ccrud-filters-container][data-instance="${this.instance}"]`);
        
        if (!container.length) {
            return;
        }

        container.empty();

        if (this.filters.length === 0) {
            container.append('<p class="text-muted small mb-0">Nenhum filtro aplicado</p>');
            return;
        }

        const filtersList = $('<div class="ccrud-filters-list"></div>');

        this.filters.forEach((filter, index) => {
            const filterItem = this.renderFilterItem(filter, index);
            filtersList.append(filterItem);
        });

        container.append(filtersList);
    }

    /**
     * Renderiza um item de filtro
     * @private
     * @param {Object} filter - Filtro a ser renderizado
     * @param {number} index - Índice do filtro
     * @returns {jQuery} Elemento jQuery do filtro
     */
    renderFilterItem(filter, index) {
        const operatorLabel = this.getOperatorLabel(filter.operator);
        
        const item = $(`
            <div class="ccrud-filter-item d-flex align-items-center gap-2 mb-2">
                ${index > 0 ? `<span class="badge bg-secondary">${filter.logicalOperator}</span>` : ''}
                <span class="badge bg-primary">${filter.column}</span>
                <span class="badge bg-info">${operatorLabel}</span>
                <span class="badge bg-light text-dark">${filter.value}</span>
                <button type="button" class="btn btn-sm btn-outline-danger" 
                        data-ccrud-action="remove-filter" 
                        data-instance="${this.instance}"
                        data-filter-index="${index}">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        `);

        return item;
    }

    /**
     * Obtém o label do operador
     * @private
     * @param {string} operator - Operador
     * @returns {string} Label do operador
     */
    getOperatorLabel(operator) {
        const operators = {
            '=': 'igual a',
            '!=': 'diferente de',
            '>': 'maior que',
            '>=': 'maior ou igual',
            '<': 'menor que',
            '<=': 'menor ou igual',
            'LIKE': 'contém',
            'NOT LIKE': 'não contém',
            'IN': 'está em',
            'NOT IN': 'não está em',
            'BETWEEN': 'entre',
            'IS NULL': 'é nulo',
            'IS NOT NULL': 'não é nulo'
        };

        return operators[operator] || operator;
    }

    /**
     * Mostra modal para adicionar filtro
     * @private
     */
    showAddFilterModal() {
        // Disparar evento para que o cCrud mostre o modal
        $(document).trigger('cCrudShowAddFilterModal', [this.instance]);
    }

    /**
     * Salva os filtros no localStorage
     * @private
     */
    saveFilters() {
        try {
            localStorage.setItem(this.config.storageKey, JSON.stringify(this.filters));
        } catch (e) {
            console.error('Erro ao salvar filtros:', e);
        }
    }

    /**
     * Carrega os filtros do localStorage
     * @private
     */
    loadFilters() {
        try {
            const stored = localStorage.getItem(this.config.storageKey);
            if (stored) {
                this.filters = JSON.parse(stored);
                this.renderFilters();
            }
        } catch (e) {
            console.error('Erro ao carregar filtros:', e);
            this.filters = [];
        }
    }

    /**
     * Salva uma visualização personalizada
     * @param {string} name - Nome da visualização
     * @param {Object} config - Configuração da visualização
     */
    saveView(name, config = {}) {
        const view = {
            name: name,
            filters: [...this.filters],
            columns: config.columns || [],
            orderBy: config.orderBy || null,
            limit: config.limit || null,
            createdAt: new Date().toISOString()
        };

        this.savedViews[name] = view;
        this.saveSavedViews();

        $(document).trigger('cCrudViewSaved', [view, this.instance]);

        return view;
    }

    /**
     * Carrega uma visualização salva
     * @param {string} name - Nome da visualização
     */
    loadView(name) {
        const view = this.savedViews[name];
        
        if (!view) {
            console.error(`Visualização "${name}" não encontrada`);
            return;
        }

        this.filters = [...view.filters];
        this.currentView = name;
        
        this.saveFilters();
        this.renderFilters();

        $(document).trigger('cCrudViewLoaded', [view, this.instance]);

        // Aplicar os filtros da visualização
        this.applyFilters();
    }

    /**
     * Deleta uma visualização salva
     * @param {string} name - Nome da visualização
     */
    deleteView(name) {
        if (this.savedViews[name]) {
            delete this.savedViews[name];
            this.saveSavedViews();

            if (this.currentView === name) {
                this.currentView = null;
            }

            $(document).trigger('cCrudViewDeleted', [name, this.instance]);
        }
    }

    /**
     * Mostra modal para salvar visualização
     * @private
     */
    showSaveViewModal() {
        $(document).trigger('cCrudShowSaveViewModal', [this.instance]);
    }

    /**
     * Salva as visualizações no localStorage
     * @private
     */
    saveSavedViews() {
        try {
            localStorage.setItem(this.config.viewsStorageKey, JSON.stringify(this.savedViews));
        } catch (e) {
            console.error('Erro ao salvar visualizações:', e);
        }
    }

    /**
     * Carrega as visualizações do localStorage
     * @private
     * @returns {Object} Visualizações salvas
     */
    loadSavedViews() {
        try {
            const stored = localStorage.getItem(this.config.viewsStorageKey);
            return stored ? JSON.parse(stored) : {};
        } catch (e) {
            console.error('Erro ao carregar visualizações:', e);
            return {};
        }
    }

    /**
     * Obtém todas as visualizações salvas
     * @returns {Object} Visualizações salvas
     */
    getSavedViews() {
        return { ...this.savedViews };
    }

    /**
     * Obtém os filtros atuais
     * @returns {Array} Filtros atuais
     */
    getFilters() {
        return [...this.filters];
    }
}

// Exportar para uso global
if (typeof window !== 'undefined') {
    window.cCrudFilters = cCrudFilters;
}
