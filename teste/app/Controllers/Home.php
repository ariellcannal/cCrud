<?php

declare(strict_types=1);

namespace App\Controllers;

use Faker\Factory;
use cCrud\cCrud;
use App\Models\CcrudTestModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

/**
 * Controlador principal para testes do cCrud.
 */
class Home extends BaseController
{
    /**
     * Exibe o cCrud da tabela de testes quando o ambiente está configurado.
     *
     * @return string
     */
    public function index(): string
    {
        $model = new CcrudTestModel();

        if (! $this->databaseReady($model)) {
            try {
                $this->createAndSeedTable();
            } catch (DatabaseException $e) {
                return view('setup', ['hasEnv' => false]);
            }
        }

        $crud = new cCrud($model);

        return $crud->render();
    }

    /**
     * Verifica se a base de dados e a tabela estão prontas para uso.
     *
     * @param CcrudTestModel $model Instância do model de testes.
     *
     * @return bool
     */
    private function databaseReady(CcrudTestModel $model): bool
    {
        try {
            if (! $model->db->tableExists($model->getTable())) {
                return false;
            }

            return $model->countAll() > 0;
        } catch (DatabaseException $e) {
            return false;
        }
    }

    /**
     * Cria a tabela ccrud_testes e popula com dados aleatórios.
     *
     * @return void
     */
    private function createAndSeedTable(): void
    {
        $db    = \Config\Database::connect();
        $forge = \Config\Database::forge();

        // Define colunas da tabela de testes
        $fields = [
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'tinyint_col'   => ['type' => 'TINYINT', 'null' => true],
            'smallint_col'  => ['type' => 'SMALLINT', 'null' => true],
            'mediumint_col' => ['type' => 'MEDIUMINT', 'null' => true],
            'int_col'       => ['type' => 'INT', 'null' => true],
            'bigint_col'    => ['type' => 'BIGINT', 'null' => true],
            'decimal_col'   => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true],
            'float_col'     => ['type' => 'FLOAT', 'null' => true],
            'double_col'    => ['type' => 'DOUBLE', 'null' => true],
            'bit_col'       => ['type' => 'BIT', 'constraint' => 1, 'null' => true],
            'char_col'      => ['type' => 'CHAR', 'constraint' => 10, 'null' => true],
            'varchar_col'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'text_col'      => ['type' => 'TEXT', 'null' => true],
            'date_col'      => ['type' => 'DATE', 'null' => true],
            'datetime_col'  => ['type' => 'DATETIME', 'null' => true],
            'timestamp_col' => ['type' => 'TIMESTAMP', 'null' => true],
            'time_col'      => ['type' => 'TIME', 'null' => true],
            'year_col'      => ['type' => 'YEAR', 'null' => true],
            'binary_col'    => ['type' => 'BINARY', 'constraint' => 16, 'null' => true],
            'varbinary_col' => ['type' => 'VARBINARY', 'constraint' => 16, 'null' => true],
            'blob_col'      => ['type' => 'BLOB', 'null' => true],
            'enum_col'      => ['type' => 'ENUM', 'constraint' => ['A','B','C'], 'null' => true],
            'set_col'       => ['type' => 'SET', 'constraint' => ['X','Y','Z'], 'null' => true],
            'json_col'      => ['type' => 'JSON', 'null' => true],
            // Tipos do cCrud
            'ccrud_string'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'ccrud_text'     => ['type' => 'TEXT', 'null' => true],
            'ccrud_int'      => ['type' => 'INT', 'null' => true],
            'ccrud_float'    => ['type' => 'FLOAT', 'null' => true],
            'ccrud_bool'     => ['type' => 'TINYINT', 'constraint' => 1, 'null' => true],
            'ccrud_date'     => ['type' => 'DATE', 'null' => true],
            'ccrud_datetime' => ['type' => 'DATETIME', 'null' => true],
            'ccrud_time'     => ['type' => 'TIME', 'null' => true],
            'ccrud_json'     => ['type' => 'JSON', 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'     => ['type' => 'DATETIME', 'null' => true],
        ];

        // Cria a tabela caso não exista
        if (! $db->tableExists('ccrud_testes')) {
            $forge->addField($fields);
            $forge->addKey('id', true);
            $forge->createTable('ccrud_testes', true);
        }

        // Popula a tabela com 150 registros
        $builder = $db->table('ccrud_testes');
        $current = $builder->countAllResults();

        if ($current >= 150) {
            return;
        }

        $faker = Factory::create();

        for ($i = $current; $i < 150; $i++) {
            $builder->insert([
                'tinyint_col'   => $faker->numberBetween(0, 127),
                'smallint_col'  => $faker->numberBetween(0, 32767),
                'mediumint_col' => $faker->numberBetween(0, 8388607),
                'int_col'       => $faker->numberBetween(0, 2147483647),
                'bigint_col'    => $faker->randomNumber(5),
                'decimal_col'   => $faker->randomFloat(2, 0, 9999),
                'float_col'     => $faker->randomFloat(2, 0, 9999),
                'double_col'    => $faker->randomFloat(2, 0, 9999),
                'bit_col'       => $faker->boolean ? 1 : 0,
                'char_col'      => substr($faker->lexify(str_repeat('?', 10)), 0, 10),
                'varchar_col'   => $faker->sentence(3),
                'text_col'      => $faker->paragraph,
                'date_col'      => $faker->date('Y-m-d'),
                'datetime_col'  => $faker->date('Y-m-d H:i:s'),
                'timestamp_col' => $faker->date('Y-m-d H:i:s'),
                'time_col'      => $faker->time('H:i:s'),
                'year_col'      => $faker->year,
                'binary_col'    => random_bytes(16),
                'varbinary_col' => random_bytes(16),
                'blob_col'      => random_bytes(16),
                'enum_col'      => $faker->randomElement(['A','B','C']),
                'set_col'       => implode(',', $faker->randomElements(['X','Y','Z'], $faker->numberBetween(1, 3))),
                'json_col'      => json_encode(['value' => $faker->word]),
                'ccrud_string'   => $faker->word,
                'ccrud_text'     => $faker->paragraph,
                'ccrud_int'      => $faker->randomNumber(),
                'ccrud_float'    => $faker->randomFloat(2, 0, 9999),
                'ccrud_bool'     => $faker->boolean ? 1 : 0,
                'ccrud_date'     => $faker->date('Y-m-d'),
                'ccrud_datetime' => $faker->date('Y-m-d H:i:s'),
                'ccrud_time'     => $faker->time('H:i:s'),
                'ccrud_json'     => json_encode(['value' => $faker->word]),
                'created_at'     => $faker->date('Y-m-d H:i:s'),
                'updated_at'     => $faker->date('Y-m-d H:i:s'),
            ]);
        }
    }
}
