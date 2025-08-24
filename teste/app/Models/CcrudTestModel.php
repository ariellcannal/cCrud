<?php
namespace App\Models;

use CodeIgniter\Model;
use App\Entities\CcrudTest;

/**
 * Model para manipular a tabela cCrud_testes.
 */
class CcrudTestModel extends Model
{
    /** @var string Nome da tabela */
    protected $table = 'cCrud_testes';

    /** @var string Chave primária */
    protected $primaryKey = 'id';

    /** @var string Classe de retorno */
    protected $returnType = CcrudTest::class;

    /** @var array Campos permitidos para escrita */
    protected $allowedFields = [
        'tinyint_col',
        'smallint_col',
        'mediumint_col',
        'int_col',
        'bigint_col',
        'decimal_col',
        'float_col',
        'double_col',
        'bit_col',
        'char_col',
        'varchar_col',
        'text_col',
        'date_col',
        'datetime_col',
        'timestamp_col',
        'time_col',
        'year_col',
        'binary_col',
        'varbinary_col',
        'blob_col',
        'enum_col',
        'set_col',
        'json_col',
        'ccrud_string',
        'ccrud_text',
        'ccrud_int',
        'ccrud_float',
        'ccrud_bool',
        'ccrud_date',
        'ccrud_datetime',
        'ccrud_time',
        'ccrud_json',
    ];
}
