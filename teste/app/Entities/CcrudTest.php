<?php

declare(strict_types=1);

namespace App\Entities;

use CodeIgniter\Entity\Entity;

/**
 * Entidade para representar os registros da tabela de testes ccrud.
 */
class CcrudTest extends Entity
{
    /**
     * Campos tratados como datas pelo framework.
     *
     * @var array<int,string>
     */
    protected array $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Conversões automáticas de tipos.
     *
     * @var array<string,string>
     */
    protected array $casts = [
        'json_col'   => 'array',
        'ccrud_json' => 'array',
    ];

    /**
     * Mapeamento de propriedades para colunas do banco.
     *
     * @var array<string,string>
     */
    protected array $datamap = [
        'stringField' => 'ccrud_string',
        'jsonField'   => 'ccrud_json',
    ];

    /**
     * Atributos padrão da entidade.
     *
     * @var array<string,mixed>
     */
    protected array $attributes = [
        'json_col'   => [],
        'ccrud_json' => [],
    ];

    /**
     * Rótulos humanizados dos campos da tabela.
     *
     * @var array<string,string>
     */
    public array $labels = [
        'id'              => 'ID',
        'tinyint_col'     => 'Tinyint',
        'smallint_col'    => 'Smallint',
        'mediumint_col'   => 'Mediumint',
        'int_col'         => 'Inteiro',
        'bigint_col'      => 'Bigint',
        'decimal_col'     => 'Decimal',
        'float_col'       => 'Float',
        'double_col'      => 'Double',
        'bit_col'         => 'Bit',
        'char_col'        => 'Caractere',
        'varchar_col'     => 'Varchar',
        'text_col'        => 'Texto',
        'date_col'        => 'Data',
        'datetime_col'    => 'Data e Hora',
        'timestamp_col'   => 'Timestamp',
        'time_col'        => 'Hora',
        'year_col'        => 'Ano',
        'binary_col'      => 'Binário',
        'varbinary_col'   => 'Varbinário',
        'blob_col'        => 'Blob',
        'enum_col'        => 'Enum',
        'set_col'         => 'Set',
        'json_col'        => 'JSON',
        'ccrud_string'    => 'String cCrud',
        'ccrud_text'      => 'Texto cCrud',
        'ccrud_int'       => 'Inteiro cCrud',
        'ccrud_float'     => 'Flutuante cCrud',
        'ccrud_bool'      => 'Booleano cCrud',
        'ccrud_date'      => 'Data cCrud',
        'ccrud_datetime'  => 'Data e Hora cCrud',
        'ccrud_time'      => 'Hora cCrud',
        'ccrud_json'      => 'JSON cCrud',
        'created_at'      => 'Criado em',
        'updated_at'      => 'Atualizado em',
        'deleted_at'      => 'Excluído em',
    ];
}

