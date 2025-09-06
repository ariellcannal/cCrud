<?php

declare(strict_types=1);

namespace App\Models;

use App\Entities\CcrudTest;
use CodeIgniter\Model;

/**
 * Model para manipular a tabela ccrud_testes utilizando recursos avançados.
 */
class CcrudTestModel extends Model
{
    /**
     * Nome da tabela.
     *
     * @var string
     */
    protected string $table = 'ccrud_testes';

    /**
     * Nome humanizado da tabela.
     *
     * @var string|null
     */
    public ?string $tableName = 'Lista de Testes';

    /**
     * Chave primária da tabela.
     *
     * @var string
     */
    protected string $primaryKey = 'id';

    /**
     * Classe de retorno para os resultados.
     *
     * @var string
     */
    protected string $returnType = CcrudTest::class;

    /**
     * Utiliza timestamps automáticos.
     *
     * @var bool
     */
    protected bool $useTimestamps = true;

    /**
     * Habilita soft deletes.
     *
     * @var bool
     */
    protected bool $useSoftDeletes = true;

    /**
     * Nome do campo de criação.
     *
     * @var string
     */
    protected string $createdField = 'created_at';

    /**
     * Nome do campo de atualização.
     *
     * @var string
     */
    protected string $updatedField = 'updated_at';

    /**
     * Nome do campo de exclusão lógica.
     *
     * @var string
     */
    protected string $deletedField = 'deleted_at';

    /**
     * Campos permitidos para escrita em massa.
     *
     * @var array<int,string>
     */
    protected array $allowedFields = [
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
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Regras de validação.
     *
     * @var array<string,string>
     */
    protected array $validationRules = [
        'varchar_col'  => 'permit_empty|max_length[255]',
        'ccrud_string' => 'permit_empty|max_length[255]',
    ];

    /**
     * Mensagens de validação personalizadas.
     *
     * @var array<string,array<string,string>>
     */
    protected array $validationMessages = [
        'varchar_col' => [
            'max_length' => 'O campo varchar_col excede o tamanho máximo.',
        ],
        'ccrud_string' => [
            'max_length' => 'O campo ccrud_string excede o tamanho máximo.',
        ],
    ];

    /**
     * Callbacks antes de inserir registros.
     *
     * @var array<int,string>
     */
    protected array $beforeInsert = ['sanitizeData'];

    /**
     * Callbacks após buscas.
     *
     * @var array<int,string>
     */
    protected array $afterFind = ['decodeJson'];

    /**
     * Callbacks após atualizações.
     *
     * @var array<int,string>
     */
    protected array $afterUpdate = ['reportUpdate'];

    /**
     * Sanitiza dados antes de inserir.
     *
     * @param array<string,mixed> $data Dados do evento.
     *
     * @return array<string,mixed>
     */
    protected function sanitizeData(array $data): array
    {
        if (isset($data['data'])) {
            $data['data'] = array_map(static fn($value) => is_string($value) ? trim($value) : $value, $data['data']);
        }

        return $data;
    }

    /**
     * Decodifica campos JSON após a busca.
     *
     * @param array<string,mixed> $data Dados retornados do evento.
     *
     * @return array<string,mixed>
     */
    protected function decodeJson(array $data): array
    {
        if (isset($data['data'])) {
            foreach ($data['data'] as &$row) {
                if (is_array($row) && isset($row['json_col'])) {
                    $row['json_col'] = json_decode((string) $row['json_col'], true);
                }

                if (is_array($row) && isset($row['ccrud_json'])) {
                    $row['ccrud_json'] = json_decode((string) $row['ccrud_json'], true);
                }
            }
        }

        return $data;
    }

    /**
     * Registra informação após atualização de dados.
     *
     * @param array<string,mixed> $data Dados do evento.
     *
     * @return array<string,mixed>
     */
    protected function reportUpdate(array $data): array
    {
        log_message('info', 'Registro atualizado', ['id' => $data['id'][0] ?? null]);

        return $data;
    }
}

