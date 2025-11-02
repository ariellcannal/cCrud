/**
 * cCrud Columns Manager
 * 
 * Gerencia a exibição, ordenação e redimensionamento de colunas
 * com persistência de preferências do usuário.
 * 
 * @class cCrudColumns
 * @version 2.0.0
 */
class cCrudColumns {
    /**
     * Construtor
     * @param {string} instance - Nome da instância do cCrud
     * @param {Array} columns - Colunas disponíveis
     * @param {Object} config - Configurações
     */
    constructor(instance, columns = [], config = {}) {
        this.instance = instance;
        this.config = {
            storageKey: `cCrud_columns_${instance}`,
            enableResize: config.enableResize !== false,
            enableReorder: config.enableReorder !== false,
            enableVisibility: config.enableVisibility !== false,
            minWidth: config.minWidth || 80,
            maxWidth: config.maxWidth || 500,
            ...config
        };

        this.availableColumns = columns;
        this.visibleColumns = [];
        this.columnWidths = {};
        this.columnOrder = [];

        this.init();
    }

    /**
     * Inicializa o gerenciador de colunas
     * @private
     */
    init() {
        this.loadPreferences();
        this.attachEventListeners();
        this.applyPreferences();
    }

    /**
     * Anexa event listeners
     * @private
     */
    attachEventListeners() {
        if (this.config.enableVisibility) {
            this.attachVisibilityListeners();
        }

        if (this.config.enableResize) {
            this.attachResizeListeners();
        }

        if (this.config.enableReorder) {
            this.attachReorderListeners();
        }
    }

    /**
     * Anexa listeners para visibilidade de colunas
     * @private
     */
    attachVisibilityListeners() {
        $(document).on('change', `[data-ccrud-column-toggle][data-instance="${this.instance}"]`, (e) => {
            const columnName = $(e.currentTarget).data('column-name');
            const visible = $(e.currentTarget).is(':checked');

            this.setColumnVisibility(columnName, visible);
        });
    }

    /**
     * Anexa listeners para redimensionamento de colunas
     * @private
     */
    attachResizeListeners() {
        const self = this;
        let resizing = false;
        let startX = 0;
        let startWidth = 0;
        let currentColumn = null;

        // Adicionar handles de redimensionamento
        $(document).on('mouseenter', `table[data-instance="${this.instance}"] th`, function() {
            if (!$(this).find('.ccrud-resize-handle').length) {
                $(this).append('<div class="ccrud-resize-handle"></div>');
            }
        });

        // Iniciar redimensionamento
        $(document).on('mousedown', '.ccrud-resize-handle', function(e) {
            e.preventDefault();
            resizing = true;
            currentColumn = $(this).parent();
            startX = e.pageX;
            startWidth = currentColumn.width();
            
            $('body').addClass('ccrud-resizing');
        });

        // Durante o redimensionamento
        $(document).on('mousemove', function(e) {
            if (!resizing) return;

            const diff = e.pageX - startX;
            const newWidth = Math.max(self.config.minWidth, Math.min(self.config.maxWidth, startWidth + diff));
            
            currentColumn.width(newWidth);
        });

        // Finalizar redimensionamento
        $(document).on('mouseup', function(e) {
            if (!resizing) return;

            resizing = false;
            $('body').removeClass('ccrud-resizing');

            if (currentColumn) {
                const columnName = currentColumn.data('column-name');
                const newWidth = currentColumn.width();
                
                self.setColumnWidth(columnName, newWidth);
                currentColumn = null;
            }
        });
    }

    /**
     * Anexa listeners para reordenação de colunas
     * @private
     */
    attachReorderListeners() {
        const self = this;
        const tableSelector = `table[data-instance="${this.instance}"] thead`;

        // Verificar se jQuery UI Sortable está disponível
        if (typeof $.fn.sortable === 'function') {
            $(document).on('cCrudTableRendered', function() {
                const $thead = $(tableSelector);
                
                if ($thead.length && !$thead.hasClass('ui-sortable')) {
                    $thead.sortable({
                        items: 'th:not(.ccrud-actions-column)',
                        axis: 'x',
                        cursor: 'move',
                        helper: 'clone',
                        opacity: 0.6,
                        update: function(event, ui) {
                            self.updateColumnOrder();
                        }
                    });
                }
            });
        } else {
            // Implementação alternativa com drag and drop nativo
            this.attachNativeDragListeners();
        }
    }

