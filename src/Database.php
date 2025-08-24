<?php
namespace cCrud;

use cCrud\Config\cCrud as cCrudConfig;
use RuntimeException;

class Database
{

    private static $_instance = array();

    private $connect;

    public $result;

    private $dbhost;

    private $dbuser;

    private $dbpass;

    private $dbname;

    private $dbencoding;

    private $magic_quotes;

    private $ci;

    /**
     * Configurações do cCrud.
     *
     * @var cCrudConfig
     */
    private cCrudConfig $config;

    public static function get_instance($params = false, &$ci)
    {
        if (is_array($params)) {
            list ($dbuser, $dbpass, $dbname, $dbhost, $dbencoding) = $params;
            $instance_name = sha1($dbuser . $dbpass . $dbname . $dbhost . $dbencoding);
        } else {
            $instance_name = 'db_instance_default';
        }
        if (! isset(self::$_instance[$instance_name]) or null === self::$_instance[$instance_name]) {
            $config = config('cCrud\cCrud');
            if (! is_array($params)) {
                $dbuser     = $config->dbuser;
                $dbpass     = $config->dbpass;
                $dbname     = $config->dbname;
                $dbhost     = $config->dbhost;
                $dbencoding = $config->dbencoding;
            }
            self::$_instance[$instance_name] = new self($dbuser, $dbpass, $dbname, $dbhost, $dbencoding, $ci, $config);
        }
        return self::$_instance[$instance_name];
    }

    /**
     * Construtor da classe.
     *
     * @param string      $dbuser     Usuário do banco de dados
     * @param string      $dbpass     Senha do banco de dados
     * @param string      $dbname     Nome do banco de dados
     * @param string      $dbhost     Host do banco de dados
     * @param string      $dbencoding Codificação utilizada
     * @param mixed       $ci         Instância do CodeIgniter
     * @param cCrudConfig $config     Configurações do cCrud
     *
     * @throws RuntimeException Quando ocorrer falha na conexão com o banco
     */
    private function __construct($dbuser, $dbpass, $dbname, $dbhost, $dbencoding, &$ci, cCrudConfig $config)
    {
        $this->ci     = &$ci;
        $this->config = $config;
        $this->ci->load->model('xcrud_model');
        return;

        $this->magic_quotes = get_magic_quotes_runtime();
        if (strpos($dbhost, ':') !== false) {
            list($host, $port) = explode(':', $dbhost, 2);
            preg_match('/^([0-9]*)([^0-9]*.*)$/', $port, $socks);
            $this->connect = mysqli_connect($host, $dbuser, $dbpass, $dbname, $socks[1] ? $socks[1] : null, $socks[2] ? $socks[2] : null);
        } else {
            $this->connect = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
        }
        if (! $this->connect) {
            throw new RuntimeException(lang('cCrud.db_connection_error'));
        }
        $this->connect->set_charset($dbencoding);
        if ($this->connect->error) {
            // Lança exceção com mensagem de erro do banco traduzida
            throw new RuntimeException(lang('cCrud.db_error', [$this->connect->error]));
        }
        if ($this->config->db_time_zone) {
            $this->connect->query('SET time_zone = \'\'' . $this->config->db_time_zone . '\'\'');
        }
        if (! $this->connect)
            $this->erro('db_connection_error');
        $this->connect->set_charset($dbencoding);
        if ($this->connect->error)
            $this->erro('db_error', 500, [$this->connect->error]);
        if ($this->config->db_time_zone)
            $this->connect->query('SET time_zone = \'' . $this->config->db_time_zone . '\'');
    }

