<?php

namespace cCrud\Traits;

/**
 * Trait para suporte a filtros avançados
 * 
 * Adiciona funcionalidade de filtros com múltiplas colunas,
 * operadores E/OU e visualizações personalizadas.
 * 
 * @package cCrud\Traits
 * @version 2.0.0
 */
trait AdvancedFilters
{
    /**
     * Filtros ativos
     * @var array
     */
    protected array $activeFilters = [];

    /**
     * Operador lógico padrão (AND/OR)
     * @var string
     */
    protected string $defaultLogicalOperator = 'AND';

    /**
     * Adiciona um filtro
     * 
     * @param string $column Nome da coluna
     * @param mixed $value Valor do filtro
     * @param string $operator Operador (=, !=, >, <, >=, <=, LIKE, IN, etc.)
     * @param string $logicalOperator Operador lógico (AND/OR)
     * @return self
     */
    public function addFilter(string $column, $value, string $operator = '=', string $logicalOperator = 'AND'): self
    {
        $this->activeFilters[] = [
            'column' => $column,
            'value' => $value,
            'operator' => strtoupper($operator),
            'logical' => strtoupper($logicalOperator),
        ];

        return $this;
    }

    /**
     * Adiciona múltiplos filtros de uma vez
     * 
     * @param array $filters Array de filtros
     * @return self
     */
    public function addFilters(array $filters): self
    {
        foreach ($filters as $filter) {
            $this->addFilter(
                $filter['column'],
                $filter['value'],
                $filter['operator'] ?? '=',
                $filter['logical'] ?? 'AND'
            );
        }

        return $this;
    }

    /**
     * Remove todos os filtros
     * 
     * @return self
     */
    public function clearFilters(): self
    {
        $this->activeFilters = [];
        return $this;
    }

    /**
     * Aplica os filtros ao model
     * 
     * @param \CodeIgniter\Model $model Model do CodeIgniter
     * @return \CodeIgniter\Model
     */
    protected function applyFiltersToModel($model)
    {
        if (empty($this->activeFilters)) {
            return $model;
        }

        foreach ($this->activeFilters as $index => $filter) {
            $column = $this->table_name . '.' . $filter['column'];
            $value = $filter['value'];
            $operator = $filter['operator'];
            $logical = $index === 0 ? '' : $filter['logical'];

            // Determinar o método do query builder baseado no operador lógico
            $method = $logical === 'OR' ? 'orWhere' : 'where';

            switch ($operator) {
                case '=':
                case '!=':
                case '>':
                case '<':
                case '>=':
                case '<=':
                    if ($logical === 'OR') {
                        $model->orWhere($column . ' ' . $operator, $value);
                    } else {
                        $model->where($column . ' ' . $operator, $value);
                    }
                    break;

                case 'LIKE':
                    if ($logical === 'OR') {
                        $model->orLike($column, $value);
                    } else {
                        $model->like($column, $value);
                    }
                    break;

                case 'NOT LIKE':
                    if ($logical === 'OR') {
                        $model->orNotLike($column, $value);
                    } else {
                        $model->notLike($column, $value);
                    }
                    break;

                case 'IN':
                    $values = is_array($value) ? $value : explode(',', $value);
                    if ($logical === 'OR') {
                        $model->orWhereIn($column, $values);
                    } else {
                        $model->whereIn($column, $values);
                    }
                    break;

                case 'NOT IN':
                    $values = is_array($value) ? $value : explode(',', $value);
                    if ($logical === 'OR') {
                        $model->orWhereNotIn($column, $values);
                    } else {
                        $model->whereNotIn($column, $values);
                    }
                    break;

                case 'BETWEEN':
                    if (is_array($value) && count($value) === 2) {
                        if ($logical === 'OR') {
                            $model->orWhere("$column >=", $value[0]);
                            $model->orWhere("$column <=", $value[1]);
                        } else {
                            $model->where("$column >=", $value[0]);
                            $model->where("$column <=", $value[1]);
                        }
                    }
                    break;

                case 'IS NULL':
                    if ($logical === 'OR') {
                        $model->orWhere($column, null);
                    } else {
                        $model->where($column, null);
                    }
                    break;

                case 'IS NOT NULL':
                    if ($logical === 'OR') {
                        $model->orWhere("$column IS NOT NULL");
                    } else {
                        $model->where("$column IS NOT NULL");
                    }
                    break;
            }
        }

        return $model;
    }

    /**
     * Processa filtros recebidos via POST/GET
     * 
     * @param array $filtersData Dados dos filtros
     * @return self
     */
    public function processFiltersFromRequest(array $filtersData): self
    {
        $this->clearFilters();

        if (empty($filtersData['conditions'])) {
            return $this;
        }

        foreach ($filtersData['conditions'] as $index => $condition) {
            $logical = $filtersData['logic'][$index] ?? 'AND';
            
            $this->addFilter(
                $condition['column'],
                $this->parseFilterValue($condition['value'], $condition['type'] ?? 'text'),
                $condition['operator'] ?? '=',
                $logical
            );
        }

        return $this;
    }

    /**
     * Faz parse do valor do filtro baseado no tipo
     * 
     * @param mixed $value Valor bruto
     * @param string $type Tipo do campo
     * @return mixed Valor processado
     */
    protected function parseFilterValue($value, string $type)
    {
        switch ($type) {
            case 'int':
            case 'integer':
                return (int) $value;

            case 'float':
            case 'decimal':
            case 'double':
                return (float) $value;

            case 'bool':
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);

            case 'date':
            case 'datetime':
            case 'timestamp':
                // Manter como string para comparação SQL
                return $value;

            case 'array':
            case 'json':
                return is_array($value) ? $value : json_decode($value, true);

            default:
                return $value;
        }
    }

    /**
     * Obtém os filtros ativos
     * 
     * @return array
     */
    public function getActiveFilters(): array
    {
        return $this->activeFilters;
    }

    /**
     * Verifica se há filtros ativos
     * 
     * @return bool
     */
    public function hasFilters(): bool
    {
        return !empty($this->activeFilters);
    }

    /**
     * Exporta os filtros para array
     * 
     * @return array
     */
    public function exportFilters(): array
    {
        return [
            'conditions' => array_map(function($filter) {
                return [
                    'column' => $filter['column'],
                    'operator' => $filter['operator'],
                    'value' => $filter['value'],
                ];
            }, $this->activeFilters),
            'logic' => array_map(function($filter) {
                return $filter['logical'];
            }, $this->activeFilters),
        ];
    }
}