    /**
     * Anexa listeners para drag and drop nativo
     * @private
     */
    attachNativeDragListeners() {
        const self = this;
        let draggedColumn = null;

        $(document).on('dragstart', `table[data-instance="${this.instance}"] th[draggable="true"]`, function(e) {
            draggedColumn = $(this);
            e.originalEvent.dataTransfer.effectAllowed = 'move';
            $(this).addClass('ccrud-dragging');
        });

        $(document).on('dragover', `table[data-instance="${this.instance}"] th`, function(e) {
            if (e.preventDefault) {
                e.preventDefault();
            }
            e.originalEvent.dataTransfer.dropEffect = 'move';
            return false;
        });

        $(document).on('dragenter', `table[data-instance="${this.instance}"] th`, function(e) {
            if (draggedColumn && draggedColumn[0] !== this) {
                $(this).addClass('ccrud-drag-over');
            }
        });

        $(document).on('dragleave', `table[data-instance="${this.instance}"] th`, function(e) {
            $(this).removeClass('ccrud-drag-over');
        });

        $(document).on('drop', `table[data-instance="${this.instance}"] th`, function(e) {
            if (e.stopPropagation) {
                e.stopPropagation();
            }

            if (draggedColumn && draggedColumn[0] !== this) {
                const fromIndex = draggedColumn.index();
                const toIndex = $(this).index();

                // Mover coluna no DOM
                if (fromIndex < toIndex) {
                    draggedColumn.insertAfter($(this));
                } else {
                    draggedColumn.insertBefore($(this));
                }

                // Atualizar ordem das células do corpo
                const table = $(this).closest('table');
                table.find('tbody tr').each(function() {
                    const cells = $(this).children();
                    if (fromIndex < toIndex) {
                        cells.eq(fromIndex).insertAfter(cells.eq(toIndex));
                    } else {
                        cells.eq(fromIndex).insertBefore(cells.eq(toIndex));
                    }
                });

                self.updateColumnOrder();
            }

            $(this).removeClass('ccrud-drag-over');
            return false;
        });

        $(document).on('dragend', `table[data-instance="${this.instance}"] th`, function(e) {
            $(this).removeClass('ccrud-dragging');
            $(`table[data-instance="${self.instance}"] th`).removeClass('ccrud-drag-over');
            draggedColumn = null;
        });

        // Tornar colunas arrastáveis
        $(document).on('cCrudTableRendered', function() {
            $(`table[data-instance="${self.instance}"] th:not(.ccrud-actions-column)`).attr('draggable', 'true');
        });
    }

    /**
     * Define a visibilidade de uma coluna
     * @param {string} columnName - Nome da coluna
     * @param {boolean} visible - Se a coluna deve estar visível
     */
    setColumnVisibility(columnName, visible) {
        const index = this.visibleColumns.indexOf(columnName);

        if (visible && index === -1) {
            this.visibleColumns.push(columnName);
        } else if (!visible && index !== -1) {
            this.visibleColumns.splice(index, 1);
        }

        this.savePreferences();
        this.applyColumnVisibility(columnName, visible);

        $(document).trigger('cCrudColumnVisibilityChanged', [columnName, visible, this.instance]);
    }

    /**
     * Aplica a visibilidade de uma coluna
     * @private
     * @param {string} columnName - Nome da coluna
     * @param {boolean} visible - Se a coluna deve estar visível
     */
    applyColumnVisibility(columnName, visible) {
        const table = $(`table[data-instance="${this.instance}"]`);
        const columnIndex = table.find(`th[data-column-name="${columnName}"]`).index();

        if (columnIndex === -1) return;

        if (visible) {
            table.find(`th:eq(${columnIndex}), td:nth-child(${columnIndex + 1})`).show();
        } else {
            table.find(`th:eq(${columnIndex}), td:nth-child(${columnIndex + 1})`).hide();
        }
    }

    /**
     * Define a largura de uma coluna
     * @param {string} columnName - Nome da coluna
     * @param {number} width - Largura em pixels
     */
    setColumnWidth(columnName, width) {
        this.columnWidths[columnName] = width;
        this.savePreferences();

        $(document).trigger('cCrudColumnWidthChanged', [columnName, width, this.instance]);
    }

    /**
     * Atualiza a ordem das colunas baseada no DOM atual
     * @private
     */
    updateColumnOrder() {
        const table = $(`table[data-instance="${this.instance}"]`);
        const newOrder = [];

        table.find('thead th[data-column-name]').each(function() {
            newOrder.push($(this).data('column-name'));
        });

        this.columnOrder = newOrder;
        this.savePreferences();

        $(document).trigger('cCrudColumnOrderChanged', [newOrder, this.instance]);
    }