    /**
     * Executa consulta SQL.
     *
     * @param string $query Consulta a ser executada
     *
     * @return mixed Número de linhas afetadas
     *
     * @throws RuntimeException Quando a consulta retornar erro
     */
    public function query($query = '')
    {
        $this->result = $this->ci->xcrud_model->consulta($query);
        if (is_array($this->result)) {
            throw new RuntimeException(lang('cCrud.db_query_error', [$this->result['message'], $query]), (int) $this->result['code']);
            $this->erro('db_error_query', 500, [$this->result['message'], $query, $this->result['code']]);
            return;
        } else {
            return $this->ci->xcrud_model->linhasAfetadas();
        }

        return $this->ci->xcrud_model->linhasAfetadas();
    }

    public function idInserido()
    {
        return $this->ci->xcrud_model->insert_id();
    }

    public function resultado()
    {
        return $this->ci->xcrud_model->result($this->result);

        $out = array();
        if ($this->result) {
            while ($obj = $this->result->fetch_assoc()) {
                $out[] = $obj;
            }
            $this->result->free();
        }
        return $out;
    }

    public function linha()
    {
        return $this->ci->xcrud_model->row($this->result);

        $obj = $this->result->fetch_assoc();
        $this->result->free();
        return $obj;
    }

    public function escape($val, $not_qu = false, $type = false, $null = false, $bit = false)
    {
        if ($type) {
            switch ($type) {

                case 'bool':
                    if ($bit) {
                        return (int) $val ? 'b\'1\'' : 'b\'0\'';
                    }
                    return (int) $val ? 1 : ($null ? 'NULL' : 0);
                    break;
                case 'int':
                    if (! empty($val)) {
                        $val = preg_replace('/[^0-9\-]/', '', $val);
                    }
                    if ($val == '') {
                        if ($null) {
                            return 'NULL';
                        } else {
                            $val = 0;
                        }
                    }
                    if ($bit) {
                        return 'b\'' . $val . '\'';
                    }
                    return $val;
                    break;
                case 'float':
                    if ($val === '') {
                        if ($null) {
                            return 'NULL';
                        } else {
                            $val = 0;
                        }
                    }
                    return '\'' . $this->ci->xcrud_model->escaparString($val) . '\'';
                    break;
                default:
                    if (is_null($val) || trim($val) == '') {
                        if ($null) {
                            return 'NULL';
                        } else {
                            return '\'\'';
                        }
                    } else {
                        if ($type == 'point') {
                            $val = preg_replace('[^0-9\.\,\-]', '', $val);
                        }
                        // return '\'' . ($this->magic_quotes ? (string )$val : $this->connect->real_escape_string((string )$val)) . '\'';
                    }
                    break;
            }
        }
        if ($not_qu)
            return $this->magic_quotes ? (string) $val : $this->ci->xcrud_model->escaparString((string) $val);
        return '\'' . ($this->magic_quotes ? (string) $val : $this->ci->xcrud_model->escaparString((string) $val)) . '\'';
    }

    public function escape_like($val, $pattern = array(
        '%',
        '%'
    ))
    {
        if (is_int($val))
            return '\'' . $pattern[0] . (int) $val . $pattern[1] . '\'';
        if ($val == '') {
            return '\'\'';
        } else {
            return '\'' . $pattern[0] . ($this->magic_quotes ? (string) str_replace(' ', '%', $val) : $this->ci->xcrud_model->escaparString((string) str_replace(' ', '%', $val))) . $pattern[1] . '\'';
        }
    }

    /**
     *
     * @author Ariel Canal
     *         Inserido o filtro do erro de Foreing Key.
     */
    /**
     * Dispara uma exceção de runtime com mensagem traduzida
     *
     * @param string $chave       Chave da mensagem de idioma
     * @param int    $codigoHttp  Código HTTP associado
     * @param array  $parametros  Parâmetros para a mensagem de idioma
     *
     * @throws RuntimeException Quando ocorre falha de execução
     */
    private function erro(string $chave = 'undefined_error', int $codigoHttp = 500, array $parametros = []): void
    {
        // Lança exceção com mensagem localizada
        throw new \RuntimeException(lang('cCrud.' . $chave, $parametros), $codigoHttp);
    }
}