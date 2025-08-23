<?php
namespace cCrud\Libraries;

use cCrud\Config\cCrudConfig;

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
            $config = config('cCrudConfig');
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

    private function __construct($dbuser, $dbpass, $dbname, $dbhost, $dbencoding, &$ci, cCrudConfig $config)
    {
        $this->ci     = &$ci;
        $this->config = $config;
        $this->ci->load->model('xcrud_model');
        return;

        $this->magic_quotes = get_magic_quotes_runtime();
        if (strpos($dbhost, ':') !== false) {
            list ($host, $port) = explode(':', $dbhost, 2);
            preg_match('/^([0-9]*)([^0-9]*.*)$/', $port, $socks);
            $this->connect = mysqli_connect($host, $dbuser, $dbpass, $dbname, $socks[1] ? $socks[1] : null, $socks[2] ? $socks[2] : null);
        } else
            $this->connect = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
        if (! $this->connect)
            $this->error('Connection error. Can not connect to database');
        $this->connect->set_charset($dbencoding);
        if ($this->connect->error)
            $this->error($this->connect->error);
        if ($this->config->db_time_zone)
            $this->connect->query('SET time_zone = \'' . $this->config->db_time_zone . '\'');
    }

    public function query($query = '')
    {
        $this->result = $this->ci->xcrud_model->consulta($query);
        if (is_array($this->result)) {
            $this->error($this->result['message'] . '<pre>' . $query . '</pre>', $this->result['code'], $this->result['message']);
            return;
        } else {
            return $this->ci->xcrud_model->linhasAfetadas();
        }
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
    private function error($text = 'Error!', $errno = null, $error = null)
    {
        if ($errno) {
            $log_number = time() . rand(0, 999);
            switch ($errno) {
                case "1451": // erro de foreign key
                    echo '<script>alertify.alert("Este registro não pode ser removido pois está vinculado a outros cadastros. <br/><br/><small> Erro: ' . $log_number . '</small>");</script>';
                    break;
                case "1062": // duplicate entry
                    echo '<script>alertify.alert("Este registro já existe. <br/><br/><small> Erro: ' . $log_number . '</small>");</script>';
                    break;
                case "1264": // out of range
                    echo '<script>alertify.alert("Dado inválido. <br/><br/><small> Erro: ' . $log_number . '</small>");</script>';
                    break;
                default:
                    if (ENVIRONMENT != "production") {
                        exit('<div class="xcrud-error" style="position:relative;line-height:1.25;padding:15px;color:#BA0303;margin:10px;border:1px solid #BA0303;border-radius:4px;font-family:Arial,sans-serif;background:#FFB5B5;box-shadow:inset 0 0 80px #E58989;">' . $text . '</div>');
                    } else {
                        echo '<script>alertify.alert("Ocorreu um erro ao completar a operação. <br/><br/><small> Erro: ' . $log_number . '</small>");</script>';
                    }
                    break;
            }
            $fp = fopen($_SERVER['DOCUMENT_ROOT'] . '/application/logs/' . $log_number, "w+");
            fwrite($fp, $text);
            fclose($fp);
        } else {
            exit('<div class="xcrud-error" style="position:relative;line-height:1.25;padding:15px;color:#BA0303;margin:10px;border:1px solid #BA0303;border-radius:4px;font-family:Arial,sans-serif;background:#FFB5B5;box-shadow:inset 0 0 80px #E58989;">' . $text . '</div>');
        }
    }
}