    /**
     * Aplica as preferências salvas
     * @private
     */
    applyPreferences() {
        // Aplicar visibilidade
        this.visibleColumns.forEach(columnName => {
            this.applyColumnVisibility(columnName, true);
        });

        this.availableColumns.forEach(column => {
            if (!this.visibleColumns.includes(column.name)) {
                this.applyColumnVisibility(column.name, false);
            }
        });

        // Aplicar larguras
        Object.keys(this.columnWidths).forEach(columnName => {
            const width = this.columnWidths[columnName];
            $(`table[data-instance="${this.instance}"] th[data-column-name="${columnName}"]`).width(width);
        });

        // Aplicar ordem (se necessário)
        if (this.columnOrder.length > 0) {
            this.applyColumnOrder();
        }
    }

    /**
     * Aplica a ordem das colunas
     * @private
     */
    applyColumnOrder() {
        const table = $(`table[data-instance="${this.instance}"]`);
        const thead = table.find('thead tr');
        const tbody = table.find('tbody');

        // Reordenar cabeçalhos
        this.columnOrder.forEach((columnName, newIndex) => {
            const th = thead.find(`th[data-column-name="${columnName}"]`);
            const currentIndex = th.index();

            if (currentIndex !== newIndex && currentIndex !== -1) {
                if (newIndex === 0) {
                    th.prependTo(thead);
                } else {
                    th.insertAfter(thead.find('th').eq(newIndex - 1));
                }
            }
        });

        // Reordenar células do corpo
        tbody.find('tr').each(function() {
            const row = $(this);
            this.columnOrder.forEach((columnName, newIndex) => {
                const td = row.find(`td[data-column-name="${columnName}"]`);
                const currentIndex = td.index();

                if (currentIndex !== newIndex && currentIndex !== -1) {
                    if (newIndex === 0) {
                        td.prependTo(row);
                    } else {
                        td.insertAfter(row.find('td').eq(newIndex - 1));
                    }
                }
            });
        }.bind(this));
    }

    /**
     * Salva as preferências no localStorage
     * @private
     */
    savePreferences() {
        const preferences = {
            visibleColumns: this.visibleColumns,
            columnWidths: this.columnWidths,
            columnOrder: this.columnOrder
        };

        try {
            localStorage.setItem(this.config.storageKey, JSON.stringify(preferences));
        } catch (e) {
            console.error('Erro ao salvar preferências de colunas:', e);
        }
    }

    /**
     * Carrega as preferências do localStorage
     * @private
     */
    loadPreferences() {
        try {
            const stored = localStorage.getItem(this.config.storageKey);
            
            if (stored) {
                const preferences = JSON.parse(stored);
                this.visibleColumns = preferences.visibleColumns || this.availableColumns.map(c => c.name);
                this.columnWidths = preferences.columnWidths || {};
                this.columnOrder = preferences.columnOrder || [];
            } else {
                // Padrão: todas as colunas visíveis
                this.visibleColumns = this.availableColumns.map(c => c.name);
            }
        } catch (e) {
            console.error('Erro ao carregar preferências de colunas:', e);
            this.visibleColumns = this.availableColumns.map(c => c.name);
            this.columnWidths = {};
            this.columnOrder = [];
        }
    }

    /**
     * Reseta as preferências para o padrão
     */
    resetPreferences() {
        this.visibleColumns = this.availableColumns.map(c => c.name);
        this.columnWidths = {};
        this.columnOrder = [];
        
        this.savePreferences();
        this.applyPreferences();

        $(document).trigger('cCrudColumnsReset', [this.instance]);
    }

    /**
     * Obtém as colunas visíveis
     * @returns {Array} Colunas visíveis
     */
    getVisibleColumns() {
        return [...this.visibleColumns];
    }

    /**
     * Obtém as larguras das colunas
     * @returns {Object} Larguras das colunas
     */
    getColumnWidths() {
        return { ...this.columnWidths };
    }

    /**
     * Obtém a ordem das colunas
     * @returns {Array} Ordem das colunas
     */
    getColumnOrder() {
        return [...this.columnOrder];
    }
}

// Exportar para uso global
if (typeof window !== 'undefined') {
    window.cCrudColumns = cCrudColumns;
}
