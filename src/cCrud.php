<?php
namespace cCrud;

use cCrud\Config\cCrud as cCrudConfig;
use cCrud\Config\Views as ViewsConfig;
use CodeIgniter\Model;
use CodeIgniter\I18n\Time;
use Config\Services;
use CodeIgniter\Session\Session;
use CodeIgniter\HTTP\ResponseInterface;
use cCrud\Postdata;
use Psr\Log\LoggerInterface;
use RuntimeException;

// direct access to DB driver and config
define('CCRUD_PATH', str_replace('\\', '/', dirname(__file__)));

// str_replace
// - windows
// trick
/**
 * Classe principal responsável pela geração do CRUD dinâmico.
 *
 * Centraliza configuração, renderização e processamento das
 * operações de criação, leitura, atualização e remoção utilizando
 * os recursos do CodeIgniter.
 */
class cCrud
{

    protected static $instance = array();

    protected static $css_loaded = false;

    protected static $js_loaded = false;

    protected static $classes = array();


    protected $ajax_request = false;

    /**
     * Instância das configurações do cCrud.
     *
     * @var cCrudConfig
     */
    protected cCrudConfig $config;

    /**
     * Model utilizado para todas as consultas ao banco de dados.
     *
     * @var Model|null
     */
    protected ?Model $model = null;

    /**
     * Manipulador de sessões do CodeIgniter 4.
     *
     * @var Session|null
     */
    protected ?Session $session = null;

    /**
     * Manipulador de logs do CodeIgniter 4.
     *
     * @var LoggerInterface|null
     */
    protected ?LoggerInterface $logger = null;

    /**
     * Configurações do tema do cCrud.
     *
     * @var array<string,string>
     */
    protected array $theme_config = [];

    public $instance_name;

    protected $instance_count;

    public $table;

    // ALTERADO PARA PUBLIC, NECESSÁRIO NOS HELPERS CALLBACK'S
    protected $table_name;

    protected $primary_key;

    // name of primary
    protected $primary_val;

    // value of a primary
    protected $where = array();

    protected $order_by = array();

    protected $relation = array();

    protected $fields = array();

    protected $fields_create = array();

    protected $fields_edit = array();

    protected $fields_view = array();

    protected $fields_list = array();

    protected $columns_select = false;

    protected $fields_list_default = array();

    protected $fields_names = array();

    protected $labels = array();

    protected $columns = array();

    protected $columns_names = array();

    public $is_create = true;

    protected $is_edit = true;

    protected $is_view = true;

    protected $is_remove = true;

    public $is_csv = true;

    public $is_search = true;

    public $is_print = true;

    protected $is_title = true;

    protected $is_numbers = true;

    protected $is_duplicate = false;

    public $is_inner = false;

    protected $is_pagination = true;

    protected $is_limitlist = true;

    protected $is_sortable = true;

    protected $is_list = true;

    protected $buttons_position = 'right';

    public $buttons = array();

    protected $custom_buttons = array();

    protected $readonly = array();

    protected $disabled = array();

    protected $field_type = array();

    protected $field_attr = array();

    public $defaults = array();

    // ALTERADO PARA PUBLIC, NECESSÁRIO NOS HELPERS
    // CALLBACK'S
    protected $limit = 20;

    protected $limit_list = array(
        '20',
        '50',
        '100',
        'all'
    );

    protected $column_cut = 50;

    protected $column_cut_list = array();

    protected $no_editor = array();

    protected $show_primary_ai_field = false;

    protected $show_primary_ai_column = false;

    protected $url;

    protected $key;

    protected $benchmark = false;

    protected $search_pattern = array(
        '%',
        '%'
    );

    protected $remove_confirm = false;

    protected $upload_folder = array();

    protected $upload_config = array();

    protected $upload_folder_def = '../uploads';

    protected $upload_to_save = array();

    protected $upload_to_remove = array();

    protected $binary = array();

    protected $pass_var = array();

    protected $reverse_fields = array();

    protected $no_quotes = array();

    protected $join = array();

    protected $inner_where = array();

    protected $inner_table_instance = array();

    protected $condition = array();

    protected $unique = array();

    protected $fk_relation = array();

    protected $links_label = array();

    protected $emails_label = array();

    protected $sum = array();

    protected $subselect = array();

    protected $subselect_before = array();

    protected $highlight = array();

    protected $highlight_row = array();

    protected $modal = array();

    protected $column_class = array();

    protected $no_select = array();

    // only subselect flag for correct sorting
    protected $primary_ai = false;

    protected $language = 'en';

    protected static $lang_arr = array();

    protected $subselect_query = array();

    protected $where_pri = array();

    protected $field_params = array();


    protected $column_callback = array();

    protected $field_callback = array();

    protected $replace_insert = array();

    protected $replace_update = array();

    protected $replace_remove = array();

    protected $send_external_create = array();

    protected $send_external_edit = array();

    protected $locked_fields = array();

    // disallow save data in form fields
    protected $column_pattern = array();

    protected $field_tabs = array();

    protected $field_marker = array();

    protected $field_tooltip = array();

    protected $table_tooltip = array();

    protected $column_tooltip = array();

    protected $search_columns = array();

    protected $search_default = null;

    protected $column_width = array();

    protected $order_column = null;

    protected $order_direct = 'asc';

    protected $result_list = array();

    // DB grid result becomes glodal
    protected $result_row = array();

    // DB details result becomes glodal
    protected $result_total = 0;

    protected $is_get = false;

    public $after = null;

    protected $table_info = null;

    // fields information from database
    protected $before_upload = array();

    protected $before_download = array();

    protected $after_upload = array();

    protected $after_resize = array();

    protected $custom_vars = array();

    protected $tabdesc = array();

    public $column_name = array();

    public $search = 0;

    protected $hidden_columns = array();

    // allows to select non in grid data
    protected $hidden_fields = array();

    // allows save data in non in form fields
    protected $range = '';

    protected $task = '';

    protected $previous_task = '';

    protected $column = false;

    protected $column2 = false;

    protected $phrase = '';

    protected $phrase2 = '';

    protected $inner_value = false;

    protected $fields_output = array();

    protected $hidden_fields_output = array();

    protected $start = 0;

    protected $before = '';

    protected $bit_field = array();

    protected $point_field = array();

    protected $float_field = array();

    protected $text_field = array();

    protected $int_field = array();

    protected $grid_condition = array();

    // ***** remove *****
    protected $hide_button = array();

    protected $set_lang = array();

    public $table_ro = false;

    /**
     * Lista de views utilizadas nos diferentes modos.
     *
     * @var array<string,string>
     */
    protected $views = array(
        'list'   => 'list.php',
        'create' => 'detail.php',
        'edit'   => 'detail.php',
        'view'   => 'detail.php',
        'report' => 'report.php'
    );

    protected $grid_restrictions = array();

    protected $direct_select_tags = array();

    // get unselectable {tags}
    protected $action = array();

    protected $exception = false;

    protected $exception_fields = array();

    protected $exception_text = '';

    protected $messages = array();

    protected $nested_rendered = array();

    protected $default_tab = false;

    protected $prefix = '';

    protected $query = '';

    protected $total_query = '';

    protected $condition_backup = array();

    protected static $sess_id = null;


    protected $strip_tags = true;

    protected $safe_output = true;

    protected $before_list = array();

    protected $before_create = array();

    protected $before_edit = array();

    protected $before_view = array();

    protected $lists_null_opt = true;

    protected $custom_fields = array();

    protected $date_format = array();

    protected $cancel_file_saving = false;

    protected $parent = null;

    protected $alphabetical_filter = '';

    protected $alphabetical_field = '';

    protected $alphabetical_index = array();

    protected $custom_filter = array();

    protected $custom_filter_active = array();

    protected $custom_filter_all_label = array();

    protected $totalizers = array();

    protected $is_modal = false;

    protected $nested_readonly_on_view = true;

    protected $active_tab_id = null;

    protected $table_always_edit_mode = false;

    protected $record_changes = false;

    protected $custom_lists = false;

    protected $unset_custom_columns = array();

    protected $custom_lists_active = array();

    protected $custom_lists_static = array();

    protected $columns_default = '';

    protected $mass_actions = array();

    protected $opened_tab = false;

    protected $nested_default_render = 'list';

    protected $nested_default_render_primary = false;

    protected $join_relation = array();

    protected $parameters = array();

    protected $fields_report = array();

    protected $report = array();

    protected $report_reverse = array();

    protected $report_tabs = array();

    protected $report_values = array();

    protected $group_by = array();

    protected $search_submit = array();

    protected $search_lines = 2;


    /**
     * Construtor que define as variáveis básicas do cCrud,
     * podendo ser alteradas por métodos públicos.
     *
     */
    /**
     * Construtor que inicializa dependências do cCrud.
     *
     * @param Model $model            Modelo do CodeIgniter utilizado pelo CRUD
     * @param LoggerInterface|null $logger Registrador de logs opcional
     */
    /**
     * Construtor da classe principal do cCrud.
     *
     * @param Model|null          $model  Modelo do CodeIgniter utilizado pelo CRUD
     * @param LoggerInterface|null $logger Manipulador de logs do framework
     */
    public function __construct(?Model $model = null, ?LoggerInterface $logger = null)
    {
        // Define o Model e o Logger utilizados pelo cCrud, quando fornecidos
        $this->model  = $model;
        $this->logger = $logger ?? Services::logger();

        if ($model !== null) {
            // Define automaticamente a tabela e o nome exibido a partir do Model
            $this->table      = property_exists($model, 'table') ? $model->table : '';
            $this->table_name = $model->tableName;

            // Carrega rótulos definidos na Entity associada, caso existam
            $returnType = method_exists($model, 'getReturnType') ? $model->getReturnType() : $model->returnType;
            if ($returnType && class_exists($returnType)) {
                $entity = new $returnType();
                if (property_exists($entity, 'labels') && is_array($entity->labels)) {
                    foreach ($entity->labels as $field => $label) {
                        $this->labels[$field] = $label;
                        if ($this->table) {
                            $this->labels[$this->table . '.' . $field] = $label;
                        }
                    }
                }
            }
        }

        // Inicia o manipulador de sessões do CodeIgniter 4
        $this->session = Services::session();

        $this->config = cCrudConfig::instance();

        // Verifica se as rotas base estão configuradas no CodeIgniter
        $requestUri = trim($this->config->request_uri, '/');
        $routes     = Services::routes();
        $configured = false;
        foreach ($routes->getRoutes() as $methods) {
            foreach (array_keys($methods) as $route) {
                if (strpos($route, $requestUri) === 0) {
                    $configured = true;
                    break 2;
                }
            }
        }
        if (! $configured) {
            $message = "Rota base \"{$requestUri}\" não configurada. Adicione ao arquivo app/Config/Routes.php:\n" .
                "\$routes->group('{$requestUri}', ['namespace' => 'cCrud'], static function (RouteCollection \$routes): void {\n" .
                "    \$routes->add('(:any)', 'cCrud::router');\n" .
                "});";
            throw new RuntimeException($message);
        }

        $this->limit              = $this->config->limit;
        $this->limit_list         = $this->config->limit_list;
        $this->column_cut = $this->config->column_cut;
        $this->show_primary_ai_field = $this->config->show_primary_ai_field;
        $this->show_primary_ai_column = $this->config->show_primary_ai_column;

        $this->benchmark = $this->config->benchmark;
        $this->remove_confirm = $this->config->remove_confirm;
        $this->upload_folder_def = $this->config->upload_folder_def;

        $this->is_print = $this->config->enable_printout;
        $this->is_title = $this->config->enable_table_title;
        $this->is_csv = $this->config->enable_csv_export;
        $this->is_numbers = $this->config->enable_numbers;
        $this->is_pagination = $this->config->enable_pagination;
        $this->is_search = $this->config->enable_search;
        $this->is_limitlist = $this->config->enable_limitlist;
        $this->is_sortable = $this->config->enable_sorting;

        $this->language = config('App')->defaultLocale;

        $this->search_pattern = $this->config->search_pattern;

        $this->default_tab = $this->config->default_tab;


        $this->strip_tags = $this->config->strip_tags;
        $this->safe_output = $this->config->safe_output;

        $this->lists_null_opt = $this->config->lists_null_opt;

        $this->date_format = array(
            'php_d' => $this->config->php_date_format,
            'php_t' => $this->config->php_time_format
        );
        $this->nested_readonly_on_view = $this->config->nested_readonly_on_view;

    }

    protected function __clone()
    {}

    /**
     * Retorna a saída renderizada do componente.
     *
     * @return string HTML final do cCrud
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Garante a instância da sessão do CodeIgniter 4.
     *
     * @return Session Instância de sessão.
     */
    protected function getSession(): Session
    {
        if (! $this->session instanceof Session) {
            $this->session = Services::session();
        }

        return $this->session;
    }

    /**
     * Escapa valores para cláusulas LIKE.
     *
     * @param string $val Valor a ser escapado.
     * @param array<int,string> $pattern Padrões de prefixo e sufixo.
     *
     * @return string Valor formatado para cláusulas LIKE.
     */
    protected function escapeLike(string $val, array $pattern): string
    {
        $escaped = str_replace(' ', '%', $val);
        $escaped = $this->model->db->escapeLikeString($escaped);
        return "'" . $pattern[0] . $escaped . $pattern[1] . "'";
    }

    /**
     * Retorna uma instância do cCrud utilizando o Model informado.
     *
     * @param Model               $model  Modelo do CodeIgniter
     * @param LoggerInterface|null $logger Registrador de logs opcional
     * @param string|false        $name   Nome da instância
     *
     * @return self|ResponseInterface
     */
    public static function getInstance(Model $model, ?LoggerInterface $logger = null, $name = false)
    {
        self::initPrepare();
        if (! $name) {
            $name = sha1(rand() . microtime());
        }
        if (! isset(self::$instance[$name]) || null === self::$instance[$name]) {
            self::$instance[$name]             = new self($model, $logger);
            self::$instance[$name]->instance_name = $name;
        }
        self::$instance[$name]->instance_count = count(self::$instance);

        $returnType = method_exists($model, 'getReturnType') ? $model->getReturnType() : $model->returnType;
        if (! is_subclass_of($returnType, '\\CodeIgniter\\Entity\\Entity')) {
            return Services::response()->setStatusCode(500)->setBody(lang('cCrud.model_entity_required'));
        }

        return self::$instance[$name];
    }

    /**
     * Recupera a instância solicitada via requisição Ajax.
     *
     * @param Model|null          $model  Modelo associado, quando disponível
     * @param LoggerInterface|null $logger Registrador de logs opcional
     *
     * @return string|ResponseInterface
     */
    public static function getRequestedInstance(?Model $model = null, ?LoggerInterface $logger = null)
    {
        $request  = Services::request();
        $security = Services::security();
        $postData = $request->getPost('cCrud');
        $getData  = $request->getGet('cCrud');

        if (is_array($postData) && isset($postData['instance'], $postData['key'], $postData['task'])) {
            self::init_prepare('post');
            if (empty($postData['key'])) {
                return Services::response()->setStatusCode(400)->setBody(lang('cCrud.security_key_empty'));
            }
            $key = $security->clean($postData['key']);
            if (empty($postData['instance'])) {
                return Services::response()->setStatusCode(400)->setBody(lang('cCrud.instance_name_empty'));
            }
            $inst_name = $security->clean($postData['instance']);
            $is_get    = false;
        } elseif (is_array($getData) && isset($getData['instance'], $getData['key'], $getData['task']) && $getData['task'] == 'file') {
            self::init_prepare('get');
            if (empty($getData['key'])) {
                return Services::response()->setStatusCode(400)->setBody(lang('cCrud.security_key_empty'));
            }
            $key = $security->clean($getData['key']);
            if (empty($getData['instance'])) {
                return Services::response()->setStatusCode(400)->setBody(lang('cCrud.instance_name_empty'));
            }
            $inst_name = $security->clean($getData['instance']);
            $is_get    = true;
        } else {
            return Services::response()->setStatusCode(400)->setBody(lang('cCrud.wrong_request'));
        }
        $session = \Config\Services::session();
        $cCrud_session = $session->get('cCrud_session');

        // var_dump($cCrud_session[$inst_name]);
        // if (isset($cCrud_session[$inst_name]['key']) && $cCrud_session[$inst_name]['key'] == $key) {
        if (1 == 1) {
            self::$instance[$inst_name]             = new self($model, $logger);
            self::$instance[$inst_name]->is_get     = $is_get;
            self::$instance[$inst_name]->ajax_request = true;
            self::$instance[$inst_name]->instance_name = $inst_name;
            self::$instance[$inst_name]->import_vars($key);
            self::$instance[$inst_name]->inner_where();
            return self::$instance[$inst_name]->render();
        }

        return Services::response()->setStatusCode(401)->setBody(lang('cCrud.verification_key_outdated'));
    }

    /**
     * Prepara a sessão para o cCrud.
     *
     * @return void
     */
    protected static function initPrepare(): void
    {
        $config = cCrudConfig::instance();
        Services::session();
        if (is_callable($config->before_construct)) {
            call_user_func($config->before_construct);
        }
    }

    /**
     * Encaminha as requisições recebidas para o manipulador adequado.
     *
     * @return ResponseInterface Resposta HTTP do recurso solicitado
     */
    public function router(): ResponseInterface
    {
        $segment = Services::uri()->getSegment(2);

        return match ($segment) {
            'ajax' => $this->ajax(),
            'css'  => $this->css(),
            'js'   => $this->js(),
            default => Services::response()->setStatusCode(ResponseInterface::HTTP_NOT_FOUND),
        };
    }

    /**
     * Processa requisições Ajax encaminhadas ao cCrud.
     *
     * @return ResponseInterface Resposta HTTP contendo o resultado da operação
     */
    public function ajax(): ResponseInterface
    {
        // obtém a resposta processada pela instância solicitada
        $output = self::getRequestedInstance($this->model, $this->logger);

        // retorna o conteúdo como uma resposta HTTP
        return $output instanceof ResponseInterface
            ? $output
            : Services::response()->setBody($output);
    }

    /**
     * Retorna o conteúdo CSS utilizado pelo cCrud.
     *
     * @return ResponseInterface Resposta HTTP contendo o CSS
     */
    public function css(): ResponseInterface
    {
        $content = file_get_contents(CCRUD_PATH . '/views/cCrud.css');
        return Services::response()->setContentType('text/css')->setBody($content);
    }

    /**
     * Retorna o conteúdo JavaScript utilizado pelo cCrud.
     *
     * @return ResponseInterface Resposta HTTP contendo o JavaScript
     */
    public function js(): ResponseInterface
    {
        $content = file_get_contents(CCRUD_PATH . '/views/cCrud.js');
        return Services::response()->setContentType('application/javascript')->setBody($content);
    }

    /**
     * Ativa a confirmação antes de remover registros.
     *
     * @param bool $bool Verdadeiro para solicitar confirmação
     *
     * @return self
     */
    public function removeConfirm($bool = true)
    {
        $this->remove_confirm = (bool) $bool;
        return $this;
    }

    /**
     * Define o número máximo de registros exibidos por página.
     *
     * @param int $limit Quantidade de registros
     *
     * @return self
     */
    public function limit($limit = 20)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Define a lista de limites para exibição.
     *
     * @param array<int,string>|string $limit_list Lista de limites permitidos
     *
     * @return self
     */
    public function limitList($limit_list = '')
    {
        if ($limit_list) {
            if (is_array($limit_list))
                $this->limit_list = array_unique($limit_list);
            else {
                $this->limit_list = array_unique($this->parse_comma_separated($limit_list));
            }
        }
        return $this;
    }

    /**
     * Exibe o campo de chave primária auto-incremento em criação/edição.
     *
     * @param bool $bool Verdadeiro para exibir o campo
     *
     * @return self
     */
    public function showPrimaryAiField($bool = true)
    {
        $this->show_primary_ai_field = (bool) $bool;
        return $this;
    }

    /**
     * Exibe a coluna de chave primária auto-incremento na listagem.
     *
     * @param bool $bool Verdadeiro para exibir a coluna
     *
     * @return self
     */
    public function showPrimaryAiColumn($bool = true)
    {
        $this->show_primary_ai_column = (bool) $bool;
        return $this;
    }

    /**
     * Define a tabela base utilizada nas operações CRUD.
     *
     * @param string      $table  Nome da tabela
     * @param string|bool $prefix Prefixo opcional da tabela
     *
     * @return self
     */
    public function table($table = '', $prefix = false)
    {
        if ($prefix !== false) {
            $this->prefix = $prefix;
        }
        $this->table = $this->prefix . $table;
        return $this;
    }

    /**
     * Define o nome da tabela para exibição.
     *
     * @param string      $name    Nome da tabela
     * @param string|bool $tooltip Texto de ajuda
     * @param string|bool $icon    Ícone exibido
     *
     * @return self
     */
    public function tableName($name = '', $tooltip = false, $icon = false)
    {
        if ($name)
            $this->table_name = $name;
        if ($tooltip) {
            $this->table_tooltip = array(
                'tooltip' => $tooltip,
                'icon' => $icon
            );
        }
        return $this;
    }

    /**
     * Adiciona cláusulas WHERE às consultas.
     *
     * @param string|array $fields    Campo(s) para comparação
     * @param mixed        $where_val Valor comparativo
     * @param string       $glue      Operador lógico entre cláusulas
     * @param int|bool     $index     Índice específico para substituição
     *
     * @return self
     */
    public function where($fields = false, $where_val = false, $glue = 'AND', $index = false)
    {
        if ($fields && $where_val !== false) {
            $fdata = $this->_parse_field_names($fields, 'where');
            foreach ($fdata as $fitem) {
                if ($index) {
                    $this->where[$index] = array(
                        'table' => $fitem['table'],
                        'field' => $fitem['field'],
                        'value' => isset($fitem['value']) ? $fitem['value'] : $where_val,
                        'glue' => $glue
                    );
                } else {
                    $this->where[] = array(
                        'table' => $fitem['table'],
                        'field' => $fitem['field'],
                        'value' => isset($fitem['value']) ? $fitem['value'] : $where_val,
                        'glue' => $glue
                    );
                }
            }
            unset($fields, $fdata);
        } elseif ($fields && ! is_array($fields) && $where_val === false) {
            if ($index) {
                $this->where[$index] = array(
                    'custom' => $fields,
                    'glue' => $glue
                );
            } else {
                $this->where[] = array(
                    'custom' => $fields,
                    'glue' => $glue
                );
            }
            unset($fields);
        } elseif (! $fields && $where_val) {
            if ($index) {
                $this->where[$index] = array(
                    'custom' => $where_val,
                    'glue' => $glue
                );
            } else {
                $this->where[] = array(
                    'custom' => $where_val,
                    'glue' => $glue
                );
            }
            unset($where_val);
        } elseif (! $fields && ! $where_val && $index && isset($this->where[$index])) {
            unset($this->where[$index]);
        }
        return $this;
    }

    /**
     * Adiciona condição OR ao filtro.
     *
     * @param string $fields    Campos utilizados
     * @param mixed  $where_val Valor comparado
     *
     * @return self
     */
    public function orWhere($fields = '', $where_val = false)
    {
        return $this->where($fields = '', $where_val = '', 'OR');
    }

    /**
     * Define a ordenação dos resultados.
     *
     * @param string $fields    Campos ordenados
     * @param string $direction Direção da ordenação
     *
     * @return self
     */
    public function orderBy($fields = '', $direction = 'asc')
    {
        if ($fields) {
            if ($direction === false && is_string($fields)) {
                $this->order_by[$fields] = false;
            } else {
                $fdata = $this->_parse_field_names($fields, 'order_by');
                foreach ($fdata as $key => $fitem) {
                    $this->order_by[$key] = isset($fitem['value']) ? $fitem['value'] : $direction;
                }
            }
        }
        unset($fields);
        return $this;
    }

    /**
     * Agrupa os resultados conforme os campos informados.
     *
     * @param string $fields Campos utilizados
     *
     * @return self
     */
    public function groupBy($fields = '')
    {
        if ($fields) {
            $fdata = $this->_parse_field_names($fields, 'group_by');
            foreach ($fdata as $key => $fitem) {
                $this->group_by[$key] = $fitem['table'] . '.' . $fitem['field'];
            }
        } else {
            $this->group_by[$fields] = false;
        }
        unset($fields);
        return $this;
    }

    /**
     * Configura relacionamento simples com outra tabela.
     *
     * @param string|array $fields               Campo(s) locais
     * @param string       $rel_tbl              Tabela relacionada
     * @param string       $rel_field            Campo de chave
     * @param string       $rel_name             Campo de exibição
     * @param array        $rel_where           Condições adicionais
     * @param mixed        $order_by            Ordenação
     * @param bool         $multi               Permite múltiplas seleções
     * @param string       $rel_concat_separator Separador de concatenação
     * @param mixed        $join                Dados de junção
     * @param bool         $tree                Ativa modo em árvore
     * @param string       $depend_field        Campo dependente
     * @param string       $depend_on           Valor dependente
     *
     * @return self
     */
    public function relation($fields = '', $rel_tbl = '', $rel_field = '', $rel_name = '', $rel_where = array(), $order_by = false, $multi = false, $rel_concat_separator = ' ',$join = null, $tree = false, $depend_field = '', $depend_on = '')
    {
        if ($fields && $rel_tbl && $rel_field && $rel_name) {
            if ($depend_on) {
                $fdata = $this->_parse_field_names($depend_on, 'relation');
                $depend_on = key($fdata);
            }
            $fdata = $this->_parse_field_names($fields, 'relation');
            foreach ($fdata as $fitem) {
                $this->relation[$fitem['table'] . '.' . $fitem['field']] = array(
                    'rel_tbl' => $rel_tbl,
                    'rel_alias' => 'alias' . rand(),
                    'rel_field' => $rel_field,
                    'rel_name' => $rel_name,
                    'rel_where' => $rel_where,
                    'rel_separator' => $rel_concat_separator,
                    'order_by' => $order_by,
                    'multi' => $multi,
                    'join' => $join,
                    'table' => $fitem['table'],
                    'field' => $fitem['field'],
                    'tree' => $tree,
                    'depend_field' => $depend_field,
                    'depend_on' => $depend_on
                );
            }
        }
        return $this;
    }

    /**
     * Configura relacionamento de chave estrangeira.
     *
     * @param string $label              Rótulo exibido
     * @param string $fields             Campo principal
     * @param string $fk_table           Tabela estrangeira
     * @param string $in_fk_field        Campo de entrada
     * @param string $out_fk_field       Campo de saída
     * @param string $rel_tbl            Tabela de relação
     * @param string $rel_field          Campo de chave
     * @param string $rel_name           Campo de exibição
     * @param array  $rel_where          Condição adicional
     * @param string $rel_orderby        Ordenação
     * @param string $rel_concat_separator Separador de concatenação
     * @param string $before             Campo precedente
     * @param array  $add_data           Dados extras
     *
     * @return self
     */
    public function fkRelation($label = '', $fields = '', $fk_table = '', $in_fk_field = '', $out_fk_field = '', $rel_tbl = '', $rel_field = '', $rel_name = '', $rel_where = array(), $rel_orderby = '', $rel_concat_separator = ' ', $before = '', array $add_data = array())
    {
        if ($fields && $rel_tbl && $rel_field && $rel_name && $label) {
            $fdata = $this->_parse_field_names($fields, 'fk_relation');
            $fitem = reset($fdata);
            $table = $this->_get_table('subselect');
            // foreach ($fdata as $key => $fitem)
            // {
            // $alias = 'tfkalias' . base_convert(rand(), 10, 36);
            $alias = $table . '.' . $label;
            $this->fk_relation[$alias] = array(
                'table' => $fitem['table'],
                'field' => $fitem['field'],
                'label' => $label,
                'before' => $before ? key(reset($this->_parse_field_names($before, 'fk_relation'))) : '',
                'alias' => $alias,
                'rel_alias' => 'ralias' . rand(),
                'fk_alias' => 'fkalias' . rand(),
                'fk_table' => $fk_table,
                'in_fk_field' => $in_fk_field,
                'out_fk_field' => $out_fk_field,
                'rel_tbl' => $rel_tbl,
                'rel_field' => $rel_field,
                'rel_name' => $rel_name,
                'rel_where' => $rel_where,
                'rel_orderby' => $rel_orderby,
                'rel_separator' => $rel_concat_separator,
                'add_data' => $add_data
            );
            $this->field_type[$alias] = 'fk_relation';
            $this->defaults[$alias] = '';
            if (! isset($this->field_attr[$alias])) {
                $this->field_attr[$alias] = array();
            }

            // }
        }
        return $this;
    }

    /* Ariel Canal */
    /**
     * Adiciona junção personalizada à consulta.
     *
     * @param string      $fields               Campos a selecionar
     * @param string      $join_tbl             Tabela a ser unida
     * @param string      $join_field           Campo de junção
     * @param mixed       $join_additional_cond Condição adicional
     * @param string|bool $alias                Apelido da tabela
     * @param bool        $not_insert           Ignora na inserção
     *
     * @return void
     */
    public function joinCustom($fields = '', $join_tbl = '', $join_field = '', $join_additional_cond = false, $alias = false, $not_insert = false)
    {
        $this->join($fields, $join_tbl, $join_field, $alias, $not_insert, $join_additional_cond);
    }

    public function join($fields = '', $join_tbl = '', $join_field = '', $alias = false, $not_insert = false, $join_additional_cond = false)
    {
        $fdata = $this->_parse_field_names($fields, 'join');
        $alias = $alias ? $alias : $join_tbl;
        $key = key($fdata);
        $this->join[$alias] = array(
            'table' => $fdata[$key]['table'],
            'field' => $fdata[$key]['field'],
            'join_table' => $this->prefix . $join_tbl,
            'join_field' => $join_field,
            'not_insert' => $not_insert,
            'additional_cond' => $join_additional_cond
        );
        // $this->field_type[$this->join[$alias]['join_table'] . '.' .
        // $this->join[$alias]['join_field']] = 'hidden';
        $this->pass_var($alias . '.' . $join_field, '{' . $key . '}', 'edit');
        return $this;
    }

    /**
     * Registra tabela aninhada para exibição.
     *
     * @param string $field         Campo relacionado na tabela principal
     * @param Model  $inner_model   Modelo da tabela interna
     * @param string $inner_field   Campo relacionado na tabela interna
     * @param string $instance_name Nome da instância
     *
     * @return self|null Instância da tabela interna ou null
     */
    public function nestedTable(string $field, Model $inner_model, string $inner_field, string $instance_name)
    {
        if ($field && $inner_field && $instance_name) {
            $fdata     = $this->_parse_field_names($field, 'nestedTable');
            $inner_tbl = method_exists($inner_model, 'getTable') ? $inner_model->getTable() : '';
            foreach ($fdata as $fitem) {
                // nome do campo armazenado na instância pai
                $this->inner_table_instance[$instance_name] = $fitem['table'] . '.' . $fitem['field'];

                // nova instância do cCrud para a tabela interna
                $instance           = cCrud::getInstance($inner_model, $this->logger, $instance_name);
                $instance->is_inner = true; // flag de aninhamento
                $instance->parent   = $this->instance_name;

                // conecta os campos entre as tabelas
                $fdata2 = $this->_parse_field_names($inner_field, 'nestedTable', $inner_tbl);
                $instance->inner_where[$fitem['table'] . '.' . $fitem['field']] = key($fdata2);

                return $instance; // apenas um ciclo
            }
        }
        return null;
    }

    public function fields($fields = '', $reverse = false, $tabname = false, $mode = false)
    {
        $fdata = $this->_parse_field_names($fields, 'fields');
        switch ($mode) {
            case 'create':
                if (! $reverse && $tabname) {
                    $this->field_tabs['create'][$tabname] = $tabname;
                }
                break;
            case 'edit':
                if (! $reverse && $tabname) {
                    $this->field_tabs['edit'][$tabname] = $tabname;
                }
                break;
            case 'view':
                if (! $reverse && $tabname) {
                    $this->field_tabs['view'][$tabname] = $tabname;
                }
                break;
            default:
                if (! $reverse && $tabname) {
                    $this->field_tabs['create'][$tabname] = $tabname;
                    $this->field_tabs['edit'][$tabname] = $tabname;
                    $this->field_tabs['view'][$tabname] = $tabname;
                }
                break;
        }
        foreach ($fdata as $fitem) {
            $fitem['tab'] = $tabname;
            switch ($mode) {
                case 'create':
                    $this->fields_create[$fitem['table'] . '.' . $fitem['field']] = $fitem;
                    $this->reverse_fields['create'] = $reverse;
                    break;
                case 'edit':
                    $this->fields_edit[$fitem['table'] . '.' . $fitem['field']] = $fitem;
                    $this->reverse_fields['edit'] = $reverse;
                    break;
                case 'view':
                    $this->fields_view[$fitem['table'] . '.' . $fitem['field']] = $fitem;
                    $this->reverse_fields['view'] = $reverse;
                    break;
                default:
                    $this->fields_create[$fitem['table'] . '.' . $fitem['field']] = $fitem;
                    $this->fields_edit[$fitem['table'] . '.' . $fitem['field']] = $fitem;
                    $this->fields_view[$fitem['table'] . '.' . $fitem['field']] = $fitem;
                    $this->reverse_fields['create'] = $reverse;
                    $this->reverse_fields['edit'] = $reverse;
                    $this->reverse_fields['view'] = $reverse;
                    break;
            }
        }
        unset($fields, $fdata);
        return $this;
    }

    public function unique($fields = '')
    {
        $fdata = $this->_parse_field_names($fields, 'unique');
        foreach ($fdata as $fitem) {
            $this->unique[$fitem['table'] . '.' . $fitem['field']] = $fitem;
        }
        unset($fields);
        return $this;
    }

    public function label($fields = '', $label = '')
    {
        $fdata = $this->_parse_field_names($fields, 'label');
        foreach ($fdata as $fitem) {
            $this->labels[$fitem['table'] . '.' . $fitem['field']] = isset($fitem['value']) ? $fitem['value'] : $label;
        }
        return $this;
    }

    public function columns($columns = '', $reverse = false, $is_default = true)
    {
        $fdata = $this->_parse_field_names($columns, 'columns');
        $this->fields_list = array();
        foreach ($fdata as $fitem) {
            $this->fields_list_default[$fitem['table'] . '.' . $fitem['field']] = $fitem;
            $this->fields_list[$fitem['table'] . '.' . $fitem['field']] = $fitem;
        }
        if ($is_default)
            $this->columns_default = $columns;
        $this->reverse_fields['list'] = $reverse;
        unset($columns);
        return $this;
    }

    /**
     * Define quais colunas estarão ativas.
     *
     * @param array|string $columns Colunas desejadas
     *
     * @return self
     */
    public function columnsActive($columns = '')
    {
        if ($columns == "") {
            return $this;
        }
        $this->columns_select = true;
        $fdata = $this->_parse_field_names($columns, 'columns');
        $this->fields_list = array();
        foreach ($fdata as $fitem) {
            $this->fields_list[$fitem['table'] . '.' . $fitem['field']] = $fitem;
        }
        return $this;
    }

    /**
     * Renderiza seletor de colunas.
     *
     * @return string|null
     */
    public function renderColumnsSelect()
    {
        if (! $this->columns_select)
            return null;
        $out = "";
        $out .= $this->open_tag('select', 'pull-right bootstrap-select cCrud-columnsList-select', array(
            'table_name' => $this->table_name,
            'multiple' => 'multiple',
            'title' => 'Colunas da Listagem',
            'data-selected-text-format' => 'static',
            'data-header' => 'Selecione as colunas da listagem'
        ));
        foreach ($this->fields_list_default as $field => $fdata) {
            $attr = array();
            if (array_key_exists($field, $this->fields_list)) {
                $attr['selected'] = 'selected';
            }
            $attr['value'] = $field;
            $out .= $this->open_tag('option', null, $attr) . \esc($this->labels[$field]) . $this->close_tag('option');
        }
        $out .= $this->close_tag('select');
        return $out;
    }

    public function unset_add($bool = true)
    {
        $this->is_create = ! (bool) $bool;
        return $this;
    }

    /**
     *
     * @author Ariel Canal
     *         Inserção do parametro $alt_task
     *         Possibilidade de armazenar mais de uma condição
     */
    public function unset_edit($bool = true, $field = false, $operand = false, $value = false, $alt_task = false)
    {
        $this->is_edit = ! (bool) $bool;
        if ($field && $operand && $value !== false) {
            $this->grid_restrictions['edit'][] = array(
                'bool' => $bool,
                'field' => $field,
                'operator' => $operand,
                'value' => $value,
                'alt' => $alt_task
            );
        }
        return $this;
    }

    /**
     *
     * @author Ariel Canal
     *         Inserção do parâmetro $alt_task
     *         Possibilidade de armazenar mais de uma condição
     */
    public function unset_view($bool = true, $field = false, $operand = false, $value = false, $alt_task = false)
    {
        $this->is_view = ! (bool) $bool;
        if ($field && $operand && $value !== false) {
            $this->grid_restrictions['view'][] = array(
                'bool' => $bool,
                'field' => $field,
                'operator' => $operand,
                'value' => $value,
                'alt' => $alt_task
            );
        }
        return $this;
    }

    /**
     *
     * @author Ariel Canal
     *         Possibilidade de armazenar mais de uma condição
     */
    public function unset_remove($bool = true, $field = false, $operand = false, $value = false)
    {
        $this->is_remove = ! (bool) $bool;
        if ($field && $operand && $value !== false) {
            $this->grid_restrictions['remove'][] = array(
                'bool' => $bool,
                'field' => $field,
                'operator' => $operand,
                'value' => $value
            );
        }
        return $this;
    }

    /**
     *
     * @author Ariel Canal
     *         Possibilidade de armazenar mais de uma condição
     */
    public function duplicate_button($bool = true, $field = false, $operand = false, $value = false)
    {
        $this->is_duplicate = (bool) $bool;
        if ($field && $operand && $value !== false) {
            $this->grid_restrictions['duplicate'][] = array(
                'bool' => $bool,
                'field' => $field,
                'operator' => $operand,
                'value' => $value
            );
        }
        return $this;
    }

    public function unset_csv($bool = true)
    {
        $this->is_csv = ! (bool) $bool;
        return $this;
    }

    public function unset_print($bool = true)
    {
        $this->is_print = ! (bool) $bool;
        return $this;
    }

    public function unset_title($bool = true)
    {
        $this->is_title = ! (bool) $bool;
        return $this;
    }

    public function unset_numbers($bool = true)
    {
        $this->is_numbers = ! (bool) $bool;
        return $this;
    }

    public function unset_search($bool = true)
    {
        $this->is_search = ! (bool) $bool;
        return $this;
    }

    public function unset_limitlist($bool = true)
    {
        $this->is_limitlist = ! (bool) $bool;
        return $this;
    }

    public function unset_pagination($bool = true)
    {
        $this->is_pagination = ! (bool) $bool;
        return $this;
    }

    public function unset_sortable($bool = true)
    {
        $this->is_sortable = ! (bool) $bool;
        return $this;
    }

    public function unset_list($bool = true)
    {
        $this->is_list = ! (bool) $bool;
        return $this;
    }

    /**
     *
     * @author Ariel Canal
     *         Ao atribuir o array de configuração do botão à $this->buttons, a
     *         chave é
     *         gravada também. Isso impede que sejam criados vários botões
     *         iguais quando há mais de
     *         uma instância do CRUD na mesma página.
     */
    public function button($link = '', $name = '', $icon = '', $class = '', $parameters = array(), $conditions = array(), $table_ro = true)
    {
        if ($link) {
            $this->buttons[$name] = array(
                'link' => $link,
                'name' => $name,
                'icon' => $icon,
                'class' => $class,
                'table_ro' => $table_ro,
                'params' => (array) $parameters
            );
        }
        foreach ($conditions as $condition) {
            if ($condition && is_array($condition) && count($condition) == 3 && $name) {
                list ($field, $operator, $value) = $condition;
                $this->grid_restrictions[$name][] = array(
                    'field' => $field,
                    'operator' => $operator,
                    'value' => $value,
                    'bool' => false
                );
            }
        }
        return $this;
    }

    public function custom_button($link = '', $label = '', $icon = '', $class = '', $tag = array())
    {
        if (! $link || ! $label) {
            return "";
        } else {
            $this->custom_buttons[$label] = array(
                'link' => $link,
                'label' => $label,
                'icon' => $icon,
                'class' => $class,
                'tag' => $tag
            );
        }
        return $this;
    }

    public function change_type($fields = '', $type = '', $default = false, $attr = array())
    {
        if ($type) {
            $fdata = $this->_parse_field_names($fields, 'change_type');
            foreach ($fdata as $fitem) {

                switch ($type) {
                    case 'file':
                    case 'image':
                        $this->upload_config[$fitem['table'] . '.' . $fitem['field']] = $attr;
                        break;
                    case 'price':
                        $def_attr = array(
                            'max' => 10,
                            'decimals' => 2,
                            'separator' => ',',
                            'prefix' => '',
                            'suffix' => '',
                            'point' => '.'
                        );
                        $this->field_attr[$fitem['table'] . '.' . $fitem['field']] = array_merge($def_attr, (array) $attr);
                        break;
                    case 'select':
                    case 'multiselect':
                    case 'radio':
                    case 'checkboxes':
                        if (! is_array($attr) or ! isset($attr['values'])) {
                            $this->field_attr[$fitem['table'] . '.' . $fitem['field']]['values'] = $attr;
                        } else {
                            $this->field_attr[$fitem['table'] . '.' . $fitem['field']] = $attr;
                        }
                        break;
                    case 'point':

                        // $this->field_attr[$fitem['table'] . '.' .
                        // $fitem['field']] = $map_attr;
                        $def_attr = array( // defaults
                            'text' => $this->config->default_text,
                            'search_text' => $this->config->default_search_text,
                            'zoom' => $this->config->default_zoom,
                            'width' => $this->config->default_width,
                            'height' => $this->config->default_height,
                            'search' => $this->config->default_coord,
                            'coords' => $this->config->default_search
                        );
                        $this->field_attr[$fitem['table'] . '.' . $fitem['field']] = array_merge($def_attr, (array) $attr);
                        break;
                    case 'remote_image':
                        if (is_array($attr) && ! isset($attr['link'])) {
                            $attr['link'] = '';
                        } elseif (is_string($attr)) {
                            $attr = array(
                                'link' => $attr
                            );
                        }
                        $this->field_attr[$fitem['table'] . '.' . $fitem['field']] = $attr;
                    default:
                        if ($attr && ! is_array($attr)) {
                            $attr = array(
                                'maxlength' => (int) $attr
                            );
                        }
                        $this->field_attr[$fitem['table'] . '.' . $fitem['field']] = $attr;
                        break;
                }
                $this->field_type[$fitem['table'] . '.' . $fitem['field']] = $type;
                $this->defaults[$fitem['table'] . '.' . $fitem['field']] = $default;
            }
        }
        return $this;
    }

    public function create_field($fields = '', $type = '', $default = false, $attr = array())
    {
        $fdata = $this->_parse_field_names($fields, 'create_field');
        foreach ($fdata as $fkey => $fitem) {
            $this->custom_fields[$fkey] = $fitem;
        }
        return $this->change_type($fields, $type, $default, $attr);
    }

    public function pass_default($fields = '', $value = '')
    {
        $fdata = $this->_parse_field_names($fields, 'pass_default');
        foreach ($fdata as $fitem) {
            $this->defaults[$fitem['table'] . '.' . $fitem['field']] = isset($fitem['value']) ? $fitem['value'] : $value;
        }
        return $this;
    }

    public function pass_var($fields = '', $value = '', $type = 'all', $eval = false)
    {
        $fdata = $this->_parse_field_names($fields, 'pass_var');
        $type = str_replace(' ', '', $type);
        $types = $this->parse_comma_separated($type);
        foreach ($fdata as $fitem) {
            $findex = $fitem['table'] . '.' . $fitem['field'];
            $pass_var = array(
                'table' => $fitem['table'],
                'field' => $fitem['field'],
                'value' => isset($fitem['value']) ? $fitem['value'] : $value,
                'eval' => $eval
            );
            foreach ($types as $tp) {
                if ($tp == 'all') {
                    $this->pass_var['create'][$findex] = $pass_var;
                    $this->pass_var['edit'][$findex] = $pass_var;
                    $this->pass_var['view'][$findex] = $pass_var;
                    break;
                } elseif ($tp == 'create' || $tp == 'edit' || $tp == 'view') {
                    $this->pass_var[$tp][$findex] = $pass_var;
                }
            }
        }
        return $this;
    }

    public function no_quotes($fields = '')
    {
        $fdata = $this->_parse_field_names($fields, 'no_quotes');
        foreach ($fdata as $fkey => $fitem) {
            $this->no_quotes[$fkey] = true;
        }
        return $this;
    }

    public function sum($fields = '', $class = '', $custom_text = '')
    {
        $fdata = $this->_parse_field_names($fields, 'sum');
        foreach ($fdata as $fkey => $fitem) {
            $this->sum[$fkey] = array(
                'table' => $fitem['table'],
                'column' => $fitem['field'],
                'class' => isset($fitem['value']) ? $fitem['value'] : $class,
                'custom' => $custom_text
            );
        }
        return $this;
    }

    public function readonly_on_create($field = '')
    {
        return $this->readonly($field, 'create');
    }

    public function disabled_on_create($field = '')
    {
        return $this->disabled($field, 'create');
    }

    public function readonly_on_edit($field = '')
    {
        return $this->readonly($field, 'edit');
    }

    public function disabled_on_edit($field = '')
    {
        return $this->disabled($field, 'edit');
    }

    public function readonly($fields = '', $mode = false) // needs to be
                                                           // re-written
    {
        $fdata = $this->_parse_field_names($fields, 'readonly');
        foreach ($fdata as $key => $fitem) {
            $this->readonly[$key] = $this->parse_mode($mode);
        }
        return $this;
    }

    public function disabled($fields = '', $mode = false)
    {
        $fdata = $this->_parse_field_names($fields, 'disabled');
        foreach ($fdata as $key => $fitem) {
            $this->disabled[$key] = $this->parse_mode($mode);
        }
        return $this;
    }

    public function condition($fields = '', $operator = '', $value = '', $method = '', $params = array(), $mode = false)
    {
        if ($fields && $method && $operator) {
            $fdata = $this->_parse_field_names($fields, 'condition');
            foreach ($fdata as $key => $fitem) {
                $this->condition[] = array(
                    'field' => $key,
                    'value' => $value,
                    'operator' => $operator,
                    'method' => $method,
                    'params' => (array) $params,
                    'mode' => $this->parse_mode($mode)
                );
            }
        }
        return $this;
    }

    public function instance_name()
    {
        return $this->instance_name;
    }

    public function benchmark($bool = true)
    {
        $this->benchmark = (bool) $bool;
        return $this;
    }

    public function column_cut($int = 50, $fields = false, $safe_output = false)
    {
        if ($fields === false) {
            $this->column_cut = (int) $int ? (int) $int : false;
            $this->safe_output = $safe_output;
        } else {
            $fdata = $this->_parse_field_names($fields, 'column_cut');
            foreach ($fdata as $fitem) {
                $this->column_cut_list[$fitem['table'] . '.' . $fitem['field']] = array(
                    'count' => $int,
                    'safe' => $safe_output
                );
            }
        }
        return $this;
    }

    public function links_label($text = '')
    {
        if ($text) {
            $this->links_label['text'] = trim($text);
        }
        return $this;
    }

    public function emails_label($text = '')
    {
        if ($text) {
            $this->emails_label['text'] = trim($text);
        }
        return $this;
    }

    public function no_editor($fields = '')
    {
        $fdata = $this->_parse_field_names($fields, 'no_editor');
        foreach ($fdata as $fitem) {
            $this->no_editor[$fitem['table'] . '.' . $fitem['field']] = true;
        }
        return $this;
    }



    public function send_external($path, $data = array(), $method = 'include', $mode = 'all', $where_field = '', $where_val = '')
    {
        if ($where_field) {
            $fdata = $this->_parse_field_names($where_field, 'send_external');
            $where_field = key($fdata) /*$fdata[0]['table'] . '.' . $fdata[0]['field']*/;
        }
        if ($mode == 'all' or $mode == 'create')
            $this->send_external_create = array(
                'data' => $data,
                'path' => $path,
                'method' => $method,
                'where_field' => $where_field,
                'where_val' => $where_val
            );
        if ($mode == 'all' or $mode == 'edit')
            $this->send_external_edit = array(
                'data' => $data,
                'path' => $path,
                'method' => $method,
                'where_field' => $where_field,
                'where_val' => $where_val
            );
        return $this;
    }

    public function page_call($url = '', $data = array(), $where_param = '', $where_value = '', $method = 'get')
    {
        return $this->send_external($url, $data, $method, 'all', $where_param, $where_value);
    }

    public function page_call_create($url = '', $data = array(), $where_param = '', $where_value = '', $method = 'get')
    {
        return $this->send_external($url, $data, $method, 'create', $where_param, $where_value);
    }

    public function page_call_edit($url = '', $data = array(), $where_param = '', $where_value = '', $method = 'get')
    {
        return $this->send_external($url, $data, $method, 'edit', $where_param, $where_value);
    }

    public function subselect($column_name = '', $sql = '', $before = false)
    {
        if ($column_name && $sql) {
            $table = $this->_get_table('subselect');
            $column_alias = $table . '.' . $column_name;
            if ($before) {
                $fdata = $this->_parse_field_names($before, 'subselect');
                $before = key($fdata) /*$fdata[0]['table'] . '.' . $fdata[0]['field']*/;
            }
            $this->subselect[$column_alias] = $sql;
            $this->subselect_before[$column_alias] = $before;
            // $this->no_select[$column_alias] = true;
            $this->labels[$column_alias] = $column_name;
            $this->field_type[$column_alias] = 'none';
            $this->defaults[$column_alias] = '';
        }
        return $this;
    }

    public function highlight($columns = '', $operator = '', $value = '', $color = '', $class = '')
    {
        if ($columns && $operator) {
            $fdata = $this->_parse_field_names($columns, 'highlight');
            foreach ($fdata as $fitem) {
                $this->highlight[$fitem['table'] . '.' . $fitem['field']][] = array(
                    'value' => $value,
                    'operator' => $operator,
                    'color' => $color,
                    'class' => $class
                );
            }
        }
        return $this;
    }

    public function highlight_row($columns = '', $operator = '', $value = '', $color = '', $class = '')
    {
        if ($columns && $operator) {
            $fdata = $this->_parse_field_names($columns, 'highlight_row');
            foreach ($fdata as $fitem) {
                $this->highlight_row[] = array(
                    'field' => $fitem['table'] . '.' . $fitem['field'],
                    'value' => $value,
                    'operator' => $operator,
                    'color' => $color,
                    'class' => $class
                );
            }
        }
        return $this;
    }

    public function modal($columns = '', $icon = false)
    {
        $fdata = $this->_parse_field_names($columns, 'modal');
        foreach ($fdata as $fitem) {
            $this->modal[$fitem['table'] . '.' . $fitem['field']] = isset($fitem['value']) ? $fitem['value'] : $icon;
        }
        return $this;
    }

    public function column_class($columns = '', $class = '')
    {
        $fdata = $this->_parse_field_names($columns, 'column_class');
        foreach ($fdata as $fitem) {
            $this->column_class[$fitem['table'] . '.' . $fitem['field']][] = isset($fitem['value']) ? $fitem['value'] : $class;
        }
        return $this;
    }

    public function language($lang = 'en')
    {
        $this->language = $lang;
        return $this;
    }

    public function field_tooltip($fields = '', $tooltip = '', $icon = false)
    {
        if ($fields && $tooltip) {
            $fdata = $this->_parse_field_names($fields, 'column_class');
            foreach ($fdata as $fitem) {
                $this->field_tooltip[$fitem['table'] . '.' . $fitem['field']] = array(
                    'tooltip' => isset($fitem['value']) ? $fitem['value'] : $tooltip,
                    'icon' => $icon
                );
            }
        }
        return $this;
    }

    public function search_columns($fields = false, $default = false)
    {
        if ($fields) {
            $fdata = $this->_parse_field_names($fields, 'search_columns');
            foreach ($fdata as $fkey => $fitem) {
                $this->search_columns[$fkey] = $fitem;
            }
        }
        if ($default !== false) {
            if ($default == '') {
                $this->search_default = false;
            } else {
                $fdata = $this->_parse_field_names($default, 'search_columns');
                $this->search_default = key($fdata) /*$fdata[0]['table'] . '.' . $fdata[0]['field']*/;
            }
        }
        return $this;
    }

    public function column_width($fields = '', $width = '')
    {
        if ($fields && $width) {
            $fdata = $this->_parse_field_names($fields, 'column_width');
            foreach ($fdata as $fitem) {
                $this->column_width[$fitem['table'] . '.' . $fitem['field']] = $width;
            }
        }
        return $this;
    }

    /**
     *
     * @author Ariel Canal
     *         COMPATIBILIZAÇÃO COM A ARQUITETURA DO CODEIGINITER
     */
    public function after_upload($callable = '', $path = 'functions.php')
    {
        if ($callable && $path) {
            $this->after_upload['callable'] = $callable;
            $this->after_upload['path'] = "/../../helpers/" . $path;
        }
        return $this;
    }

    /**
     *
     * @author Ariel Canal
     *         COMPATIBILIZAÇÃO COM A ARQUITETURA DO CODEIGINITER
     */
    public function before_upload($callable = '', $path = 'functions.php')
    {
        if ($callable && $path) {
            $this->before_upload['callable'] = $callable;
            $this->before_upload['path'] = "/../../helpers/" . $path;
        }
        return $this;
    }

    public function before_download($callable = '', $path = 'functions.php')
    {
        if ($callable && $path) {
            $this->before_download['callable'] = $callable;
            $this->before_download['path'] = "/../../helpers/" . $path;
        }
        return $this;
    }

    /**
     *
     * @author Ariel Canal
     *         COMPATIBILIZAÇÃO COM A ARQUITETURA DO CODEIGINITER
     */
    public function column_callback($fields = '', $callback = '', $path = 'functions.php')
    {
        if ($fields && $callback && $path) {
            $fdata = $this->_parse_field_names($fields, 'column_callback');
            foreach ($fdata as $fitem) {
                $this->column_callback[$fitem['table'] . '.' . $fitem['field']] = array(
                    'name' => $fitem['table'] . '.' . $fitem['field'],
                    'path' => rtrim("/../../helpers/" . $path, '/'),
                    'callback' => $callback
                );
            }
        }
        return $this;
    }

    /**
     *
     * @author Ariel Canal
     *         COMPATIBILIZAÇÃO COM A ARQUITETURA DO CODEIGINITER
     */
    public function field_callback($fields = '', $callback = '', $path = 'functions.php')
    {
        if ($fields && $callback && $path) {
            $fdata = $this->_parse_field_names($fields, 'field_callback');
            foreach ($fdata as $fitem) {
                $this->field_callback[$fitem['table'] . '.' . $fitem['field']] = array(
                    'name' => $fitem['table'] . '.' . $fitem['field'],
                    'path' => rtrim("/../../helpers/" . $path, '/'),
                    'callback' => $callback
                );
            }
        }
        return $this;
    }

    /**
     *
     * @author Ariel Canal
     *         COMPATIBILIZAÇÃO COM A ARQUITETURA DO CODEIGINITER
     */
    public function replace_insert($callable = '', $path = 'functions.php')
    {
        if ($callable) {
            $this->replace_insert = array(
                'callable' => $callable,
                'path' => "/../../helpers/" . $path
            );
        }
        return $this;
    }

    /**
     *
     * @author Ariel Canal
     *         COMPATIBILIZAÇÃO COM A ARQUITETURA DO CODEIGINITER
     */
    public function replace_update($callable = '', $path = 'functions.php')
    {
        if ($callable) {
            $this->replace_update = array(
                'callable' => $callable,
                'path' => "/../../helpers/" . $path
            );
        }
        return $this;
    }

    /**
     *
     * @author Ariel Canal
     *         COMPATIBILIZAÇÃO COM A ARQUITETURA DO CODEIGINITER
     */
    public function replace_remove($callable = '', $path = 'functions.php')
    {
        if ($callable) {
            $this->replace_remove = array(
                'callable' => $callable,
                'path' => "/../../helpers/" . $path
            );
        }
        return $this;
    }

    /**
     *
     * @author Ariel Canal
     *         COMPATIBILIZAÇÃO COM A ARQUITETURA DO CODEIGINITER
     */
    public function before_list($callable = '', $path = 'functions.php')
    {
        if ($callable && $path) {
            $this->before_list['callable'] = $callable;
            $this->before_list['path'] = "/../../helpers/" . $path;
        }
        return $this;
    }

    /**
     *
     * @author Ariel Canal
     *         COMPATIBILIZAÇÃO COM A ARQUITETURA DO CODEIGINITER
     */
    public function before_create($callable = '', $path = 'functions.php')
    {
        if ($callable && $path) {
            $this->before_create['callable'] = $callable;
            $this->before_create['path'] = "/../../helpers/" . $path;
        }
        return $this;
    }

    /**
     *
     * @author Ariel Canal
     *         COMPATIBILIZAÇÃO COM A ARQUITETURA DO CODEIGINITER
     */
    public function before_edit($callable = '', $path = 'functions.php')
    {
        if ($callable && $path) {
            $this->before_edit['callable'] = $callable;
            $this->before_edit['path'] = "/../../helpers/" . $path;
        }
        return $this;
    }

    /**
     *
     * @author Ariel Canal
     *         COMPATIBILIZAÇÃO COM A ARQUITETURA DO CODEIGINITER
     */
    public function before_view($callable = '', $path = 'functions.php')
    {
        if ($callable && $path) {
            $this->before_view['callable'] = $callable;
            $this->before_view['path'] = "/../../helpers/" . $path;
        }
        return $this;
    }

    public function call_update($postdata, $primary)
    {
        if (! $this->task) {
            // Lança exceção quando método é usado fora de callbacks
            return Services::response()->setStatusCode(500)->setBody(lang('cCrud.call_update_only_callbacks'));
        }
        return $this->_update($postdata->toArray(), $primary);
    }

    public function set_var($name = null, $value = null)
    {
        if ($name) {
            $this->custom_vars[$name] = $value;
        }
        return $this;
    }

    public function get_var($name = null)
    {
        if ($name) {
            return isset($this->custom_vars[$name]) ? $this->custom_vars[$name] : false;
        } else {
            return false;
        }
    }

    public function unset_var($name)
    {
        if (isset($this->custom_vars[$name])) {
            unset($this->custom_vars[$name]);
        }
        return $this;
    }

    public function column_name($fields = '', $text = '')
    {
        $fdata = $this->_parse_field_names($fields, 'column_name');
        foreach ($fdata as $fitem) {
            $this->column_name[$fitem['table'] . '.' . $fitem['field']] = $text;
        }
        unset($fields);
        return $this;
    }

    public function column_pattern($fields, $patern)
    {
        if ($fields && $patern) {
            $fdata = $this->_parse_field_names($fields, 'column_pattern');
            foreach ($fdata as $fkey => $fitem) {
                $this->column_pattern[$fkey] = $patern;
            }
        }
        return $this;
    }

    public function column_tooltip($fields = '', $tooltip = '', $icon = false)
    {
        if ($fields && $tooltip) {
            $fdata = $this->_parse_field_names($fields, 'column_tooltip');
            foreach ($fdata as $fkey => $fitem) {
                $this->column_tooltip[$fkey] = array(
                    'tooltip' => isset($fitem['value']) ? $fitem['value'] : $tooltip,
                    'icon' => $icon
                );
            }
        }
        return $this;
    }

    public function buttons_position($position = 'left')
    {
        switch ($position) {
            case 'left':
            case 'right':
            case 'none':
                $this->buttons_position = $position;
                break;
        }
        return $this;
    }

    /* INCLUIDO O PARÂMETRO $remove */
    public function hide_button($names = '', $remove = false)
    {
        foreach ($this->parse_comma_separated($names) as $name) {
            if ($remove) {
                unset($this->hide_button[$name]);
            } else {
                $this->hide_button[$name] = 1;
            }
        }
        return $this;
    }

    public function set_lang($var = '', $translate = '')
    {
        if ($var) {
            $this->set_lang[$var] = $translate;
        }
        return $this;
    }

    public function search_pattern($left = '%', $right = '%')
    {
        $this->search_pattern = array(
            $left,
            $right
        );
        return $this;
    }

    /**
     * Define uma view personalizada para o modo informado.
     *
     * @param string $mode Modo de funcionamento do cCrud
     * @param string $file Caminho da view personalizada
     *
     * @return $this
     */
    public function setView($mode = '', $file = '')
    {
        if ($mode && $file) {
            switch ($mode) {
                case 'list':
                case 'create':
                case 'edit':
                case 'view':
                    $this->views[$mode] = '../' . $file;
                    break;
                default:
                    // Modo informado é inválido
                    return false;
            }
        }
        return $this;
    }

    /**
     *
     * @author Ariel Canal
     *         Alterado o path default para os helpers.
     *         Incluído o parâmetro icon.
     *         Incluído o parâmetro button_attr
     *         Incluídos os parâmetros conditions.
     *         Incluído o mode.
     */
    public function create_action($name = '', $callable = '', $path = 'functions.php', $icon = null, $button_attr = null, $cond_field = null, $cond_operator = null, $cond_value = null, $mode = null)
    {
        if ($callable && $name) {
            if ($cond_field && ! is_array($cond_field)) {
                $conditions[0][0] = $cond_field;
                $conditions[0][1] = $cond_operator;
                $conditions[0][2] = $cond_value;
            } else if ($cond_field) {
                $conditions = $cond_field;
            } else {
                $conditions = array();
            }
            $this->action[$name] = array(
                'callable' => $callable,
                'path' => "/../../helpers/" . $path,
                'icon' => $icon,
                'button_attr' => $button_attr,
                'conditions' => $conditions,
                'mode' => $mode
            );
        }
        return $this;
    }

    public function get($name = '')
    {
        if (! $this->task) {
            // Lança exceção quando método é usado fora de callbacks
            return Services::response()->setStatusCode(500)->setBody(lang('cCrud.get_only_callbacks'));
        }
        if ($this->_get('key')) {
            return $this->_get($name);
        } else {
            return $this->_post($name);
        }
    }

    public function default_tab($name = false)
    {
        $this->default_tab = $name;
        return $this;
    }

    public function query($query = '')
    {
        $this->query = $query;
        return $this;
    }

    public function check()
    {
        $array = array();
        $phpvers = phpversion();
        $array['PHP version'] = array(
            'value' => $phpvers,
            'state' => ((int) $phpvers >= 5 ? 'passed' : 'error')
        );
    }

    public function set_attr($fields = '', array $attr = array())
    {
        if ($fields && $attr) {
            $fdata = $this->_parse_field_names($fields, 'set_attr');
            foreach ($fdata as $fkey => $fitem) {
                if (isset($this->field_attr[$fkey])) {
                    $this->field_attr[$fkey] = array_merge((array) $this->field_attr[$fkey], $attr);
                } else {
                    $this->field_attr[$fkey] = $attr;
                }
            }
        }
        return $this;
    }

    public function lists_null_opt($bool = true)
    {
        $this->lists_null_opt = $bool;
        return $this;
    }

    /**
     * public renderer, final instance method
     */
    public function render($task = false, $primary = false)
    {
        $this->benchmark_start();
        $this->_receive_post($task, $primary);
        $this->_regenerate_key();
        $this->_remove_and_save_uploads();
        $this->_get_language();
        $this->_get_theme_config();
        if ($this->query) {
            return $this->render_custom_query_task();
        }
        $this->_get_table_info();
        return $this->_run_task();
    }

    /**
     * main task trigger
     */
    protected function _run_task()
    {
        if ($this->after && $this->after == $this->task) {
            // Previne recursão de tarefas
            return Services::response()->setStatusCode(500)->setBody(lang('cCrud.task_recursion'));
        }
        if (! $this->task) {
            $this->task = 'list';
        }
        switch ($this->task) {
            case 'create':
                $this->_set_field_types('create');
                // $this->_sort_defaults();
                return $this->_create();
                break;
            case 'edit':
                $this->_set_field_types('edit');
                return $this->_entry('edit');
                break;
            case 'save':
                if (! $this->before) {
                    // Tarefa restrita sem callback definido
                    return Services::response()->setStatusCode(403)->setBody(lang('cCrud.restricted_task'));
                }
                $this->_set_field_types($this->before);
                return $this->_save();

                /*
                 * $this->task = $this->after;
                 * $this->after = null;
                 * return $this->_run_task();
                 */
                break;
            case 'remove':
                $this->set_custom_lists();
                $this->_set_field_types('list');
                $this->_remove();
                return $this->_list();
                break;
            case 'mass':
                return $this->_mass_action();
                break;
            case 'upload':
                ini_set('post_max_size', '64M');
                ini_set('upload_max_filesize', '64M');
                return $this->_upload();
                break;
            case 'remove_upload':
                return $this->_remove_upload();
                break;
            case 'crop_image':
                return $this->manual_crop();
                break;
            case 'unique':
                $this->_set_field_types('edit');
                return $this->_check_unique_value();
                break;
            case 'clone':
                $this->set_custom_lists();
                $this->_set_field_types('list');
                $this->_clone_row();
                return $this->_list();
                break;
            case 'print':
                if (! $this->is_print) {
                    // Acesso restrito à tarefa de impressão
                    return Services::response()->setStatusCode(403)->setBody(lang('cCrud.restricted'));
                }
                $this->_set_field_types('list', $this->config->print_all_fields);
                $this->set_custom_lists();
                return $this->_list();
                break;
            case 'depend':
                return $this->create_relation($this->_post('name', false, 'base64'), $this->_post('value'), $this->get_field_attr($this->_post('name', false, 'base64'), 'edit'), $this->_post('dependval'));
                break;
            case 'view':
                $this->_set_field_types('view');
                return $this->_entry('view');
                break;
            case 'query':

                break;
            case 'external':

                break;
            case 'action':
                return $this->_call_action();
                break;
            case 'file':
                $this->_set_field_types('list');
                return $this->_render_file();
                break;
            case 'csv':
                $this->set_custom_lists();
                $this->_set_field_types('list', $this->config->csv_all_fields);
                return $this->_csv();
                break;
            case 'relation_search':
                return $this->relation_search($this->_post('name', false, 'base64'), $this->_post('dependval'));
                break;
            case 'join_relation':
                return $this->join_relation_data($this->_post('jr_field', false, 'base64'), $this->_post('jr_value', false));
                break;
            case 'report':
                $this->unset_add();
                $this->unset_remove();
                $this->unset_edit();
                $this->unset_view();
                $this->mass_remove(false);
                $this->unset_sortable(false);
                $this->unset_csv(false);
                $this->unset_print();
                $this->unset_list(false);
                $this->_set_field_types('report');
                return $this->_report();
                break;
            case 'make_report':
                $this->_set_field_types('list');
                $this->_save_report_values();
                return $this->_list();
                break;
            case 'change_columns':
                $this->columnsActive($this->_post('columns'));
                $this->_set_field_types('list');
                return $this->_list();
            case 'list':
            default:
                $this->set_custom_lists();
                $this->_set_field_types('list');
                return $this->_list();
                break;
        }
    }

    protected function render_custom_query_task()
    {
        $this->is_edit = false;
        $this->is_remove = false;
        $this->is_create = false;
        $this->is_view = false;
        $this->is_search = false;
        switch ($this->task) {
            case 'print':
                if (! $this->is_print) {
                    // Acesso restrito à tarefa de impressão
                    return Services::response()->setStatusCode(403)->setBody(lang('cCrud.restricted'));
                }
                $this->start = 0;
                $this->limit = 0;
                return $this->render_custom_datagrid();
                break;
            case 'action':
                return $this->_call_action();
                break;
            case 'csv':
                return $this->render_custom_csv();
                break;
            default:
                return $this->render_custom_datagrid();
                break;
        }
    }

    protected function render_custom_datagrid()
    {
        $query = $this->parse_query_params();
        $countQuery = $this->model->db->query('SELECT COUNT(*) as `count` FROM (SELECT NULL' . $this->total_query . ') counts');
        $this->sum_row = $countQuery->getRowArray();
        $this->result_total = $this->sum_row['count'];
        $order_by = $this->_build_order_by();
        $limit = $this->_build_limit($this->result_total);
        $this->result_list = $this->model->db->query($query . ' ' . $order_by . ' ' . $limit)->getResultArray();
        $this->columns = reset($this->result_list);
        unset($this->columns['primary_key']);
        foreach ($this->columns as $key => $tmp) {
            $this->columns[$key] = array(
                'table' => '',
                'field' => $key
            );
            if (! isset($this->field_type[$key])) {
                $this->field_type[$key] = 'text';
            }
        }
        $this->fields_list = $this->columns;
        $this->_set_column_names();
        if (! $this->table_name) {
            $this->table_name = '&nbsp;';
        }
        return $this->_render_list();
    }

    protected function render_custom_csv()
    {
        if (! $this->is_csv) {
            // CSV indisponível para esta solicitação
            return Services::response()->setStatusCode(403)->setBody(lang('cCrud.restricted'));
        }
        $this->columns = $this->fields_list;
        $query = $this->parse_query_params();
        $order_by = $this->_build_order_by();
        $this->_set_column_names();
        ini_set('auto_detect_line_endings', true);
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: public");
        header("Content-type: application/octet-stream");
        $table_name = $this->_clean_file_name(trim(html_entity_decode($this->table_name, ENT_QUOTES, 'utf-8')));
        header("Content-Disposition: attachment; filename=\"" . ($table_name ? $table_name : 'table') . ".csv\"");
        header("Content-Transfer-Encoding: binary");
        $output = fopen('php://output', 'w');
        fwrite($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // bom
        fputcsv($output, $this->columns_names, $this->config->csv_delimiter, $this->config->csv_enclosure);
        $queryResult = $this->model->db->query($query . ' ' . $order_by);
        foreach ($queryResult->getResultArray() as $row) {
            $out = array();
            foreach ($this->columns as $field => $fitem) {
                $out[] = htmlspecialchars_decode(strip_tags($this->_render_export_item($field, $row[$field], $row['primary_key'], $row)), ENT_QUOTES);
            }
            fputcsv($output, $out, $this->config->csv_delimiter, $this->config->csv_enclosure);
        }
    }

    protected function parse_query_params()
    {
        $query = preg_replace('/\s+/u', ' ', $this->query);
        $query = preg_replace('/[\)`\s]from[\(`\s]/ui', ' FROM ', $query);

        if (preg_match('/(limit([0-9\s\,]+)){1}$/ui', $query, $matches)) {
            $query = str_ireplace($matches[0], '', $query);
            if (! $this->ajax_request) {
                $tmp = explode(',', $matches[2]);
                if (isset($tmp[1])) {
                    $this->start = (int) trim($tmp[0]);
                    $this->limit = (int) trim($tmp[1]);
                } else {
                    $this->start = 0;
                    $this->limit = (int) trim($tmp[0]);
                }
            }
        }
        if (preg_match('/(order\sby([^\(\)]+)){1}$/ui', $query, $matches)) {
            $query = str_ireplace($matches[0], '', $query);
            if (! $this->ajax_request) {
                $tmp = explode(',', $matches[2]);
                foreach ($tmp as $item) {
                    $item = trim($item);
                    $direct = (mb_strripos($item, ' desc') == (mb_strlen($item) - 5) || mb_strripos($item, '`desc') == (mb_strlen($item) - 5)) ? 'desc' : 'asc';
                    $item = str_ireplace(array(
                        ' asc',
                        ' desc',
                        '`asc',
                        '`desc',
                        '`'
                    ), '', $item);
                    $this->order_by[$item] = $direct;
                }
            }
        }
        $tmp = preg_replace_callback('/\( (?> [^)(]+ | (?R) )+ \)/xui', array(
            $this,
            'query_params_callback'
        ), $query);
        $from_pos = mb_strpos($tmp, ' FROM ');
        $this->total_query = mb_substr($query, $from_pos);
        $query = mb_substr($query, 0, $from_pos) . ',(0) AS `primary_key`' . mb_substr($query, $from_pos);
        return $query;
    }

    protected function query_params_callback($matches)
    {
        return preg_replace('/./Uui', '*', $matches[0]);
    }

    /**
     * Gera o conteúdo final a ser enviado ao navegador.
     *
     * @param string $content Conteúdo principal renderizado
     *
     * @return string
     */
    protected function render_output(string $content)
    {
        if ($this->ajax_request) {
            $contents = $this->render_control_fields() . $content;
            $this->after_render();
        } else {
            $contents = '';
            if (! self::$css_loaded && ! $this->config->manual_load) {
                $contents .= self::load_css();
            }

            $viewPath = __DIR__ . '/views';
            $renderer = Services::renderer($viewPath);
            $contents .= $renderer->setData([
                'cCrud'    => $this,
                'content' => $content
            ])->render('container.php');

            if (! self::$js_loaded && ! $this->config->manual_load) {
                $contents .= self::load_js();
            }
            $this->after_render();
        }

        if (in_array($this->task, [
            'create',
            'edit'
        ])) {
            return $this->open_tag('form') . $contents . $this->close_tag('form');
        }

        return $contents;
    }

    /**
     * Envia o conteúdo renderizado para a resposta HTTP.
     *
     * @param string $output Conteúdo final
     *
     * @return ResponseInterface
     */
    protected function set_output(string $output): string
    {
        return $output;
    }

    protected function after_render()
    {
        switch ($this->task) {
            case 'file':
            case 'depend':
            case 'print':
            case 'csv':
                break;
            default:
                if (self::$instance) {
                    foreach (self::$instance as $i) {
                        $i->_export_vars();
                    }
                }
                break;
        }
        if (is_callable($this->config->after_render)) {
            call_user_func($this->config->after_render);
        }
    }

    /**
     * files and images rendering
     */
    protected function _render_file()
    {
        $field = str_replace('`', '', $this->_get('field'));
        if (! $field)
            exit();
        $thumb = $this->_get('thumb', false);
        $crop = (bool) $this->_get('crop', false);
        $settings = $this->upload_config[$field];
        $blob = false;

        $image = array_search($field, array_reverse($this->upload_to_save));
        if (! $image) {
            list ($tmp1, $tmp2) = explode('.', $field);

            $this->where_pri($this->primary_key, $this->primary_val);
            $where = $this->_build_where();
            $table_join = $this->_build_table_join();

            $query = $this->model->db->query("SELECT `$tmp1`.`$tmp2`\r\n FROM `{$this->table}`\r\n {$table_join}\r\n {$where}\r\n LIMIT 1");
            $row = $query->getRowArray();
            $image = $row[$tmp2];
            if (isset($this->upload_config[$field]['blob']) && $this->upload_config[$field]['blob'] === true) {
                $blob = true;
            } else {
                if ($thumb !== false) {
                    if (isset($settings['thumbs'][$thumb])) {
                        $thumb_set = $settings['thumbs'][$thumb];
                        $path = $this->get_thumb_path($image, $field, $thumb_set);
                    } else {
                        $folder = $this->get_image_folder($field);
                        $path = $folder . '/' . $image;
                    }
                } else {
                    $folder = $this->get_image_folder($field);
                    $path = $folder . '/' . $image;
                }
                // $image = ($thumb ? substr_replace($image, $marker,
                // strrpos($image, '.'), 0) : $image);
                // $path = $this->check_folder($folder, 'render_image') . '/' .
                // $image;
                if (! is_file($path)) {
                    header("HTTP/1.0 404 Not Found");
                    self::erro('not_found', 404);
                }
                // $output = file_get_contents($path);
            }
        } else {
            // $folder = $this->upload_folder[$field];
            if ($crop) {
                $folder = $this->get_image_folder($field);
                $tmp_filename = substr($image, 0, strrpos($image, '.')) . '.tmp';
                $path = $folder . '/' . $tmp_filename;
            } elseif ($thumb !== false) {
                $thumb_set = $settings['thumbs'][$thumb];
                $path = $this->get_thumb_path($image, $field, $thumb_set);
            } else {
                $folder = $this->get_image_folder($field);
                $path = $folder . '/' . $image;
            }
            // $image = ($thumb ? substr_replace($image, $marker,
            // strrpos($image, '.'), 0) : $image);
            // $path = $this->check_folder($folder, 'render_image') . '/' .
            // $image;
            if (! is_file($path)) {
                header("HTTP/1.0 404 Not Found");
                self::erro('not_found', 404);
            }
            // $output = file_get_contents($path);
        }
        if ($this->before_download) {
            $path = $this->check_file($this->before_download['path'], 'before_download');
            include_once ($path);
            $callable = $this->before_download['callable'];
            if (is_callable($callable)) {
                call_user_func_array($callable, array(
                    $field,
                    $this->filter_file_name($image),
                    $settings,
                    $this
                ));
                if ($this->exception) {
                    $out = $this->call_exception();
                }
            }
        }

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: public");
        if ($this->field_type[$field] == 'image' && ! $blob) {
            $size = getimagesize($path);
            switch ($size[2]) {
                case 1:
                    header("Content-type: image/gif");
                    break;
                case 2:
                    header("Content-type: image/jpeg");
                    break;
                case 3:
                    header("Content-type: image/png");
                    break;
            }
        } elseif ($blob && $this->field_type[$field] == 'image') {
            header("Content-type: image/jpeg");
        } elseif ($blob) {
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=\"" . (isset($settings['filename']) ? $settings['filename'] : 'binary_data') . "\"");
            header("Content-Transfer-Encoding: binary");
        } else {
            if (trim(strtolower(strrchr($path, '.')), '.') == 'pdf') {
                header("Content-type: application/pdf");
                header("Content-Disposition: inline; filename=\"" . (isset($settings['filename']) ? $settings['filename'] : $this->filter_file_name($image)) . "\"");
            } else {
                header("Content-type: application/octet-stream");
                header("Content-Disposition: attachment; filename=\"" . (isset($settings['filename']) ? $settings['filename'] : $this->filter_file_name($image)) . "\"");
            }

            header("Content-Transfer-Encoding: binary");
        }
        if ($blob)
            header("Content-Length: " . strlen($image));
        else
            header("Content-Length: " . filesize($path));
        @ob_clean();
        flush();
        if ($blob) {
            return $image;
        } else {
            readfile($path);
        }
        exit();
    }

    public function filter_file_name($name)
    {
        $pos = strpos($name, '_@cCrud');
        if ($pos === false) {
            return $name;
        } else if ($pos === 0) {
            return substr($name, 7, strlen($name) - 7);
        } else {
            $ext = substr($name, strrpos($name, '.'), strlen($name) - strrpos($name, '.'));
            return substr($name, 0, $pos) . $ext;
        }
    }

    public function _csv()
    {
        if (! $this->is_csv) {
            // Acesso restrito à exportação CSV
            return Services::response()->setStatusCode(403)->setBody(lang('cCrud.restricted'));
        }
        $select = $this->_build_select_list(true);
        $table_join = $this->_build_table_join();
        $where = $this->_build_where();
        $order_by = $this->_build_order_by();
        $this->_set_column_names();
        $headers = array();
        foreach ($this->columns as $field => $fitem) {
            if (isset($this->field_type[$field]) && ($this->field_type[$field] == 'password' or $this->field_type[$field] == 'hidden'))
                continue;
            $headers[] = $this->columns_names[$field];
        }
        // print "SELECT {$select} FROM `{$this->table}` {$table_join} {$where} {$order_by}";exit;
        $query = $this->model->db->query("SELECT {$select} FROM `{$this->table}` {$table_join} {$where} {$order_by}");
        if ($query->getNumRows() > $this->config->csv_limit)
            // Quantidade de registros acima do permitido
            return Services::response()->setStatusCode(413)->setBody(lang('cCrud.max_records_exceeded'));
        ini_set('auto_detect_line_endings', true);
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: public");
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"" . $this->_clean_file_name($this->table_name ? $this->table_name : $this->table) . '_' . date('Ymd_His') . ".csv\"");
        header("Content-Transfer-Encoding: binary");
        $output = fopen('php://output', 'w');
        fwrite($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // bom
        fputcsv($output, $headers, $this->config->csv_delimiter, $this->config->csv_enclosure);

        foreach ($query->getResultArray() as $row) {
            $out = array();
            foreach ($this->columns as $field => $fitem) {
                if (isset($this->field_type[$field]) && ($this->field_type[$field] == 'password' or $this->field_type[$field] == 'hidden'))
                    continue;
                $out[] = htmlspecialchars_decode(strip_tags($this->_render_export_item($field, $row[$field], $row['primary_key'], $row)), ENT_QUOTES);
            }
            fputcsv($output, $out, $this->config->csv_delimiter, $this->config->csv_enclosure);
        }
    }

    /**
     * Recupera variáveis enviadas via POST.
     *
     * @param string $field   Nome do campo solicitado.
     * @param mixed  $default Valor padrão se o campo não existir.
     * @param mixed  $filter  Tipo de filtro adicional.
     *
     * @return mixed
     */
    protected function _post(string $field = '', $default = false, $filter = false)
    {
        $request = Services::request();
        $post    = $request->getPost('cCrud');

        if (! is_array($post) || ! array_key_exists($field, $post)) {
            return $default;
        }

        $security = $this->config->auto_xss_filtering ? Services::security() : null;
        $value    = $post[$field];

        if (($field === 'postdata' || $field === 'unique') && $value) {
            $dataKeys = array_keys($value);
            foreach ($dataKeys as $k => $key) {
                $dataKeys[$k] = $security ? $security->clean($this->fieldname_decode($key)) : $this->fieldname_decode($key);
                if ($security) {
                    $value[$key] = $security->clean($value[$key]);
                }
            }
            return array_combine($dataKeys, $value);
        }

        if ($filter) {
            switch ($filter) {
                case 'key':
                    $value = str_replace('`', '', $value);
                    return $security ? $security->clean($value) : $value;
                case 'int':
                    return (int) $value;
                case 'trim':
                    $value = trim($value);
                    return $security ? $security->clean($value) : $value;
                case 'base64':
                    $decoded = $this->fieldname_decode($value);
                    return $security ? $security->clean($decoded) : $decoded;
                default:
                    return $security ? $security->clean($value) : $value;
            }
        }

        return $security ? $security->clean($value) : $value;
    }

    /**
     * Recupera variáveis enviadas via GET.
     *
     * @param string $field   Nome do campo solicitado.
     * @param mixed  $default Valor padrão se o campo não existir.
     * @param mixed  $filter  Tipo de filtro adicional.
     *
     * @return mixed
     */
    protected function _get(string $field = '', $default = false, $filter = false)
    {
        $request = Services::request();
        $get     = $request->getGet('cCrud');

        if (! is_array($get) || ! array_key_exists($field, $get)) {
            return $default;
        }

        $security = $this->config->auto_xss_filtering ? Services::security() : null;
        $value    = $get[$field];

        if ($filter) {
            switch ($filter) {
                case 'key':
                    $value = str_replace('`', '', $value);
                    return $security ? $security->clean($value) : $value;
                case 'int':
                    return (int) $value;
                case 'trim':
                    $value = trim($value);
                    return $security ? $security->clean($value) : $value;
                default:
                    return $security ? $security->clean($value) : $value;
            }
        }

        return $security ? $security->clean($value) : $value;
    }

    protected function stripslashes_callback(&$item, $key)
    {
        $item = stripslashes($item);
    }

    /**
     * creates fieldlist for adding record
     */
    protected function _create($postdata = array())
    {
        if (! $this->is_create || $this->table_ro)
            // Operação de criação não permitida
            return Services::response()->setStatusCode(403)->setBody(lang('cCrud.forbidden'));
        $this->primary_val = null;
        $this->result_row = array_merge($this->defaults, $postdata);

        if ($this->before_create) {
            $path = $this->check_file($this->before_create['path'], 'before_create');
            include_once ($path);
            if (is_callable($this->before_create['callable'])) {
                $postdata = new PostData($this->result_row, $this);
                call_user_func_array($this->before_create['callable'], array(
                    $postdata,
                    $this
                ));
                $this->result_row = $postdata->toArray();
            }
        }

        $this->_set_field_names();

        /**
         * conditions process
         */
        if ($this->condition) {
            foreach ($this->condition as $params) {
                if (! isset($params['mode']['create']))
                    continue;

                $params['value'] = $this->replace_text_variables($params['value'], $this->result_row);
                if (array_key_exists($params['field'], $this->result_row) && $this->_compare($this->result_row[$params['field']], $params['operator'], $params['value'])) {
                    if (is_array($params['method']) && is_callable($params['method'])) {
                        call_user_func_array($params['method'], $params['params']);
                    } elseif (is_callable(array(
                        $this,
                        $params['method']
                    ))) {
                        $this->condition_backup($params['method']);
                        call_user_func_array(array(
                            $this,
                            $params['method']
                        ), $params['params']);
                    } elseif (is_callable($params['method'])) {
                        call_user_func_array($params['method'], $params['params']);
                    }
                }
            }
        }

        return $this->_render_details($this->task);
    }

    /**
     *
     * @author Ariel Canal
     *         EXECUTA A PRIMEIRA TAREFA ALTERNATIVA SE A PRIMÁRIA ESTIVER
     *         BLOQUEADA
     *         compatibilizada com reports
     */
    /**
     * creates fieldlist for editing or viewing record
     */
    protected function _entry($mode = 'edit', $postdata = array())
    {
        $this->where_pri($this->primary_key, $this->primary_val);
        $select = $this->_build_select_details($mode);
        $where = $this->_build_where();
        $table_join = $this->_build_table_join();
        $query = $this->model->db->query("SELECT {$select}\r\n FROM `{$this->table}`\r\n {$table_join}\r\n {$where}\r\n LIMIT 1");
        $this->result_row = array_merge((array) $query->getRowArray(), $postdata);

        // moved here to support conditions for buttons
        if (((! $this->is_edit($this->result_row) || $this->table_ro) && $mode == 'edit') or (! $this->is_view($this->result_row) && $mode == 'view'))
                if ($this->grid_restrictions[$mode][0]['alt']) {
                    $this->task = $this->grid_restrictions[$mode][0]['alt'];
                    return $this->_run_task();
                } else {
                    // Acesso proibido à entrada solicitada
                    return Services::response()->setStatusCode(403)->setBody(lang('cCrud.forbidden'));
                }

        $callback_method = 'before_' . $mode;
        if ($mode != "report" && $this->{$callback_method}) {
            $path = $this->check_file($this->{$callback_method}['path'], $callback_method);
            include_once ($path);
            if (is_callable($this->{$callback_method}['callable'])) {
                $postdata = new PostData($this->result_row, $this);
                call_user_func_array($this->{$callback_method}['callable'], array(
                    $postdata,
                    $this->primary_val,
                    $this
                ));
                $this->result_row = $postdata->toArray();
            }
        }

        $this->_set_field_names();

        /**
         * conditions process
         */
        if ($this->condition) {
            foreach ($this->condition as $params) {
                if (! isset($params['mode'][$mode]))
                    continue;

                $params['value'] = $this->replace_text_variables($params['value'], $this->result_row);
                if (isset($this->result_row[$params['field']]) && $this->_compare($this->result_row[$params['field']], $params['operator'], $params['value'])) {
                    if (is_array($params['method']) && is_callable($params['method'])) {
                        call_user_func_array($params['method'], $params['params']);
                    } elseif (is_callable(array(
                        $this,
                        $params['method']
                    ))) {
                        $this->condition_backup($params['method']);
                        call_user_func_array(array(
                            $this,
                            $params['method']
                        ), $params['params']);
                    } elseif (is_callable($params['method'])) {
                        call_user_func_array($params['method'], $params['params']);
                    }
                }
            }
        }
        /**
         * hidden fields pass_var_process *
         */
        if ($mode == 'edit' && isset($this->pass_var['edit'])) {
            $data = array();
            foreach ($this->result_row as $key => $val) {
                if (! isset($this->fields[$key])) {

                    foreach ($this->pass_var['edit'] as $pkey => $param) {
                        $data[$key] = $val;
                    }
                }
            }
            if ($data)
                $this->pass_var['edit'][$pkey]['tmp_value'] = $this->replace_text_variables($param['value'], $data);
        }

        return $this->_render_details($mode);
    }


    /**
     *
     * @author Ariel Canal
     *         Se um campo está 'disabled' ou 'readonly', a função ignora ele
     *         ao montar a SQL, mesmo que
     *         haja um pass_var[create] para este campo.
     *         Correção: a função só ignorará o campo, se não houver
     *         pass_var[create] nele.
     *        
     *         Corrigido a inserção do fk_relation quando há o atributo add_data e mais de um campo para a mesma tabela
     */
    /**
     * main insert constructor
     */
    protected function _insert($postdata, $no_processing = false, $no_processing_fields = array())
    {
        if (! $postdata) {
            // Dados de postagem ausentes
            return Services::response()->setStatusCode(400)->setBody(lang('cCrud.postdata_empty'));
        }
        $set = array();
        $db = $this->model->db;
        $fields = array_merge($this->fields, $this->hidden_fields);
        foreach ($postdata as $key => $val) {
            if (isset($fields[$key]) && ! isset($this->locked_fields[$key]) && ! isset($this->custom_fields[$key]) && ((! isset($this->disabled[$key]['create']) && ! isset($this->readonly[$key]['create'])) || isset($this->pass_var['create'][$key]))) {
                if (isset($this->field_type[$key])) {
                    switch ($this->field_type[$key]) {
                        case 'password':
                            if (trim($val) == '') {
                                continue 2;
                            } elseif ($this->defaults[$key]) {
                                $val = hash($this->defaults[$key], $val);
                            }
                            break;
                        case 'fk_relation': //
                            continue 2;
                            break;
                    }
                }

                // Armazena o valor sem escape; o Query Builder cuidará do bind
                $set[$fields[$key]['table']][$fields[$key]['field']] = $val;

                /*
                 * if (is_array($val))
                 * {
                 * $set[$fields[$key]['table']]['`' . $fields[$key]['field'] .
                 * '`'] = $db->escape(implode(',', $val), false, $this->
                 * field_type[$key], $this->field_null[$key],
                 * isset($this->bit_field[$key]));
                 * }
                 * elseif (isset($this->point_field[$key]))
                 * {
                 * $set[$fields[$key]['table']]['`' . $fields[$key]['field'] .
                 * '`'] = 'Point(' . $db->escape($val, true, 'point', $this->
                 * field_null[$key], isset($this->bit_field[$key])) . ')';
                 * }
                 * elseif (isset($this->float_field[$key]))
                 * {
                 * }
                 * elseif (isset($this->float_field[$key]))
                 * {
                 * }
                 * else
                 * $set[$fields[$key]['table']]['`' . $fields[$key]['field'] .
                 * '`'] = ((isset($this->no_quotes[$key]) && isset($this->
                 * pass_var['create'][$key])) ? $db->escape($val, true) :
                 * $db->escape($val, false, $this->field_type[$key], $this->
                 * field_null[$key], isset($this->bit_field[$key])));
                 */
            } elseif ($no_processing) {
                /*
                 * $set[$no_processing_fields[$key]['table']]['`' .
                 * $no_processing_fields[$key]['field'] . '`'] =
                 * ((isset($this->no_quotes[$key]) &&
                 * isset($this->pass_var['create'][$key])) ? $db->escape($val,
                 * true) : $db->escape($val, false, $this->field_type[$key],
                 * $this->
                 * field_null[$key], isset($this->bit_field[$key])));
                 */
                $set[$no_processing_fields[$key]['table']][$no_processing_fields[$key]['field']] = $val;
            }
        }
        // $keys = array_keys($set[$this->table]);
        if (! $set) {
            // Nenhum dado para inserir
            return Services::response()->setStatusCode(422)->setBody(lang('cCrud.nothing_to_insert'));
        }
        if (! $this->primary_ai && ! isset($postdata[$this->table . '.' . $this->primary_key])) {
            // Registro sem valor primário
            return Services::response()->setStatusCode(400)->setBody(lang('cCrud.no_primary_value'));
        }
        // Inserção utilizando Query Builder
        $db->table($this->table)->insert($set[$this->table]);
        if ($this->primary_ai) {
            $ins_id = $db->insertID();
            $set[$this->table][$this->primary_key] = $ins_id;
            $postdata[$this->table . '.' . $this->primary_key] = $ins_id;
        } else {
            $ins_id = $postdata[$this->table . '.' . $this->primary_key];
        }
        // Inserção das tabelas vinculadas por join
        if ($this->join) {
            foreach ($this->join as $alias => $param) {
                $set[$alias][$param['join_field']] = $set[$param['table']][$param['field']];
                if (! $param['not_insert']) {
                    $db->table($param['join_table'])->insert($set[$alias]);
                }
            }
        }

        // Inserção das relações de chave estrangeira
        if ($this->fk_relation) {
            foreach ($this->fk_relation as $fk) {
                $field = $fk['table'] . '.' . $fk['field'];
                if (array_key_exists($fk['alias'], $postdata) && array_key_exists($field, $postdata)) {
                    $builder = $db->table($fk['fk_table']);
                    $builder->where($fk['in_fk_field'], $postdata[$field]);
                    if (count($fk['add_data'])) {
                        foreach ($fk['add_data'] as $k => $v) {
                            $builder->where($k, $v);
                        }
                    }
                    $builder->delete();

                    $fkids = $this->parse_comma_separated($postdata[$fk['alias']]);
                    if ($fkids) {
                        $batch = array();
                        foreach ($fkids as $fkid) {
                            $row = array();
                            if ($fk['add_data']) {
                                foreach ($fk['add_data'] as $add_key => $add_val) {
                                    $row[$add_key] = $add_val;
                                }
                            }
                            $row[$fk['in_fk_field']] = $postdata[$field];
                            $row[$fk['out_fk_field']] = $fkid;
                            $batch[] = $row;
                        }
                        if ($batch) {
                            $db->table($fk['fk_table'])->insertBatch($batch);
                        }
                    }
                }
            }
        }

        unset($set, $postdata);
        return $ins_id;
    }

    /**
     *
     * @author Ariel Canal
     *         Se um campo está 'disabled' ou 'readonly', a função ignora ele
     *         ao montar a SQL, mesmo que
     *         haja um pass_var[create] para este campo.
     *         Correção: a função só ignorará o campo, se não houver
     *         pass_var[create] nele.
     *        
     *         Compatibilizado com join_relation
     *         main update constructor
     */
    protected function _update($postdata, $primary)
    {
        if (! $postdata) {
            // Dados de postagem ausentes
            return Services::response()->setStatusCode(400)->setBody(lang('cCrud.postdata_empty'));
        }
        $res = false;
        $set = array();
        $setStrings = array(); // Usado para registrar alterações
        $db = $this->model->db;
        $fields = array_merge($this->fields, $this->hidden_fields);
        foreach ($postdata as $key => $val) {
            if (isset($fields[$key]) && ! isset($this->locked_fields[$key]) && ! isset($this->custom_fields[$key]) && ((! isset($this->disabled[$key]['edit']) && ! isset($this->readonly[$key]['edit'])) || isset($this->pass_var['edit'][$key]))) {
                if (isset($this->field_type[$key])) {
                    switch ($this->field_type[$key]) {
                        case 'password':
                            if (trim($val) == '') {
                                continue 2;
                            } elseif ($this->defaults[$key]) {
                                $val = hash($this->defaults[$key], $val);
                            }
                            break;
                        case 'fk_relation': //
                            continue 2;
                            break;
                    }
                }
                /*
                 * if (is_array($val))
                 * {
                 * $set[] = '`' . $fields[$key]['table'] . '`.`' .
                 * $fields[$key]['field'] . '` = ' . $db->escape(implode(',',
                 * $val), false,
                 * $this->field_type[$key], $this->field_null[$key],
                 * isset($this->bit_field[$key]));
                 * }
                 * elseif (isset($this->point_field[$key]) && trim($val))
                 * {
                 * $set[] = '`' . $fields[$key]['table'] . '`.`' .
                 * $fields[$key]['field'] . '` = Point(' . $db->escape($val,
                 * true, 'point',
                 * $this->field_null[$key], isset($this->bit_field[$key])) .
                 * ')';
                 * }
                 * else
                 * $set[] = '`' . $fields[$key]['table'] . '`.`' .
                 * $fields[$key]['field'] . '` = ' .
                 * ((isset($this->no_quotes[$key]) &&
                 * isset($this->pass_var['edit'][$key])) ? $db->escape($val,
                 * true) : $db->escape(trim($val), false,
                 * $this->field_type[$key],
                 * $this->field_null[$key], isset($this->bit_field[$key])));
                 */
                $set[$fields[$key]['table']][$fields[$key]['field']] = $val;
                $setStrings[] = '`' . $fields[$key]['table'] . '`.`' . $fields[$key]['field'] . '` = ' . $db->escape($val);
            }
        }
        if (! $set) {
            // Nenhum dado para atualizar
            return Services::response()->setStatusCode(422)->setBody(lang('cCrud.nothing_to_update'));
        }
        $this->apply_record_changes($setStrings);
        if (! $this->join && ! $this->join_relation) {
            $res = $db->table($this->table)->update($set[$this->table], [$this->primary_key => $primary]);
        } else {
            $builder = $db->table($this->table);
            foreach ($this->join as $alias => $param) {
                $builder->join($param['join_table'] . ' ' . $alias, "{$param['table']}.{$param['field']} = {$alias}.{$param['join_field']} {$param['additional_cond']}", 'inner');
            }
            if (count($this->join_relation)) {
                foreach ($this->join_relation as $field => $params) {
                    if (isset($this->relation[$field])) {
                        $r_params = $this->relation[$field];
                        $builder->join($r_params['rel_tbl'], "$field = {$r_params['rel_tbl']}.{$r_params['rel_field']}", 'inner');
                    }
                }
            }
            foreach ($set as $table => $fieldsSet) {
                foreach ($fieldsSet as $field => $value) {
                    $builder->set($table . '.' . $field, $value);
                }
            }
            $builder->where($this->table . '.' . $this->primary_key, $primary);
            $res = $builder->update();
        }
        if (isset($postdata[$this->table . '.' . $this->primary_key]) && $res)
            $primary = $postdata[$this->table . '.' . $this->primary_key];
        else {
            $postdata[$this->table . '.' . $this->primary_key] = $primary;
        }

        if ($this->fk_relation) {
            foreach ($this->fk_relation as $fk) {
                $field = $fk['table'] . '.' . $fk['field'];
                if (array_key_exists($fk['alias'], $postdata) && array_key_exists($field, $postdata)) {
                    $builder = $db->table($fk['fk_table']);
                    $builder->where($fk['in_fk_field'], $postdata[$field]);
                    if ($fk['add_data']) {
                        foreach ($fk['add_data'] as $add_key => $add_val) {
                            $builder->where($add_key, $add_val);
                        }
                    }
                    $builder->delete();

                    $fkids = $this->parse_comma_separated($postdata[$fk['alias']]);
                    if ($fkids) {
                        $batch = array();
                        foreach ($fkids as $fkid) {
                            $row = array();
                            if ($fk['add_data']) {
                                foreach ($fk['add_data'] as $add_key => $add_val) {
                                    $row[$add_key] = $add_val;
                                }
                            }
                            $row[$fk['in_fk_field']] = $postdata[$field];
                            $row[$fk['out_fk_field']] = $fkid;
                            $batch[] = $row;
                        }
                        if ($batch) {
                            $db->table($fk['fk_table'])->insertBatch($batch);
                        }
                    }
                }
            }
        }

        unset($set, $postdata);
        return $primary;
    }

    /**
     * main delete
     */
    protected function _remove()
    {
        $del = false;
        if ($this->table_ro)
            // Operação de remoção não permitida
            return Services::response()->setStatusCode(403)->setBody(lang('cCrud.forbidden'));
        if ($this->replace_remove) {
            $path = $this->check_file($this->replace_remove['path'], 'replace_remove');
            include_once ($path);
            if (is_callable($this->replace_remove['callable'])) {
                $this->primary_val = call_user_func_array($this->replace_remove['callable'], array(
                    $this->primary_val,
                    $this
                ));
            }
        } else {
            // remove case
            $db = $this->model->db;
            $del_row = array();
            $del = false;
            $fields = array();
            $this->find_details_text_variables();
            if ($this->direct_select_tags) // tags for unset condition
            {
                foreach ($this->direct_select_tags as $key => $dsf) {
                    if (! $dsf['table'] == $this->table || (isset($this->join[$dsf['table']]) && $this->join[$dsf['table']]['not_insert'] != true)) {
                        $fields[$key] = "`{$dsf['table']}`.`{$dsf['field']}` AS `{$key}`";
                    }
                }
            }
            if (in_array('image', $this->field_type) or in_array('file', $this->field_type) or in_array('fk_relation', $this->field_type)) // images
                                                                                                                                            // &&
                                                                                                                                            // fk
            {
                foreach ($this->field_type as $key => $type) {
                    switch ($type) {
                        case 'image':
                        case 'file':
                            $tmp = explode('.', $key);
                            $fields[$key] = '`' . $tmp[0] . '`.`' . $tmp[1] . '` AS `' . $key . '`';
                            break;
                        case 'fk_relation':
                            $fields[$this->fk_relation[$key]['table'] . '.' . $this->fk_relation[$key]['field']] = '`' . $this->fk_relation[$key]['table'] . '`.`' . $this->fk_relation[$key]['field'] . '` AS `' . $this->fk_relation[$key]['table'] . '.' . $this->fk_relation[$key]['field'] . '`';
                            break;
                    }
                    if ($type == 'image' or $type == 'file') {
                        $tmp = explode('.', $key);
                        $fields[$key] = "`{$tmp[0]}`.`{$tmp[1]}` AS `{$key}`";
                    }
                }
            }
            if (! $this->join) {
                if ($fields) {
                    $query = $db->query('SELECT ' . implode(',', $fields) . " FROM `{$this->table}` WHERE `{$this->primary_key}` = " . $db->escape($this->primary_val) . ' LIMIT 1');
                    $del_row = $query->getRowArray();
                }
                    if (! $this->is_remove($del_row)) {
                        // Remoção não autorizada
                        return Services::response()->setStatusCode(403)->setBody(lang('cCrud.forbidden'));
                    }
                $del = $db->query("DELETE FROM `{$this->table}` WHERE `{$this->primary_key}` = " . $db->escape($this->primary_val) . " LIMIT 1");
            } else {
                $tables = array(
                    '`' . $this->table . '`'
                );
                $joins = array();
                foreach ($this->join as $alias => $param) {
                    if (! $param['not_insert']) {
                        $tables[] = '`' . $alias . '`';
                        $joins[] = "INNER JOIN `{$param['join_table']}` AS `{$alias}`ON `{$param['table']}`.`{$param['field']}` = `{$alias}`.`{$param['join_field']}` " . $param['additional_cond'];
                    }
                }
                if ($fields) {
                    $query = $db->query('SELECT ' . implode(',', $fields) . " FROM `{$this->table}` AS `{$this->table}` " . implode(' ', $joins) . " WHERE `{$this->table}`.`{$this->primary_key}` = " . $db->escape($this->primary_val));
                    $del_row = $query->getRowArray();
                }
                    if (! $this->is_remove($del_row)) {
                        // Remoção não autorizada
                        return Services::response()->setStatusCode(403)->setBody(lang('cCrud.forbidden'));
                    }
                $del = $db->query("DELETE " . implode(',', $tables) . " FROM `{$this->table}` AS `{$this->table}` " . implode(' ', $joins) . " WHERE `{$this->table}`.`{$this->primary_key}` = " . $db->escape($this->primary_val));
            }
            if ($del_row) {
                foreach ($del_row as $key => $val) {
                    if ($val && isset($this->upload_config[$key]) && ! isset($this->upload_config[$key]['blob'])) {
                        $this->remove_file($val, $key);
                    }
                }
            }
            if ($this->fk_relation) // removing FK relations
            {
                foreach ($this->fk_relation as $fk) {
                    $field = $fk['table'] . '.' . $fk['field'];
                    if (array_key_exists($field, $del_row)) {
                        $in_val = $db->escape($del_row[$field]);
                        $db->query('DELETE FROM `' . $fk['fk_table'] . '` WHERE `' . $fk['in_fk_field'] . '` = ' . $in_val);
                    }
                }
            }
            // end of remove case
        }
        $this->task = 'list';
        $this->primary_val = null;
        return $del;
    }

    /*
     * @author Ariel Canal
     * Compatibilização com a funcionalidade auto_insert para join_relations
     */
    protected function check_postdata($postdata, $primary)
    {
        $mode = $primary ? 'edit' : 'create';
        foreach ($postdata as $key => $val) {
            if (isset($this->disabled[$key][$mode]) && ! isset($this->readonly[$key][$mode])) {
                unset($postdata[$key]);
                continue;
            }
            if (isset($this->field_type[$key])) {
                switch ($this->field_type[$key]) {
                    case 'password':
                        if (trim($val) == '') {
                            unset($postdata[$key]);
                        }
                        break;
                    case 'datetime':
                        if ($val !== '') {
                            $timeObj = is_numeric($val)
                                ? Time::createFromFormat('U', (string) $val)
                                : Time::createFromFormat('d/m/Y H:i', $val);
                            $postdata[$key] = $timeObj->toDateTimeString();
                        } else {
                            $postdata[$key] = $this->field_null[$key] ? null : '0000-00-00 00:00:00';
                        }
                        break;
                    case 'date':
                        if ($val !== '') {
                            $timeObj = is_numeric($val)
                                ? Time::createFromFormat('U', (string) $val)
                                : Time::createFromFormat('d/m/Y', $val);
                            $postdata[$key] = $timeObj->toDateString();
                        } else {
                            $postdata[$key] = $this->field_null[$key] ? null : '0000-00-00';
                        }
                        break;
                    case 'time':
                        if ($val !== '') {
                            $timeObj = is_numeric($val)
                                ? Time::createFromFormat('U', (string) $val)
                                : Time::createFromFormat(strlen($val) === 5 ? 'H:i' : 'H:i:s', $val);
                            $postdata[$key] = $timeObj->toTimeString();
                        } else {
                            $postdata[$key] = $this->field_null[$key] ? null : '00:00:00';
                        }
                        break;
                }
            }
        }
        $postdata = $this->join_relation_tag($postdata);
        return $postdata;
    }

    /**
     * save events switcher
     */
    /**
     * FUNÇÃO ALTERADA DO PADRÃO
     *
     * @author Ariel Canal
     *         Função make_upload_process() é chamada apenas uma vez após os
     *         callbacks e durante a atualização das informações, e não só na
     *         inserção.
     */
    protected function _save()
    {
        $postdata = $this->_post('postdata');
        if (! $postdata) {
            // Nenhum dado disponível para salvar
            return Services::response()->setStatusCode(400)->setBody(lang('cCrud.no_data_to_save'));
        }

        $postdata = $this->check_postdata($postdata, $this->primary_val);

        if ($this->inner_value !== false) // is nested
        {
            $field = reset($this->inner_where);
            if (! isset($postdata[$field])) // nested table connection field MUST
                                             // be
                                             // defined
            {
                $fdata = $this->_parse_field_names($field, 'build_select_details');
                $this->hidden_fields = array_merge($this->hidden_fields, $fdata);
                // $this->hidden_fields[$field] = $fdata[0];
                $postdata[$field] = $this->inner_value;
            }
        }
        // Validação dos dados utilizando regras definidas no Model
        if (! $this->model->validate($postdata)) {
            foreach ($this->model->errors() as $field => $error) {
                $this->set_exception_fields($field, $error);
            }
        }
        if ($this->exception) {
            return $this->call_exception($postdata);
        }
        if (! $this->primary_val) {
            if (! $this->is_create || $this->table_ro)
                // Operação de criação não permitida
                return Services::response()->setStatusCode(403)->setBody(lang('cCrud.forbidden'));
            if (isset($this->pass_var['create'])) {
                foreach ($this->pass_var['create'] as $field => $param) {
                    if ($param['eval']) {
                        $param['value'] = eval($param['value']);
                    }
                    $postdata[$field] = $this->replace_text_variables($param['value'], $postdata);
                    $this->hidden_fields[$field] = array(
                        'table' => $param['table'],
                        'field' => $param['field']
                    );
                }
            }

            $pd = new Postdata($postdata, $this);
            $postdata = $pd->toArray();


            if ($this->replace_insert) {
                $path = $this->check_file($this->replace_insert['path'], 'replace_insert');
                include_once ($path);
                if (is_callable($this->replace_insert['callable'])) {
                    $this->primary_val = call_user_func_array($this->replace_insert['callable'], array(
                        $pd,
                        $this
                    ));
                    $postdata = $pd->toArray();
                    if ($this->exception) {
                        return $this->call_exception($postdata);
                    }
                }
            } else {
                $this->primary_val = $this->_insert($postdata);
            }
            // Processa upload apenas após todos os callbacks
            $this->make_upload_process($pd);

            if ($this->send_external_create) {
                if (! $this->send_external_create['where_field'] or $postdata[$this->send_external_create['where_field']] == $this->send_external_create['where_val']) {
                    foreach ($this->send_external_create['data'] as $key => $value) {
                        $this->send_external_create['data'][$key] = $this->replace_text_variables($value, $postdata + array(
                            $this->table . '.' . $this->primary_key => $this->primary_val
                        ));
                    }
                    switch ($this->send_external_create['method']) {
                        case 'include':
                            $path = $this->check_file($this->send_external_create['path'], 'send_external_create');
                            ob_start();
                            extract($this->send_external_create['data']);
                            include ($path);
                            ob_end_clean();
                            break;
                        case 'get':
                        case 'post':
                            $this->send_http_request($this->send_external_create['path'], $this->send_external_create['data'], $this->send_external_create['method'], false);
                            break;
                    }
                }
            }
        } else {
            if ($this->table_ro)
                // Operação não permitida em tabela somente leitura
                return Services::response()->setStatusCode(403)->setBody(lang('cCrud.forbidden'));
            $fields = array();
            $row = array();
            $this->find_details_text_variables();
            if ($this->direct_select_tags) {
                foreach ($this->direct_select_tags as $key => $dsf) {
                    $fields[$key] = "`{$dsf['table']}`.`{$dsf['field']}` AS `{$key}`";
                }
            }
            if ($fields) {
                $db = $this->model->db;
                if (! $this->join) {
                    $query = $db->query('SELECT ' . implode(',', $fields) . " FROM `{$this->table}` WHERE `{$this->primary_key}` = " . $db->escape($this->primary_val) . " LIMIT 1");
                    $row = $query->getRowArray();
                } else {
                    $tables = array(
                        '`' . $this->table . '`'
                    );
                    $joins = array();
                    foreach ($this->join as $alias => $param) {
                        $tables[] = '`' . $alias . '`';
                        $joins[] = "INNER JOIN `{$param['join_table']}` AS `{$alias}`
                    ON `{$param['table']}`.`{$param['field']}` = `{$alias}`.`{$param['join_field']}` " . $param['additional_cond'];
                    }
                    $query = $db->query('SELECT ' . implode(',', $fields) . " FROM `{$this->table}` AS `{$this->table}` " . implode(' ', $joins) . " WHERE `{$this->table}`.`{$this->primary_key}` = " . $db->escape($this->primary_val));
                    $row = $query->getRowArray();
                }
            }

            if (! $this->is_edit($row))
                // Edição não autorizada
                return Services::response()->setStatusCode(403)->setBody(lang('cCrud.forbidden'));
            if (isset($this->pass_var['edit'])) {
                foreach ($this->pass_var['edit'] as $field => $param) {
                    if (isset($param['tmp_value'])) {
                        $param['value'] = $param['tmp_value'];
                        unset($this->pass_var['edit'][$field]['tmp_value']);
                    }
                    if ($param['eval']) {
                        $param['value'] = eval($param['value']);
                    }
                    $postdata[$field] = $this->replace_text_variables($param['value'], $postdata);
                    $postdata[$field] = $this->replace_text_variables($param['value'], $row);
                    $this->hidden_fields[$field] = array(
                        'table' => $param['table'],
                        'field' => $param['field']
                    );
                }
            }

            $pd = new PostData($postdata, $this);
            $this->make_upload_process($pd);
            $postdata = $pd->toArray();


            if ($this->replace_update) {
                $path = $this->check_file($this->replace_update['path'], 'replace_update');
                include_once ($path);
                if (is_callable($this->replace_update['callable'])) {
                    $this->primary_val = call_user_func_array($this->replace_update['callable'], array(
                        $pd,
                        $this->primary_val,
                        $this
                    ));
                    $postdata = $pd->toArray();
                    if ($this->exception) {
                        return $this->call_exception($postdata);
                    }
                }
            } else
                $this->primary_val = $this->_update($postdata, $this->primary_val);
            if ($this->send_external_edit) {
                if (! $this->send_external_edit['where_field'] or $postdata[$this->send_external_edit['where_field']] == $this->send_external_edit['where_val']) {
                    foreach ($this->send_external_edit['data'] as $key => $value) {
                        $this->send_external_edit['data'][$key] = $this->replace_text_variables($value, $postdata);
                    }
                    switch ($this->send_external_edit['method']) {
                        case 'include':
                            $path = $this->check_file($this->send_external_edit['path'], 'send_external_edit');
                            ob_start();
                            extract($this->send_external_edit['data']);
                            include ($path);
                            ob_end_clean();
                            break;
                        case 'get':
                        case 'post':
                            $this->send_http_request($this->send_external_edit['path'], $this->send_external_edit['data'], $this->send_external_edit['method'], false);
                            break;
                    }
                }
            }
        }
        unset($postdata);
        $this->previous_task = $this->task;
        $this->task = $this->after;
        $this->after = null;
        return $this->_run_task();
    }

    protected function call_exception($postdata = array())
    {
        $this->cancel_file_saving = true;
        switch ($this->task) {
            case 'upload':
                switch ($this->_post('type')) {
                    case 'image':
                        return $this->create_image($this->_post('field'), '') . $this->render_messages();
                        break;
                    case 'file':
                        return $this->create_file($this->_post('field'), '') . $this->render_messages();
                        break;
                    default:
                        // Erro durante o envio de arquivo
                        return Services::response()->setStatusCode(500)->setBody(lang('cCrud.upload_error'));
                        break;
                }
                break;
        }

        $this->task = $this->before;
        switch ($this->before) {
            case 'create':
                return $this->_create($postdata);
                break;
            case 'edit':
            case 'view':
                return $this->_entry($this->before, $postdata);
                break;
            case 'upload':
                break;
            default:
                return $this->_list();
                break;
        }
    }

    /**
     * FUNÇÃO ALTERADA DO PADRÃO
     *
     * @author Ariel Canal
     *         Correção feita na substituição do conteúdo de $pd.
     */
    protected function make_upload_process($pd)
    {
        if ($this->upload_config) {
            foreach ($this->upload_config as $key => $opts) {
                if (isset($opts['blob']) && $opts['blob'] && $pd->get($key)) {
                    if ($pd->get($key) == 'blob-storage') {
                        $pd->del($key);
                        continue;
                    } else {
                        $folder = $this->upload_folder[$key];
                        $path = $folder . '/' . $pd->get($key);
                        if (is_file($path)) {
                            $pd->set($key, file_get_contents($path));
                            unlink($path);
                        }
                    }
                }
            }
        }
    }

    public function set_exception_fields($fields = '', $message = '', $type = 'error')
    {
        return $this->set_notify($message, $type, true, $fields);
    }

    public function set_notify($message = '', $type = 'message', $exception = false, $fields = '')
    {
        if ($message) {
            $this->messages[] = [
                'type' => $type,
                'text' => $this->lang($message),
                'exception' => $exception
            ];
        }
        if ($exception) {
            $this->exception = true;
        }
        if ($fields) {
            $fdata = $this->_parse_field_names($fields, 'set_exception_fields');
            foreach ($fdata as $key => $fitem) {
                $fitem['exception'] = $message;
                $this->exception_fields[$key] = $fitem;
            }
        }

        return $this;
    }

    protected function render_messages()
    {
        $out = '';
        if (count($this->messages)) {
            foreach ($this->messages as $message) {
                $tag = [
                    'tag' => 'input',
                    'type' => 'hidden',
                    'class' => 'cCrud-callback-message',
                    'name' => $message['type'],
                    'value' => $message['text'],
                    'data-exception' => $message['exception']
                ];
                $out .= $this->single_tag($tag);
            }
        }
        return $out;
    }

    /**
     *
     * @author Ariel Canal
     *         Implementação de totalizadores
     */
    /**
     * grid processing
     */
    protected function _list($render = true)
    {
        if (! $this->is_list) {
            // Acesso proibido à listagem
            return Services::response()->setStatusCode(403)->setBody(lang('cCrud.forbidden'));
        }
        $this->_alphabetical();
        /*
         * if (!$this->search_columns)
         * {
         * $this->search_columns = $this->columns;
         * }
         */
        if (! $this->is_inner)
            $this->where_pri = array();
        $select = $this->_build_select_list();
        $table_join = $this->_build_table_join();
        $where = $this->_build_where();
        $order_by = $this->_build_order_by();
        $group_by = $this->_build_group_by();
        $sum_tmp = array();
        if ($this->sum) {
            foreach ($this->sum as $field => $param) {
                if (isset($this->subselect[$field]))
                    $sum_tmp[$field] = 'SUM(' . $this->subselect_where($field) . ') AS `' . $field . '`';
                else
                    $sum_tmp[$field] = 'SUM(`' . $param['table'] . '`.`' . $param['column'] . '`) AS `' . $field . '`';
            }
        }
        $sum = $sum_tmp ? ', ' . implode(', ', $sum_tmp) : '';
        $db = $this->model->db;
        // $db->query("SELECT COUNT(`{$this->table}`.`{$this->primary_key}`) AS
        // `count` {$sum} \r\n FROM `{$this->table}`\r\n {$table_join}\r\n
        // {$where}");
        $query = $db->query("SELECT COUNT(*) AS `count` {$sum} \r\n FROM `{$this->table}`\r\n {$table_join}\r\n {$where}\r\n {$group_by}");
        $this->sum_row = $query->getRowArray();
        $this->result_total = $this->sum_row['count'];
        if (count($this->totalizers)) {
            foreach ($this->totalizers as $name => $totalizer_config) {
                $where_tot = $this->_build_where(false, $name);
                if ($totalizer_config['select'])
                    $select_tot = $totalizer_config['select'];
                else
                    $select_tot = 'COUNT(*)';
                $query = $db->query("SELECT " . $select_tot . " AS `count` \r\n FROM `{$this->table}`\r\n {$table_join}\r\n {$where_tot}");
                $this->totalizers[$name]['total'] = $query->getRowArray();
                $this->totalizers[$name]['total'] = $this->totalizers[$name]['total']['count'];
            }
        }
        $limit = $this->_build_limit($this->result_total);
        // var_dump("SELECT {$select} \r\n FROM `{$this->table}`\r\n {$table_join}\r\n {$where}\r\n {$group_by}\r\n {$order_by}\r\n {$limit}");
        $query = $db->query("SELECT {$select} \r\n FROM `{$this->table}`\r\n {$table_join}\r\n {$where}\r\n {$group_by}\r\n {$order_by}\r\n {$limit}");
        $this->result_list = $query->getResultArray();

        if ($this->before_list) {
            $path = $this->check_file($this->before_list['path'], 'before_list');
            include_once ($path);
            if (is_callable($this->before_list['callable'])) {
                call_user_func_array($this->before_list['callable'], array(
                    $this->result_list,
                    $this
                ));
            }
        }

        $this->_set_column_names();
        if ($render)
            return $this->_render_list();
    }

    /**
     * defines primary condition for internal usage
     */
    protected function where_pri($fields = false, $where_val = false, $glue = 'AND', $index = false)
    {
        if ($fields !== false && $where_val !== false) {
            $fdata = $this->_parse_field_names($fields, 'where_pri');
            foreach ($fdata as $fitem) {
                if ($index) {
                    $this->where_pri[$index] = array(
                        'table' => $fitem['table'],
                        'field' => $fitem['field'],
                        'value' => isset($fitem['value']) ? $fitem['value'] : $where_val,
                        'glue' => $glue
                    );
                } else {
                    $this->where_pri[] = array(
                        'table' => $fitem['table'],
                        'field' => $fitem['field'],
                        'value' => isset($fitem['value']) ? $fitem['value'] : $where_val,
                        'glue' => $glue
                    );
                }
            }
            unset($fields, $fdata);
        } elseif ($fields) {
            if ($index) {
                $this->where_pri[$index] = array(
                    'custom' => $fields,
                    'glue' => $glue
                );
            } else {
                $this->where_pri[] = array(
                    'custom' => $fields,
                    'glue' => $glue
                );
            }
            unset($where_val);
        }
        return $this;
    }

    /**
     * 'select' subquery for grid view
     */
    protected function _build_select_list($csv = false)
    {
        $this->find_grid_text_variables();

        if ($this->model->db === null) {
            return '';
        }

        $db = $this->model->db;
        $columns = array();

        // $subselect_before = $this->subselect_before;
        foreach ($this->columns as $field_index => $val) {

            if ($val) {
                // $field_index = $key;
                if (isset($this->subselect[$field_index])) {
                    $columns[] = $this->subselect_query($field_index);
                } elseif (isset($this->point_field[$field_index])) {
                    $columns[] = 'CONCAT(X(`' . $val['table'] . '`.`' . $val['field'] . '`),\',\',Y(`' . $val['table'] . '`.`' . $val['field'] . '`)) AS `' . $val['table'] . '.' . $val['field'] . '`' . "\r\n";
                } elseif (isset($this->relation[$field_index])) {
                    $join = '';
                    if (is_array($this->relation[$field_index]['join'])) {
                        foreach ($this->relation[$field_index]['join'] as $params) {
                            if (! is_null($params['lfield']) && ! is_null($params['table']) && ! is_null($params['rfield'])) {
                                $join .= 'INNER JOIN `' . $params['table'] . '` AS '.md5($params['table']).' ON `' . $this->relation[$field_index]['rel_tbl'] . '`.`' . $params['lfield'] . '` = `' . md5($params['table']) . '`.`' . $params['rfield'] . '` ';
                            }
                        }
                    }
                    if (is_array($this->relation[$field_index]['rel_name'])) {
                        $tmp_fields = array();

                        foreach ($this->relation[$field_index]['rel_name'] as $tmp) {
                            $tmp_fields[] = "`{$tmp}` \r\n";
                        }
                        if ($this->relation[$field_index]) {
                            $where = "FIND_IN_SET(`{$this->relation[$field_index]['rel_tbl']}`.`{$this->relation[$field_index]['rel_field']}`,`{$this->relation[$field_index]['table']}`.`{$this->relation[$field_index]['field']}`)";
                        } else {
                            $where = "`{$this->relation[$field_index]['rel_tbl']}`.`{$this->relation[$field_index]['rel_field']}` = `{$this->relation[$field_index]['table']}`.`{$this->relation[$field_index]['field']}`";
                        }
                        $columns[] = "(SELECT GROUP_CONCAT(DISTINCT (CONCAT_WS('{$this->relation[$field_index]['rel_separator']}'," . implode(',', $tmp_fields) . ")) SEPARATOR ', ')
                            FROM `{$this->relation[$field_index]['rel_tbl']}` {$join} 
                            WHERE {$where})
                            AS `rel.{$val['table']}.{$val['field']}`, \r\n `{$val['table']}`.`{$val['field']}` AS `{$val['table']}.{$val['field']}` \r\n";
                    } elseif ($this->relation[$field_index]['multi']) {
                        $columns[] = "(SELECT GROUP_CONCAT(DISTINCT `{$this->relation[$field_index]['rel_name']}` SEPARATOR ', ')
                        FROM `{$this->relation[$field_index]['rel_tbl']}`  {$join} WHERE
                        FIND_IN_SET(`{$this->relation[$field_index]['rel_tbl']}`.`{$this->relation[$field_index]['rel_field']}`,`{$this->relation[$field_index]['table']}`.`{$this->relation[$field_index]['field']}`)
                        ORDER BY `{$this->relation[$field_index]['rel_name']}` ASC)
                         AS `rel.{$val['table']}.{$val['field']}`, \r\n `{$val['table']}`.`{$val['field']}` AS `{$val['table']}.{$val['field']}` \r\n";
                    } else {
                        $columns[] = "(SELECT `{$this->relation[$field_index]['rel_alias']}`.`{$this->relation[$field_index]['rel_name']}`
                            FROM `{$this->relation[$field_index]['rel_tbl']}` AS `{$this->relation[$field_index]['rel_alias']}`  {$join}
                            WHERE `{$this->relation[$field_index]['rel_alias']}`.`{$this->relation[$field_index]['rel_field']}` = `{$this->relation[$field_index]['table']}`.`{$this->relation[$field_index]['field']}`
                            LIMIT 1)
                            AS `rel.{$val['table']}.{$val['field']}`, \r\n `{$val['table']}`.`{$val['field']}` AS `{$val['table']}.{$val['field']}` \r\n";
                    }
                } //
                elseif (isset($this->fk_relation[$field_index])) {
                    $fk = $this->fk_relation[$field_index];
                    if (is_array($fk['rel_name'])) {
                        foreach ($fk['rel_name'] as $tmp) {
                            $tmp_fields[] = '`' . $fk['rel_tbl'] . '`.`' . $tmp . '`';
                            $rel_name = 'CONCAT_WS(' . $db->escape($fk['rel_separator']) . ',' . implode(',', $tmp_fields) . ')';
                        }
                    } else {
                        $rel_name = '`' . $fk['rel_tbl'] . '`.`' . $fk['rel_name'] . '`';
                    }
                    $columns[] = '(SELECT GROUP_CONCAT(DISTINCT ' . $rel_name . ' SEPARATOR \', \')
                        FROM `' . $fk['rel_tbl'] . '`
						INNER JOIN `' . $fk['fk_table'] . '` ON `' . $fk['fk_table'] . '`.`' . $fk['out_fk_field'] . '` = `' . $fk['rel_tbl'] . '`.`' . $fk['rel_field'] . '` WHERE `' . $fk['fk_table'] . '`.`' . $fk['in_fk_field'] . '` = `' . $fk['table'] . '`.`' . $fk['field'] . '` AND ' . $this->_build_rel_where($field_index) . ')
                         AS `' . $fk['alias'] . '` ' . "\r\n";
                } elseif (isset($this->bit_field[$field_index])) {
                    $columns[] = "CAST(`{$val['table']}`.`{$val['field']}` AS UNSIGNED) AS `{$val['table']}.{$val['field']}` \r\n";
                } else {
                    $columns[] = "`{$val['table']}`.`{$val['field']}` AS `{$val['table']}.{$val['field']}` \r\n";
                }
            }
        }
        if ($this->hidden_columns) {
            foreach ($this->hidden_columns as $field_index => $val) {
                if (isset($this->subselect[$field_index])) {
                    $columns[] = $this->subselect_query($field_index);
                } else {
                    $columns[] = "`{$val['table']}`.`{$val['field']}` AS `{$field_index}` \r\n";
                }
            }
        }

        if (! $this->primary_key) {
            $columns[] = "(0) AS `primary_key` \r\n";
        } else {
            $columns[] = "`{$this->table}`.`{$this->primary_key}` AS `primary_key` \r\n";
        }
        return implode(',', $columns);
    }

    /**
     * creates subselect subquery for grid view
     */
    protected function subselect_query($name)
    {
        if (isset($this->subselect_query[$name])) {
            $sql = $this->subselect_query[$name];
        } else {
            $sql = preg_replace_callback('/\{(.+)\}/Uu', array(
                $this,
                'subselect_callback'
            ), $this->subselect[$name]);
            $this->subselect_query[$name] = $sql;
        }
        return "({$sql}) AS `{$name}`";
    }

    protected function subselect_where($name)
    {
        if (isset($this->subselect_query[$name])) {
            return '(' . $this->subselect_query[$name] . ')';
        } else {
            $this->subselect_query($name);
            return '(' . $this->subselect_query[$name] . ')';
        }
    }

    protected function subselect_callback($matches)
    {
        if (strpos($matches[1], '.')) {
            $tmp = explode('.', $matches[1]);
            if (isset($this->subselect[$this->prefix . $tmp[0] . '.' . $tmp[1]])) {
                return $this->subselect_where($this->prefix . $tmp[0] . '.' . $tmp[1]);
            } else
                return '`' . $this->prefix . $tmp[0] . '`.`' . $tmp[1] . '`';
        } else {
            if (isset($this->subselect[$this->table . '.' . $matches[1]])) {
                return $this->subselect_where($this->table . '.' . $matches[1]);
            } else
                return '`' . $this->table . '`.`' . $matches[1] . '`';
        }
    }

    /**
     * alterado - join_relation
     * 'select' subquery part for edit/details view
     */
    protected function _build_select_details($mode)
    {
        $this->find_details_text_variables();
        $fields = array();
        if ($this->inner_table_instance) { // nested table
            foreach ($this->inner_table_instance as $inst_name => $field) {
                if (! isset($this->fields[$field])) // nested table connection field
                                                     // MUST be extracted from DB,
                                                     // even if not
                                                     // defined
                {
                    $fdata = $this->_parse_field_names($field, 'build_select_details');
                    // $this->hidden_fields[$field] = $fdata[0];
                    $this->hidden_fields = array_merge($this->hidden_fields, $fdata);
                }
            }
        }

        if ($this->fields) {
            foreach ($this->fields as $key => $val) {
                if ($val && ! isset($this->custom_fields[$key])) {
                    if (isset($this->subselect[$key])) {
                        $fields[] = $this->subselect_query($key);
                    } elseif (isset($this->fk_relation[$key])) {
                        $fk = $this->fk_relation[$key];
                        $fields[] = '(SELECT GROUP_CONCAT(DISTINCT `' . $fk['rel_tbl'] . '`.`' . $fk['rel_field'] . '` SEPARATOR \',\')
	                        FROM `' . $fk['rel_tbl'] . '`
							INNER JOIN `' . $fk['fk_table'] . '` ON `' . $fk['fk_table'] . '`.`' . $fk['out_fk_field'] . '` = `' . $fk['rel_tbl'] . '`.`' . $fk['rel_field'] . '` WHERE `' . $fk['fk_table'] . '`.`' . $fk['in_fk_field'] . '` = `' . $fk['table'] . '`.`' . $fk['field'] . '` AND ' . $this->_build_rel_where($key) . ')
	                         AS `' . $fk['alias'] . '` ' . "\r\n";
                    } elseif (isset($this->point_field[$key])) {
                        $fields[] = 'CONCAT(X(`' . $val['table'] . '`.`' . $val['field'] . '`),\',\',Y(`' . $val['table'] . '`.`' . $val['field'] . '`)) AS `' . $val['table'] . '.' . $val['field'] . '`' . "\r\n";
                    } elseif (isset($this->bit_field[$key])) {
                        $fields[] = "CAST(`{$val['table']}`.`{$val['field']}` AS UNSIGNED) AS `$key`";
                    } else {
                        $fields[] = "`{$val['table']}`.`{$val['field']}` AS `$key`";
                    }
                }
            }
        }
        if ($this->hidden_fields) {
            foreach ($this->hidden_fields as $key => $val) {
                if ($val)
                    $fields[] = "`{$val['table']}`.`{$val['field']}` AS `{$key}`";
            }
        }
        if (count($this->join_relation)) {
            foreach ($this->join_relation as $f => $params) {
                $r_params = $this->relation[$f];
                foreach ($params['rel_additional_fields'] as $f => $p) {
                    $fields[] = "`{$p['table']}`.`{$p['field']}` AS `" . $p['table'] . "." . $p['field'] . "`";
                }
            }
        }
        $fields[] = "`{$this->table}`.`{$this->primary_key}` AS `primary_key`";
        return implode(',', $fields);
    }

    /* alterado - join_selection */
    protected function _build_table_join()
    {
        $join = '';
        if (count($this->join)) {
            $join_arr = array();
            foreach ($this->join as $alias => $params) {
                $join_arr[] = "INNER JOIN `{$params['join_table']}` AS `{$alias}`
                ON `{$params['table']}`.`{$params['field']}` = `{$alias}`.`{$params['join_field']}` " . $params['additional_cond'];
            }
        }
        if (count($this->join_relation)) {
            foreach ($this->join_relation as $field => $params) {
                if (isset($this->relation[$field])) {
                    // array('rel_tbl' => $rel_tbl,'rel_alias' => 'alias' . rand(),'rel_field' => $rel_field,'rel_name' => $rel_name,'rel_where' => $rel_where,'rel_separator' => $rel_concat_separator,'order_by' => $order_by,'multi' => $multi,'table' => $fitem['table'],'field' => $fitem['field'],'tree' => $tree,'depend_field' => $depend_field,'depend_on' => $depend_on);
                    $r_params = $this->relation[$field];

                    $join_arr[] = "INNER JOIN `{$r_params['rel_tbl']}` AS `{$r_params['rel_tbl']}`
					ON $field = `{$r_params['rel_tbl']}`.`{$r_params['rel_field']}` ";
                }
            }
        }
        if (isset($join_arr)) {
            $join .= implode(' ', $join_arr);
        }
        return $join;
    }

    /**
     *
     * @author Ariel Canal
     *         As condições definidas pelo controller e pelos filtros só são
     *         consideradas caso a task não seja edit ou view.
     *         Isso evita que o formulário seja renderizado em branco caso, após o
     *         'save', o registro não esteja mais na listagem filtrada pelo
     *         usuário.
     */
    /**
     * builds main where condition for query
     */
    protected function _build_where($build_alphabetical = true, $is_totalizer = false)
    {
        if ($this->model->db === null) {
            return '';
        }

        $db = $this->model->db;
        $where_arr = array();
        $where_arr_pri = array();

        // user defined conditions
        if ($this->where && ! in_array($this->task, array(
            'edit',
            'view'
        ))) {
            foreach ($this->where as $key => $params) {
                if ($where_arr)
                    $where_arr[] = $params['glue'];

                if (! isset($params['custom'])) {
                    $fieldkey = $this->_where_fieldkey($params);
                    if (is_array($params['value'])) {
                        $in_arr = array();
                        foreach ($params['value'] as $in_val) {
                            $in_arr[] = $db->escape($in_val);
                        }
                        if (isset($this->subselect[$fieldkey])) {
                            $where_arr[] = $this->subselect_where($fieldkey) . $this->_cond_from_where_in($params['field']) . '(' . implode(',', $in_arr) . ')';
                        } else {
                            $where_arr[] = $this->_where_field($params) . $this->_cond_from_where_in($params['field']) . '(' . implode(',', $in_arr) . ')';
                        }
                    } else {
                        if (isset($this->subselect[$fieldkey])) {
                            $where_arr[] = $this->subselect_where($fieldkey) . $this->_cond_from_where($params['field']) . $db->escape($params['value']);
                        } elseif (isset($this->point_field[$fieldkey])) {
                            $where_arr[] = 'CONCAT(X(`' . $this->_where_field($params) . '`),\',\',Y(`' . $this->_where_field($params) . '`))' . $this->_cond_from_where($params['field']) . $db->escape($params['value']);
                        } else {
                            $where_arr[] = $this->_where_field($params) . $this->_cond_from_where($params['field']) . $db->escape($params['value']);
                        }
                    }
                } else {
                    $where_arr[] = '(' . $params['custom'] . ')';
                }
            }
        }

        // internal condition
        if ($this->where_pri) {
            foreach ($this->where_pri as $params) {
                if ($where_arr_pri)
                    $where_arr_pri[] = $params['glue'];
                if (isset($params['custom'])) {
                    $where_arr_pri[] = '(' . $params['custom'] . ')';
                } else {
                    $where_arr_pri[] = $this->_where_field($params) . $this->_cond_from_where($params['field']) . $db->escape($params['value']);
                }
            }
        }
        // search condition
        if ($this->search && ($this->task == 'list' or $this->task == 'make_report' or $this->task == 'print' or $this->task == 'csv' or $this->after == 'list')) {
            $search_columns = $this->search_columns ? $this->search_columns : $this->columns;
            if (is_array($this->search_submit) && count($this->search_submit)) {
                for ($i = 1; $i <= $this->search_lines; $i ++) {
                    if (isset($this->search_submit[$i]['phrase']) && $this->search_submit[$i]['phrase'] != "" && isset($this->search_submit[$i]) && is_array($this->search_submit[$i])) {
                        if ($this->search_submit[$i]['column'] && isset($search_columns[$this->search_submit[$i]['column']])) {
                            // if relation
                            if (isset($this->relation[$this->search_submit[$i]['column']])) {
                                $search_arr[] = $this->_build_relation_subwhere($this->search_submit[$i]['column'], $i);
                            } // if fk-relation
                            elseif (isset($this->fk_relation[$this->search_submit[$i]['column']])) {
                                $search_arr[] = $this->_build_fk_relation_subwhere($this->search_submit[$i]['column'], $i);
                            } // search in subselect
                            elseif (isset($this->subselect[$this->search_submit[$i]['column']])) {
                                $search_arr[] = '(' . $this->subselect_query[$this->search_submit[$i]['column']] . ') LIKE ' . $this->escapeLike($this->search_submit[$i]['phrase'], $this->search_pattern);
                            } elseif (isset($this->point_field[$this->search_submit[$i]['column']])) {
                                $fdata = $this->_parse_field_names($this->search_submit[$i]['column'], 'build_where', false, false);
                                $fitem = reset($fdata);
                                $search_arr[] = 'CONCAT(X(`' . $fitem['table'] . '`.`' . $fitem['field'] . '`),\',\',Y(`' . $fitem['table'] . '`.`' . $fitem['field'] . '`))LIKE ' . $this->escapeLike($this->search_submit[$i]['phrase'], $this->search_pattern);
                            } else {
                                $fdata = $this->_parse_field_names($this->search_submit[$i]['column'], 'build_where', false, false);
                                $fitem = reset($fdata);
                                $key = key($fdata);
                                // search via fild types
                                switch ($this->field_type[$this->search_submit[$i]['column']]) {
                                    case 'timestamp':
                                    case 'datetime':
                                    case 'date':
                                    case 'time':
                                        $from = $this->search_submit[$i]['phrase']['from'];
                                        $to = $this->search_submit[$i]['phrase']['to'];

                                        if ($this->field_type[$this->search_submit[$i]['column']] === 'time') {
                                            if ($from) {
                                                $fmt = strlen($from) === 5 ? 'H:i' : 'H:i:s';
                                                $from = Time::createFromFormat($fmt, $from)->toTimeString();
                                            }
                                            if ($to) {
                                                $fmt = strlen($to) === 5 ? 'H:i' : 'H:i:s';
                                                $to = Time::createFromFormat($fmt, $to)->toTimeString();
                                            }
                                        } else {
                                            if ($from) {
                                                $fmt = 'd/m/Y';
                                                if (str_contains($from, ' ')) {
                                                    $timePart = explode(' ', $from)[1];
                                                    $fmt .= strlen($timePart) === 5 ? ' H:i' : ' H:i:s';
                                                }
                                                $fromObj = Time::createFromFormat($fmt, $from);
                                                $from = str_contains($from, ' ')
                                                    ? $fromObj->toDateTimeString()
                                                    : $fromObj->toDateString();
                                            }
                                            if ($to) {
                                                $fmt = 'd/m/Y';
                                                if (str_contains($to, ' ')) {
                                                    $timePart = explode(' ', $to)[1];
                                                    $fmt .= strlen($timePart) === 5 ? ' H:i' : ' H:i:s';
                                                }
                                                $toObj = Time::createFromFormat($fmt, $to);
                                                $to = str_contains($to, ' ')
                                                    ? $toObj->toDateTimeString()
                                                    : $toObj->toDateString();
                                            }
                                        }

                                        if ($from && $to) {
                                            $search_arr[] = '(`' . $fitem['table'] . '`.`' . $fitem['field'] . '` BETWEEN ' . $db->escape($from) . ' AND ' . $db->escape($to) . ')';
                                        } elseif ($from) {
                                            $search_arr[] = '(`' . $fitem['table'] . '`.`' . $fitem['field'] . '` >= ' . $db->escape($from) . ')';
                                        } elseif ($to) {
                                            $search_arr[] = '(`' . $fitem['table'] . '`.`' . $fitem['field'] . '` <= ' . $db->escape($to) . ')';
                                        }
                                        break;
                                    case 'select':
                                    case 'radio':
                                        $search_arr[] = '(`' . $fitem['table'] . '`.`' . $fitem['field'] . '` = ' . $db->escape($this->search_submit[$i]['phrase']) . ')';
                                        break;
                                    /*
                                     * case 'multiselect':
                                     * case 'checkboxes':
                                     * break;
                                     */
                                    case 'bool':
                                        if (isset($this->bit_field[$key])) {
                                            $search_arr[] = 'CAST(`' . $fitem['table'] . '`.`' . $fitem['field'] . '` AS UNSIGNED) = ' . ((int) $this->search_submit[$i]['phrase']);
                                        } else {
                                            $search_arr[] = '(`' . $fitem['table'] . '`.`' . $fitem['field'] . '` = ' . ((int) $this->search_submit[$i]['phrase']) . ')';
                                        }
                                        break;
                                    default:
                                        if (isset($this->point_field[$key])) {
                                            $search_arr[] = 'CONCAT(X(`' . $fitem['table'] . '`.`' . $fitem['field'] . '`),\',\',Y(`' . $fitem['table'] . '`.`' . $fitem['field'] . '`)) LIKE ' . $this->escapeLike($this->search_submit[$i]['phrase'], $this->search_pattern);
                                        } elseif (isset($this->bit_field[$key])) {
                                            $search_arr[] = 'CAST(`' . $fitem['table'] . '`.`' . $fitem['field'] . '` AS UNSIGNED) LIKE ' . $this->escapeLike($this->search_submit[$i]['phrase'], $this->search_pattern);
                                        } else {
                                            $search_arr[] = '(`' . $fitem['table'] . '`.`' . $fitem['field'] . '` LIKE ' . $this->escapeLike($this->search_submit[$i]['phrase'], $this->search_pattern) . ')';
                                        }
                                        break;
                                }
                            }
                        } else if ($this->search_submit[$i]['phrase']) {
                            // multicolumn search
                            // $f_array = array();
                            $or_array = array();
                            // $search_columns = $this->search_columns ?
                            // $this->search_columns : $this->columns;
                            foreach ($search_columns as $key => $fitem) {
                                if (isset($this->relation[$key])) {
                                    $or_array[] = $this->_build_relation_subwhere($key, $i);
                                } elseif (isset($this->fk_relation[$key])) {
                                    $or_array[] = $this->_build_fk_relation_subwhere($key, $i);
                                } elseif (isset($this->subselect[$key])) {
                                    $or_array[] = '(' . $this->subselect_query[$key] . ') LIKE ' . $this->escapeLike($this->search_submit[$i]['phrase'], $this->search_pattern);
                                } elseif ($this->field_type[$key] == 'date' || $this->field_type[$key] == 'datetime' || $this->field_type[$key] == 'timestamp' || $this->field_type[$key] == 'time') {
                                    if (preg_match('/^[0-9\-\:\s]+$/', $this->search_submit[$i]['phrase'])) {
                                        $or_array[] = '`' . $fitem['table'] . '`.`' . $fitem['field'] . '` LIKE ' . $this->escapeLike($this->search_submit[$i]['phrase'], $this->search_pattern);
                                    }
                                } elseif (isset($this->point_field[$key])) {
                                    $or_array[] = 'CONCAT(X(`' . $fitem['table'] . '`.`' . $fitem['field'] . '`),\',\',Y(`' . $fitem['table'] . '`.`' . $fitem['field'] . '`)) LIKE ' . $this->escapeLike($this->search_submit[$i]['phrase'], $this->search_pattern);
                                } elseif (isset($this->bit_field[$key])) {
                                    $or_array[] = 'CAST(`' . $fitem['table'] . '`.`' . $fitem['field'] . '` AS UNSIGNED) LIKE ' . $this->escapeLike($this->search_submit[$i]['phrase'], $this->search_pattern);
                                } else {
                                    // $f_array[] = '`' . $fitem['table'] . '`.`' .
                                    // $fitem['field'] . '`';
                                    $or_array[] = '`' . $fitem['table'] . '`.`' . $fitem['field'] . '` LIKE ' . $this->escapeLike($this->search_submit[$i]['phrase'], $this->search_pattern);
                                }
                            }
                            $where = '(';
                            /*
                             * if ($f_array)
                             * {
                             * $where .= 'CONCAT_WS(\' \',' . implode(',', $f_array) . ')
                             * LIKE ' . $this->escapeLike($this->phrase, $this->
                             * search_pattern);
                             * }
                             * if ($f_array && $or_array)
                             * {
                             * $where .= ' OR ';
                             * }
                             */
                            if ($or_array) {
                                $where .= implode(' OR ', $or_array);
                            }
                            $where .= ')';
                            $search_arr[] = $where;
                        }
                        $search_conditions[] = implode(' AND ', $search_arr);
                        $search_arr = array();
                    }
                }
            }
            if (isset($search_conditions) && count($search_conditions)) {
                if ($where_arr) {
                    $where_arr[] = 'AND';
                }
                $search_operator = 'AND';
                $where_arr[] = '(' . implode(' ' . $search_operator . ' ', $search_conditions) . ')';
            }
        }

        // alphabetical condidion
        if ($build_alphabetical && $this->alphabetical_filter != "" && ! in_array($this->task, array(
            'edit',
            'view'
        ))) {
            if (count($where_arr))
                $where_arr[] = 'AND';
            if ($this->alphabetical_filter == "#")
                $where_arr[] = 'SUBSTRING(`' . $this->table . '`.`' . $this->alphabetical_field . '`,1,1) NOT IN (\'' . implode("','", range('A', 'Z')) . '\')';
            else
                $where_arr[] = 'SUBSTRING(`' . $this->table . '`.`' . $this->alphabetical_field . '`,1,1) = \'' . substr(strtoupper($this->alphabetical_filter), 0, 1) . '\'';
        }

        // custom filters
        if (! in_array($this->task, array(
            'edit',
            'view'
        ))) {
            foreach ($this->custom_filter as $filter_name => $opts) {
                unset($current_label);
                if (isset($this->custom_filter_active[$filter_name])) {
                    $current_label = $this->custom_filter_active[$filter_name];
                    $custom_filter = $this->custom_filter[$filter_name][$current_label];

                    if ($is_totalizer && $filter_name == 'totalizer') {} else {
                        if (is_string($custom_filter)) {
                            $glue = "AND";
                            $cond = $custom_filter;
                        } else if (is_array($custom_filter) && isset($custom_filter['where']) && isset($custom_filter['glue'])) {
                            $glue = $custom_filter['glue'];
                            $cond = $custom_filter['where'];
                        } else if (is_array($custom_filter) && isset($custom_filter['where'])) {
                            $glue = "AND";
                            $cond = $custom_filter['where'];
                        }

                        if ($glue && count($where_arr))
                            $where_arr[] = $glue;
                        if ($cond)
                            $where_arr[] = '(' . $cond . ')';
                    }
                }
            }
        }

        // totalizers
        if ($is_totalizer && isset($this->totalizers[$is_totalizer]) && ! in_array($this->task, array(
            'edit',
            'view'
        ))) {
            $totalizer = $this->totalizers[$is_totalizer]['where'];
            if (is_string($totalizer)) {
                $glue = "AND";
                $cond = $totalizer;
            } else if (is_array($totalizer) && isset($totalizer['where']) && isset($totalizer['glue'])) {
                $glue = $totalizer['glue'];
                $cond = $totalizer['where'];
            } else if (is_array($totalizer) && isset($totalizer['where'])) {
                $glue = "AND";
                $cond = $totalizer['where'];
            }

            if ($glue && count($where_arr))
                $where_arr[] = $glue;
            if ($cond)
                $where_arr[] = '(' . $cond . ')';
        }

        // reports
        if (count($this->report_values)) {
            foreach ($this->parameters as $name => $attr) {
                if (in_array($attr['field'], array_keys($this->custom_fields))) {
                    // continue;
                }
                if (isset($this->report_values[$attr['field']])) {
                    switch ($attr['operator']) {
                        case "=":
                            $w[] = $attr['field'] . " = " . $db->escape($this->report_values[$attr['field']]);
                            break;
                        case "FIND_IN_SET":
                            if (is_string($this->report_values[$attr['field']])) {
                                $this->report_values[$attr['field']] = explode(',', $this->report_values[$attr['field']]);
                            }

                            $values = array();
                            foreach ($this->report_values[$attr['field']] as $k => $v) {
                                $values[] = "FIND_IN_SET(" . $db->escape($v) . "," . $attr['field'] . ")";
                            }
                            $w[] = "(" . implode(' OR ', $values) . ")";

                            break;
                        case "IN":
                            if (is_string($this->report_values[$attr['field']])) {
                                $this->report_values[$attr['field']] = explode(',', $this->report_values[$attr['field']]);
                            }
                            $values = array();
                            foreach ($this->report_values[$attr['field']] as $k => $v) {
                                $values[] = $db->escape($v);
                            }
                            $w[] = $attr['field'] . " IN (" . implode(',', $values) . ")";
                            break;
                        case "between":
                            if (isset($this->report_values[$attr['field'] . '_to'])) {
                                $w[] = $attr['field'] . " BETWEEN " . $db->escape($this->report_values[$attr['field']]) . " AND " . $db->escape($this->report_values[$attr['field'] . '_to']);
                            } else {
                                $w[] = $attr['field'] . " >= " . $db->escape($this->report_values[$attr['field']]);
                            }
                            break;
                        case "_to":
                            if (! isset($this->report_values[$attr['field']])) {
                                $w[] = $attr['field'] . " <= " . $db->escape($this->report_values[$attr['field']]);
                            }
                            break;
                    }
                }
            }
            if (isset($w)) {
                if (count($where_arr)) {
                    $where_arr[] = 'AND';
                }
                $where_arr[] = '(' . implode(' AND ', $w) . ')';
            }
        }
        if (sizeof($where_arr) === 1 && trim($where_arr[0]) == "()")
            $where_arr = false;
        // final part
        if ($where_arr or $where_arr_pri) {
            $return = 'WHERE ' . (($where_arr) ? '(' . implode(' ', $where_arr) . ')' : '') . ($where_arr_pri ? ($where_arr ? ' AND ' : '') . implode(' ', $where_arr_pri) : '');
            // var_dump($return);
            return $return;
        } else {
            return '';
        }
    }

    /**
     * relation values will be searched by displayed name (not by id)
     */
    protected function _build_relation_subwhere($key, $i = 1) // multicolumn name
    {
        $db = $this->model->db;

        if ($key) {
            $rel = $this->relation[$key];
            
            $join = '';
            if (is_array($rel['join'])) {
                foreach ($rel['join'] as $params) {
                    if (! is_null($params['lfield']) && ! is_null($params['table']) && ! is_null($params['rfield'])) {
                        $join .= 'INNER JOIN `' . $params['table'] . '` AS '.md5($params['table']).' ON `' . $rel['rel_tbl'] . '`.`' . $params['lfield'] . '` = `' . md5($params['table']) . '`.`' . $params['rfield'] . '` ';
                    }
                }
            }
            if (is_array($rel['rel_name'])) {
                $tmp_fields = array();
                
                foreach ($rel['rel_name'] as $tmp) {
                    $tmp_fields[] = '`' . $tmp . '`' . "\r\n";
                }
                // multiselect relation
                if ($rel['multi']) {
                    $where = '`' . $rel['rel_tbl'] . '`.`' . $rel['rel_field'] . '` LIKE `' . $rel['table'] . '`.`' . $rel['field'] . '`';
                } else {
                    $where = '`' . $rel['rel_tbl'] . '`.`' . $rel['rel_field'] . '` = `' . $rel['table'] . '`.`' . $rel['field'] . '`';
                }
                $select = "(SELECT GROUP_CONCAT(DISTINCT (CONCAT_WS('{$rel['rel_separator']}'," . implode(',', $tmp_fields) . ")) SEPARATOR ', ')
                            FROM `{$rel['rel_tbl']}` {$join}
                            WHERE {$where})\r\n";
            } // multiselect relation
            elseif ($rel['multi']) {
                $select = "(SELECT GROUP_CONCAT(DISTINCT `{$rel['rel_name']}` SEPARATOR ', ')
                        FROM `{$rel['rel_tbl']}` {$join} WHERE
                        FIND_IN_SET(`{$rel['rel_tbl']}`.`{$rel['rel_field']}`,`{$rel['table']}`.`{$rel['field']}`)
                        ORDER BY `{$rel['rel_name']}` ASC)\r\n";
            } else {
                $select = "(SELECT `{$rel['rel_alias']}`.`{$rel['rel_name']}`
                            FROM `{$rel['rel_tbl']}` AS `{$rel['rel_alias']}` {$join}
                            WHERE `{$rel['rel_alias']}`.`{$rel['rel_field']}` = `{$rel['table']}`.`{$rel['field']}`
                            LIMIT 1) \r\n";
            }
            return "{$select} LIKE " . $this->escapeLike($this->search_submit[$i]['phrase'], $this->search_pattern);
        }
        /*
         * else
         * {
         * $orWhere = array();
         * foreach ($this->relation as $column => $param)
         * {
         * if (is_array($this->relation[$column]['rel_name']))
         * {
         * $tmp_fields = array();
         * foreach ($this->relation[$column]['rel_name'] as $tmp)
         * {
         * $tmp_fields[] = '`' . $tmp . '`' . "\r\n";
         * }
         * // multiselect relation
         * if ($this->relation[$column]['multi'])
         * {
         * $where = '`' . $this->relation[$column]['rel_tbl'] . '`.`' .
         * $this->relation[$column]['rel_field'] . '` LIKE `' . $this->
         * relation[$column]['table'] . '`.`' .
         * $this->relation[$column]['field'] . '`';
         * }
         * else
         * {
         * $where = '`' . $this->relation[$column]['rel_tbl'] . '`.`' .
         * $this->relation[$column]['rel_field'] . '` = `' . $this->
         * relation[$column]['table'] . '`.`' .
         * $this->relation[$column]['field'] . '`';
         * }
         * $select = "(SELECT GROUP_CONCAT(DISTINCT
         * (CONCAT_WS('{$this->relation[$column]['rel_separator']}'," .
         * implode(',', $tmp_fields) .
         * ")) SEPARATOR ', ')
         * FROM `{$this->relation[$column]['rel_tbl']}`
         * WHERE {$where})\r\n";
         * }
         * // multiselect relation
         * elseif ($this->relation[$column]['multi'])
         * {
         * $select = "(SELECT GROUP_CONCAT(DISTINCT
         * `{$this->relation[$column]['rel_name']}` SEPARATOR ', ')
         * FROM `{$this->relation[$column]['rel_tbl']}` WHERE
         * FIND_IN_SET(`{$this->relation[$column]['rel_tbl']}`.`{$this->relation[$column]['rel_field']}`,`{$this->relation[$column]['table']}`.`{$this->relation[$column]['field']}`)
         * ORDER BY `{$this->relation[$column]['rel_name']}` ASC)\r\n";
         * }
         * else
         * {
         * $select = "(SELECT
         * `{$this->relation[$column]['rel_alias']}`.`{$this->relation[$column]['rel_name']}`
         * FROM `{$this->relation[$column]['rel_tbl']}` AS
         * `{$this->relation[$column]['rel_alias']}`
         * WHERE
         * `{$this->relation[$column]['rel_alias']}`.`{$this->relation[$column]['rel_field']}`
         * =
         * `{$this->relation[$column]['table']}`.`{$this->relation[$column]['field']}`
         * LIMIT 1) \r\n";
         * }
         * $orWhere[] = $select . ' LIKE ' . $this->escapeLike($this->phrase,
         * $this->search_pattern);
         * }
         * return implode(' OR ', $orWhere);
         * }
         */
    }

    protected function _build_fk_relation_subwhere($key, $i = 1) // multicolumn name
    {
        $db = $this->model->db;
        $fk = $this->fk_relation[$key];

        if (is_array($fk['rel_name'])) {
            foreach ($fk['rel_name'] as $tmp) {
                $tmp_fields[] = '`' . $fk['rel_tbl'] . '`.`' . $tmp . '`';
                $rel_name = 'CONCAT_WS(' . $db->escape($fk['rel_separator']) . ',' . implode(',', $tmp_fields) . ')';
            }
        } else {
            $rel_name = '`' . $fk['rel_tbl'] . '`.`' . $fk['rel_name'] . '`';
        }
        $select = '(SELECT GROUP_CONCAT(DISTINCT ' . $rel_name . ' SEPARATOR \', \')
            FROM `' . $fk['rel_tbl'] . '`
            INNER JOIN `' . $fk['fk_table'] . '` ON `' . $fk['fk_table'] . '`.`' . $fk['out_fk_field'] . '` = `' . $fk['rel_tbl'] . '`.`' . $fk['rel_field'] . '` WHERE `' . $fk['fk_table'] . '`.`' . $fk['in_fk_field'] . '` = `' . $fk['table'] . '`.`' . $fk['field'] . '` AND ' . $this->_build_rel_where($key) . ')' . "\r\n";
        return $select . ' LIKE ' . $this->escapeLike($this->search_submit[$i]['phrase'], $this->search_pattern);
    }

    protected function _build_rel_where($name)
    {
        $where_arr = array();
        if ($this->fk_relation[$name]['rel_where']) {
            $db = $this->model->db;
            if (is_array($this->fk_relation[$name]['rel_where'])) {
                foreach ($this->fk_relation[$name]['rel_where'] as $field => $val) {
                    $val = preg_replace_callback('/\{(.+)\}/Uu', array(
                        $this,
                        'rel_where_callback'
                    ), $val);
                    $where_arr[] = $this->_field_from_where($field) . $this->_cond_from_where($field) . $db->escape($val);
                }
            } else {
                $where_arr[] = preg_replace_callback('/\{(.+)\}/Uu', array(
                    $this,
                    'rel_where_callback'
                ), $this->fk_relation[$name]['rel_where']);
            }
            return implode(' AND ', $where_arr);
        } else {
            return 1;
        }
    }

    protected function _build_rel_ins_where($name)
    {
        $where_arr = array();
        if ($this->fk_relation[$name]['add_data']) {
            $db = $this->model->db;
            if (is_array($this->fk_relation[$name]['add_data'])) {
                foreach ($this->fk_relation[$name]['add_data'] as $field => $val) {
                    $val = preg_replace_callback('/\{(.+)\}/Uu', array(
                        $this,
                        'rel_where_callback'
                    ), $val);
                    $where_arr[] = $this->_field_from_where($field) . $this->_cond_from_where($field) . $db->escape($val);
                }
            } else {
                $where_arr[] = preg_replace_callback('/\{(.+)\}/Uu', array(
                    $this,
                    'rel_where_callback'
                ), $this->fk_relation[$name]['add_data']);
            }
            return implode(' AND ', $where_arr);
        } else {
            return 1;
        }
    }

    protected function rel_where_callback($matches)
    {
        if (strpos($matches[1], '.')) {
            $tmp = explode('.', $matches[1]);
            return '`' . $this->prefix . $tmp[0] . '`.`' . $tmp[1] . '`';
        } else {
            return '`' . $this->table . '`.`' . $matches[1] . '`';
        }
    }

    /**
     * FUNÇÃO ALTERADA DO PADRÃO
     * compatibilizada com reports
     */
    /**
     * receiving user data
     */
    protected function _receive_post($task = false, $primary = false)
    {
        if (! $this->table_name && ! $this->query)
            $this->table_name = $this->_humanize(mb_substr($this->table, mb_strlen($this->prefix)));
        if ($task) {
            switch ($task) {
                case 'create':
                    $this->task = $task;
                    $this->before = $task;
                    return;
                    break;
                case 'edit':
                case 'view':
                    if ($primary !== false) {
                        $this->task = $task;
                        $this->before = $task;
                        $this->primary_val = $primary;
                        return;
                    }
                    break;
                case 'list':
                    $this->task = $task;
                    return;
                    break;
                case 'report':
                    $this->task = $task;
                    return;
                    break;
            }
        } else {
            $this->task = $this->_post('task', 'list');
        }
        if ($this->is_get) {
            $this->task = $this->_get('task');
            $this->primary_val = $this->_get('primary');
        } else {
            $this->order_column = $this->_post('orderby', false, 'key');
            // var_dump($this->order_column);
            $this->order_direct = $this->_post('order') == 'desc' ? 'desc' : 'asc';
            if ($this->order_column) {
                if (! $this->query)
                    $this->order_column = key($this->_parse_field_names($this->order_column, 'receive_post', false, false));
                if (isset($this->order_by[$this->order_column]))
                    unset($this->order_by[$this->order_column]);
                $this->order_by = array_merge(array(
                    $this->order_column => $this->order_direct
                ), $this->order_by);
            }
            // var_dump($this->order_column);
            $this->is_modal = $this->_post('is_modal', false);

            $request  = Services::request();
            $postData = $request->getPost('cCrud');
            if (is_array($postData) && array_key_exists('search', $postData) && $this->_post('search', $this->search, 'int') === 0) {
                // clicou em limpar busca
                $this->search = $this->_post('search', $this->search, 'int');
                $this->search_submit = array();
            } else if (is_array($postData) && array_key_exists('search', $postData) && $this->_post('search', $this->search, 'int') === 1) {
                // nova busca
                $this->search = $this->_post('search', $this->search, 'int');
                $this->search_submit = $this->_post('search_submit', false, array());
            } else if ($this->search) {} else {
                $this->search = $this->_post('search', $this->search, 'int');
                $this->search_submit = array();
            }
            $this->start = $this->_post('start', 0, 'int');
            $this->limit = $this->_post('limit', ($this->limit ? $this->limit : $this->config->limit));
            $this->after = $this->_post('after');
            $this->primary_val = $this->_post('primary');
            $this->active_tab_id = $this->_post('active_tab_id');

            $cCrud_session = $this->getSession()->get('cCrud_session');

            if (isset($cCrud_session[$this->instance_name]))
                $this->alphabetical_filter = $this->_post('alphabetical', $cCrud_session[$this->instance_name]['alphabetical_filter']);
            if ($this->search && ! $this->_post('alphabetical', false))
                $this->alphabetical_filter = '';

            $filter = $this->_post('filter', false);
            $filter_label = $this->_post('label', false);
            if ($filter && $filter_label) {
                switch ($filter_label) {
                    case "all":
                        unset($this->custom_filter_active[$filter]);
                        break;
                    default:
                        $this->custom_filter_active[$filter] = $filter_label;
                }
            }
        }
    }

    protected function _build_order_by()
    {
        if (count($this->order_by)) {
            $order_arr = array();
            foreach ($this->order_by as $field => $direction) {
                if (! array_key_exists($field, $this->columns)) {
                    continue;
                }
                if ($direction === false) {
                    $order_arr[] = $field;
                } elseif (isset($this->relation[$field])) {
                    $order_arr[] = '`rel.' . $field . '` ' . $direction;
                } elseif (isset($this->subselect[$field]) or isset($this->columns[$field]) or isset($this->no_select[$field]) or ! strpos($field, '.') or isset($this->fk_relation[$field])) {
                    $order_arr[] = '`' . $field . '` ' . $direction;
                } else {
                    $tmp = explode('.', $field);
                    $order_arr[] = '`' . $tmp[0] . '`.`' . $tmp[1] . '` ' . $direction;
                }
            }
            if (count($order_arr)) {
                return 'ORDER BY ' . implode(',', $order_arr);
            } else {
                return '';
            }
        } else {
            /*
             * if (isset($this->columns[$this->table . '.' .
             * $this->primary_key]))
             * {
             * $this->order_by[$this->table . '.' . $this->primary_key] = 'ASC';
             * return "ORDER BY `{$this->table}.{$this->primary_key}` ASC";
             * }
             * else
             * return "ORDER BY `{$this->table}`.`{$this->primary_key}` ASC";
             */
            return '';
        }
    }

    protected function _build_group_by()
    {
        if (count($this->group_by)) {
            $group_arr = array();
            foreach ($this->group_by as $field) {
                if (isset($this->subselect[$field]) or isset($this->no_select[$field]) or ! strpos($field, '.') or isset($this->fk_relation[$field])) {
                    $group_arr[] = '`' . $field . '` ';
                } else {
                    $tmp = explode('.', $field);
                    $group_arr[] = '`' . $tmp[0] . '`.`' . $tmp[1] . '` ';
                }
            }
            return 'GROUP BY ' . implode(',', $group_arr);
        }
        return false;
    }

    protected function _build_limit($total)
    {
        if ($this->limit != 'all') {
            if ($this->start > 0 && $this->start >= $this->result_total) {
                $this->start = $this->result_total > $this->limit ? $this->result_total - $this->limit : 0;
            }
            $this->start = floor($this->start / $this->limit) * $this->limit;
            return "LIMIT {$this->start},{$this->limit}";
        } else {
            $this->start = 0;
            return '';
        }
    }

    /**
     * Recupera informações das colunas das tabelas utilizadas.
     *
     * @return void
     */
    protected function _get_table_info(): void
    {
        $this->table_info = array();

        if ($this->model->db === null) {
            return;
        }

        $db = $this->model->db;
        $query = $db->query("SHOW COLUMNS FROM `{$this->table}`");
        $this->table_info[$this->table] = $query->getResultArray();
        if ($this->join) {
            foreach ($this->join as $alias => $join) {
                $query = $db->query("SHOW COLUMNS FROM `{$join['join_table']}`");
                $this->table_info[$alias] = $query->getResultArray();
            }
        }
        if ($this->join_relation) {
            foreach ($this->join_relation as $item) {
                $query = $db->query("SHOW COLUMNS FROM `{$item['rel_table']}`");
                $this->table_info[$item['rel_table']] = $query->getResultArray();
            }
        }
    }

    protected function _set_field_types($mode = 'create', $all_fields = false)
    {
        if (is_array($this->table_info) && count($this->table_info)) {
            $uni = false;
            $this->primary_ai = false;
            $fields = array();
            // var_dump($this->table_info);
            foreach ($this->table_info as $table => $types) {
                foreach ($types as $row) {
                    $field_index = $table . '.' . $row['Field'];
                    if (! $all_fields) {
                        $fields_object_name = 'fields_' . $mode;
                        $fields_object = $this->$fields_object_name;
                    } else {
                        $fields_object = array();
                    }
                    $this->field_null[$field_index] = $row['Null'] == 'YES' ? true : false;
                    if ($row['Type'] == 'point') {
                        $this->point_field[$field_index] = true;
                    }

                    if ($row['Key'] == 'PRI' or $row['Key'] == 'UNI') {
                        $this->unique[$field_index] = true;
                        if ($table == $this->table && ! $uni) {
                            $uni = $row['Field'];
                        }
                    }

                    if ($row['Key'] == 'PRI' && $row['Extra'] == 'auto_increment' && $mode != "report") {
                        if ($table == $this->table) {
                            $this->primary_ai = true;
                            if (! $this->primary_key) {
                                $this->primary_key = $row['Field'];
                            }
                        }

                        if (((! $this->show_primary_ai_column && $mode == 'list') or (! $this->show_primary_ai_field && $mode != 'list')) && ! isset($fields_object[$field_index])) {
                            if (! isset($this->field_type[$field_index])) {
                                $this->_define_field_type($row, $field_index);
                            }
                            continue;
                        } else {
                            $this->disabled[$field_index] = $this->parse_mode(false);
                        }
                    }

                    if ($this->join && isset($this->join[$table]) && $this->join[$table]['join_field'] == $row['Field']) {
                        if (! isset($this->field_type[$field_index])) {
                            $this->_define_field_type($row, $field_index);
                        }
                        continue;
                    }

                    if (! $fields_object) {
                        $fields[$field_index] = array(
                            'table' => $table,
                            'field' => $row['Field'],
                            'tab' => ''
                        );
                    } elseif ($fields_object && isset($this->reverse_fields[$mode]) && $this->reverse_fields[$mode]) {
                        if (! isset($fields_object[$field_index])) {
                            $fields[$field_index] = array(
                                'table' => $table,
                                'field' => $row['Field'],
                                'tab' => ''
                            );
                        }
                    } elseif (isset($fields_object[$field_index])) {
                        $fields[$field_index] = $fields_object[$field_index];
                    }

                    if (isset($this->relation[$field_index])) {
                        $this->field_type[$field_index] = 'relation';
                        if (! isset($this->defaults[$field_index])) {
                            $this->defaults[$field_index] = $row['Default'];
                        }
                        $this->_define_field_type($row, $field_index);
                        continue;
                    }

                    $this->_define_field_type($row, $field_index);
                }
            }

            // resorting
            if ($fields_object && (! isset($this->reverse_fields[$mode]) || ! $this->reverse_fields[$mode])) {
                $fields = array_merge($fields_object, $fields);
                if ($mode == 'list') {
                    $this->columns = $fields;
                } else {
                    $this->fields = $fields;
                }
            } else {
                if ($mode !== 'view' && $mode != 'list') {
                    $fields = array_merge($fields, $this->custom_fields);
                }

                $fk_before = array();
                if ($this->fk_relation) {
                    foreach ($this->fk_relation as $fk) {
                        $fk_before[$fk['alias']] = $fk['before'];
                    }
                }

                if ($mode == 'list') {
                    $subselect_before = $this->subselect_before;

                    foreach ($fields as $field_index => $field) {
                        // subselect
                        if ($name = array_search($field_index, $subselect_before)) {
                            if (! isset($fields_object[$name])) {
                                $fdata = $this->_parse_field_names($name, 'set_field_types');
                                $this->columns[$name] = reset($fdata);
                            }
                            unset($subselect_before[$name]);
                        }
                        if ($name = array_search($field_index, $fk_before)) {
                            if (! isset($fields_object[$name])) {
                                $fdata = $this->_parse_field_names($name, 'set_field_types');
                                $this->columns[$name] = reset($fdata);
                            }
                            unset($fk_before[$name]);
                        }
                        $this->columns[$field_index] = $field;
                    }
                    if (count($subselect_before)) {
                        foreach ($subselect_before as $name => $before) {
                            if (! isset($fields_object[$name])) {
                                $fdata = $this->_parse_field_names($name, 'set_field_types');
                                $this->columns[$name] = reset($fdata);
                            }
                            unset($subselect_before[$name]);
                        }
                    }
                    if (count($fk_before)) {
                        foreach ($fk_before as $name => $before) {
                            if (! isset($fields_object[$name])) {
                                $fdata = $this->_parse_field_names($name, 'set_field_types');
                                $this->columns[$name] = reset($fdata);
                            }
                            unset($fk_before[$name]);
                        }
                    }
                } elseif ($mode != 'create') {
                    $subselect_before = $this->subselect_before;
                    foreach ($fields as $field_index => $field) {
                        // subselect
                        if ($name = array_search($field_index, $subselect_before)) {
                            if (! isset($fields_object[$name])) {
                                $fdata = $this->_parse_field_names($name, 'set_field_types');
                                $this->fields[$name] = reset($fdata);
                            }
                            unset($subselect_before[$name]);
                        }
                        if ($name = array_search($field_index, $fk_before)) {
                            if (! isset($fields_object[$name])) {
                                $fdata = $this->_parse_field_names($name, 'set_field_types');
                                $this->fields[$name] = reset($fdata);
                            }
                            unset($fk_before[$name]);
                        }
                        $this->fields[$field_index] = $field;
                    }
                    if (count($subselect_before)) {
                        foreach ($subselect_before as $name => $before) {
                            if (! isset($fields_object[$name])) {
                                $fdata = $this->_parse_field_names($name, 'set_field_types');
                                $this->fields[$name] = reset($fdata);
                            }
                            unset($subselect_before[$name]);
                        }
                    }
                    if (count($fk_before)) {
                        foreach ($fk_before as $name => $before) {
                            if (! isset($fields_object[$name])) {
                                $fdata = $this->_parse_field_names($name, 'set_field_types');
                                $this->fields[$name] = reset($fdata);
                            }
                            unset($fk_before[$name]);
                        }
                    }
                } elseif ($fk_before) {
                    foreach ($fields as $field_index => $field) {
                        if ($name = array_search($field_index, $fk_before)) {
                            if (! isset($fields_object[$name])) {
                                $fdata = $this->_parse_field_names($name, 'set_field_types');
                                $this->fields[$name] = reset($fdata);
                            }
                            unset($fk_before[$name]);
                        }
                        $this->fields[$field_index] = $field;
                    }
                    if (count($fk_before)) {
                        foreach ($fk_before as $name => $before) {
                            if (! isset($fields_object[$name])) {
                                $fdata = $this->_parse_field_names($name, 'set_field_types');
                                $this->fields[$name] = reset($fdata);
                            }
                            unset($fk_before[$name]);
                        }
                    }
                } else {
                    $this->fields = $fields;
                }
            }

            // echo'<pre>'.print_r($fields,true).'</pre>';

            if (! $this->primary_key) {
                if ($uni)
                    $this->primary_key = $uni;
                else { // changed to prevent data rewriting
                       // $this->primary_key =
                       // $this->table_info[$this->table][0]['Field'];
                    switch ($this->task) {
                        case 'list':
                        case 'action':
                        case 'print':
                        case 'csv':
                            $this->is_edit = false;
                            $this->is_remove = false;
                            $this->is_create = false;
                            $this->is_view = false;
                            // $this->is_search = false;
                            break;
                        default:
                            // Tabela sem chave primária ou única
                            return Services::response()->setStatusCode(500)->setBody(lang('cCrud.table_no_primary_or_unique', [$this->table]));
                            break;
                    }
                }
            }
            unset($fields);
        }
    }

    protected function _define_field_type($row, $field_index)
    {
        if (preg_match('/^([A-Za-z]+)\((.+)\)/u', $row['Type'], $matches)) {
            $type = strtolower($matches[1]);
            $max_l = $matches[2];
        } else {
            $type = strtolower($row['Type']);
            $max_l = null;
        }
        if (! isset($this->field_attr[$field_index])) {
            $this->field_attr[$field_index] = array();
        }
        if ($max_l && (! isset($this->field_attr[$field_index]['maxlength']) or ! $this->field_attr[$field_index]['maxlength'])) {
            $this->field_attr[$field_index]['maxlength'] = (int) $max_l;
        }

        switch ($type) {
            case 'tinyint':
            case 'bit':
            case 'bool':
            case 'boolean':
                if ($type == 'bit') {
                    $this->bit_field[$field_index] = 1;
                } else {
                    $this->int_field[$field_index] = 1;
                }

                if (isset($this->field_type[$field_index])) {
                    return;
                }
                if ($max_l == 1 && $this->config->make_checkbox) {
                    $this->field_type[$field_index] = 'bool';
                    if (! isset($this->defaults[$field_index]))
                        $this->defaults[$field_index] = $row['Default'];
                } else {
                    $this->field_type[$field_index] = 'int';
                    // $this->field_attr[$field_index]['maxlength'] =
                    // (int)$max_l;
                    if (! isset($this->defaults[$field_index]))
                        $this->defaults[$field_index] = $row['Default'];
                }
                break;
            case 'smallint':
            case 'mediumint':
            case 'int':
            case 'bigint':
            case 'serial':
                $this->int_field[$field_index] = 1;
                if (isset($this->field_type[$field_index])) {
                    return;
                }
                $this->field_type[$field_index] = 'int';
                // $this->field_attr[$field_index]['maxlength'] = (int)$max_l;
                if (! isset($this->defaults[$field_index]))
                    $this->defaults[$field_index] = $row['Default'];
                break;
            case 'decimal':
            case 'numeric':
            case 'float':
            case 'double':
            case 'real':
                $this->float_field[$field_index] = 1;
                if (isset($this->field_type[$field_index])) {
                    return;
                }
                $this->field_type[$field_index] = 'float';
                // if ($max_l)
                // $this->field_attr[$field_index]['maxlength'] = (int)$max_l +
                // 1;
                if (! isset($this->defaults[$field_index]))
                    $this->defaults[$field_index] = $row['Default'];
                break;
            case 'char':
            case 'varchar':
            case 'binary':
            case 'varbinary':
            default:
                $this->text_field[$field_index] = 1;
                if (isset($this->field_type[$field_index])) {
                    return;
                }
                $this->field_type[$field_index] = 'text';
                // $this->field_attr[$field_index]['maxlength'] = (int)$max_l;
                if (! isset($this->defaults[$field_index]))
                    $this->defaults[$field_index] = $row['Default'];
                break;
            case 'text':
            case 'tinytext':
            case 'mediumtext':
            case 'longtext':
                $this->text_field[$field_index] = 1;
                if (isset($this->field_type[$field_index])) {
                    return;
                }
                if (! isset($this->no_editor[$field_index]) && $this->config->auto_editor_insertion)
                    $this->field_type[$field_index] = 'texteditor';
                else
                    $this->field_type[$field_index] = 'textarea';
                if (! isset($this->defaults[$field_index]))
                    $this->defaults[$field_index] = $row['Default'];
                break;
            case 'blob':
            case 'tinyblob':
            case 'mediumblob':
            case 'longblob':
                if (isset($this->field_type[$field_index])) {
                    return;
                }
                $this->field_type[$field_index] = 'binary';
                $this->defaults[$field_index] = '';
                break;
            case 'date':
                if (isset($this->field_type[$field_index])) {
                    return;
                }
                $this->field_type[$field_index] = 'date';
                if (! isset($this->defaults[$field_index]))
                    $this->defaults[$field_index] = $row['Default'];
                break;
            case 'datetime':
            case 'timestamp':
                if (isset($this->field_type[$field_index])) {
                    return;
                }
                $this->field_type[$field_index] = 'datetime';
                if (! isset($this->defaults[$field_index])) {
                    if ($row['Default'] == 'CURRENT_TIMESTAMP') {
                        $db = $this->model->db;
                        $result = $db->query('SELECT NOW() AS `now`');
                        $tmstmp = $result->getRowArray();
                        $this->defaults[$field_index] = $tmstmp['now'];
                    } else
                        $this->defaults[$field_index] = $row['Default'];
                }
                break;
            case 'time':
                if (isset($this->field_type[$field_index])) {
                    return;
                }
                $this->field_type[$field_index] = 'time';
                if (! isset($this->defaults[$field_index]))
                    $this->defaults[$field_index] = $row['Default'];
                break;
            case 'year':
                if (isset($this->field_type[$field_index])) {
                    return;
                }
                $this->field_type[$field_index] = 'year';
                if (! isset($this->defaults[$field_index]))
                    $this->defaults[$field_index] = $row['Default'];
                break;
            case 'enum':
                if (isset($this->field_type[$field_index])) {
                    return;
                }
                $this->field_type[$field_index] = $this->config->enum_as_radio ? 'radio' : 'select';
                $this->field_attr[$field_index]['values'] = $max_l;
                if (! isset($this->defaults[$field_index]))
                    $this->defaults[$field_index] = $row['Default'];
                break;
            case 'set':
                if (isset($this->field_type[$field_index])) {
                    return;
                }
                $this->field_type[$field_index] = $this->config->set_as_checkboxes ? 'checkboxes' : 'multiselect';
                $this->field_attr[$field_index]['values'] = $max_l;
                if (! isset($this->defaults[$field_index]))
                    $this->defaults[$field_index] = $row['Default'];
                break;
            case 'point':
                if (isset($this->field_type[$field_index])) {
                    return;
                }
                $this->field_type[$field_index] = 'point';
                $this->field_attr[$field_index] = array( // defaults
                    'text' => $this->config->default_text,
                    'search_text' => $this->config->default_search_text,
                    'zoom' => $this->config->default_zoom,
                    'width' => $this->config->default_width,
                    'height' => $this->config->default_height,
                    'search' => $this->config->default_coord,
                    'coords' => $this->config->default_search
                );
                if (! isset($this->defaults[$field_index]))
                    $this->defaults[$field_index] = $this->config->default_point ? $this->config->default_point : '0,0';
                break;
        }
    }

    /**
     * Define os nomes das colunas utilizando rótulos informados e humanização.
     *
     * @return void
     */
    protected function _set_column_names()
    {
        $subselect_before = $this->subselect_before;

        foreach ($this->columns as $key => $col) {
            if ($name = array_search($key, $subselect_before)) {
                $this->columns_names[$name] = \esc($this->labels[$name]);
                unset($subselect_before[$name]);
            }
            if (isset($this->column_name[$key])) {
                $this->columns_names[$key] = \esc($this->column_name[$key]);
            } elseif (isset($this->labels[$key])) {
                $this->columns_names[$key] = \esc($this->labels[$key]);
            } elseif ($this->fk_relation && isset($this->fk_relation[$key])) {
                $this->columns_names[$key] = $this->fk_relation[$key]['label'];
            } elseif (isset($this->labels[$col['field']])) {
                $this->columns_names[$key] = \esc($this->labels[$col['field']]);
            } else {
                $this->columns_names[$key] = \esc($this->_humanize($col['field']));
            }
        }
        if ($subselect_before) {
            foreach ($this->subselect_before as $name => $none) {
                $this->columns_names[$name] = \esc($this->labels[$name]);
                unset($subselect_before[$name]);
            }
        }
    }

    /* ALTERADA DO PADRÃO - join_fields */
    protected function _set_field_names()
    {
        foreach ($this->fields as $key => $value) {
            $render_fields[$key] = $value;
            if (array_key_exists($key, $this->join_relation) && is_array($this->join_relation[$key]['rel_additional_fields'])) {
                foreach ($this->join_relation[$key]['rel_additional_fields'] as $k => $v) {
                    $render_fields[$k] = $v;
                }
            }
        }
        foreach ($render_fields as $key => $field) {
            if (isset($this->labels[$key])) {
                $this->fields_names[$key] = \esc($this->labels[$key]);
            } else {
                $this->fields_names[$key] = \esc($this->_humanize($field['field']));
            }
        }
    }

    protected function _render_list()
    {
        if (count($this->order_by)) {
            reset($this->order_by);
            $this->order_column = key($this->order_by);
            $this->order_direct = strtolower($this->order_by[$this->order_column]);
        } else {
            // $this->order_column = $this->table . '.' . $this->primary_key;
            // $this->order_direct = 'asc';
        }

        if ($this->column === false) {
            if ($this->search_default) {
                $this->column = $this->search_default;
            } elseif (! $this->config->search_all) {
                if ($this->search_columns) {
                    $this->column = key($this->search_columns);
                } else {
                    $this->column = key($this->columns);
                }
            }
        }
        $mode = 'list';
        $view_file = __DIR__ . '/views/' . $this->views['list'];
        $view_file = $this->check_file($view_file, 'render');
        $viewPath = dirname($view_file);
        $renderer = Services::renderer($viewPath);
        $content = $renderer->setVar('cCrud', $this)->render(basename($view_file));

        $output = $this->render_output($content);
        return $this->set_output($output);
    }

    /**
     *
     * @author Ariel Canal
     *         Se a função field_callback retornar falso, rederiza o field
     *         padrão.
     *         Mesmo com Field Calback, cria o campo.
     */
    /**
     * renders details view template
     */
    protected function _render_details($mode)
    {
        if (count($this->order_by)) {
            $order_direct = strtolower(reset($this->order_by));
            $order_column = key($this->order_by);
        } else {
            $order_column = $this->table . '.' . $this->primary_key;
            $order_direct = 'asc';
        }

        /*
         * if ($mode == 'create')
         * {
         * $this->disabled = $this->disabled_on_create;
         * $this->readonly = $this->readonly_on_create;
         * }
         * elseif ($mode == 'edit')
         * {
         * $this->disabled = $this->disabled_on_edit;
         * $this->readonly = $this->readonly_on_edit;
         * }
         */
        if (isset($this->result_row['primary_key'])) {
            $this->primary_val = $this->result_row['primary_key'];
        }
        if ($this->result_row) {
            foreach ($this->fields as $key => $value) {
                $this->fields[$key] = $value;
                if (array_key_exists($key, $this->join_relation) && is_array($this->join_relation[$key]['rel_additional_fields'])) {
                    foreach ($this->join_relation[$key]['rel_additional_fields'] as $k => $v) {
                        $this->fields[$k] = $v;
                        $temp[] = $k;
                        $parents[$k] = $key;
                        if (isset($value['tab'])) {
                            $this->fields[$k]['tab'] = $value['tab'];
                        }
                    }
                    foreach ($this->field_tabs as $k => $v) {}
                }
            }
            foreach ($this->fields as $field => $fitem) {
                if (isset($this->custom_fields[$field])) {
                    $this->result_row[$field] = $this->defaults[$field];
                }
                if ($this->field_type[$field] == 'hidden') {
                    $this->hidden_fields_output[$field] = $this->create_hidden($field, $this->result_row[$field]);
                } else {

                    if ($mode == 'view') {
                        $func = 'create_view_' . $this->field_type[$field];
                    } else {
                        $func = 'create_' . $this->field_type[$field];
                    }
                    if (isset($this->field_callback[$field]) && $mode != 'view') {
                        $path = $this->check_file($this->field_callback[$field]['path'], 'field_callback');
                        include_once ($path);
                        if (is_callable($this->field_callback[$field]['callback'])) {
                            $attr = $this->get_field_attr($field, $mode);
                            $field_default = call_user_func_array(array(
                                $this,
                                $func
                            ), array(
                                $field,
                                $this->result_row[$field],
                                $attr
                            ));
                            $field_rendered = call_user_func_array($this->field_callback[$field]['callback'], array(
                                $this->result_row[$field],
                                $field,
                                $mode,
                                $this->result_row,
                                $this,
                                $field_default
                            ));
                            if (! $field_rendered)
                                $field_rendered = call_user_func_array(array(
                                    $this,
                                    $func
                                ), array(
                                    $field,
                                    $this->result_row[$field],
                                    $attr
                                ));
                            $this->fields_output[$field] = array(
                                'label' => $this->fields_names[$field],
                                'field' => $field_rendered,
                                'name' => $field,
                                'value' => $this->result_row[$field]
                            );
                            if (array_key_exists($field, $this->exception_fields)) {
                                $this->fields_output[$field]['exception'] = $this->exception_fields[$field]['exception'];
                            }
                        }
                    } elseif (isset($this->column_callback[$field]) && $mode == 'view') {
                        $path = $this->check_file($this->column_callback[$field]['path'], 'column_callback');
                        include_once ($path);
                        if (is_callable($this->column_callback[$field]['callback']) && $this->result_row) {
                            @$field_rendered = call_user_func_array($this->column_callback[$field]['callback'], array(
                                $this->result_row[$field],
                                $field,
                                $mode,
                                $this->result_row,
                                $this
                            ));
                            if (! $field_rendered)
                                $field_rendered = call_user_func_array($this->column_callback[$field]['callback'], array(
                                    $this->result_row[$field],
                                    $field,
                                    $this->primary_val,
                                    $this->result_row,
                                    $this
                                ));
                            $this->fields_output[$field] = array(
                                'label' => $this->fields_names[$field],
                                'field' => $field_rendered,
                                'name' => $field,
                                'value' => $this->result_row[$field]
                            );
                            if (array_key_exists($field, $this->exception_fields)) {
                                $this->fields_output[$field]['exception'] = $this->exception_fields[$field]['exception'];
                            }
                        }
                    } else {
                        $attr = $this->get_field_attr($field, $mode);
                        if (isset($temp) && in_array($field, $temp)) {
                            $attr['class'] .= ' jr_additional';
                            $attr['jr-parent'] = $this->fieldname_encode($parents[$field]);
                        }
                        if (! method_exists($this, $func))
                            continue;

                        $this->fields_output[$field] = array(
                            'label' => $this->fields_names[$field],
                            'field' => call_user_func_array(array(
                                $this,
                                $func
                            ), array(
                                $field,
                                $this->result_row[$field],
                                $attr
                            )),
                            'name' => $field,
                            'value' => $this->result_row[$field]
                        );
                        if ($this->exception_fields[$field]) {
                            $this->fields_output[$field]['exception'] = $this->exception_fields[$field]['exception'];
                        }
                        if (isset($this->column_pattern[$field]) && $mode == 'view') {
                            $this->fields_output[$field]['field'] = str_ireplace('{value}', $this->fields_output[$field]['field'], $this->column_pattern[$field]);
                            $this->fields_output[$field]['field'] = $this->replace_text_variables($this->fields_output[$field]['field'], $this->result_row, true);
                        }
                    }
                }
            }
        }

        if ($this->inner_table_instance && ($mode == 'view' or $mode == 'edit')) // restoring
                                                                                  // nested
                                                                                  // objects
        {
            foreach ($this->inner_table_instance as $inst_name => $field) {
                if (isset($this->result_row[$field])) {
                    $instance = self::getInstance($this->model, $this->logger, $inst_name);
                    $instance->ajax_request = true;
                    $instance->import_vars();
                    $instance->inner_where($this->result_row[$field]);
                    if ($mode == 'view' && $this->nested_readonly_on_view && ! $instance->table_always_edit_mode) {

                        $instance->table_ro = true;
                    } else {
                        $instance->table_ro = false;
                    }
                    $this->nested_rendered[$instance->table_name] = '<div class="cCrud-nested-container cCrud-container"><div class="cCrud-ajax" id="cCrud-ajax-' . base_convert(rand(), 10, 36) . '">' . $instance->render($instance->nested_default_render, $instance->nested_default_render_primary) . '</div></div>';
                }
            }
        }

        $view_file = __DIR__ . '/views/' . $this->views[$mode];
        $view_file = $this->check_file($view_file, 'render');
        $viewPath = dirname($view_file);
        $renderer = Services::renderer($viewPath);
        $content = $renderer->setData([
            'cCrud' => $this,
            'mode' => $mode,
            'title' => $this->get_var('title')
        ])->render(basename($view_file));

        $content = $this->render_search_hidden() . $content;
        /*
         * if ($this->inner_table_instance && ($mode == 'view' or $mode ==
         * 'edit')) // restoring nested objects
         * {
         * foreach ($this->inner_table_instance as $inst_name => $field)
         * {
         * if (isset($this->result_row[$field]))
         * {
         * $instance = self::getInstance($inst_name);
         * $instance->ajax_request = true;
         * $instance->import_vars();
         * $instance->inner_where($this->result_row[$field]);
         * if ($mode == 'view' && $this->config->nested_readonly_on_view)
         * {
         * $instance->table_ro = true;
         * }
         * else
         * {
         * $instance->table_ro = false;
         * }
         * cCrud-container"><div class="cCrud-ajax" id="cCrud-ajax-' .
         * // base_convert(rand(), 10, 36) . '">' . $instance->render('list') .
         * '</div></div>';
         * $this->nested_rendered[$inst_name] = '<div
         * class="cCrud-nested-container cCrud-container"><div
         * class="cCrud-ajax" id="cCrud-ajax-' .
         * base_convert(rand(), 10, 36) . '">' . $instance->render('list') .
         * '</div></div>';
         * }
         * }
         * }
         */
        if ($this->nested_rendered) {
            $content .= implode('', $this->nested_rendered);
        }

        $output = $this->render_output($content);
        return $this->set_output($output);
    }

    /**
     * defines nested main condition, must be public.
     * Only for internal usage.
     */
    public function inner_where($value = false)
    {
        if ($value !== false) {
            $this->inner_value = $value;
        }
        // nested table connection
        if ($this->is_inner && $this->inner_where) {
            $field = reset($this->inner_where);
            $this->where_pri($field, $this->inner_value, 'AND', 'nstd_tbl');
            $this->pass_default($field, $this->inner_value);
        }
    }

    protected function _pagination($total, $start, $limit, $numpos = 10, $numlr = 2)
    {
        if ($total > $limit) {
            $pages = ceil($total / $limit);
            $curent = ceil(($start + $limit) / $limit);
            $links = array();
            /*
             * for ($i = 1; $i <= $pages; ++$i)
             * {
             * $limit1 = $i * $limit - $limit;
             * if ($i == $curent)
             * $links[$i] = '<li class="' .
             * $this->theme_config('pagination_active') . '"><span>' . $i .
             * '</span></li>';
             * else
             * {
             * $links[$i] = '<li class="' .
             * $this->theme_config('pagination_item') .
             * '"><a href="javascript:;" class="cCrud-action" data-start="' .
             * $limit1 . '">' . $i . '</a></li>';
             * }
             * }
             */
            $html = '<ul class="' . $this->theme_config('pagination_container') . '">';
            if ($pages > $numpos) {

                if ($curent <= $numlr + 3) {
                    for ($i = 1; $i <= $numpos - $numlr - 1; ++ $i) {
                        $html .= $this->_pagination_item($i, $curent, $limit);
                    }
                    $html .= '<li class="' . $this->theme_config('pagination_dots') . '"><a class="' . $this->theme_config('pagination_link') . '">&#133;</a></li>';
                    for ($i = $pages - $numlr + 1; $i <= $pages; ++ $i) {
                        $html .= $this->_pagination_item($i, $curent, $limit);
                    }
                } else if ($curent >= $pages - $numlr - 2) {
                    for ($i = 1; $i <= $numlr; ++ $i) {
                        $html .= $this->_pagination_item($i, $curent, $limit);
                    }
                    $html .= '<li class="' . $this->theme_config('pagination_dots') . '"><a class="' . $this->theme_config('pagination_link') . '">&#133;</a></li>';
                    for ($i = $pages - $numpos + $numlr + 2; $i <= $pages; ++ $i) {
                        $html .= $this->_pagination_item($i, $curent, $limit);
                    }
                } else {
                    for ($i = 1; $i <= $numlr; ++ $i) {
                        $html .= $this->_pagination_item($i, $curent, $limit);
                    }
                    $html .= '<li class="' . $this->theme_config('pagination_dots') . '"><a class="' . $this->theme_config('pagination_link') . '">&#133;</a></li>';
                    $offset = floor(($numpos - $numlr - $numlr - 1) / 2);
                    for ($i = $curent - $offset; $i <= $curent + $offset; ++ $i) {
                        $html .= $this->_pagination_item($i, $curent, $limit);
                    }

                    $html .= '<li class="' . $this->theme_config('pagination_dots') . '"><a class="' . $this->theme_config('pagination_link') . '">&#133;</a></li>';
                    for ($i = $pages - $numlr + 1; $i <= $pages; ++ $i) {
                        $html .= $this->_pagination_item($i, $curent, $limit);
                    }
                }
            } else {
                // $html .= implode('', $links);
                for ($i = 1; $i <= $pages; ++ $i) {
                    $html .= $this->_pagination_item($i, $curent, $limit);
                }
            }
            $html .= '</ul>';
            return $html;
        }
    }

    protected function _pagination_item($i, $curent, $limit)
    {
        $limit1 = $i * $limit - $limit;
        if ($i == $curent)
            return '<li class="' . $this->theme_config('pagination_active') . '"><a href="javascript:;" class="' . $this->theme_config('pagination_link') . '" aria-current="page" data-start="' . $limit1 . '">' . $i . '</a></li>';
        else {
            return '<li class="' . $this->theme_config('pagination_item') . '"><a href="javascript:;" class="cCrud-action ' . $this->theme_config('pagination_link') . '" data-start="' . $limit1 . '">' . $i . '</a></li>';
        }
    }

    /**
     * Corta texto conforme configuração da coluna.
     *
     * @param string|null $string   Texto original.
     * @param string      $field    Campo de referência.
     * @param bool        $wordsafe Preserva palavras inteiras.
     * @param bool        $dots     Adiciona reticências.
     *
     * @return string|null
     */
    protected function _cut(?string $string, string $field, bool $wordsafe = true, bool $dots = true): ?string
    {
        if (isset($this->column_cut_list[$field])) {
            $len = $this->column_cut_list[$field]['count'];
            $safe = $this->column_cut_list[$field]['safe'];
        } else {
            $len = $this->column_cut;
            $safe = $this->safe_output;
        }

        $charset = config('App')->charset;

        if (! is_null($string)) {
            $string = html_entity_decode($string, ENT_QUOTES, $charset);
        }

        if (! $len) {
            $string = $this->strip_tags ? strip_tags($string) : $string;
            return $safe ? \esc($string) : $string;
        }
        if (! is_null($string)) {
            $strip_string = trim(strip_tags($string));
        }

        $slen = mb_strlen($strip_string, $charset);
        if ($slen <= $len || $this->config->print_full_texts) {
            $string = $this->strip_tags ? strip_tags($string) : $string;
            return $safe ? \esc($string) : $string;
        }
        if ($wordsafe) {
            $end = $len;
            while ((mb_substr($strip_string, -- $len, 1, $charset) != ' ') && ($len > 0)) {}
            if ($len == 0) {
                $len = $end;
            }
            $sub = mb_substr($strip_string, 0, $len, $charset);
            $sub = $safe ? \esc($sub) : $sub;
            return $sub . ($dots ? '&#133;' : '');
        }
        $sub = mb_substr($strip_string, 0, $len, $charset);
        $sub = $safe ? \esc($sub) : $sub;
        return $sub . ($dots ? '&#133;' : '');
    }

    /**
     * Converte texto com underscores para formato legível.
     *
     * @param string $text Texto a ser humanizado.
     *
     * @return string Texto humanizado.
     */
    protected function _humanize(string $text): string
    {
        $charset = config('App')->charset;

        return mb_convert_case(str_replace('_', ' ', $text), MB_CASE_TITLE, $charset);
    }

    protected function _regenerate_key()
    {
        switch ($this->task) {
            case 'file':
            case 'depend':
            case 'print':
            case 'csv':
            case 'upload':
            case 'remove_upload':
            case 'crop_image':
            case 'unique':
                break;
            default:
                $this->key = sha1(microtime() . rand());
                break;
        }
    }

    public function _export_vars()
    {
        $inst_name = $this->instance_name;
        $this->time = $time = time();

        $cCrud_session = $this->getSession()->get('cCrud_session');

        // session auto-clearing, must start on first instance
        if ($this->instance_count == 1 && ! $this->ajax_request) {
            if (isset($cCrud_session) && $cCrud_session) {
                foreach ($cCrud_session as $s_key => $s_val) {
                    // workaround on some servers session duplication
                    $old_time = isset($s_val['time']) ? (int) $s_val['time'] : 0;
                    if ($time > $old_time + $this->config->autoclean_timeout) {
                        // autocleaner
                        unset($cCrud_session[$s_key]);
                    }
                }
            }
        }
        $this->condition_restore();

        // Armazena todos os atributos atuais da instância
        $vars = get_object_vars($this);
        unset($vars['session']); // Evita armazenar a instância de sessão

        $cCrud_session[$inst_name]            = $vars;
        $cCrud_session[$inst_name]['before'] = $this->find_prev_task();

        $this->getSession()->set('cCrud_session', $cCrud_session);
    }

    protected function find_prev_task()
    {
        switch ($this->task) {
            case 'create':
            case 'view':
            case 'edit':
            case 'list':
                return $this->task;
                break;
            case '':
                return 'list';
                break;
            default:
                return ($this->before ? $this->before : 'list');
                break;
        }
    }

    public function import_vars($key = false)
    {
        $inst_name     = $this->instance_name;
        $cCrud_session = $this->getSession()->get('cCrud_session');

        if (isset($cCrud_session[$inst_name])) {
            foreach ($cCrud_session[$inst_name] as $property => $value) {
                if ($property !== 'session') {
                    $this->{$property} = $value;
                }
            }
        }

        if ($key) {
            $this->key = $key;
        }
    }

    protected function get_field_attr($name, $mode)
    {
        $tag = array(
            'class' => 'cCrud-input',
            'name' => $this->fieldname_encode($name),
            'id' => $this->fieldname_encode($name)
        );
        if (! empty($this->field_attr[$name]['class'])) {
            $tag['class'] .= ' ' . $this->field_attr[$name]['class'];
        }
        
        if (isset($this->readonly[$name][$mode])) {
            $tag['readonly'] = '';
        }
        if (isset($this->disabled[$name][$mode])) {
            $tag['disabled'] = '';
        }
        if (isset($this->unique[$name])) {
            $tag['data-unique'] = '';
        }
        if (isset($this->relation[$name]['depend_on']) && $this->relation[$name]['depend_on']) {
            $tag['data-depend'] = $this->relation[$name]['depend_on'];
        }
        return $tag;
    }

    protected function create_none($name, $value = '', $tag = array())
    {
        return '<span class="cCrud-none">' . $value . '</span>';
    }

    protected function create_view_none($name, $value = '')
    {
        return '<span class="cCrud-none">' . $value . '</span>';
    }

    protected function create_bool($name, $value = '', $tag = array())
    {
        if (isset($this->subselect[$name])) {
            return $this->create_view_bool($name, $value);
        }
        $tag = $tag + array(
            'tag' => 'input',
            'type' => 'checkbox',
            'data-type' => 'bool',
            'value' => 1
        );
        if ($value) {
            $tag['checked'] = '';
        }
        $out = '';

        $out .= $this->open_tag('div', $this->theme_config('bool_container'));
        $out .= $this->single_tag($tag, $this->theme_config('bool_field'), $this->field_attr[$name]);
        $out .= $this->single_tag('label', $this->theme_config('bool_label'),['for'=>$tag['id']]);
        $out .= $this->close_tag('div');

        return $out;
    }

    protected function create_view_bool($name, $value = '', $tag = array())
    {
        return (int) $value ? $this->lang('bool_on') : $this->lang('bool_off');
    }

    protected function create_int($name, $value = '', $tag = array())
    {
        if (isset($this->subselect[$name])) {
            return $this->create_view_int($name, $value);
        }
        $tag = $tag + array(
            'tag' => 'input',
            'type' => 'text',
            'data-type' => 'int',
            'value' => $value,
            'data-pattern' => 'integer'
        );

        return $this->single_tag($tag, $this->theme_config('int_field'), $this->field_attr[$name]);
    }

    protected function create_view_int($name, $value = '', $tag = array())
    {
        return $value;
    }

    protected function create_float($name, $value = '', $tag = array())
    {
        if (isset($this->subselect[$name])) {
            return $this->create_view_float($name, $value);
        }
        $tag = $tag + array(
            'tag' => 'input',
            'type' => 'text',
            'data-type' => 'float',
            'value' => $value,
            'data-pattern' => 'numeric'
        );

        return $this->single_tag($tag, $this->theme_config('float_field'), $this->field_attr[$name]);
    }

    protected function create_view_float($name, $value = '', $tag = array())
    {
        return $value;
    }

    protected function create_price($name, $value = '', $tag = array())
    {
        if (isset($this->subselect[$name])) {
            return $this->create_view_price($name, $value);
        }
        $tag = $tag + array(
            'tag' => 'input',
            'type' => 'text',
            'data-type' => 'price',
            'value' => $this->cast_number_format($value, $name, true),
            'data-plugin' => 'currency',
            'data-prefix' => $this->field_attr[$name]['prefix'],
            'data-thousands' => $this->field_attr[$name]['separator'],
            'data-decimal' => $this->field_attr[$name]['point']
        );

        return $this->single_tag($tag, $this->theme_config('price_field'), $this->field_attr[$name]);
    }

    protected function create_view_price($name, $value = '', $tag = array())
    {
        $out = '';
        $out .= $this->cast_number_format($value, $name);
        return $out;
    }

    protected function create_number($name, $value = '', $tag = array())
    {
        if (isset($this->subselect[$name])) {
            return $this->create_view_text($name, $value);
        }
        $tag = $tag + array(
            'tag' => 'input',
            'type' => 'number',
            'data-type' => 'text',
            'value' => $value
        );

        return $this->single_tag($tag, $this->theme_config('text_field'), $this->field_attr[$name]);
    }

    protected function create_view_number($name, $value = '', $tag = array())
    {
        return $value;
    }

    protected function create_tel($name, $value = '', $tag = array())
    {
        if (isset($this->subselect[$name])) {
            return $this->create_view_text($name, $value);
        }
        $tag = $tag + array(
            'tag' => 'input',
            'type' => 'tel',
            'data-type' => 'text',
            'value' => $value
        );

        return $this->single_tag($tag, $this->theme_config('text_field'), $this->field_attr[$name]);
    }

    protected function create_view_tel($name, $value = '', $tag = array())
    {
        return $value;
    }

    protected function create_email($name, $value = '', $tag = array())
    {
        if (isset($this->subselect[$name])) {
            return $this->create_view_text($name, $value);
        }
        $tag = $tag + array(
            'tag' => 'input',
            'type' => 'email',
            'data-type' => 'text',
            'value' => $value
        );

        return $this->single_tag($tag, $this->theme_config('text_field'), $this->field_attr[$name]);
    }

    protected function create_view_email($name, $value = '', $tag = array())
    {
        if ($this->config->clickable_list_links) {
            $value = $this->make_links($value);
            $value = $this->make_mailto($value);
        }
        return $value;
    }

    protected function create_text($name, $value = '', $tag = array())
    {
        if (isset($this->subselect[$name])) {
            return $this->create_view_text($name, $value);
        }
        $tag = $tag + array(
            'tag' => 'input',
            'type' => 'text',
            'data-type' => 'text',
            'value' => $value
        );

        return $this->single_tag($tag, $this->theme_config('text_field'), $this->field_attr[$name]);
    }

    protected function create_view_text($name, $value = '', $tag = array())
    {
        if ($this->config->clickable_list_links) {
            $value = $this->make_links($value);
            $value = $this->make_mailto($value);
        }
        return $value;
    }

    /**
     *
     * @author Ariel Canal
     *         Caso o campo esteja desabilitado, renderiza em modo view.
     */
    protected function create_textarea($name, $value = '', $tag = array())
    {
        if (isset($this->subselect[$name]) || isset($this->disabled[$name][$this->task])) {
            return $this->create_view_textarea($name, $value);
        }
        $tag = $tag + array(
            'tag' => 'textarea',
            'data-type' => 'textarea'
        );

        return $this->open_tag($tag, $this->theme_config('textarea_field'), $this->field_attr[$name], true) . \esc($value) . $this->close_tag($tag);
    }

    protected function create_view_textarea($name, $value = '', $tag = array())
    {
        return $value;
    }

    /**
     *
     * @author Ariel Canal
     *         Caso o campo esteja desabilitado, renderiza em modo view.
     */
    protected function create_texteditor($name, $value = '', $tag = array())
    {
        if (isset($this->subselect[$name]) || isset($this->disabled[$name][$this->task])) {
            return $this->create_view_texteditor($name, $value, $tag = array());
        }
        $tag = $tag + array(
            'tag' => 'textarea',
            'data-type' => 'texteditor',
            'id' => 'editor_' . base_convert(rand(), 10, 36)
        );
        $tag['class'] .= ' cCrud-texteditor';

        return $this->open_tag($tag, $this->theme_config('texteditor_field'), $this->field_attr[$name], true) . \esc($value) . $this->close_tag($tag);
    }

    protected function create_view_texteditor($name, $value = '', $tag = array())
    {
        return $value;
    }

    protected function create_date($name, $value = '', $tag = array())
    {
        if (isset($this->subselect[$name])) {
            return $this->create_view_date($name, $value, $tag = array());
        }
        $tag = $tag + array(
            'tag' => 'input',
            'type' => 'text',
            'data-type' => 'date',
            'value' => ($value && $value !== '0000-00-00')
                ? Time::parse($value)->toDateString()
                : ''
        );
        $tag['class'] .= ' cCrud-datepicker';

        $r = isset($this->field_attr[$name]) ? $this->field_attr[$name] : '';
        if ($r) {
            if (isset($r['range_end'])) {
                $fdata = $this->_parse_field_names($r['range_end'], 'create_date');
                $tag['data-rangeend'] = key($fdata) /*$fdata[0]['table'] . '.' . $fdata[0]['field']*/;
            }
            if (isset($r['range_start'])) {
                $fdata = $this->_parse_field_names($r['range_start'], 'create_date');
                $tag['data-rangestart'] = key($fdata) /*$fdata[0]['table'] . '.' . $fdata[0]['field']*/;
            }
        }
        return $this->single_tag($tag, $this->theme_config('date_field'), $this->field_attr[$name]);
    }

    protected function create_view_date($name, $value = '', $tag = array())
    {
        return ($value && $value !== '0000-00-00')
            ? Time::parse($value)->toDateString()
            : '';
    }

    protected function create_datetime($name, $value = '', $tag = array())
    {
        if (isset($this->subselect[$name])) {
            return $this->create_view_datetime($name, $value, $tag = array());
        }
        $tag = $tag + array(
            'tag' => 'input',
            'type' => 'text',
            'data-type' => 'datetime',
            'value' => ($value && $value !== '0000-00-00 00:00:00')
                ? Time::parse($value)->toDateTimeString()
                : ''
        );
        $tag['class'] .= ' cCrud-datepicker';

        return $this->single_tag($tag, $this->theme_config('datetime_field'), $this->field_attr[$name]);
    }

    protected function create_view_datetime($name, $value = '', $tag = array())
    {
        return ($value && $value !== '0000-00-00 00:00:00')
            ? Time::parse($value)->toDateTimeString()
            : '';
    }

    protected function create_timestamp($name, $value = '', $tag = array())
    {
        if (isset($this->subselect[$name])) {
            return $this->create_view_timestamp($name, $value, $tag = array());
        }
        $tag = $tag + array(
            'tag' => 'input',
            'type' => 'text',
            'data-type' => 'timestamp',
            'value' => ($value && $value !== '0000-00-00 00:00:00')
                ? Time::parse($value)->toDateTimeString()
                : ''
        );
        $tag['class'] .= ' cCrud-datepicker';

        return $this->single_tag($tag, $this->theme_config('timestamp_field'), $this->field_attr[$name]);
    }

    protected function create_view_timestamp($name, $value = '', $tag = array())
    {
        return ($value && $value !== '0000-00-00 00:00:00')
            ? Time::parse($value)->toDateTimeString()
            : '';
    }

    protected function create_time($name, $value = '', $tag = array())
    {
        if (isset($this->subselect[$name])) {
            return $this->create_view_time($name, $value, $tag);
        }
        $tag = $tag + array(
            'tag' => 'input',
            'type' => 'text',
            'data-type' => 'time',
            'value' => $value ? Time::parse($value)->toTimeString() : ''
        );
        $tag['class'] .= ' cCrud-datepicker';

        return $this->single_tag($tag, $this->theme_config('time_field'), $this->field_attr[$name]);
    }

    protected function create_view_time($name, $value = '', $tag = array())
    {
        return $value ? Time::parse($value)->toTimeString() : '';
    }

    protected function create_year($name, $value = '', $tag = array())
    {
        if (isset($this->subselect[$name])) {
            return $this->create_view_year($name, $value, $tag);
        }
        $tag = $tag + array(
            'tag' => 'input',
            'type' => 'text',
            'data-type' => 'year',
            'value' => (int) $value
        );
        $tag['class'] .= ' cCrud-datepicker';

        return $this->single_tag($tag, $this->theme_config('year_field'), $this->field_attr[$name]);
    }

    protected function create_view_year($name, $value = '', $tag = array())
    {
        return $value;
    }

    protected function create_select($name, $value = '', $tag = array())
    {
        if (isset($this->subselect[$name])) {
            return $this->create_view_select($name, $value, $tag);
        }
        $out = '';
        $tag = $tag + array(
            'tag' => 'select',
            'data-type' => 'select'
        );

        $out .= $this->open_tag($tag, $this->theme_config('select_field'), $this->field_attr[$name]);

        if (is_array($this->field_attr[$name]['values'])) {
            foreach ($this->field_attr[$name]['values'] as $optkey => $opt) {
                if (is_array($opt)) {
                    $out .= $this->open_tag(array(
                        'tag' => 'optgroup',
                        'label' => $optkey
                    ));
                    foreach ($opt as $k_key => $k_opt) {
                        $opt_tag = array(
                            'tag' => 'option',
                            'value' => $k_key
                        );
                        if ($k_key == $value) {
                            $opt_tag['selected'] = '';
                        }
                        $out .= $this->open_tag($opt_tag) . \esc($k_opt) . $this->close_tag($opt_tag);
                    }
                    $out .= $this->close_tag('optgroup');
                } else {
                    $opt_tag = array(
                        'tag' => 'option',
                        'value' => $optkey
                    );
                    if ($optkey == $value) {
                        $opt_tag['selected'] = '';
                    }
                    $out .= $this->open_tag($opt_tag) . \esc($opt) . $this->close_tag($opt_tag);
                }
            }
        } else {
            $tmp = $this->parse_comma_separated($this->field_attr[$name]['values']);
            foreach ($tmp as $opt) {
                $opt = trim($opt, '\'');
                $opt_tag = array(
                    'tag' => 'option',
                    'value' => $opt
                );
                if ($opt == $value) {
                    $opt_tag['selected'] = '';
                }
                $out .= $this->open_tag($opt_tag) . \esc($opt) . $this->close_tag($opt_tag);
            }
        }
        $out .= $this->close_tag($tag);
        return $out;
    }

    protected function create_radio($name, $value = '', $tag = array())
    {
        if (isset($this->subselect[$name])) {
            return $this->create_view_radio($name, $value, $tag);
        }
        $out = '';
        
        $container = [
            'tag' => 'div'
        ];
        
        $field = $tag + [
            'tag' => 'input',
            'type' => 'radio'
        ];
        
        $label = [
            'tag' => 'label'
        ];
        
        if (is_array($this->field_attr[$name]['values'])) {
            $options = $this->field_attr[$name]['values'];
            $value_var = 'key';
        } else {
            $options = $this->parse_comma_separated($this->field_attr[$name]['values']);
            $value_var = 'val';
        }
        foreach ($options as $key => $val) {
            $value_val = $$value_var;
            $attr = [
                'value' => $value_val
            ];
            if ($value_val == $value) {
                $attr['checked'] = '';
            }
            $out .= $this->open_tag($container, $this->theme_config('radio_container'));
            $out .= $this->single_tag($field, $this->theme_config('radio_field'), array_merge($this->field_attr[$name], $attr));
            $out .= $this->open_tag($label, $this->theme_config('radio_label'));
            $out .= \esc(val);
            $out .= $this->close_tag($label);
            $out .= $this->close_tag($container);
        }
        return $out;
    }

    protected function create_view_select($name, $value = '', $tag = array())
    {
        if (is_array($this->field_attr[$name]['values'])) {
            if (is_array(reset($this->field_attr[$name]['values']))) {
                foreach ($this->field_attr[$name]['values'] as $tmp) {
                    if (isset($tmp[$value])) {
                        return $tmp[$value];
                    }
                }
            } else {
                if (isset($this->field_attr[$name]['values'][$value])) {
                    return $this->field_attr[$name]['values'][$value];
                }
            }
        } else {
            return $value;
        }
    }

    protected function create_view_radio($name, $value = '', $tag = array())
    {
        return $this->create_view_select($name, $value, $tag);
    }

    protected function create_multiselect($name, $value = '', $tag = array())
    {
        if (isset($this->subselect[$name])) {
            return $this->create_view_multiselect($name, $value, $tag);
        }
        $out = '';
        $values = $this->parse_comma_separated($value);
        $tag = $tag + array(
            'tag' => 'select',
            'data-type' => 'select',
            'multiple' => ''
        );

        if (is_array($this->field_attr[$name]['values'])) {
            if (is_array(reset($this->field_attr[$name]['values']))) {
                $size = 0;
                foreach ($this->field_attr[$name]['values'] as $tmp) {
                    $size += (count($tmp) + 1);
                }
            } else {
                $size = count($this->field_attr[$name]['values']);
            }
        } else {
            $tmp = $this->parse_comma_separated($this->field_attr[$name]['values']);
            $size = count($tmp);
        }
        $tag['size'] = $size > 10 ? 10 : $size;

        $out .= $this->open_tag($tag, $this->theme_config('multiselect_field'), $this->field_attr[$name]);

        if (is_array($this->field_attr[$name]['values'])) {
            foreach ($this->field_attr[$name]['values'] as $optkey => $opt) {
                if (is_array($opt)) {
                    $out .= $this->open_tag(array(
                        'tag' => 'optgroup',
                        'label' => $optkey
                    ));
                    foreach ($opt as $k_key => $k_opt) {
                        $opt_tag = array(
                            'tag' => 'option',
                            'value' => $k_key
                        );
                        if (in_array($k_key, $values)) {
                            $opt_tag['selected'] = '';
                        }
                        $out .= $this->open_tag($opt_tag) . \esc($k_opt) . $this->close_tag($opt_tag);
                    }
                    $out .= $this->close_tag('optgroup');
                } else {
                    $opt_tag = array(
                        'tag' => 'option',
                        'value' => $optkey
                    );
                    if (in_array($optkey, $values)) {
                        $opt_tag['selected'] = '';
                    }
                    $out .= $this->open_tag($opt_tag) . \esc($opt) . $this->close_tag($opt_tag);
                }
            }
        } else {
            $tmp = $this->parse_comma_separated($this->field_attr[$name]['values']);
            foreach ($tmp as $opt) {
                $opt = trim(trim($opt, '\''));
                $opt_tag = array(
                    'tag' => 'option',
                    'value' => $opt
                );
                if (in_array($opt, $values)) {
                    $opt_tag['selected'] = '';
                }
                $out .= $this->open_tag($opt_tag) . \esc($opt) . $this->close_tag($opt_tag);
            }
        }
        $out .= $this->close_tag($tag);
        return $out;
    }

    protected function create_checkboxes($name, $value = '', $tag = array())
    {
        if (isset($this->subselect[$name])) {
            return $this->create_view_checkboxes($name, $value, $tag);
        }
        $out = '';
        $values = $this->parse_comma_separated($value);
        $tag = $tag + [
            'tag' => 'input',
            'type' => 'checkbox'
        ];

        $label_tag = [
            'tag' => 'label'
        ];

        if (! is_array($this->field_attr[$name]['values'])) {
            $this->field_attr[$name]['values'] = $this->parse_comma_separated($this->field_attr[$name]['values']);
        }
        foreach ($this->field_attr[$name]['values'] as $optkey => $opt) {
            $opt = trim(trim($opt, '\''));
            $out .= $this->open_tag('div', $this->theme_config('checkboxes_container'));

            /* INPUT */
            $attr = array(
                'value' => $opt,
                'id' => $optkey
            );
            if (in_array($opt, $values)) {
                $attr['checked'] = '';
            }
            $out .= $this->single_tag($tag, $this->theme_config('checkboxes_field'), array_merge($this->field_attr[$name], $attr));

            /* LABEL */
            $out .= $this->open_tag($label_tag, $this->theme_config('checkboxes_label'), [
                'for' => $optkey
            ]);
            $out .= \esc($opt);
            $out .= $this->close_tag($label_tag);

            $out .= $this->close_tag('div');
        }
        return $out;
    }

    protected function create_view_multiselect($name, $value = '', $tag = array())
    {
        $out = array();
        $values = $this->parse_comma_separated($value);
        foreach ($values as $val) {
            if (is_array($this->field_attr[$name]['values'])) {
                if (is_array(reset($this->field_attr[$name]['values']))) {
                    foreach ($this->field_attr[$name]['values'] as $tmp) {
                        if (isset($tmp[$val])) {
                            $out[] = $tmp[$val];
                        }
                    }
                } else {
                    if (isset($this->field_attr[$name]['values'][$val])) {
                        $out[] = $this->field_attr[$name]['values'][$val];
                    }
                }
            } else {
                $out[] = $val;
            }
        }
        return implode(', ', $out);
    }

    protected function create_view_checkboxes($name, $value = '', $tag = array())
    {
        return $this->create_view_multiselect($name, $value, $tag);
    }

    protected function create_hidden($name, $value = '', $tag = array())
    {
        return $this->single_tag($tag + array(
            'tag' => 'input',
            'type' => 'hidden',
            'value' => $value
        ), 'cCrud-input', $this->field_attr[$name]);
    }

    protected function create_password($name, $value = '', $tag = array())
    {
        if (isset($this->subselect[$name])) {
            return $this->create_view_password($name, $value, $tag);
        }
        $tag = $tag + array(
            'tag' => 'input',
            'type' => 'password',
            'data-type' => 'password',
            'value' => '',
            'placeholder' => $value ? '*****' : ''
        );

        return $this->single_tag($tag, $this->theme_config('password_field'), $this->field_attr[$name]);
    }

    protected function create_view_password($name, $value = '', $tag = array())
    {
        return '*****';
    }

    /**
     *
     * @author Ariel Canal
     *         Função alterada do padrão.
     *         Compatibilizada com relation AJAX.
     *         Compatibilizada com join_relation.
     */
    protected function create_relation($name, $value = '', $tag = array(), $dependval = false)
    {
        if (! isset($this->relation[$name])) {
            return 'Restricted.';
        }
        $out = "";
        // if ($dependval === false &&
        // $this->result_row[$this->relation[$name]['depend_on']])
        // $dependval = $this->result_row[$this->relation[$name]['depend_on']];
        $tag = $tag + array(
            'tag' => 'select',
            'data-type' => 'select'
        );

        if ($this->relation[$name]['multi']) {
            $tag['multiple'] = '';
            $tag['size'] = 10;
            $values = ! is_array($value) ? $this->parse_comma_separated($value) : $value;
            $tag['class'] .= ' ' . $this->theme_config('multiselect_field');
        } else {
            $values = ! is_array($value) ? array(
                $value
            ) : $value;

            $tag['class'] .= ' ' . $this->theme_config('select_field');
        }
        $db = $this->model->db;
        $where_arr = array();
        if ($this->relation[$name]['rel_where']) {
            if (is_array($this->relation[$name]['rel_where'])) {
                foreach ($this->relation[$name]['rel_where'] as $field => $val) {
                    $val = $this->replace_text_variables($val, $this->result_row);
                    $fdata = $this->_parse_field_names($field, 'create_relation', $this->relation[$name]['rel_tbl']);
                    $fitem = reset($fdata);
                    $where_arr[] = $this->_where_field($fitem) . $this->_cond_from_where($field) . $db->escape($val);
                }
            } else {
                $where_arr[] = $this->replace_text_variables($this->relation[$name]['rel_where'], $this->result_row);
            }
        }
        if ($dependval !== false) {
            if (is_array($dependval) && count($dependval)) {
                foreach ($dependval as $k => $v) {
                    $dependval[$k] = $db->escape($v);
                }
                $where_arr[] = $this->_field_from_where($this->relation[$name]['depend_field']) . ' IN ' . "(" . implode(',', $dependval) . ")";
            } else if (is_string($dependval)) {
                // $dependval = $db->escape($dependval);
                $where_arr[] = $this->_field_from_where($this->relation[$name]['depend_field']) . $this->_cond_from_where($this->relation[$name]['depend_field']) . $db->escape($dependval);
            }
        }

        if ($where_arr)
            $where = 'WHERE ' . implode(' AND ', $where_arr);
        else
            $where = '';

        if (is_array($this->relation[$name]['rel_name'])) {
            $name_select = 'CONCAT_WS(' . $db->escape($this->relation[$name]['rel_separator']) . ',`' . implode('`,`', $this->relation[$name]['rel_name']) . '`) AS `name`';
        } else {
            $name_select = '`' . $this->relation[$name]['rel_name'] . '` AS `name`';
        }
        $result = $db->query('SELECT COUNT(*) as total FROM `' . $this->relation[$name]['rel_tbl'] . '` ' . $where);
        $total = $result->getRowArray();
        unset($this->field_attr[$name]['data-relationajax']);
        $this->field_attr[$name]['class'] = $tag['class'];
        if ($total['total'] >= $this->config->relation_ajax) {
            $this->field_attr[$name]['data-relationajax'] = $this->fieldname_encode($name);
            $this->field_attr[$name]['class'] = $tag['class'] . ' select2-ajax';
        }
        if (array_key_exists($name, $this->join_relation) && is_array($this->join_relation[$name]['rel_additional_fields']) && count($this->join_relation[$name]['rel_additional_fields'])) {
            $this->field_attr[$name]['class'] .= " join_relation";
            $this->field_attr[$name]['data-jr_field'] = base64_encode($name);
        }
        $out .= $this->open_tag($tag, $this->theme_config('relation_field'));

        if ($this->relation[$name]['depend_on'] && $dependval === false) {
            $options = false;
            // if ($this->lists_null_opt) {
            foreach ($values as $val) {
                $out .= $this->open_tag(array(
                    'tag' => 'option',
                    'value' => $val,
                    'selected' => ''
                )) . $this->lang('null_option') . $this->close_tag('option');
            }
            // }
        } else {
            if ($total['total'] > $this->config->relation_ajax) {
                $where = "WHERE `" . $this->relation[$name]['rel_field'] . "` IS NULL";
            }
            $vals = implode("','", $values);
            if (is_array($values) && $vals != "") {
                $or_values = '(`' . $this->relation[$name]['rel_field'] . '` IN (\'' . $vals . '\') ';
                if ($dependval !== false && is_string($dependval)) {
                    $or_values .= " AND " . $this->_field_from_where($this->relation[$name]['depend_field']) . $this->_cond_from_where($this->relation[$name]['depend_field']) . $db->escape($dependval);
                } elseif ($dependval !== false && is_array($dependval)) {
                    $or_values .= " AND " . $this->_field_from_where($this->relation[$name]['depend_field']) . ' IN  (' . implode(',', $dependval) . ')';
                }
                $or_values .= ")";

                if ($where != "") {
                    $where = "WHERE (" . substr($where, 6) . ") OR " . $or_values;
                } else if ($where_arr) {
                    $where = "WHERE " . $or_values;
                }
            }
            $join = '';
            if (is_array($this->relation[$name]['join'])) {
                foreach ($this->relation[$name]['join'] as $params) {
                    if (! is_null($params['lfield']) && ! is_null($params['table']) && ! is_null($params['rfield'])) {
                        $join .= 'INNER JOIN `' . $params['table'] . '` AS '.md5($params['table']).' ON `' . $this->relation[$name]['rel_tbl'] . '`.`' . $params['lfield'] . '` = `' . md5($params['table']) . '`.`' . $params['rfield'] . '` ';
                    }
                }
            }
            $query = 'SELECT `' . $this->relation[$name]['rel_field'] . '` AS `field`,' . $name_select . $this->get_relation_tree_fields($this->relation[$name]) . ' FROM `' . $this->relation[$name]['rel_tbl'] . '` ' . $join . ' ' . $where . ' GROUP BY `field` ORDER BY ' . $this->get_relation_ordering($this->relation[$name]);

            $result = $db->query($query);
            $options = $this->resort_relation_opts($result->getResultArray(), $this->relation[$name]);
            if ($this->lists_null_opt) {
                $out .= $this->open_tag(array(
                    'tag' => 'option',
                    'value' => ''
                )) . $this->lang('null_option') . $this->close_tag('option');
            }
        }
        if ($options) {
            foreach ($options as $opt) {
                unset($attr_opt);
                $attr_opt['value'] = $opt['field'];
                if (in_array($opt['field'], $values)) {
                    $attr_opt['selected'] = "selected";
                }
                $out .= $this->open_tag('option', '', $attr_opt) . \esc($opt['name']) . $this->close_tag('option');
            }
        }
        $out .= $this->close_tag($tag);
        unset($options);
        return $out;
    }

    protected function create_view_relation($name, $value = '', $tag = array(), $dependval = false)
    {
        if ($value === null || $value === '') {
            return '';
        }
        $db = $this->model->db;
        if (is_array($this->relation[$name]['rel_name'])) {
            $field = 'CONCAT_WS(' . $db->escape($this->relation[$name]['rel_separator']) . ',`' . implode('`,`', $this->relation[$name]['rel_name']) . '`) as `name`';
        } else {
            $field = '`' . $this->relation[$name]['rel_name'] . '` as `name`';
        }
        if ($this->relation[$name]['multi']) {
            $values = $this->parse_comma_separated($value);
            foreach ($values as $key => $val) {
                $values[$key] = $db->escape($val);
            }
            $where = 'IN(' . implode(',', $values) . ')';
        } else {
            $where = ' = ' . $db->escape($value);
        }
        $result = $db->query('SELECT ' . $field . ' FROM `' . $this->relation[$name]['rel_tbl'] . '` WHERE `' . $this->relation[$name]['rel_field'] . '` ' . $where . ' GROUP BY `' . $this->relation[$name]['rel_field'] . '`');
        $options = $result->getResultArray();
        $out = array();
        foreach ($options as $opt) {
            $out[] = $opt['name'];
        }
        return implode(', ', $out);
    }

    protected function get_relation_ordering($rel)
    {
        if ($rel['tree'] && isset($rel['tree']['left_key']) && isset($rel['tree']['level_key'])) {
            return '`' . $rel['tree']['left_key'] . '` ASC';
        } elseif ($rel['tree'] && isset($rel['tree']['parent_key']) && isset($rel['tree']['primary_key'])) {
            return ($rel['order_by'] ? $rel['order_by'] : '`name` ASC');
        } elseif ($rel['order_by']) {
            return $rel['order_by'];
        } else
            return '`name` ASC';
    }

    protected function get_relation_tree_fields($rel)
    {
        if ($rel['tree'] && isset($rel['tree']['left_key']) && isset($rel['tree']['level_key'])) {
            return ',`' . $rel['tree']['left_key'] . '`,`' . $rel['tree']['level_key'] . '`';
        } elseif ($rel['tree'] && isset($rel['tree']['parent_key']) && isset($rel['tree']['primary_key'])) {
            return ',`' . $rel['tree']['parent_key'] . '` AS `pk`, `' . $rel['tree']['primary_key'] . '` AS `pri`';
        } else
            return '';
    }

    protected function resort_relation_opts($options, $rel)
    {
        if ($rel['tree'] && isset($rel['tree']['left_key']) && isset($rel['tree']['level_key'])) {
            foreach ($options as $key => $opt) {
                $level = (int) $opt[$rel['tree']['level_key']];
                $out = '';
                for ($i = 0; $i < $level; ++ $i) {
                    $out .= '. ';
                }
                if ($out)
                    $out .= ' └ ';
                $out .= $opt['name'];
                $options[$key]['name'] = $out;
            }
        } elseif ($rel['tree'] && isset($rel['tree']['parent_key']) && isset($rel['tree']['primary_key'])) {
            $opts_multiarr = array();
            foreach ($options as $key => $opt) {
                $opt['children'] = array();
                $opts_multiarr[] = $opt;
            }
            foreach ($opts_multiarr as $key => $opt) {
                $this->recursive_push($opts_multiarr, $opts_multiarr[$key]);
            }
            $new_opts = array();
            $this->recursive_opts($new_opts, $opts_multiarr, 0);
            $options = $new_opts;
        }
        return $options;
    }

    protected function recursive_push(&$options, &$insert)
    {
        foreach ($options as $key => $opt) {
            if (! $opt) {
                continue;
            }
            if ($opt['pri'] == $insert['pk']) {
                $options[$key]['children'][] = $insert;
                $insert = null;
            } elseif ($options[$key]['children']) {
                $this->recursive_push($options[$key]['children'], $insert);
            }
        }
    }

    protected function recursive_opts(&$options, $array, $level)
    {
        $level = $level + 1;
        foreach ($array as $opt) {
            if (! $opt) {
                continue;
            }
            $out = '';
            for ($i = 1; $i < $level; ++ $i) {
                $out .= '. ';
            }
            if ($out)
                $out .= ' └ ';
            $opt['name'] = $out . $opt['name'];
            $options[] = $opt;
            if (count($opt['children'])) {
                $this->recursive_opts($options, $opt['children'], $level);
            }
        }
    }

    protected function create_fk_relation($name, $value = '', $tag = array())
    {
        if (! isset($this->fk_relation[$name])) {
            return 'Restricted.';
        }
        $out = '';
        $tag = $tag + array(
            'tag' => 'select',
            'data-type' => 'select',
            'multiple' => '',
            'size' => 10
        );
        $tag['class'] .= ' ' . $this->theme_config('multiselect_field');
        $values = $this->parse_comma_separated($value);

        $db = $this->model->db;
        $where_arr = array();
        if ($this->fk_relation[$name]['rel_where']) {
            if (is_array($this->fk_relation[$name]['rel_where'])) {
                foreach ($this->fk_relation[$name]['rel_where'] as $field => $val) {
                    $val = $this->replace_text_variables($val, $this->result_row);
                    $fitem = reset($this->_parse_field_names($field, 'create_fk_relation', $this->fk_relation[$name]['rel_tbl']));
                    $where_arr[] = $this->_where_field($fitem) . $this->_cond_from_where($field) . $db->escape($val);
                }
            } else {
                $where_arr[] = $this->replace_text_variables($this->fk_relation[$name]['rel_where'], $this->result_row);
            }
        }
        $out .= $this->open_tag($tag, '', $this->field_attr[$name]);

        if ($where_arr)
            $where = 'WHERE ' . implode(' AND ', $where_arr);
        else
            $where = '';

        if (is_array($this->fk_relation[$name]['rel_name'])) {
            $optnames = array();
            foreach ($this->fk_relation[$name]['rel_name'] as $optnms) {
                $optnames[] = '`' . $this->fk_relation[$name]['rel_tbl'] . '`.`' . $optnms . '`';
            }
            $name_select = 'CONCAT_WS(' . $db->escape($this->fk_relation[$name]['rel_separator']) . ',' . implode(',', $optnames) . ') AS `name`';
        } else {
            $name_select = '`' . $this->fk_relation[$name]['rel_tbl'] . '`.`' . $this->fk_relation[$name]['rel_name'] . '` AS `name`';
        }

        if ($this->fk_relation[$name]['rel_orderby']) {
            $order_by = $this->fk_relation[$name]['rel_orderby'];
        } else {
            $order_by = '`name` ASC';
        }

        $result = $db->query('SELECT `' . $this->fk_relation[$name]['rel_tbl'] . '`.`' . $this->fk_relation[$name]['rel_field'] . '` AS `field`,' . $name_select . ' FROM `' . $this->fk_relation[$name]['rel_tbl'] . '` ' . $where . ' GROUP BY `field` ORDER BY ' . $order_by);
        $options = $result->getResultArray();

        if ($this->lists_null_opt) {
            $out .= $this->open_tag(array(
                'tag' => 'option',
                'value' => ''
            )) . $this->lang('null_option') . $this->close_tag('option');
        }
        if ($options) {
            foreach ($options as $opt) {
                $opt_tag = array(
                    'tag' => 'option',
                    'value' => $opt['field']
                );
                if (in_array($opt['field'], $values)) {
                    $opt_tag['selected'] = "";
                }
                $out .= $this->open_tag($opt_tag) . \esc($opt['name']) . $this->close_tag($opt_tag);
            }
        }
        $out .= $this->close_tag($tag);
        unset($options);
        return $out;
    }

    protected function create_view_fk_relation($name, $value = '', $tag = array())
    {
        if (! isset($this->fk_relation[$name])) {
            return 'Restricted.';
        }

        if (! $value) {
            return '';
        }

        $db = $this->model->db;
        if (is_array($this->fk_relation[$name]['rel_name'])) {
            $optnames = array();
            foreach ($this->fk_relation[$name]['rel_name'] as $optnms) {
                $optnames[] = '`' . $this->fk_relation[$name]['rel_tbl'] . '`.`' . $optnms . '`';
            }
            $name_select = 'CONCAT_WS(' . $db->escape($this->fk_relation[$name]['rel_separator']) . ',' . implode(',', $optnames) . ') AS `name`';
        } else {
            $name_select = '`' . $this->fk_relation[$name]['rel_tbl'] . '`.`' . $this->fk_relation[$name]['rel_name'] . '` AS `name`';
        }

        $values = $this->parse_comma_separated($value);
        foreach ($values as $key => $val) {
            $values[$key] = $db->escape($val);
        }
        $where = 'IN(' . implode(',', $values) . ')';

        if ($this->fk_relation[$name]['rel_orderby']) {
            $order_by = $this->fk_relation[$name]['rel_orderby'];
        } else {
            $order_by = '`name` ASC';
        }

        $result = $db->query('SELECT `' . $this->fk_relation[$name]['rel_tbl'] . '`.`' . $this->fk_relation[$name]['rel_field'] . '` AS `field`,' . $name_select . ' FROM `' . $this->fk_relation[$name]['rel_tbl'] . '` WHERE `' . $this->fk_relation[$name]['rel_tbl'] . '`.`' . $this->fk_relation[$name]['rel_field'] . '` ' . $where . ' GROUP BY `field` ORDER BY ' . $order_by);

        $options = $result->getResultArray();
        $out = array();
        foreach ($options as $opt) {
            $out[] = $opt['name'];
        }
        return implode(', ', $out);
    }

    protected function create_file($name, $value = '', $tag = array(), $is_upload = false)
    {
        if (isset($this->subselect[$name])) {
            return $this->create_view_file($name, $value, $tag);
        }
        $out = ''; // upload container
        $out .= $this->open_tag('div', 'cCrud-upload-container'); // file and
                                                                  // delete button
        $out .= $this->open_tag('div', $this->theme_config('grid_button_group'));
        if ($value) {
            // $out .= $this->open_tag('span', 'cCrud-file-name');
            $binary = isset($this->upload_config[$name]['blob']) ? true : false;
            if ($binary && ! $is_upload) {
                $file_size = $this->_file_size_bin($value);
                $value = 'blob-storage';
                $ext = 'binary';
            } else {
                $path = $this->get_image_folder($name);
                $file_size = $this->_file_size($path . '/' . $value);
                $ext = trim(strtolower(strrchr($value, '.')), '.');
            }

            $attr = array(
                'href' => (isset($this->upload_config[$name]['url']) ? $this->real_file_link($value, $this->upload_config[$name], true) : $this->file_link($name, $this->primary_val)),
                'class' => 'cCrud-file-name cCrud-' . $ext,
                'target' => '_blank'
            );

            $out .= $this->open_tag('a', $this->theme_config('file_name'), $attr);
            $out .= $this->open_tag('strong');
            $out .= (isset($this->upload_config[$name]['text']) ? $this->upload_config[$name]['text'] : $this->filter_file_name($value));
            $out .= $this->close_tag('strong');
            $out .= ' ' . $file_size . $this->close_tag('a');

            // $out .= $this->close_tag('span');
            if (! isset($tag['readonly']) && ! isset($tag['disabled'])) {
                $out .= $this->remove_upload_button($name);
            }
        } else {
            $out .= $this->open_tag('span', 'cCrud-nofile ' . $this->theme_config('no_file'));
            $out .= $this->lang('no_file') . $this->close_tag('span');
        }

        if (! isset($tag['readonly']) && ! isset($tag['disabled'])) {
            // hidden field
            $attr = $tag + array(
                'value' => $value,
                'type' => 'hidden'
            );
            $out .= $this->single_tag('input', 'cCrud-input', $attr);
            // upload button
            $out .= $this->upload_file_button($name, $value, $tag);
        }

        // close upload container
        $out .= $this->close_tag('div');
        $out .= $this->close_tag('div');

        return $out;
    }

    protected function upload_file_button($name, $value, $tag = array())
    {
        $out = '';
        $out .= $this->open_tag('span', $this->theme_config('upload_button'), array(
            'class' => 'cCrud-add-file'
        ));
        if ($this->theme_config('upload_button_icon')) {
            $out .= $this->open_tag('i', $this->theme_config('upload_button_icon')) . $this->close_tag('i') . ' ';
        }
        if ($value) {
            $out .= $this->lang('replace_file');
        } else {
            $out .= $this->lang('add_file');
        }
        $attr = array(
            'id' => 'xfupl' . rand(),
            'value' => '',
            'type' => 'file',
            'data-type' => 'file',
            'data-field' => $name,
            'class' => 'cCrud-upload',
            'name' => 'cCrud-attach'
        );
        if (isset($tag['data-required']) && ! $value) {
            $attr['data-required'] = '';
        }
        $out .= $this->single_tag('input', '', $attr);
        $out .= $this->close_tag('span');
        return $out;
    }

    protected function upload_image_button($name, $value, $tag = array())
    {
        $out = '';
        $out .= $this->open_tag('span', $this->theme_config('upload_button'), array(
            'class' => 'cCrud-add-file'
        ));
        if ($this->theme_config('upload_button_icon')) {
            $out .= $this->open_tag('i', $this->theme_config('upload_button_icon')) . $this->close_tag('i') . ' ';
        }
        if ($value) {
            $out .= $this->lang('replace_image');
        } else {
            $out .= $this->lang('add_image');
        }
        $attr = array(
            'id' => 'xfupl' . rand(),
            'value' => '',
            'type' => 'file',
            'data-type' => 'image',
            'data-field' => $name,
            'class' => 'cCrud-upload',
            'accept' => 'image/jpeg,image/png,image/gif',
            'name' => 'cCrud-attach',
            'capture' => 'camera'
        );
        if (isset($tag['data-required']) && ! $value) {
            $attr['data-required'] = '';
        }
        $out .= $this->single_tag('input', '', $attr);
        $out .= $this->close_tag('span');
        return $out;
    }

    protected function remove_upload_button($name)
    {
        $out = '';
        $attr = array(
            'href' => 'javascript:;',
            'class' => 'cCrud-remove-file',
            'data-field' => $name
        );
        $out .= $this->open_tag('a', $this->theme_config('remove_button'), $attr);
        if ($this->theme_config('remove_button_icon')) {
            $out .= $this->open_tag('i', $this->theme_config('remove_button_icon')) . $this->close_tag('i') . ' ';
        }
        $out .= $this->lang('remove');
        $out .= $this->close_tag('a');
        return $out;
    }

    protected function create_view_file($name, $value = '', $tag = array(), $is_upload = false)
    {
        $out = '';
        if ($value) {
            $binary = isset($this->upload_config[$name]['blob']) ? true : false;
            if ($binary && ! $is_upload) {
                $file_size = $this->_file_size_bin($value);
                $value = 'blob-storage';
                $ext = 'binary';
            } else {
                $path = $this->get_image_folder($name);
                $file_size = $this->_file_size($path . '/' . $value);
                $ext = trim(strtolower(strrchr($value, '.')), '.');
            }
            $attr = array(
                'href' => isset($this->upload_config[$name]['url']) ? $this->real_file_link($value, $this->upload_config[$name], true) : $this->file_link($name, $this->primary_val),
                'class' => 'cCrud-file cCrud-' . $ext,
                'target' => '_blank'
            );
            $out .= $this->open_tag('span', 'cCrud-file-name');
            $out .= $this->open_tag('a', '', $attr);
            $out .= (isset($this->upload_config[$name]['text']) ? $this->upload_config[$name]['text'] : $value) . $this->close_tag('a');
            $out .= ' ' . $file_size;
            $this->close_tag('span');
        } else {
            $out .= $this->open_tag('span', 'cCrud-nofile');
            $out .= $this->lang('no_file') . $this->close_tag('span');
        }
        return $out;
    }

    protected function create_image($name, $value = '', $tag = array(), $is_upload = false)
    {
        if (isset($this->subselect[$name])) {
            return $this->create_view_image($name, $value, $tag = array());
        }
        $out = ''; // upload container
        $out .= $this->open_tag('div', 'cCrud-upload-container'); // image and
                                                                  // delete button
        if ($value) {
            $binary = isset($this->upload_config[$name]['blob']) ? true : false;
            if ($binary && ! $is_upload) {
                $value = 'blob-storage';
            } else {}
            $attr = array(
                'src' => isset($this->upload_config[$name]['url']) ? $this->real_file_link($value, $this->upload_config[$name], true) : $this->file_link($name, $this->primary_val, (isset($this->upload_config[$name]['detail_thumb']) ? $this->upload_config[$name]['detail_thumb'] : false), false, $value),
                'alt' => ''
            );
            $out .= $this->single_tag('img', $this->theme_config('image'), $attr);

            if (! isset($tag['readonly']) && ! isset($tag['disabled'])) {
                $out .= $this->open_tag('div', $this->theme_config('grid_button_group'));
                // delete button
                $out .= $this->remove_upload_button($name);
            }
        } else {
            $out .= $this->open_tag('div', $this->theme_config('grid_button_group'));
            $out .= $this->open_tag('span', 'cCrud-noimage ' . $this->theme_config('no_file'));
            $out .= $this->lang('no_image') . $this->close_tag('span') . $this->close_tag('div');
        }

        if (! isset($tag['readonly']) && ! isset($tag['disabled'])) {
            // hidden field
            $attr = $tag + array(
                'value' => $value,
                'type' => 'hidden'
            );
            $out .= $this->single_tag('input', 'cCrud-input', $attr, true);
            // upload button
            $out .= $this->upload_image_button($name, $value, $tag);
            // close upload container
            if ($value) {
                $out .= $this->close_tag('div');
            }
        }
        $out .= $this->close_tag('div');

        return $out;
    }

    protected function create_view_image($name, $value = '', $tag = array())
    {
        $out = ''; // image and delete button
        if ($value) {
            /*
             * $binary = isset($this->upload_config[$name]['blob']) ? true :
             * false;
             * if ($binary)
             * {
             * $value = 'blob-storage';
             * }
             * else
             * {
             * }
             */
            $attr = array(
                'src' => isset($this->upload_config[$name]['url']) ? $this->real_file_link($value, $this->upload_config[$name], true) : $this->file_link($name, $this->primary_val, (isset($this->upload_config[$name]['detail_thumb']) ? $this->upload_config[$name]['detail_thumb'] : false), false, $value),
                'alt' => ''
            );
            $out .= $this->single_tag('img', $this->theme_config('image'), $attr);
        } else {
            $out .= $this->open_tag('span', 'cCrud-noimage');
            $out .= $this->lang('no_image') . $this->close_tag('span');
        }

        return $out;
    }

    protected function create_binary($name, $value = '', $tag = array())
    {
        return $value ? '[binary data]' : '[binary null]';
    }

    protected function create_view_binary($name, $value = '', $tag = array())
    {
        return $value ? '[binary data]' : '[binary null]';
    }

    protected function create_remote_image($name, $value = '', $tag = array())
    {
        if (isset($this->subselect[$name])) {
            return $this->create_view_remote_image($name, $value, $tag);
        }
        $out = '';

        $attr = $this->field_attr[$name];

        $tag = $tag + array(
            'tag' => 'input',
            'type' => 'text',
            'value' => $value
        );

        if ($value) {
            $img = array(
                'tag' => 'img',
                'alt' => '',
                'src' => $attr['link'] . $value
            );
            $out .= $this->single_tag($img, $this->theme_config('remote_image'));
        }
        unset($attr['link']);
        $out .= $this->single_tag($tag, $this->theme_config('remote_image_field'), $attr, true);
        return $out;
    }

    protected function create_view_remote_image($name, $value = '', $tag = array())
    {
        if ($value) {
            $attr = $this->field_attr[$name];
            $img = array(
                'tag' => 'img',
                'alt' => '',
                'src' => $attr['link'] . $value
            );
            return $this->single_tag($img, $this->theme_config('remote_image'));
        }
    }

    protected function create_point($name, $value = '', $tag = array())
    {
        if (isset($this->subselect[$name])) {
            return $this->create_view_point($name, $value, $tag);
        }
        $out = '';
        $attr = $this->field_attr[$name];
        if (! $value) {
            $value = $this->config->default_point ? $this->config->default_point : '0,0';
        }

        $tag = $tag + array(
            'tag' => 'input',
            'value' => $value,
            'data-type' => 'point'
        );
        if ($attr['search']) {
            $search = array(
                'tag' => 'input',
                'type' => 'text',
                'autocomplete' => 'off',
                'placeholder' => $this->lang($attr['search_text']),
                'name' => $this->fieldname_encode($name . '.search'),
                'class' => 'cCrud-map-search cCrud-input'
            );
            if (isset($this->disabled[$name])) {
                $search['disabled'] = '';
            }
        } else {
            $search = false;
        }

        if ($attr['coords']) {
            $tag['type'] = 'text';
        } else {
            $tag['type'] = 'hidden';
        }

        $map = array(
            'tag' => 'div',
            'class' => 'cCrud-map',
            'data-text' => $this->lang($attr['text']),
            'data-zoom' => $attr['zoom'],
            'style' => 'width:' . $attr['width'] . 'px;height:' . $attr['height'] . 'px;'
        );

        if (isset($this->readonly[$name]) or isset($this->disabled[$name])) {
            $map['data-draggable'] = '0';
        } else {
            $map['data-draggable'] = '1';
        }
        unset($attr['text'], $attr['zoom'], $attr['width'], $attr['height'], $attr['search_text']);

        $out .= $this->single_tag($tag, $this->theme_config('point_field'), $attr);
        if ($search) {
            $out .= $this->single_tag($search, $this->theme_config('point_search'));
        }
        $out .= $this->open_tag($map, $this->theme_config('point_map')) . $this->close_tag($map);

        return $out;
    }

    protected function create_view_point($name, $value = '', $tag = array())
    {
        $out = '';
        $attr = $this->field_attr[$name];
        if (! $value) {
            $value = $this->config->default_point;
        }
        if ($value) {
            $tag = array(
                'tag' => 'input',
                'value' => $value,
                'class' => 'cCrud-input',
                'data-type' => 'point',
                'type' => 'hidden'
            );
            $map = array(
                'tag' => 'img',
                'class' => 'cCrud-map',
                'style' => 'width:' . $attr['width'] . 'px;height:' . $attr['height'] . 'px;',
                'src' => 'https://maps.googleapis.com/maps/api/staticmap?center=' . $value . '&zoom=' . $attr['zoom'] . '&size=' . '2000x' . $attr['height'] . '&maptype=roadmap&markers=color:red%7C' . $value . '&key=' . $this->config->maps_api_key
            );
            unset($attr['text'], $attr['zoom'], $attr['width'], $attr['height'], $attr['search_text']);
            $out .= $this->single_tag($tag, $this->theme_config('point_field'), $attr);
            $out .= $this->open_tag($map, $this->theme_config('point_map'));
        }
        return $out;
    }

    protected function benchmark_start()
    {
        if ($this->benchmark) {
            $start = explode(' ', microtime());
            $this->time_start = (float) ($start[1] + $start[0]);
            $this->memory_start = memory_get_usage();
        }
    }

    protected function benchmark_end()
    {
        if ($this->benchmark) {
            $end = explode(' ', microtime());
            $this->time_end = (float) ($end[1] + $end[0]);
            $this->memory_end = memory_get_usage();
            $out = '<span>' . $this->lang('exec_time') . ' ' . (number_format($this->time_end - $this->time_start, 3, '.', '')) . ' s</span>';
            $out .= '<span>' . $this->lang('memory_usage') . ' ' . (number_format(($this->memory_end - $this->memory_start) / 1024 / 1024, 2, '.', '')) . ' MB</span>';
            return $out;
        }
    }

    protected function _upload()
    {
        switch ($this->_post('type')) {
            case 'image':
                return $this->_upload_image();
                break;
            case 'file':
                return $this->_upload_file();
                break;
            default:
                // Erro no upload
                return Services::response()->setStatusCode(500)->setBody(lang('cCrud.upload_error'));
                break;
        }
    }

    protected function _clone_file($field, $filename)
    {
        $file_path = $this->get_image_folder($field);
        if (is_file($file_path . '/' . $filename)) {
            $new_filename = substr_replace($filename, substr(base64_encode($filename . time()), 0, 8), strpos($filename, '.'), 0);
            copy($file_path . '/' . $filename, $file_path . '/' . $new_filename);
            $filename = $new_filename;
        }
        return $filename;
    }

    protected function _upload_file()
    {
        $field   = $this->_post('field');
        $oldfile = $this->_post('oldfile', 0);
        $request = Services::request();
        $file    = $request->getFile('cCrud-attach');
        if ($file && $file->isValid()) {
            $fileData = [
                'name'     => $file->getName(),
                'tmp_name' => $file->getTempName(),
            ];
            $this->check_file_folders($field);
            $filename = $this->safe_file_name($fileData, $field);
            $filename = $this->get_filename_noconfict($filename, $field);

            if ($this->before_upload) {
                $path = $this->check_file($this->before_upload['path'], 'before_upload');
                include_once ($path);
                $callable = $this->before_upload['callable'];
                if (is_callable($callable)) {
                    call_user_func_array($callable, array(
                        $field,
                        $filename,
                        $this->upload_config[$field],
                        $this
                    ));
                    if ($this->exception) {
                        $out = $this->call_exception();
                        $this->after_render();
                        return $out;
                    }
                }
            }

            $this->save_file($fileData, $filename, $field);
            if ($this->exception) {
                $out = $this->call_exception();
                $this->upload_to_remove[$oldfile] = $field;
                $this->after_render();
                return $out;
            }
            if ($oldfile != $filename)
                $this->upload_to_remove[$oldfile] = $field;
            $this->upload_to_save[$filename] = $field;
            $attr = $this->get_field_attr($field, $this->_post('mode', 0));
            $out = $this->create_file($field, $filename, $attr, true);
            $this->after_render();
            return $out;
        } else
            // Arquivo não foi enviado
            return Services::response()->setStatusCode(400)->setBody(lang('cCrud.file_not_uploaded'));
    }

    protected function _upload_image()
    {
        $field   = $this->_post('field');
        $oldfile = $this->_post('oldfile', 0);
        $request = Services::request();
        $file    = $request->getFile('cCrud-attach');
        if ($file && $file->isValid()) {
            $fileData = [
                'name'     => $file->getName(),
                'tmp_name' => $file->getTempName(),
            ];
            $this->check_file_folders($field);
            $filename = $this->safe_file_name($fileData, $field);
            $filename = $this->get_filename_noconfict($filename, $field);

            if ($this->before_upload) {
                $path = $this->check_file($this->before_upload['path'], 'before_upload');
                include_once ($path);
                $callable = $this->before_upload['callable'];
                if (is_callable($callable)) {
                    call_user_func_array($callable, array(
                        $field,
                        $filename,
                        $this->upload_config[$field],
                        $this
                    ));
                }
                if ($this->exception) {
                    $out = $this->call_exception();
                    $this->after_render();
                    return $out;
                }
            }

            if ($oldfile != $filename) {
                $this->upload_to_remove[$oldfile] = $field;
            }
            $this->upload_to_save[$filename] = $field;
            if ($this->is_resize($field)) {
                $this->save_file_to_tmp($fileData, $filename, $field);
                if ($this->exception) {
                    $out = $this->call_exception();
                    $this->after_render();
                    return $out;
                }
                if ($this->is_manual_crop($field)) {
                    // $this->make_bg($filename, $field);
                    $out = $this->render_crop_window($filename, $field);
                } else {
                    $this->make_autoresize($filename, $field);
                    $this->remove_tmp_image($filename, $field);
                    if ($this->exception) {
                        $out = $this->call_exception();
                        $this->after_render();
                        return $out;
                    }
                    // $this->render_image_field($filename, $field);
                    $attr = $this->get_field_attr($field, $this->_post('mode', 0));
                    $out = $this->create_image($field, $filename, $attr, true);
                }
            } else {
                // $this->save_file($fileData, $filename, $field);
                // //$this->render_image_field($filename, $field);
                $this->save_file_to_tmp($fileData, $filename, $field);
                if ($this->exception) {
                    $out = $this->call_exception();
                    $this->after_render();
                    return $out;
                }
                $this->filter_image($filename, $field);
                $this->remove_tmp_image($filename, $field);
                if ($this->exception) {
                    $out = $this->call_exception();
                    $this->after_render();
                    return $out;
                }
                $attr = $this->get_field_attr($field, $this->_post('mode', 0));
                $out = $this->create_image($field, $filename, $attr, true);
            }
            $this->after_render();
            return $out;
        } else
            // Arquivo não foi enviado
            return Services::response()->setStatusCode(400)->setBody(lang('cCrud.file_not_uploaded'));
    }

    protected function render_crop_window($filename, $field)
    {
        $out = ''; // upload container
        $out .= $this->open_tag('div', 'cCrud-upload-container');
        $tmp_name = substr($filename, 0, strrpos($filename, '.')) . '.tmp';
        if (isset($this->labels[$field]))
            $title = \esc($this->labels[$field]);
        else {
            list ($tmp, $fieldname) = explode('.', $field);
            $title = \esc($this->_humanize($fieldname));
        }
        $path = $this->get_image_folder($field) . '/' . $tmp_name;
        list ($width, $height) = getimagesize($path);
        $ratio = isset($this->upload_config[$field]['ratio']) ? $this->upload_config[$field]['ratio'] : '';
        $attr = array(
            'src' => $this->file_link($field, $this->primary_val, false, true),
            'title' => $title,
            'data-width' => $width,
            'data-height' => $height,
            'data-ratio' => $ratio,
            'style' => 'display:none;max-width:none;',
            'alt' => ''
        );
        $out .= $this->single_tag('img', 'cCrud-crop', $attr);
        /*
         * $out .= $this->single_tag('input', 'new_key', array(
         * 'name' => 'new_key',
         * 'value' => $this->key,
         * 'type' => 'hidden'));
         */
        $out .= $this->single_tag('input', 'xrud-crop-data', array(
            'name' => 'field',
            'value' => $field,
            'type' => 'hidden'
        ));
        $out .= $this->single_tag('input', 'xrud-crop-data', array(
            'name' => 'filename',
            'value' => $filename,
            'type' => 'hidden'
        ));
        $out .= $this->single_tag('input', 'xrud-crop-data', array(
            'name' => 'x',
            'value' => 0,
            'type' => 'hidden'
        ));
        $out .= $this->single_tag('input', 'xrud-crop-data', array(
            'name' => 'y',
            'value' => 0,
            'type' => 'hidden'
        ));
        $out .= $this->single_tag('input', 'xrud-crop-data', array(
            'name' => 'x2',
            'value' => 0,
            'type' => 'hidden'
        ));
        $out .= $this->single_tag('input', 'xrud-crop-data', array(
            'name' => 'y2',
            'value' => 0,
            'type' => 'hidden'
        ));
        $out .= $this->single_tag('input', 'xrud-crop-data', array(
            'name' => 'w',
            'value' => 0,
            'type' => 'hidden'
        ));
        $out .= $this->single_tag('input', 'xrud-crop-data', array(
            'name' => 'h',
            'value' => 0,
            'type' => 'hidden'
        ));
        $out .= $this->close_tag('div');
        return $out;
    }

    protected function filter_image($filename, $field)
    {
        $tmp_name = substr($filename, 0, strrpos($filename, '.')) . '.tmp';
        $settings = $this->upload_config[$field];
        $tmp_path = $this->get_image_folder($field) . '/' . $tmp_name;
        $file_path = $this->get_image_folder($field) . '/' . $filename;
        $watermark = (isset($settings['watermark']) && $settings['watermark']) ? $this->check_file($settings['watermark'], 'try_crop_image') : false;
        $watermark_position = (isset($settings['watermark_position']) && is_array($settings['watermark_position']) && count($settings['watermark_position'] == 2)) ? array_values($settings['watermark_position']) : array(
            50,
            50
        );
        $quality = (isset($settings['quality']) && $settings['quality']) ? $settings['quality'] : 92;
        $this->_draw_watermark($tmp_path, $file_path, $quality, $watermark, $watermark_position);
        if (isset($settings['thumbs']) && is_array($settings['thumbs'])) {
            foreach ($settings['thumbs'] as $thumb) {
                $thumb_file = $this->get_thumb_path($filename, $field, $thumb);
                $this->_try_change_size($tmp_path, $thumb_file, $field, $thumb);
            }
        }
    }

    protected function make_autoresize($filename, $field)
    {
        $tmp_name = substr($filename, 0, strrpos($filename, '.')) . '.tmp';
        $settings = $this->upload_config[$field];
        $tmp_path = $this->get_image_folder($field) . '/' . $tmp_name;
        $file_path = $this->get_image_folder($field) . '/' . $filename;
        $this->_try_change_size($tmp_path, $file_path, $field, $settings);
        if (isset($settings['thumbs']) && is_array($settings['thumbs'])) {
            foreach ($settings['thumbs'] as $thumb) {
                $thumb_file = $this->get_thumb_path($filename, $field, $thumb);
                $this->_try_change_size($tmp_path, $thumb_file, $field, $thumb);
            }
        }
    }

    protected function manual_crop()
    {
        $field = $this->_post('field');
        $filename = $this->_post('filename');
        $tmp_name = substr($filename, 0, strrpos($filename, '.')) . '.tmp';
        $x = round($this->_post('x'));
        $y = round($this->_post('y'));
        $x2 = round($this->_post('x2'));
        $y2 = round($this->_post('y2'));
        $w = round($this->_post('w'));
        $h = round($this->_post('h'));
        if (! $w or ! $h) {
            $this->remove_tmp_image($filename, $field);
            $this->after_render();
            return $this->create_image($field, '');
        }
        $settings = $this->upload_config[$field];
        $ratio = (isset($settings['ratio']) && ! empty($settings['ratio'])) ? (float) $settings['ratio'] : $w / $h;
        $tmp_path = $this->get_image_folder($field) . '/' . $tmp_name;
        $file_path = $this->get_image_folder($field) . '/' . $filename;
        $this->_try_crop_image($tmp_path, $file_path, $field, $settings, $x, $y, $w, $h, $ratio);
        if (isset($settings['thumbs']) && is_array($settings['thumbs'])) {
            foreach ($settings['thumbs'] as $thumb) {
                $thumb_path = $this->get_thumb_path($filename, $field, $thumb);
                $this->_try_crop_image($tmp_path, $thumb_path, $field, $thumb, $x, $y, $w, $h, $ratio);
            }
        }
        $this->remove_tmp_image($filename, $field);
        $this->after_render();
        return $this->create_image($field, $filename);
    }

    protected function _try_crop_image($tmp_path, $file_path, $field, $settings, $x, $y, $w, $h, $ratio)
    {
        $set_w = (isset($settings['width']) && ! empty($settings['width'])) ? (int) $settings['width'] : false;
        $set_h = (isset($settings['height']) && ! empty($settings['height'])) ? (int) $settings['height'] : false;
        // $set_ratio = (isset($settings['ratio']) &&
        // !empty($settings['ratio'])) ? (float)$settings['ratio'] : false;
        $sizes = $this->_calculate_crop_sizes($w, $h, $set_w, $set_h, $ratio);
        $watermark = (isset($settings['watermark']) && $settings['watermark']) ? $this->check_file($settings['watermark'], 'try_crop_image') : false;
        $watermark_position = (isset($settings['watermark_position']) && is_array($settings['watermark_position']) && count($settings['watermark_position'] == 2)) ? array_values($settings['watermark_position']) : array(
            50,
            50
        );
        $quality = (isset($settings['quality']) && $settings['quality']) ? $settings['quality'] : 92;
        $this->_custom_image_crop($tmp_path, $file_path, $sizes['w'], $sizes['h'], $quality, $x, $y, $w, $h, $watermark, $watermark_position);
    }

    protected function _calculate_crop_sizes($w, $h, $set_w, $set_h, $set_ratio)
    {
        $sizes = array();
        if ($set_w && $set_h) {
            $tmp_ratio = $set_w / $set_h;
            if ($set_ratio > $tmp_ratio) {
                $sizes['w'] = $set_w;
                $sizes['h'] = $set_w / $set_ratio;
            } else {
                $sizes['h'] = $set_h;
                $sizes['w'] = $set_h * $set_ratio;
            }
        } elseif (! $set_w && ! $set_h) {
            $sizes['w'] = $w;
            $sizes['h'] = $h;
        } elseif (! $set_h) {
            $sizes['w'] = $set_w;
            $sizes['h'] = round($set_w / $set_ratio);
        } elseif (! $set_w) {
            $sizes['h'] = $set_h;
            $sizes['w'] = round($set_h * $set_ratio);
        }
        return $sizes;
    }

    protected function _try_change_size($tmp_path, $file_path, $field, $settings)
    {
        $crop = (isset($settings['crop']) && $settings['crop'] == true) ? true : false;
        $width = (isset($settings['width']) && $settings['width']) ? $settings['width'] : false;
        $height = (isset($settings['height']) && $settings['height']) ? $settings['height'] : false;
        $watermark = (isset($settings['watermark']) && $settings['watermark']) ? $this->check_file($settings['watermark'], 'try_change_size') : false;
        $watermark_position = (isset($settings['watermark_position']) && is_array($settings['watermark_position']) && count($settings['watermark_position'] == 2)) ? array_values($settings['watermark_position']) : array(
            50,
            50
        );
        $quality = (isset($settings['quality']) && $settings['quality']) ? $settings['quality'] : 92;
        if ($crop && $width && $height) {
            $this->_image_crop($tmp_path, $file_path, $width, $height, $quality, $watermark, $watermark_position);
        } elseif ($width or $height) {
            $this->_image_resize($tmp_path, $file_path, $width, $height, $quality, $watermark, $watermark_position);
        }
    }

    protected function _remove_upload()
    {
        $type = isset($this->field_type[$this->_post('field')]) ? $this->field_type[$this->_post('field')] : false;
        switch ($type) {
            case 'image':
                return $this->_remove_image();
                break;
            case 'file':
                return $this->_remove_file();
                break;
            default:
                // Erro ao remover arquivo
                return Services::response()->setStatusCode(500)->setBody(lang('cCrud.remove_error'));
                break;
        }
    }

    protected function _remove_file()
    {
        $field = $this->_post('field');
        $file = $this->_post('file');
        $this->upload_to_remove[$file] = $field;
        $this->after_render();
        return $this->create_file($field, '');
    }

    protected function _remove_image()
    {
        $field = $this->_post('field');
        $file = $this->_post('file');
        $this->upload_to_remove[$file] = $field;
        $this->after_render();
        return $this->create_image($field, '');
    }

    protected function _remove_and_save_uploads()
    {
        if (! $this->cancel_file_saving) {
            switch ($this->task) {
                case 'save':
                    if ($this->upload_to_remove) {
                        foreach ($this->upload_to_remove as $file => $field) {
                            if ($file) {
                                $this->remove_file($file, $field);
                            }
                        }
                    }
                    $this->upload_to_save = array();
                    $this->upload_to_remove = array();
                    break;
                case 'list':
                case 'create':
                case 'edit':
                case 'view':
                case '':
                    if ($this->upload_to_save) {
                        foreach ($this->upload_to_save as $file => $field) {
                            $this->remove_file($file, $field);
                        }
                        $f_bak = array();
                        foreach ($this->upload_to_remove as $file => $field) {
                            if (! isset($f_bak[$field])) {
                                $f_bak[$field] = true;
                                continue;
                            }
                            $this->remove_file($file, $field);
                        }
                    }
                    $this->upload_to_save = array();
                    $this->upload_to_remove = array();
                    break;
            }
        } else {
            $this->cancel_file_saving = false;
        }
    }

    protected function _image_resize($src_file, $dest_file, $new_size_w = false, $new_size_h = false, $dest_qual = 92, $watermark = false, $watermark_position = array(
        50,
        50
    ))
    {
        list ($srcWidth, $srcHeight, $type) = getimagesize($src_file);
        switch ($type) {
            case 1:
                $srcHandle = imagecreatefromgif($src_file);
                break;
            case 2:
                $srcHandle = imagecreatefromjpeg($src_file);
                break;
            case 3:
                $srcHandle = imagecreatefrompng($src_file);
                break;
            default:
                // Arquivo inexistente
                return Services::response()->setStatusCode(400)->setBody(lang('cCrud.no_file'));
                return false;
        }
        if ($srcWidth >= $srcHeight) {
            $ratio = (($new_size_w ? $srcWidth : $srcHeight) / ($new_size_w ? $new_size_w : $new_size_h));
            $ratio = max($ratio, 1.0);
            $destWidth = ($srcWidth / $ratio);
            $destHeight = ($srcHeight / $ratio);
            if ($destHeight > $new_size_h) {
                $ratio = ($destHeight / ($new_size_h ? $new_size_h : $new_size_w));
                $ratio = max($ratio, 1.0);
                $destWidth = ($destWidth / $ratio);
                $destHeight = ($destHeight / $ratio);
            }
        } elseif ($srcHeight > $srcWidth) {
            $ratio = (($new_size_h ? $srcHeight : $srcWidth) / ($new_size_h ? $new_size_h : $new_size_w));
            $ratio = max($ratio, 1.0);
            $destWidth = ($srcWidth / $ratio);
            $destHeight = ($srcHeight / $ratio);
            if ($destWidth > $new_size_w) {
                $ratio = ($destWidth / ($new_size_w ? $new_size_w : $new_size_h));
                $ratio = max($ratio, 1.0);
                $destWidth = ($destWidth / $ratio);
                $destHeight = ($destHeight / $ratio);
            }
        }
        $dstHandle = imagecreatetruecolor($destWidth, $destHeight);
        switch ($type) {
            case 1:
                $transparent_source_index = imagecolortransparent($srcHandle);
                if ($transparent_source_index !== - 1) {
                    $transparent_color = imagecolorsforindex($srcHandle, $transparent_source_index);
                    $transparent_destination_index = imagecolorallocate($dstHandle, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                    imagecolortransparent($dstHandle, $transparent_destination_index);
                    imagefill($dstHandle, 0, 0, $transparent_destination_index);
                }
                break;
            case 3:
                imagealphablending($dstHandle, false);
                imagesavealpha($dstHandle, true);
                break;
        }
        imagecopyresampled($dstHandle, $srcHandle, 0, 0, 0, 0, $destWidth, $destHeight, $srcWidth, $srcHeight);
        imagedestroy($srcHandle);
        if ($watermark) {
            list ($water_w, $water_h, $water_type) = getimagesize($watermark);
            $offsets = $this->calculate_watermark_offsets($destWidth, $destHeight, $water_w, $water_h, $watermark_position);
            switch ($water_type) {
                case 1:
                    $waterHandle = imagecreatefromgif($watermark);
                    $transparent_source_index = imagecolortransparent($waterHandle);
                    if ($transparent_source_index !== - 1) {
                        $transparent_color = imagecolorsforindex($waterHandle, $transparent_source_index);
                        $transparent_destination_index = imagecolorallocate($waterHandle, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                        imagecolortransparent($waterHandle, $transparent_destination_index);
                        imagefill($waterHandle, 0, 0, $transparent_destination_index);
                    }
                    break;
                case 2:
                    $waterHandle = imagecreatefromjpeg($watermark);
                    break;
                case 3:
                    $waterHandle = imagecreatefrompng($watermark);
                    imagealphablending($waterHandle, false);
                    imagesavealpha($waterHandle, true);
                    imagealphablending($dstHandle, true);
                    break;
                    break;
                default:
                    // Arquivo de marca d'água inexistente
                    return Services::response()->setStatusCode(404)->setBody(lang('cCrud.no_watermark_file'));
                    return false;
            }
            imagecopy($dstHandle, $waterHandle, $offsets['x'], $offsets['y'], 0, 0, $water_w, $water_h);
            imagedestroy($waterHandle);
        }
        switch ($type) {
            case 1:
                imagegif($dstHandle, $dest_file);
                break;
            case 2:
                imagejpeg($dstHandle, $dest_file, $dest_qual);
                break;
            case 3:
                imagepng($dstHandle, $dest_file);
                break;
            default:
                // Tipo de arquivo não suportado
                return Services::response()->setStatusCode(415)->setBody(lang('cCrud.file_type_not_supported'));
                return false;
        }
        imagedestroy($dstHandle);
        $newimgarray = array(
            $destWidth,
            $destHeight
        );
        return $newimgarray;
    }

    protected function _image_crop($src_file, $dest_file, $new_size_w, $new_size_h, $dest_qual = 92, $watermark = false, $watermark_position = array(
        50,
        50
    ))
    {
        list ($srcWidth, $srcHeight, $type) = getimagesize($src_file);
        switch ($type) {
            case 1:
                $srcHandle = imagecreatefromgif($src_file);
                break;
            case 2:
                $srcHandle = imagecreatefromjpeg($src_file);
                break;
            case 3:
                $srcHandle = imagecreatefrompng($src_file);
                break;
            default:
                // Arquivo inexistente
                return Services::response()->setStatusCode(400)->setBody(lang('cCrud.no_file'));
                return false;
        }
        if (! $srcHandle) {
            // Falha ao executar imagecreatefrom()
            return Services::response()->setStatusCode(500)->setBody(lang('cCrud.imagecreatefrom_failed'));
            return false;
        }
        if ($srcHeight < $srcWidth) {
            $ratio = (double) ($srcHeight / $new_size_h);
            $cpyWidth = round($new_size_w * $ratio);
            if ($cpyWidth > $srcWidth) {
                $ratio = (double) ($srcWidth / $new_size_w);
                $cpyWidth = $srcWidth;
                $cpyHeight = round($new_size_h * $ratio);
                $xOffset = 0;
                $yOffset = round(($srcHeight - $cpyHeight) / 2);
            } else {
                $cpyHeight = $srcHeight;
                $xOffset = round(($srcWidth - $cpyWidth) / 2);
                $yOffset = 0;
            }
        } else {
            $ratio = (double) ($srcWidth / $new_size_w);
            $cpyHeight = round($new_size_h * $ratio);
            if ($cpyHeight > $srcHeight) {
                $ratio = (double) ($srcHeight / $new_size_h);
                $cpyHeight = $srcHeight;
                $cpyWidth = round($new_size_w * $ratio);
                $xOffset = round(($srcWidth - $cpyWidth) / 2);
                $yOffset = 0;
            } else {
                $cpyWidth = $srcWidth;
                $xOffset = 0;
                $yOffset = round(($srcHeight - $cpyHeight) / 2);
            }
        }
        $dstHandle = ImageCreateTrueColor($new_size_w, $new_size_h);
        switch ($type) {
            case 1:
                $transparent_source_index = imagecolortransparent($srcHandle);
                if ($transparent_source_index !== - 1) {
                    $transparent_color = imagecolorsforindex($srcHandle, $transparent_source_index);
                    $transparent_destination_index = imagecolorallocate($dstHandle, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                    imagecolortransparent($dstHandle, $transparent_destination_index);
                    imagefill($dstHandle, 0, 0, $transparent_destination_index);
                }
                break;
            case 3:
                imagealphablending($dstHandle, false);
                imagesavealpha($dstHandle, true);
                break;
        }
        if (! imagecopyresampled($dstHandle, $srcHandle, 0, 0, $xOffset, $yOffset, $new_size_w, $new_size_h, $cpyWidth, $cpyHeight)) {
            // Falha ao executar imagecopyresampled()
            return Services::response()->setStatusCode(500)->setBody(lang('cCrud.imagecopyresampled_failed'));
            return false;
        }
        imagedestroy($srcHandle);
        if ($watermark) {
            list ($water_w, $water_h, $water_type) = getimagesize($watermark);
            $offsets = $this->calculate_watermark_offsets($new_size_w, $new_size_h, $water_w, $water_h, $watermark_position);
            switch ($water_type) {
                case 1:
                    $waterHandle = imagecreatefromgif($watermark);
                    $transparent_source_index = imagecolortransparent($waterHandle);
                    if ($transparent_source_index !== - 1) {
                        $transparent_color = imagecolorsforindex($waterHandle, $transparent_source_index);
                        $transparent_destination_index = imagecolorallocate($waterHandle, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                        imagecolortransparent($waterHandle, $transparent_destination_index);
                        imagefill($waterHandle, 0, 0, $transparent_destination_index);
                    }
                    break;
                case 2:
                    $waterHandle = imagecreatefromjpeg($watermark);
                    break;
                case 3:
                    $waterHandle = imagecreatefrompng($watermark);
                    imagealphablending($waterHandle, false);
                    imagesavealpha($waterHandle, true);
                    imagealphablending($dstHandle, true);
                    break;
                    break;
                default:
                    // Arquivo de marca d'água inexistente
                    return Services::response()->setStatusCode(404)->setBody(lang('cCrud.no_watermark_file'));
                    return false;
            }
            imagecopy($dstHandle, $waterHandle, $offsets['x'], $offsets['y'], 0, 0, $water_w, $water_h);
            imagedestroy($waterHandle);
        }
        switch ($type) {
            case 1:
                imagegif($dstHandle, $dest_file);
                break;
            case 2:
                imagejpeg($dstHandle, $dest_file, $dest_qual);
                break;
        case 3:
            imagepng($dstHandle, $dest_file);
            break;
        default:
            // Tipo de arquivo não suportado
            return Services::response()->setStatusCode(415)->setBody(lang('cCrud.file_type_not_supported'));
            return false;
        }
        imagedestroy($dstHandle);
        return true;
    }

    protected function _custom_image_crop($src_file, $dest_file, $new_size_w, $new_size_h, $dest_qual, $x, $y, $w, $h, $watermark = false, $watermark_position = array(
        50,
        50
    ))
    {
        list ($srcWidth, $srcHeight, $type) = getimagesize($src_file);
        switch ($type) {
            case 1:
                $srcHandle = imagecreatefromgif($src_file);
                break;
            case 2:
                $srcHandle = imagecreatefromjpeg($src_file);
                break;
            case 3:
                $srcHandle = imagecreatefrompng($src_file);
                break;
            default:
                // Arquivo inexistente
                return Services::response()->setStatusCode(400)->setBody(lang('cCrud.no_file'));
                return false;
        }
        if (! $srcHandle) {
            // Falha ao executar imagecreatefrom()
            return Services::response()->setStatusCode(500)->setBody(lang('cCrud.imagecreatefrom_failed'));
            return false;
        }
        $dstHandle = ImageCreateTrueColor($new_size_w, $new_size_h);
        switch ($type) {
            case 1:
                $transparent_source_index = imagecolortransparent($srcHandle);
                if ($transparent_source_index !== - 1) {
                    $transparent_color = imagecolorsforindex($srcHandle, $transparent_source_index);
                    $transparent_destination_index = imagecolorallocate($dstHandle, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                    imagecolortransparent($dstHandle, $transparent_destination_index);
                    imagefill($dstHandle, 0, 0, $transparent_destination_index);
                }
                break;
            case 3:
                imagealphablending($dstHandle, false);
                imagesavealpha($dstHandle, true);
                break;
        }
        if (! imagecopyresampled($dstHandle, $srcHandle, 0, 0, $x, $y, $new_size_w, $new_size_h, $w, $h)) {
            // Falha ao executar imagecopyresampled()
            return Services::response()->setStatusCode(500)->setBody(lang('cCrud.imagecopyresampled_failed'));
            return false;
        }
        imagedestroy($srcHandle);
        if ($watermark) {
            list ($water_w, $water_h, $water_type) = getimagesize($watermark);
            $offsets = $this->calculate_watermark_offsets($new_size_w, $new_size_h, $water_w, $water_h, $watermark_position);
            switch ($water_type) {
                case 1:
                    $waterHandle = imagecreatefromgif($watermark);
                    $transparent_source_index = imagecolortransparent($waterHandle);
                    if ($transparent_source_index !== - 1) {
                        $transparent_color = imagecolorsforindex($waterHandle, $transparent_source_index);
                        $transparent_destination_index = imagecolorallocate($waterHandle, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                        imagecolortransparent($waterHandle, $transparent_destination_index);
                        imagefill($waterHandle, 0, 0, $transparent_destination_index);
                    }
                    break;
                case 2:
                    $waterHandle = imagecreatefromjpeg($watermark);
                    break;
                case 3:
                    $waterHandle = imagecreatefrompng($watermark);
                    imagealphablending($waterHandle, false);
                    imagesavealpha($waterHandle, true);
                    imagealphablending($dstHandle, true);
                    break;
                    break;
                default:
                    // Arquivo de marca d'água inexistente
                    return Services::response()->setStatusCode(404)->setBody(lang('cCrud.no_watermark_file'));
                    return false;
            }
            imagecopy($dstHandle, $waterHandle, $offsets['x'], $offsets['y'], 0, 0, $water_w, $water_h);
            imagedestroy($waterHandle);
        }
        switch ($type) {
            case 1:
                imagegif($dstHandle, $dest_file);
                break;
            case 2:
                imagejpeg($dstHandle, $dest_file, $dest_qual);
                break;
            case 3:
                imagepng($dstHandle, $dest_file);
                break;
            default:
                // Tipo de arquivo não suportado
                return Services::response()->setStatusCode(415)->setBody(lang('cCrud.file_type_not_supported'));
                return false;
        }
        imagedestroy($dstHandle);
        return true;
    }

    protected function _draw_watermark($src_file, $dest_file, $dest_qual = 95, $watermark = false, $watermark_position = array(
        50,
        50
    ))
    {
        list ($srcWidth, $srcHeight, $type) = getimagesize($src_file);
        switch ($type) {
            case 1:
                $srcHandle = imagecreatefromgif($src_file);
                break;
            case 2:
                $srcHandle = imagecreatefromjpeg($src_file);
                break;
            case 3:
                $srcHandle = imagecreatefrompng($src_file);
                break;
            default:
                // Arquivo inexistente
                return Services::response()->setStatusCode(400)->setBody(lang('cCrud.no_file'));
                return false;
        }
        $dstHandle = imagecreatetruecolor($srcWidth, $srcHeight);
        switch ($type) {
            case 1:
                $transparent_source_index = imagecolortransparent($srcHandle);
                if ($transparent_source_index !== - 1) {
                    $transparent_color = imagecolorsforindex($srcHandle, $transparent_source_index);
                    $transparent_destination_index = imagecolorallocate($dstHandle, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                    imagecolortransparent($dstHandle, $transparent_destination_index);
                    imagefill($dstHandle, 0, 0, $transparent_destination_index);
                }
                break;
            case 3:
                imagealphablending($dstHandle, false);
                imagesavealpha($dstHandle, true);
                $transparent_color = imagecolorallocatealpha($dstHandle, 0, 0, 0, 127);
                imagefill($dstHandle, 0, 0, $transparent_color);
                break;
        }
        imagecopy($dstHandle, $srcHandle, 0, 0, 0, 0, $srcWidth, $srcHeight);
        imagedestroy($srcHandle);
        if ($watermark) {
            list ($water_w, $water_h, $water_type) = getimagesize($watermark);
            $offsets = $this->calculate_watermark_offsets($srcWidth, $srcHeight, $water_w, $water_h, $watermark_position);
            switch ($water_type) {
                case 1:
                    $waterHandle = imagecreatefromgif($watermark);
                    $transparent_source_index = imagecolortransparent($waterHandle);
                    if ($transparent_source_index !== - 1) {
                        $transparent_color = imagecolorsforindex($waterHandle, $transparent_source_index);
                        $transparent_destination_index = imagecolorallocate($waterHandle, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                        imagecolortransparent($waterHandle, $transparent_destination_index);
                        imagefill($waterHandle, 0, 0, $transparent_destination_index);
                    }
                    break;
                case 2:
                    $waterHandle = imagecreatefromjpeg($watermark);
                    break;
                case 3:
                    $waterHandle = imagecreatefrompng($watermark);
                    imagealphablending($waterHandle, false);
                    imagesavealpha($waterHandle, true);
                    break;
                    break;
                default:
                    // Arquivo de marca d'água inexistente
                    return Services::response()->setStatusCode(404)->setBody(lang('cCrud.no_watermark_file'));
                    return false;
            }
            imagecopy($dstHandle, $waterHandle, $offsets['x'], $offsets['y'], 0, 0, $water_w, $water_h);
            imagedestroy($waterHandle);
        }
        switch ($type) {
            case 1:
                imagegif($dstHandle, $dest_file);
                break;
            case 2:
                imagejpeg($dstHandle, $dest_file, $dest_qual);
                break;
            case 3:
                imagepng($dstHandle, $dest_file);
                break;
            default:
                // Tipo de arquivo não suportado
                return Services::response()->setStatusCode(415)->setBody(lang('cCrud.file_type_not_supported'));
                return false;
        }
        imagedestroy($dstHandle);
        return true;
    }

    protected function calculate_watermark_offsets($img_w, $img_h, $water_w, $water_h, $water_pos)
    {
        $offsets = array();
        $pos_x = ($water_pos[0] < 0 or $water_pos[0] > 100) ? 0 : $water_pos[0];
        $pos_y = ($water_pos[1] < 0 or $water_pos[1] > 100) ? 0 : $water_pos[1];
        $avail_w = $img_w - $water_w;
        $avail_h = $img_h - $water_h;
        if ($avail_w < 0)
            $avail_w = 0;
        if ($avail_h < 0)
            $avail_h = 0;
        if (! $avail_w)
            $offsets['x'] = 0;
        else {
            $offsets['x'] = round($avail_w / 100 * $pos_x);
        }
        if (! $avail_h)
            $offsets['y'] = 0;
        else {
            $offsets['y'] = round($avail_h / 100 * $pos_y);
        }
        return $offsets;
    }

    protected function _clean_file_name($txt)
    {
        $replace = array(
            'Š' => 'S',
            'Œ' => 'O',
            'Ž' => 'Z',
            'š' => 's',
            'œ' => 'oe',
            'ž' => 'z',
            'Ÿ' => 'Y',
            '¥' => 'Y',
            'µ' => 'u',
            'À' => 'A',
            'Á' => 'A',
            'Â' => 'A',
            'Ã' => 'A',
            'Ä' => 'A',
            'Å' => 'A',
            'Æ' => 'A',
            'Ç' => 'C',
            'È' => 'E',
            'É' => 'E',
            'Ê' => 'E',
            'Ë' => 'E',
            'Ì' => 'I',
            'Í' => 'I',
            'Î' => 'I',
            'Ï' => 'I',
            'І' => 'I',
            'Ð' => 'D',
            'Ñ' => 'N',
            'Ò' => 'O',
            'Ó' => 'O',
            'Ô' => 'O',
            'Õ' => 'O',
            'Ö' => 'O',
            'Ø' => 'O',
            'Ù' => 'U',
            'Ú' => 'U',
            'Û' => 'U',
            'Ü' => 'U',
            'Ý' => 'Y',
            'ß' => 'ss',
            'à' => 'a',
            'á' => 'a',
            'â' => 'a',
            'ã' => 'a',
            'ä' => 'a',
            'å' => 'a',
            'æ' => 'a',
            'ç' => 'c',
            'è' => 'e',
            'é' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'ì' => 'i',
            'í' => 'i',
            'î' => 'i',
            'ï' => 'i',
            'і' => 'i',
            'ð' => 'o',
            'ñ' => 'n',
            'ò' => 'o',
            'ó' => 'o',
            'ô' => 'o',
            'õ' => 'o',
            'ö' => 'o',
            'ø' => 'o',
            'ù' => 'u',
            'ú' => 'u',
            'û' => 'u',
            'ü' => 'u',
            'ý' => 'y',
            'ÿ' => 'y',
            'ă' => 'a',
            'ş' => 's',
            'ţ' => 't',
            'ț' => 't',
            'Ț' => 'T',
            'Ș' => 'S',
            'ș' => 's',
            'Ş' => 'S',
            'А' => 'A',
            'Б' => 'B',
            'В' => 'V',
            'Г' => 'G',
            'Д' => 'D',
            'Е' => 'E',
            'Ё' => 'E',
            'Ж' => 'J',
            'З' => 'Z',
            'И' => 'I',
            'Й' => 'I',
            'К' => 'K',
            'Л' => 'L',
            'М' => 'M',
            'Н' => 'N',
            'О' => 'O',
            'П' => 'P',
            'Р' => 'R',
            'С' => 'S',
            'Т' => 'T',
            'У' => 'U',
            'Ф' => 'F',
            'Х' => 'H',
            'Ц' => 'C',
            'Ч' => 'CH',
            'Ш' => 'SH',
            'Щ' => 'SH',
            'Ы' => 'Y',
            'Э' => 'E',
            'Ю' => 'YU',
            'Я' => 'YA',
            'а' => 'a',
            'б' => 'b',
            'в' => 'v',
            'г' => 'g',
            'д' => 'd',
            'е' => 'e',
            'ё' => 'e',
            'ж' => 'j',
            'з' => 'z',
            'и' => 'i',
            'й' => 'i',
            'к' => 'k',
            'л' => 'l',
            'м' => 'm',
            'н' => 'n',
            'о' => 'o',
            'п' => 'p',
            'р' => 'r',
            'с' => 's',
            'т' => 't',
            'у' => 'u',
            'ф' => 'f',
            'х' => 'H',
            'ц' => 'c',
            'ч' => 'ch',
            'ш' => 'sh',
            'щ' => 'sh',
            'ы' => 'y',
            'э' => 'e',
            'ю' => 'yu',
            'я' => 'ya',
            'Ā' => 'A',
            'ā' => 'a',
            'Č' => 'C',
            'č' => 'c',
            'Ē' => 'E',
            'ē' => 'e',
            'Ģ' => 'G',
            'ģ' => 'g',
            'Ī' => 'I',
            'ī' => 'i',
            'Ķ' => 'K',
            'ķ' => 'k',
            'Ļ' => 'L',
            'ļ' => 'l',
            'Ņ' => 'N',
            'ņ' => 'n',
            'Ū' => 'U',
            'ū' => 'u',
            ' ' => '_'
        );
        $txt = str_replace(array_keys($replace), array_values($replace), $txt);
        $txt = preg_replace('/[^a-zA-Z0-9_\-\.]+/', '', $txt);
        return $txt;
    }

    protected function _file_size($path)
    {
        return number_format(is_file($path) ? filesize($path) / 1024 : 0, 2, '.', ' ') . ' KB';
    }

    protected function _file_size_bin($text)
    {
        return number_format(strlen($text) / 1024, 2, '.', ' ') . ' KB';
    }

    protected function _prepare_field($field)
    {
        preg_match_all('/([^<>!=]+)/u', $field, $matches);
        preg_match_all('/([<>!=]+)/u', $field, $matches2);
        return '`' . trim($matches[0][0]) . '`' . ($matches2[0] ? implode('', $matches2[0]) : '=');
    }

    protected function _field_from_where($field)
    {
        return preg_replace('/\s*[<>!=]+\s*$/u', '', $field);
    }

    protected function _cond_from_where($field)
    {
        if (preg_match('/\s*([<>!=]+)\s*$/u', $field, $matches)) {
            return $matches[1];
        } else {
            return '=';
        }
    }

    protected function _where_field($param)
    {
        return '`' . $param['table'] . '`.`' . $this->_field_from_where($param['field']) . '`';
    }

    protected function _where_fieldkey($param)
    {
        return $param['table'] . '.' . $this->_field_from_where($param['field']);
    }

    protected function _cond_from_where_in($field)
    {
        if (preg_match('/\s*[!]+\s*$/u', $field)) {
            return ' NOT IN';
        } else {
            return ' IN';
        }
    }

    protected function _prepare_field_in($field)
    {
        preg_match_all('/([^!]+)/u', $field, $matches);
        preg_match_all('/([!]+)/u', $field, $matches2);
        return '`' . trim($matches[0][0]) . '`' . ($matches2[0] ? ' NOT IN' : ' IN');
    }

    /**
     * Compara valores utilizando operador especificado.
     * Inserido os operadores IN e NOT IN.
     *
     * @param mixed  $val1     Primeiro valor.
     * @param string $operator Operador de comparação.
     * @param mixed  $val2     Segundo valor.
     *
     * @return bool Resultado da comparação.
     */
    protected function _compare(mixed $val1, string $operator, mixed $val2): bool
    {
        $charset = config('App')->charset;

        switch ($operator) {
            case 'IN':
                if (! is_array($val2))
                    $val2 = $this->parse_comma_separated($val2);
                return in_array($val1, $val2);
            case 'NOT IN':
                if (! is_array($val2))
                    $val2 = $this->parse_comma_separated($val2);
                return ! in_array($val1, $val2);
            case '=':
                return ($val1 == $val2) ? true : false;
            case '>':
                return ($val1 > $val2) ? true : false;
            case '<':
                return ($val1 < $val2) ? true : false;
            case '>=':
                return ($val1 >= $val2) ? true : false;
            case '<=':
                return ($val1 <= $val2) ? true : false;
            case '!=':
                return ($val1 != $val2) ? true : false;
            case '^=':
                return (mb_strpos($val1, $val2, 0, $charset) === 0) ? true : false;
            case '$=':
                return (mb_strpos($val1, $val2, 0, $charset) == (mb_strlen($val1, $charset) - mb_strlen($val2, $charset))) ? true : false;
            case '~=':
                return (mb_strpos($val1, $val2, 0, $charset) !== false) ? true : false;
            default:
                return false;
        }
    }

    protected function create_modal($field, $content, $image = false)
    {
        $out = '';
        $attr = array(
            'href' => 'javascript:;',
            'data-header' => $this->columns_names[$field],
            'data-content' => $content
        );
        if ($image) {
            $attr['data-content'] = $this->single_tag('img', '', array(
                'alt' => '',
                'src' => $image
            ));
        } else {
            $attr['data-content'] = $content;
        }
        $out .= $this->open_tag('a', 'cCrud_modal', $attr);
        if ($this->config->images_in_grid && $image) {
            $out .= $content;
        } else {
            $out .= $this->open_tag('i', $this->modal[$field] ? $this->modal[$field] : $this->theme_config('modal_icon')) . $this->close_tag('i');
        }
        $out .= $this->close_tag('a');
        return $out;
    }

    protected function _render_list_item($field, $value, $primary_val, $row)
    {
        $modal = '';
        $out = '';
        $image = '';
        if (isset($this->relation[$field])) {
            $value = $row['rel.' . $field];
        }
        if (isset($this->column_callback[$field])) {
            $path = $this->check_file($this->column_callback[$field]['path'], 'column_callback');
            include_once ($path);
            if (is_callable($this->column_callback[$field]['callback']) && $row) {
                $value = call_user_func_array($this->column_callback[$field]['callback'], array(
                    $value,
                    $field,
                    $primary_val,
                    $row,
                    $this
                ));
                return $value;
            }
        }

        if (isset($this->field_type[$field])) {
            switch ($this->field_type[$field]) {
                case 'select':
                case 'radio':
                    $out .= $this->create_view_select($field, $value);
                    break;
                case 'multiselect':
                case 'checkboxes':
                    $out .= $this->create_view_multiselect($field, $value);
                    break;
                case 'timestamp':
                case 'datetime':
                    if ($value && $value !== '0000-00-00 00:00:00') {
                        $out .= Time::parse($value)->toDateTimeString();
                    }
                    break;
                case 'date':
                    if ($value && $value !== '0000-00-00') {
                        $out .= Time::parse($value)->toDateString();
                    }
                    break;
                case 'time':
                    if ($value) {
                        $out .= Time::parse($value)->toTimeString();
                    }
                    break;
                case 'price':
                    $out .= $this->cast_number_format($value, $field);
                    break;
                case 'bool':
                    $out .= $value ? $this->lang('bool_on') : $this->lang('bool_off');
                    break;
                case 'file':
                    if ($value) {
                        $out .= $this->open_tag('a', '', array(
                            'target' => '_blank',
                            'href' => isset($this->upload_config[$field]['url']) ? $this->real_file_link($value, $this->upload_config[$field]) : $this->file_link($field, $primary_val)
                        ));

                        if (isset($this->upload_config[$field]['text'])) {
                            $out .= $this->upload_config[$field]['text'];
                        } elseif (isset($this->upload_config[$field]['filename'])) {
                            $out .= $this->upload_config[$field]['filename'];
                        } elseif (isset($this->upload_config[$field]['blob']) && $this->upload_config[$field]['blob']) {
                            $out .= 'blob-storage';
                        } else {
                            $out .= $this->filter_file_name($value);
                        }

                        $out .= $this->close_tag('a');
                        break;
                    }
                case 'image':
                    if ($value) {
                        if ($this->config->images_in_grid) {
                            $settings = $this->upload_config[$field];
                            if (isset($settings['grid_thumb']) && isset($settings['thumbs'][$settings['grid_thumb']])) {
                                $thumb = $settings['grid_thumb'];
                            } else {
                                $thumb = false;
                            }
                            $out .= $this->single_tag('img', '', array(
                                'alt' => '',
                                'src' => isset($this->upload_config[$field]['url']) ? $this->real_file_link($value, $this->upload_config[$field]) : $this->file_link($field, $primary_val, $thumb, false, $value),
                                'style' => 'max-height: ' . $this->config->images_in_grid_height . 'px;'
                            ));
                        } else {
                            $out .= $this->open_tag('a', '', array(
                                'target' => '_blank',
                                'href' => isset($this->upload_config[$field]['url']) ? $this->real_file_link($value, $this->upload_config[$field]) : $this->file_link($field, $primary_val, false, false, $value)
                            ));
                            $out .= isset($this->upload_config[$field]['text']) ? $this->upload_config[$field]['text'] : $value;
                            $out .= $this->close_tag('a');
                        }
                    }
                    $image = isset($this->upload_config[$field]['url']) ? $this->real_file_link($value, $this->upload_config[$field]) : $this->file_link($field, $primary_val, false, false, $value);
                    break;
                case 'remote_image':
                    if ($value) {
                        if ($this->config->images_in_grid) {
                            $out .= $this->single_tag('img', '', array(
                                'alt' => '',
                                'src' => $value,
                                'style' => 'max-height: ' . $this->config->images_in_grid_height . 'px;'
                            ));
                        } else {
                            $out .= $this->open_tag('a', '', array(
                                'target' => '_blank',
                                'href' => $value
                            ));
                            $out .= isset($this->upload_config[$field]['text']) ? $this->upload_config[$field]['text'] : $value;
                            $out .= $this->close_tag('a');
                        }
                    }
                    $image = $value;
                    break;
                case 'binary':
                    $out .= $value ? '[binary data]' : '';
                    break;
                case 'text':
                    $value = $this->_cut($value, $field);
                    if ($this->config->clickable_list_links) {
                        $value = $this->make_links($value);
                        $value = $this->make_mailto($value);
                    }
                    $out .= $value;
                    break;
                case 'textarea':
                case 'texteditor':
                    if (isset($this->modal[$field])) {
                        $out .= $value;
                    } else if (! is_null($value = $this->_cut($value, $field))) {
                        $out .= nl2br($value);
                    }
                    break;
                default:
                    $out .= $this->_cut($value, $field);
                    break;
            }
        } else {
            $out .= $this->_cut($value, $field);
        }
        if (isset($this->column_pattern[$field])) {
            $out = str_ireplace('{value}', $out, $this->column_pattern[$field]);
            $out = $this->replace_text_variables($out, $row, false);
        }

        if (trim($out) == '') {
            $out = '&nbsp;';
        }

        if (isset($this->modal[$field]) && $value) {
            return $this->create_modal($field, $out, $image);
        } else {
            return $out;
        }
    }

    protected function make_mailto($txt)
    {
        if ($this->emails_label)
            return preg_replace('/([A-Za-z0-9_\-\.]+)\@([A-Za-z0-9_\-\.]+)\.([A-Za-z]{2,4})/', '<a target="_blank" href="mailto:$1@$2.$3">' . $this->emails_label['text'] . '</a>', $txt);
        else
            return preg_replace('/([A-Za-z0-9_\-\.]+)\@([A-Za-z0-9_\-\.]+)\.([A-Za-z]{2,4})/', '<a target="_blank" href="mailto:$1@$2.$3">$1@$2.$3</a>', $txt);
    }

    protected function make_links($txt)
    {
        if ($this->links_label)
            return preg_replace('/(http:\/\/|https:\/\/)([^\s]+)/u', '<a target="_blank" href="$1$2">' . $this->links_label['text'] . '</a>', $txt);
        else
            return preg_replace('/(http:\/\/|https:\/\/)([^\s]+)/u', '<a target="_blank" href="$1$2">$1$2</a>', $txt);
    }

    /**
     * renders grid cell content, srips tags and prepares values for export in
     * csv or other
     */
    protected function _render_export_item($field, $value, $primary_val, $row)
    {
        $out = '';
        if (isset($this->relation[$field])) {
            $value = strip_tags($row['rel.' . $field]);
        }
        if (isset($this->column_callback[$field])) {
            $path = $this->check_file($this->column_callback[$field]['path'], 'column_callback');
            include_once ($path);
            if (is_callable($this->column_callback[$field]['callback']) && $row) {
                $value = strip_tags(call_user_func_array($this->column_callback[$field]['callback'], array(
                    $value,
                    $field,
                    $primary_val,
                    $row,
                    $this
                )));
                return $value;
            }
        }

        if (isset($this->field_type[$field])) {
            switch ($this->field_type[$field]) {
                case 'select':
                case 'radio':
                    $out .= $this->create_view_select($field, $value);
                    break;
                case 'multiselect':
                case 'checkboxes':
                    $out .= $this->create_view_multiselect($field, $value);
                    break;
                case 'timestamp':
                case 'datetime':
                    if ($value && $value !== '0000-00-00 00:00:00') {
                        $out .= Time::parse($value)->toDateTimeString();
                    }
                    break;
                case 'date':
                    if ($value && $value !== '0000-00-00') {
                        $out .= Time::parse($value)->toDateString();
                    }
                    break;
                case 'time':
                    if ($value) {
                        $out .= Time::parse($value)->toTimeString();
                    }
                    break;
                case 'price':
                    $out .= $this->cast_number_format($value, $field);
                    break;
                case 'bool':
                    $out .= $value ? $this->lang('bool_on') : $this->lang('bool_off');
                    break;
                case 'file':
                case 'image':
                    if (isset($this->upload_config[$field]['blob'])) {
                        $out .= $value ? '[binary data]' : '';
                    } else {
                        $out .= isset($this->upload_config[$field]['text']) ? $this->upload_config[$field]['text'] : $value;
                    }
                    break;
                case 'remote_image':
                    $out .= $value;
                    break;
                case 'binary':
                    $out .= $value ? '[binary data]' : '';
                    break;
                case 'text':
                case 'textarea':
                case 'texteditor':
                default:
                    $out .= $value;
                    break;
            }
        } else {
            $out .= $value;
        }
        if (isset($this->column_pattern[$field])) {
            $out = str_ireplace('{value}', $out, $this->column_pattern[$field]);
            $out = $this->replace_text_variables($out, $row, true);
            $out = strip_tags($out);
        }
        return $out;
    }

    /**
     *
     * @author Ariel Canal
     *         Inserido o atributo data-after na renderização do botão de
     *         duplicação
     */
    protected function _render_list_buttons(&$row)
    {
        $out = '';
        $group = array(
            'tag' => 'span',
            'class' => $this->theme_config('grid_button_group')
        );
        $out .= $this->open_tag($group);
        if ($this->buttons) {
            foreach ($this->buttons as $button) {
                if ($button['table_ro'] == false && $this->table_ro) {
                    continue;
                }
                if ($this->is_button($button['name'], $row)) {
                    // $href = '';
                    /*
                     * if ($button['params'])
                     * {
                     * $href = http_build_query($button['params']);
                     * }
                     */
                    $link = $this->replace_text_variables($button['link'], $row, true);
                    /*
                     * if ($href)
                     * {
                     * $link = $link . ((mb_strpos($button['link'], '?') ===
                     * false) ? '?' : '&amp;') . $href;
                     * }
                     */
                    if ($button['params']) {
                        foreach ($button['params'] as $pkey => $pval) {
                            $button['params'][$pkey] = $this->replace_text_variables($pval, $row, true);
                        }
                    }
                    $tag = array(
                        'tag' => 'a',
                        'class' => $this->theme_config('grid_default'),
                        'href' => $link,
                        'title' => $button['name']
                    );
                    $out .= $this->open_tag($tag, $button['class'], $button['params']);
                    if ($button['icon']) {
                        $out .= $this->open_tag('i', $button['icon']) . $this->close_tag('i');
                    } elseif ($this->theme_config('grid_default_icon')) {
                        $out .= $this->open_tag('i', $this->theme_config('grid_default_icon')) . $this->close_tag('i');
                    }
                    if ($this->config->button_labels) {
                        $out .= ' ' . \esc($button['name']);
                    }
                    $out .= $this->close_tag($tag);
                }
            }
        }
        if (! isset($this->hide_button['duplicate']) && ! $this->table_ro && $this->is_duplicate($row)) {
            $tag = array(
                'tag' => 'a',
                'class' => 'cCrud-action',
                'title' => $this->lang('duplicate'),
                'href' => 'javascript:;',
                'data-primary' => $row['primary_key'],
                'data-confirm' => $this->lang('duplicate_confirm'),
                'data-task' => 'clone',
                'data-after' => 'edit'
            );
            $out .= $this->open_tag($tag, $this->theme_config('grid_duplicate'));
            if ($this->theme_config('grid_duplicate_icon')) {
                $out .= $this->open_tag('i', $this->theme_config('grid_duplicate_icon')) . $this->close_tag('i');
            }
            if ($this->config->button_labels) {
                $out .= ' ' . $this->lang('duplicate');
            }
            $out .= $this->close_tag($tag);
        }
        if (! isset($this->hide_button['view']) && $this->is_view($row)) {
            $tag = array(
                'tag' => 'a',
                'class' => 'cCrud-action',
                'title' => $this->lang('view'),
                'href' => 'javascript:;',
                'data-primary' => $row['primary_key'],
                'data-task' => 'view'
            );
            $out .= $this->open_tag($tag, $this->theme_config('grid_view'));
            if ($this->theme_config('grid_view_icon')) {
                $out .= $this->open_tag('i', $this->theme_config('grid_view_icon')) . $this->close_tag('i');
            }
            if ($this->config->button_labels) {
                $out .= ' ' . $this->lang('view');
            }
            $out .= $this->close_tag($tag);
        }
        if (! isset($this->hide_button['edit']) && ! $this->table_ro && $this->is_edit($row)) {
            $tag = array(
                'tag' => 'a',
                'class' => 'cCrud-action',
                'title' => $this->lang('edit'),
                'href' => 'javascript:;',
                'data-primary' => $row['primary_key'],
                'data-task' => 'edit'
            );
            $out .= $this->open_tag($tag, $this->theme_config('grid_edit'));
            if ($this->theme_config('grid_edit_icon')) {
                $out .= $this->open_tag('i', $this->theme_config('grid_edit_icon')) . $this->close_tag('i');
            }
            if ($this->config->button_labels) {
                $out .= ' ' . $this->lang('edit');
            }
            $out .= $this->close_tag($tag);
        }
        if (! isset($this->hide_button['remove']) && ! $this->table_ro && $this->is_remove($row)) {
            $tag = array(
                'tag' => 'a',
                'class' => 'cCrud-action',
                'title' => $this->lang('remove'),
                'href' => 'javascript:;',
                'data-primary' => $row['primary_key'],
                'data-task' => 'remove'
            );
            if ($this->remove_confirm) {
                $tag['data-confirm'] = $this->lang('deleting_confirm');
            }
            $out .= $this->open_tag($tag, $this->theme_config('grid_remove'));
            if ($this->theme_config('grid_remove_icon')) {
                $out .= $this->open_tag('i', $this->theme_config('grid_remove_icon')) . $this->close_tag('i');
            }
            if ($this->config->button_labels) {
                $out .= ' ' . $this->lang('remove');
            }
            $out .= $this->close_tag($tag);
        }

        $out .= $this->close_tag($group);
        return $out;
    }

    protected function render_sum_item($field)
    {
        if (isset($this->sum_row[$field])) {
            if ($this->sum[$field]['custom']) {
                return str_replace('{value}', $this->_render_list_item($field, $this->sum_row[$field], 0, null), $this->sum[$field]['custom']);
            } else {
                return $this->_render_list_item($field, $this->sum_row[$field], 0, null);
            }
        } else
            return '&nbsp;';
    }

    protected function _check_unique_value()
    {
        $db = $this->model->db;
        $unique = $this->_post('unique');
        $fdata = $this->_parse_field_names($unique, '_check_unique_value');
        $out = array();
        $table_join = $this->_build_table_join();
        if ($this->primary_val) {
            $primary_where = '`' . $this->table . '`.`' . $this->primary_key . '` != ' . $db->escape($this->primary_val) . ' AND';
        } else {
            $primary_where = '';
        }
        foreach ($fdata as $fkey => $fitem) {
            $q = 'SELECT COUNT(*) AS `count` FROM `' . $this->table . '`' . $table_join . ' WHERE ' . $primary_where . ' `' . $fitem['table'] . '`.`' . $fitem['field'] . '` = ' . $db->escape($fitem['value']);
            $query = $db->query($q);
            $this->result_row = $query->getRowArray();
            if ($this->result_row['count'] > 0) {
                $out[] = '[name="' . $this->fieldname_encode($fkey) . '"]';
            }
        }
        if ($out) {
            $data = array(
                'error' => array(
                    'selector' => implode(',', $out)
                )
            );
        } else {
            $data = array(
                'success' => 1
            );
        }
        // $data['key'] = $this->key;
        return json_encode($data);
    }

    /**
     * Gera o link para acesso a arquivos armazenados.
     *
     * @param string     $field        Campo associado ao arquivo.
     * @param int|string $primary_val  Valor da chave primária.
     * @param mixed      $thumb        Identificador da miniatura, se houver.
     * @param bool       $crop         Define se deve aplicar recorte.
     * @param bool|string $filename    Nome do arquivo, quando definido.
     *
     * @return string URL para o arquivo.
     */
    protected function file_link($field, $primary_val, $thumb = false, $crop = false, $filename = false)
    {
        $params = array(
            'cCrud' => array(
                'instance' => $this->instance_name,
                'field' => $field,
                'primary' => $primary_val,
                'key' => $this->key,
                'task' => 'file',
                'rand' => base_convert(sha1($this->instance_name . ($filename ? $filename : rand())), 10, 36)
            )
        );
        if ($thumb !== false) {
            $params['cCrud']['thumb'] = $thumb;
        }
        if ($crop) {
            $params['cCrud']['crop'] = $crop;
        }
        $requestUri = '/' . trim($this->config->request_uri, '/') . '/ajax';
        return $requestUri . '?' . http_build_query($params);
    }

    protected function real_file_link($filename, $params, $is_details = false)
    {
        $url = rtrim($params['url'], '/');
        if ($is_details && isset($params['detail_thumb']) && isset($params['thumbs'][$params['detail_thumb']])) {
            $th = $params['thumbs'][$params['detail_thumb']];
            if (isset($th['folder'])) {
                $url .= '/' . trim($th['folder'], '/');
            }
            if (isset($th['marker'])) {
                $url .= '/' . $this->_thumb_name($filename, $th['marker']);
            } else {
                $url .= '/' . $filename;
            }
        } elseif (! $is_details && isset($params['grid_thumb']) && isset($params['thumbs'][$params['grid_thumb']])) {
            $th = $params['thumbs'][$params['grid_thumb']];
            if (isset($th['folder'])) {
                $url .= '/' . trim($th['folder'], '/');
            }
            if (isset($th['marker'])) {
                $url .= '/' . $this->_thumb_name($filename, $th['marker']);
            } else {
                $url .= '/' . $filename;
            }
        } else {
            $url .= '/' . $filename;
        }
        return $url;
    }

    /**
     *
     * @author Ariel Canal
     *         Função passou a considerar o atributo data-after do botão
     */
    protected function _clone_row()
    {
        if (is_array($this->table_info) && count($this->table_info) && ! $this->table_ro) {
            $db = $this->model->db;
            $fields = array();
            $row = array();
            $this->find_details_text_variables();
            if ($this->direct_select_tags) {
                foreach ($this->direct_select_tags as $key => $dsf) {
                    if ($dsf['table'] == $this->table || (isset($this->join[$dsf['table']]) && $this->join[$dsf['table']]['not_insert'] != true)) {
                        $fields[$key] = "`{$dsf['table']}`.`{$dsf['field']}` AS `{$key}`";
                    }
                }
            }
            if ($fields) {
                if (! $this->join) {
                    $query = $db->query('SELECT ' . implode(',', $fields) . " FROM `{$this->table}` WHERE `{$this->primary_key}` = " . $db->escape($this->primary_val) . " LIMIT 1");
                    $row = $query->getRowArray();
                } else {
                    $tables = array(
                        '`' . $this->table . '`'
                    );
                    $joins = array();
                    foreach ($this->join as $alias => $param) {
                        if ($param['not_insert'] != true) {
                            $tables[] = '`' . $alias . '`';
                            $joins[] = "INNER JOIN `{$param['join_table']}` AS `{$alias}` ON `{$param['table']}`.`{$param['field']}` = `{$alias}`.`{$param['join_field']}` " . $param['additional_cond'];
                        }
                    }
                    $query = $db->query('SELECT ' . implode(',', $fields) . " FROM `{$this->table}` AS `{$this->table}` " . implode(' ', $joins) . " WHERE `{$this->table}`.`{$this->primary_key}` = " . $db->escape($this->primary_val));
                    $row = $query->getRowArray();
                }
            }

            if (! $this->is_duplicate($row)) {
                // Duplicação não permitida
                return Services::response()->setStatusCode(403)->setBody(lang('cCrud.forbidden'));
            }

            $columns = array();
            $this->primary_ai = false;
            foreach ($this->table_info as $table => $types) {
                if ($table == $this->table || (isset($this->join[$table]) && $this->join[$table]['not_insert'] != true)) {
                    foreach ($types as $row) {
                            $field_index = "{$table}.{$row['Field']}";
                            if ($row['Key'] == 'PRI' && $row['Extra'] == 'auto_increment') {
                                if ($table == $this->table) {
                                    $this->primary_ai = "`{$table}`.`{$row['Field']}`";
                                }
                            } elseif ($row['Key'] == 'UNI' or $row['Key'] == 'PRI') {
                                // Impossível duplicar: tabela possui campo único
                                return Services::response()->setStatusCode(409)->setBody(lang('cCrud.duplication_unique_field'));
                            } else {
                                $columns[$field_index] = array(
                                    'table' => $table,
                                    'field' => $row['Field']
                                );
                            }
                    }
                }
            }
            if (! $this->primary_ai) {
                // Impossível duplicar: tabela sem campo autoincremento
                return Services::response()->setStatusCode(409)->setBody(lang('cCrud.duplication_no_primary_autoincrement'));
            }
            $select = $this->_build_select_clone($columns);
            $where = $this->_build_where();
            $table_join = $this->_build_table_join();
            $where_ai = $where ? "AND {$this->primary_ai} = " . (int) $this->primary_val : "WHERE {$this->primary_ai} = " . (int) $this->primary_val;
            $query = $db->query("SELECT {$select}\r\n FROM `{$this->table}`\r\n {$table_join}\r\n {$where}\r\n {$where_ai} LIMIT 1");
            $postdata = $query->getRowArray();
            if (isset($this->pass_var['create'])) {
                foreach ($this->pass_var['create'] as $field => $pv) {
                    $postdata[$field] = $pv['value'];
                }
            }
            if (count($this->upload_config)) {
                foreach ($this->upload_config as $field => $attr) {
                    $postdata[$field] = $this->_clone_file($field, $postdata[$field]);
                }
            }
            $ins_id = $this->_insert($postdata, true, $columns);
            if ($this->after_clone && $ins_id) {
                $path = $this->check_file($this->after_clone['path'], 'after_clone');
                include_once ($path);
                if (is_callable($this->after_clone['callable'])) {
                    call_user_func_array($this->after_clone['callable'], array(
                        $this->primary_val,
                        $ins_id,
                        $this
                    ));
                    if ($this->exception) {
                        return $this->call_exception($postdata);
                    }
                }
            }
        }
        $this->task = 'list';
        return $this->_run_task();
    }

    protected function _build_select_clone($columns)
    {
        $fields = array();
        foreach ($columns as $key => $val) {
            if ($val)
                $fields[] = "`{$val['table']}`.`{$val['field']}` AS `$key`";
        }
        return implode(',', $fields);
    }

    protected function _cell_attrib($field, $value, $order, &$row, $is_sum = false, $row_color = false, $row_class = false)
    {
        $attr = array();
        if (isset($this->column_class[$field])) {
            $column_class = $this->column_class[$field];
        } else {
            $column_class = array();
        }
        if (isset($this->labels[$field])) {
            $attr['data-label'] = \esc($this->labels[$field]);
        } else {
            $attr['data-label'] = \esc($this->_humanize($field));
        }
        if ($row_class)
            $column_class[] = $row_class;
        if ($field == $order && $this->is_sortable)
            $column_class[] = 'cCrud-current';
        if ($is_sum)
            $column_class[] = 'cCrud-sum';
        if ($row_color) {
            $attr['style'] = $row_color;
        }
        if (isset($this->highlight[$field])) {
            foreach ($this->highlight[$field] as $params) {
                $params['value'] = $this->replace_text_variables($params['value'], $row, true);
                if ($this->_compare($value, $params['operator'], $params['value'])) {
                    if ($params['color'])
                        $attr['style'] = 'background-color:' . $params['color'] . ';';
                    if ($params['class'])
                        $column_class[] = $params['class'];
                }
            }
        }
        if ($column_class) {
            $column_class = array_unique($column_class);
            $attr['class'] = implode(' ', $column_class);
            $attr['class'] = $this->replace_text_variables($attr['class'], $row, true);
        }
        return $attr;
    }

    protected function _get_table($method)
    {
        if (! $this->table && ! $this->query)
            // Tabela não definida para o método requisitado
            return Services::response()->setStatusCode(500)->setBody(lang('cCrud.table_not_defined', [$method]));
        else
            return $this->table ? $this->table : '';
        return false;
    }

    /**
     *
     * @author Ariel Canal
     *         COMPATIBILIZAÇÃO COM A ARQUITETURA DO CODEIGINITER
     */
    protected function _get_language()
    {
        // Carrega o arquivo de idioma de acordo com a linguagem atual
        self::$lang_arr = lang('cCrud', [], $this->language);
        // Fallback para o inglês caso a chave não exista
        if (! self::$lang_arr) {
            self::$lang_arr = lang('cCrud', [], 'en');
        }
        if ($this->set_lang) {
            self::$lang_arr = array_merge(self::$lang_arr, $this->set_lang);
        }
    }

    /**
     *
     * @author Ariel Canal
     *         COMPATIBILIZAÇÃO COM A ARQUITETURA DO CODEIGINITER
     */
    /**
     * Carrega o arquivo de idioma padrão.
     *
     * @return void
     */
    protected static function _get_language_static(): void
    {
        self::$lang_arr = lang('cCrud', [], config('App')->defaultLocale);

        if (! self::$lang_arr) {
            self::$lang_arr = lang('cCrud', [], 'en');
        }
    }

    /**
     *
     * @author Ariel Canal
     *         COMPATIBILIZAÇÃO COM A ARQUITETURA DO CODEIGINITER
     */
    /**
     * Carrega a configuração visual padrão.
     */
    protected function _get_theme_config()
    {
        if (class_exists(ViewsConfig::class)) {
            $config = new ViewsConfig();
            $this->theme_config = $config->classes;
        } else {
            // Arquivo de configuração das views não localizado
            return Services::response()->setStatusCode(404)->setBody(lang('cCrud.view_config_not_found'));
        }
    }

    /**
     * Retorna texto traduzido.
     *
     * @param string $text Chave de tradução.
     *
     * @return string Texto traduzido.
     */
    protected function lang(string $text = ''): string
    {
        $charset = config('App')->charset;
        $langtext = mb_convert_case($text, MB_CASE_LOWER, $charset);

        return lang('cCrud.' . $langtext);
    }

    /**
     * Obtém configuração visual do tema.
     *
     * @param string $text Chave da configuração.
     *
     * @return string Valor sanitizado da configuração.
     */
    protected function theme_config(string $text = ''): string
    {
        $charset = config('App')->charset;
        $text = mb_convert_case($text, MB_CASE_LOWER, $charset);

        return htmlspecialchars((isset($this->theme_config[$text]) ? $this->theme_config[$text] : ''), ENT_QUOTES, $charset);
    }

    protected function _thumb_name($name, $marker)
    {
        return substr_replace($name, $marker, strrpos($name, '.'), 0);
    }

    public function _parse_field_names($fields = '', $location = '', $table = false, $insert_prefix = true)
    {
        $field_names = array();
        if ($fields) {
            if (! $table) {
                $table = $this->_get_table($location);
            }

            if ($insert_prefix) {
                $prefix = $this->prefix;
            } else {
                $prefix = '';
            }

            if (is_array($fields)) {
                foreach ($fields as $key => $val) {
                    if (is_int($key)) {
                        if (! strpos($val, '.'))
                            $field_names[$this->make_field_alias($table, $val)] = array(
                                'table' => $table,
                                'field' => $val
                            );
                        else {
                            $tmp = explode('.', $val, 2);
                            $field_names[$this->make_field_alias($tmp[0], $tmp[1], $prefix)] = array(
                                'table' => $prefix . $tmp[0],
                                'field' => $tmp[1]
                            );
                            unset($tmp);
                        }
                    } else {
                        if (! strpos($key, '.'))
                            $field_names[$this->make_field_alias($table, $key)] = array(
                                'table' => $table,
                                'field' => $key,
                                'value' => $val
                            );
                        else {
                            $tmp = explode('.', $key, 2);
                            $field_names[$this->make_field_alias($tmp[0], $tmp[1], $prefix)] = array(
                                'table' => $prefix . $tmp[0],
                                'field' => $tmp[1],
                                'value' => $val
                            );
                            unset($tmp);
                        }
                    }
                }
            } else {
                $fields = explode(',', $fields);
                foreach ($fields as $key => $val) {
                    $val = trim($val);
                    if (! strpos($val, '.'))
                        $field_names[$this->make_field_alias($table, $val)] = array(
                            'table' => $table,
                            'field' => $val
                        );
                    else {
                        $tmp = explode('.', $val, 2);
                        $field_names[$this->make_field_alias($tmp[0], $tmp[1], $prefix)] = array(
                            'table' => $prefix . $tmp[0],
                            'field' => $tmp[1]
                        );
                        unset($tmp);
                    }
                }
            }
            unset($fields);
        } else
            // Nome do campo obrigatório
            return Services::response()->setStatusCode(400)->setBody(lang('cCrud.field_name_required', [$location]));
        return $field_names;
    }

    protected function make_field_alias($table, $field, $pefix = '')
    {
        if ($table) {
            return $pefix . $table . '.' . $field;
        } else {
            return $field;
        }
    }

    protected function parse_comma_separated($param)
    {
        /*
         * if (!is_array($param))
         * {
         * $param = explode(',', (string )$param);
         * foreach ($param as $key => $p)
         * {
         * $param[$key] = trim($p);
         * }
         * }
         * return $param;
         */
        if (is_array($param)) {
            return $param;
        }
        if (! is_null($param)) {
            $param = trim($param);
        }

        if (! $param) {
            return array();
        }
        $param = preg_replace('/\s*\,\s*/u', ',', $param);
        return explode(',', $param);
    }

    /**
     * Carrega estilos CSS configurados.
     *
     * @return string HTML com as tags de estilo
     */
    public static function load_css(): string
    {
        $config = cCrudConfig::instance();

        if (self::$css_loaded) {
            // Estilos já carregados anteriormente
            return Services::response()->setStatusCode(409)->setBody(lang('cCrud.styles_already_rendered'));
        }

        self::$css_loaded = true;

        $out = '';
        foreach ($config->css_libs as $lib) {
            $out .= '<link href="' . $lib . '" rel="stylesheet" type="text/css" />';
        }

        $out .= '<link href="/' . trim($config->request_uri, '/') . '/css" rel="stylesheet" type="text/css" />';

        return $out;
    }

    /**
     * Carrega scripts JavaScript configurados.
     *
     * @return string HTML com as tags de script
     */
    public static function load_js(): string
    {
        if (self::$instance) {
            $instance = reset(self::$instance);
            $instance->_get_language();
        } else {
            self::_get_language_static();
            $instance = null; // usado apenas para obter o nome da tabela
        }
        $config = cCrudConfig::instance();

        if (self::$js_loaded) {
            // Scripts já carregados anteriormente
            return Services::response()->setStatusCode(409)->setBody(lang('cCrud.scripts_already_rendered'));
        }
        self::$js_loaded = true;

        $out = '';
        foreach ($config->js_libs as $lib) {
            $out .= '<script src="' . $lib . '"></script>';
        }

        $out .= '<script src="/' . trim($config->request_uri, '/') . '/js"></script>';

        $settings = [
            'url' => '/' . trim($config->request_uri, '/') . '/ajax',
            'table_name' => $instance ? $instance->table_name : '',
            'force_editor' => $config->force_editor,
            'date_first_day' => $config->date_first_day,
            'date_format' => $config->date_format,
            'time_format' => $config->time_format,
            'lang' => self::$lang_arr,
        ];
        $out .= '
            <script type="text/javascript">
            <!--

                var cCrud_config = ' . json_encode($settings) . ';

            -->
            </script>';

        return $out;
    }

    protected function getLimitList($limit = 20)
    {
        if ($this->result_total > $this->limit_list[0]) {
            $out = '';
            if (! in_array($this->limit, $this->limit_list)) {
                $this->limit_list = array_merge(array(
                    $this->limit
                ), $this->limit_list);
            }
            $container = [
                'tag' => 'div'
            ];
            $out .= $this->open_tag($container, 'dropdown ' . $this->theme_config('limit_list_container'));

            $button = [
                'tag' => 'button',
                'data-bs-toggle' => 'dropdown',
                'aria-expanded' => 'false'
            ];
            $out .= $this->open_tag($button, 'dropdown-toggle ' . $this->theme_config('limit_list_button'));
            $out .= $this->lang('limit_list_' . $this->limit_list[array_search($limit, $this->limit_list)]);
            $out .= $this->close_tag($button);

            $menu = [
                'tag' => 'ul'
            ];
            $out .= $this->open_tag($menu, 'dropdown-menu ' . $this->theme_config('limit_list_menu'));
            foreach ($this->limit_list as $limts) {
                if ($limts == $limit) {
                    $active = 'active';
                } else {
                    $active = '';
                }
                $out .= $this->open_tag('li') . $this->open_tag(array(
                    'tag' => 'a',
                    'class' => $active . ' dropdown-item cCrud-action ' . $this->theme_config('limit_list_item'),
                    'data-limit' => $limts
                )) . $this->lang('limit_list_' . $limts) . $this->close_tag('a') . $this->close_tag('li');
            }
            $out .= $this->close_tag($menu);
            $out .= $this->close_tag($container);
            return $out;
        }
    }

    /**
     *
     * @author Ariel Canal
     *         COMPATIBILIZAÇÃO COM A ARQUITETURA DO CODEIGINITER
     */
    protected function check_file($path, $func_name)
    {
        $path = str_replace('\\', '/', $path);
        list ($root_folder) = explode('/', trim(CCRUD_PATH, '/'), 2);
        list ($root_path) = explode('/', trim($path, '/'), 2);

        if (strpos($path, '../') !== false || $root_folder != $root_path) {
            $path = CCRUD_PATH . '/' . trim($path, '/');
        }

        if (! is_file($path))
            // Caminho ou arquivo inexistente
            return Services::response()->setStatusCode(404)->setBody(lang('cCrud.wrong_path_or_file', [$func_name, $path]));
        return $path;
    }

    protected function check_folder($path, $func_name)
    {
        $path = str_replace('\\', '/', $path);
        list ($root_folder) = explode('/', trim(CCRUD_PATH, '/'), 2);
        list ($root_path) = explode('/', trim($path, '/'), 2);
        if (strpos($path, '../') !== false or $root_folder != $root_path)
            $path = CCRUD_PATH . '/' . trim($path, '/');
        if (! is_dir($path)) {
            if (! @mkdir($path))
                // Caminho ou pasta inexistente
                return Services::response()->setStatusCode(404)->setBody(lang('cCrud.wrong_path_or_folder', [$func_name, $path]));
        }
        return $path;
    }

    protected function additional_columns($fields = '')
    {
        if ($fields) {
            $fdata = $this->_parse_field_names($fields, 'additional_column');
            foreach ($fdata as $key => $fitem) {
                if ($fitem['field'] != 'value' && mb_substr_count($key, '.') < 2) {
                    // if (! isset($this->subselect[$key]) && $fitem['field'] != 'value' && mb_substr_count($key, '.') < 2) {
                    if (! isset($this->columns[$key])) {
                        $this->hidden_columns[$key] = $fitem;
                    }
                    $this->direct_select_tags[$key] = array(
                        'field' => $fitem['field'],
                        'table' => $fitem['table']
                    ); // will
                       // be
                       // get
                       // from
                       // db
                       // anyway
                }
            }
        }
    }

    protected function additional_fields($fields = '')
    {
        if ($fields) {
            $fdata = $this->_parse_field_names($fields, 'additional_field');
            foreach ($fdata as $key => $fitem) {
                if (! isset($this->subselect[$key]) && $fitem['field'] != 'value' && mb_substr_count($key, '.') < 2) {
                    if (! isset($this->fields[$key])) {
                        $this->hidden_fields[$key] = array(
                            'field' => $fitem['field'],
                            'table' => $fitem['table']
                        );
                        $this->locked_fields[$key] = true;
                    }
                    $this->direct_select_tags[$key] = array(
                        'field' => $fitem['field'],
                        'table' => $fitem['table']
                    ); // will
                       // be
                       // get
                       // from
                       // db
                       // anyway
                }
            }
        }
    }

    /**
     * Unlocks additional postdata fields (locked with security reason).
     * This can be used only with callbacks
     */
    public function unlock_field($fields = '')
    {
        if ($fields) {
            $fdata = $this->_parse_field_names($fields, 'unlock_field');
            foreach ($fdata as $key => $fitem) {
                if (! isset($this->fields[$key])) {
                    $this->fields[$key] = $fitem;
                }
                if (isset($this->locked_fields[$key])) {
                    unset($this->locked_fields[$key]);
                }
            }
        }
    }

    protected function extract_fields_from_text($text, $mode = 'columns')
    {
        $found = preg_match_all('/\{([^\}]+)\}/u', $text, $matches);
        if ($found) {
            switch ($mode) {
                case 'columns':
                    $this->additional_columns($matches[1]);
                    break;
                case 'fields':
                    $this->additional_fields($matches[1]);
                    break;
            }
        }
    }

    protected function find_grid_text_variables(): void
    {
        if (! isset($this->config) || ! $this->config->performance_mode) {
            if ($this->column_pattern) {
                foreach ($this->column_pattern as $item) {
                    $this->extract_fields_from_text($item, 'columns');
                }
            }
            if ($this->buttons) {
                foreach ($this->buttons as $button) {
                    $this->extract_fields_from_text($button['link'], 'columns');
                    if ($button['params']) {
                        foreach ($button['params'] as $param) {
                            $this->extract_fields_from_text($param, 'columns');
                        }
                    }
                }
            }
            /*
             * if ($this->condition)
             * {
             * foreach ($this->condition as $item)
             * {
             * $this->extract_fields_from_text($item['value'], 'columns');
             * }
             * }
             * if ($this->grid_condition)
             * {
             * foreach ($this->grid_condition as $item)
             * {
             * $this->extract_fields_from_text($item['value'], 'columns');
             * }
             * }
             */
            if ($this->highlight) {
                foreach ($this->highlight as $item) {
                    foreach ($item as $itm) {
                        $this->extract_fields_from_text($itm['value'], 'columns');
                    }
                }
            }
            if ($this->highlight_row) {
                foreach ($this->highlight_row as $item) {
                    $this->extract_fields_from_text($item['value'], 'columns');
                }
            }
            if ($this->column_class) {
                foreach ($this->column_class as $item) {
                    $this->extract_fields_from_text(implode(' ', $item), 'columns');
                }
            }
            if ($this->grid_restrictions) {
                foreach ($this->grid_restrictions as $item) {
                    if (is_array($item)) {
                        foreach ($item as $i) {
                            $this->extract_fields_from_text($i['value'], 'columns');
                            $this->additional_columns($i['field']);
                        }
                    } else {
                        $this->extract_fields_from_text($item['value'], 'columns');
                        $this->additional_columns($i['field']);
                    }
                }
            }
            if ($this->action) {
                foreach ($this->action as $item) {
                    if (is_array($item['conditions'])) {
                        foreach ($item['conditions'] as $con) {
                            $this->additional_columns($con[0]);
                        }
                    } else {
                        $this->additional_columns($item['conditions']);
                    }
                }
            }
        }
    }

    protected function find_details_text_variables()
    {
        if ($this->send_external_create) {
            foreach ($this->send_external_create['data'] as $item) {
                $this->extract_fields_from_text($item, 'fields');
            }
            if ($this->send_external_create['where_field'])
                $this->additional_fields($this->send_external_create['where_field']);
        }
        if ($this->send_external_edit) {
            foreach ($this->send_external_edit['data'] as $item) {
                $this->extract_fields_from_text($item, 'fields');
            }
            if ($this->send_external_edit['where_field'])
                $this->additional_fields($this->send_external_edit['where_field']);
        }
        if ($this->pass_var) {
            foreach ($this->pass_var as $mode => $actions) {
                foreach ($actions as $vars) {
                    $this->extract_fields_from_text($vars['value'], 'fields');
                }
            }
        }
        if ($this->relation) {
            foreach ($this->relation as $field => $params) {
                if ($params['rel_where']) {
                    if (is_array($params['rel_where'])) {
                        foreach ($params['rel_where'] as $vars) {
                            $this->extract_fields_from_text($vars, 'fields');
                        }
                    } else {
                        $this->extract_fields_from_text($params['rel_where'], 'fields');
                    }
                }
            }
        }
        if ($this->fk_relation) {
            foreach ($this->fk_relation as $field => $params) {
                if ($params['rel_where']) {
                    if (is_array($params['rel_where'])) {
                        foreach ($params['rel_where'] as $vars) {
                            $this->extract_fields_from_text($vars, 'fields');
                        }
                    } else {
                        $this->extract_fields_from_text($params['rel_where'], 'fields');
                    }
                }
            }
        }

        /*
         * ALTERADO DO PADRÃO - Compatibilização com array de
         * grid_restrictions
         */
        if ($this->grid_restrictions) {
            foreach ($this->grid_restrictions as $items) {
                foreach ($items as $item) {
                    $this->extract_fields_from_text($item['value'], 'fields');
                    $this->additional_fields($item['field']);
                }
            }
        }
        if ($this->column_pattern) {
            foreach ($this->column_pattern as $item) {
                $this->extract_fields_from_text($item, 'fields');
            }
        }
        if ($this->condition) {
            foreach ($this->condition as $item) {
                $this->extract_fields_from_text($item['value'], 'fields');
                $this->additional_fields($item['field']);
            }
        }
    }

    protected function replace_text_variables($value, array $data, $safety = false, $null_if_empty = false)
    {
        if (! is_array($value) && ! $this->config->performance_mode && $value) {
            foreach ($data as $key => $val) {
                $tmp = explode('.', $key);
                if (count($tmp) > 1) {
                    list ($tbl, $fld) = $tmp;
                } else {
                    $tbl = $this->table;
                    $fld = $val;
                }
                if (! is_array($val) && ! is_null($val)) {
                    $value = str_ireplace('{' . $key . '}', $safety ? \esc($val) : $val, $value);
                    if ($tbl == $this->table) {
                        $value = str_ireplace('{' . $fld . '}', $safety ? \esc($val) : $val, $value);
                    }
                }
            }
        }
        if ($value === '' && $null_if_empty) {
            $value = 'NULL';
        }
        return $value;
    }

    protected function get_browser_info($ch)
    {
        $request = Services::request();
        $cookies = $request->getCookie();
        if (! empty($cookies)) {
            $ca = http_build_query($cookies);
            $ca = str_replace('&', ';', $ca);
            curl_setopt($ch, CURLOPT_COOKIE, $ca);
        }
        curl_setopt($ch, CURLOPT_REFERER, $request->getServer('HTTP_REFERER'));
        curl_setopt($ch, CURLOPT_USERAGENT, $request->getServer('HTTP_USER_AGENT'));
    }

    /**
     * Envia uma requisição HTTP.
     *
     * @param string $url           Endereço da requisição.
     * @param array  $data          Dados a serem enviados.
     * @param string $method        Método HTTP utilizado.
     * @param bool   $return_result Define se o resultado deve ser retornado.
     *
     * @return mixed Resultado da requisição ou null.
     */
    protected function send_http_request(string $url, array $data, string $method, bool $return_result = false): mixed
    {
        $charset = config('App')->charset;
        $path    = $url;
        $data    = http_build_query($data);

        switch ($method) {
            case 'get':
                $ch = curl_init($path . ((mb_strpos($path, '?', 0, $charset) === false) ? '?' : '&') . $data);
                break;
            case 'post':
                $ch = curl_init($path);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;
            default:
                return null;
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        if (! $return_result) {
            curl_setopt($ch, CURLOPT_NOBODY, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, 100);
        } else {
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        }
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        // curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        if ($this->config->use_browser_info) {
            $this->get_browser_info($ch);
        }
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    protected function open_tag($tag = '', $class = '', $attr = array())
    {
        if ($tag) {
            if (! is_array($tag)) {
                $tag = array(
                    'tag' => $tag
                );
            }
            if (isset($tag['tag'])) {
                $out = '<' . $tag['tag'];
                unset($tag['tag']);
                if ($attr) {
                    if (isset($attr['values'])) {
                        unset($attr['values']);
                    }
                    $tag = array_merge($tag, $attr);
                }
                if ($class && isset($tag['class'])) {
                    $tag['class'] .= ' ' . $class;
                } elseif ($class) {
                    $tag['class'] = $class;
                }
                if ($this->config->encode_field_names && isset($tag['data-depend'])) {
                    $tag['data-depend'] = $this->fieldname_encode($tag['data-depend']);
                }
                if ($this->config->encode_field_names && isset($tag['data-rangestart'])) {
                    $tag['data-rangestart'] = $this->fieldname_encode($tag['data-rangestart']);
                }
                if ($this->config->encode_field_names && isset($tag['data-rangeend'])) {
                    $tag['data-rangeend'] = $this->fieldname_encode($tag['data-rangeend']);
                }
                if ($tag) {
                    foreach ($tag as $key => $val) {
                        if ($key == 'href' or $key == 'src') {
                            $out .= ' ' . (string) $key . '="' . (string) $val . '"';
                        } else {
                            $out .= ' ' . (string) $key . '="' . \esc((string) $val) . '"';
                        }
                    }
                }
                $out .= '>';
                return $out;
            }
        } else
            return '';
    }

    protected function close_tag($tag = '')
    {
        if ($tag) {
            if (! is_array($tag)) {
                $tag = array(
                    'tag' => $tag
                );
            }
            if (isset($tag['tag'])) {
                return '</' . $tag['tag'] . '>';
            }
        } else
            return '';
    }

    protected function single_tag($tag = '', $class = '', $attr = array())
    {
        if ($tag) {
            if (! is_array($tag)) {
                $tag = array(
                    'tag' => $tag
                );
            }
            if (isset($tag['tag'])) {
                $out = '<' . $tag['tag'];
                unset($tag['tag']);
                if ($attr) {
                    if (isset($attr['values'])) {
                        unset($attr['values']);
                    }
                    $tag = array_merge($tag, $attr);
                }
                if ($class && isset($tag['class'])) {
                    $tag['class'] .= ' ' . $class;
                } elseif ($class) {
                    $tag['class'] = $class;
                }
                if ($this->config->encode_field_names && isset($tag['data-depend'])) {
                    $tag['data-depend'] = $this->fieldname_encode($tag['data-depend']);
                }
                if ($this->config->encode_field_names && isset($tag['data-rangestart'])) {
                    $tag['data-rangestart'] = $this->fieldname_encode($tag['data-rangestart']);
                }
                if ($this->config->encode_field_names && isset($tag['data-rangeend'])) {
                    $tag['data-rangeend'] = $this->fieldname_encode($tag['data-rangeend']);
                }
                if ($tag) {
                    foreach ($tag as $key => $val) {
                        if ($key == 'href' or $key == 'src') {
                            $out .= ' ' . (string) $key . '="' . (string) $val . '"';
                        } else {
                            $out .= ' ' . (string) $key . '="' . \esc((string) $val) . '"';
                        }
                    }
                }
                $out .= ' />';
                return $out;
            }
        } else
            return '';
    }

    protected function open_label_tag($field_key, $label_tag = 'td')
    {
        $label_class = $this->theme_config('details_label_cell');
        $attr = [];
        if (! empty($this->field_attr[$field_key]['id'])) {
            $attr['for'] = $this->field_attr[$field_key]['id'];
        }
        return $this->open_tag($label_tag, $label_class, $attr);
    }

    /**
     * Renderiza mensagem de erro de validação para o campo
     *
     * @param array $field Dados do campo
     * @return string HTML com a mensagem de erro
     */
    protected function render_field_exception($field)
    {
        $out = '';
        if (array_key_exists('exception', $field)) {
            $out .= $this->open_tag('div', $this->theme_config('validation_error_text')) . $field['exception'] . $this->close_tag('div');
        }
        return $out;
    }

    /**
     * Renderiza a lista de campos
     *
     * @param string              $mode        Modo atual
     * @param string|array|null   $container   Tag do contêiner
     * @param string|array|null   $row         Tag da linha
     * @param string|array|null   $label       Tag do rótulo
     * @param string|array|null   $field       Tag do campo
     * @param string|array|null   $tabs_block  Tag do bloco de abas
     * @param string|array|null   $tabs_head   Tag do cabeçalho das abas
     * @param string|array|null   $tabs_row    Tag da linha das abas
     * @param string|array|null   $tabs_link   Tag do link das abas
     * @param string|array|null   $tabs_content Tag do conteúdo das abas
     * @param string|array|null   $tabs_pane   Tag do painel das abas
     *
     * @return string
     */
    public function render_fields_list($mode, $container = null, $row = null, $label = null, $field = null, $tabs_block = null, $tabs_head = null, $tabs_row = null, $tabs_link = null, $tabs_content = null, $tabs_pane = null)
    {
        $out = '';
        $container   = $container   ?? $this->theme_config('fields_list_container_tag');
        $row         = $row         ?? $this->theme_config('fields_list_row_tag');
        $label       = $label       ?? $this->theme_config('fields_list_label_tag');
        $field       = $field       ?? $this->theme_config('fields_list_field_tag');
        $tabs_block  = $tabs_block  ?? $this->theme_config('fields_list_tabs_block_tag');
        $tabs_head   = $tabs_head   ?? $this->theme_config('fields_list_tabs_head_tag');
        $tabs_row    = $tabs_row    ?? $this->theme_config('fields_list_tabs_row_tag');
        $tabs_link   = $tabs_link   ?? $this->theme_config('fields_list_tabs_link_tag');
        $tabs_content = $tabs_content ?? $this->theme_config('fields_list_tabs_content_tag');
        $tabs_pane   = $tabs_pane   ?? $this->theme_config('fields_list_tabs_pane_tag');
        $tabs_out = array();
        $raw_out = array();
        foreach ($this->fields_output as $key => $item) {
            if (in_array($key, array_keys($this->custom_fields))) {
                // continue;
            }
            $row_class = $this->theme_config('details_row');
            if ($this->primary_key == $item['name']) {
                $row_class .= ' primary';
            }

            if ($mode == 'report' && $this->parameters[$item['label']]['operator'] == 'between') {
                $item['field'] .= $this->open_tag('span') . ' até ' . $this->close_tag('span');
                $item['field'] .= $this->fields_output[$key . '_to']['field'];
                $row_class_ = $row_class . ' between_field';
            } else {
                $row_class_ = $row_class;
            }
            $field_rendered = $this->open_tag($row, $row_class_) . $this->open_label_tag($key, $label) . $item['label'] . $this->get_field_tooltip($item['name'], $mode) . $this->close_tag($label) . $this->open_tag($field, $this->theme_config('details_field_cell')) . $item['field'] . $this->close_tag($field) . $this->render_field_exception($item) . $this->close_tag($row);
            if (isset($this->fields[$item['name']]['tab']) && $this->fields[$item['name']]['tab']) {
                $tabs_out[$this->fields[$item['name']]['tab']][] = $field_rendered;
            } else {

                $raw_out[] = $field_rendered;
            }
        }
        if (isset($this->field_tabs[$mode]) or $this->default_tab !== false) {
            $tabs_header = $this->open_tag($tabs_block, $this->theme_config('tabs_container'), array(
                'class' => 'cCrud-tabs'
            )) . $this->open_tag($tabs_head, $this->theme_config('tabs_header_row'));
            $tabs_body = $this->open_tag($tabs_content, $this->theme_config('tabs_content'));
            $k = 0;

            if ($this->default_tab !== false && $raw_out) {
                $tabid = 'tabid_' . base_convert(rand(), 10, 36);
                $tabs_header .= $this->open_tag($tabs_row, $this->theme_config('tabs_header_cell') . ($k == 0 ? ' ' . $this->theme_config('tabs_first_element') : '')) . $this->open_tag($tabs_link, $this->theme_config('tabs_header_link') . ($k == 0 ? ' ' . $this->theme_config('tabs_first_element') : ''), array(
                    'href' => '#' . $tabid
                )) . $this->default_tab . $this->close_tag($tabs_link) . $this->close_tag($tabs_row);
                $tabs_body .= $this->open_tag($tabs_pane, $this->theme_config('tabs_content_pane') . ($k == 0 ? ' ' . $this->theme_config('tabs_first_element') : ''), array(
                    'id' => $tabid
                )) . $this->open_tag($container, $this->theme_config('details_container')) . implode('', $raw_out) . $this->close_tag($container) . $this->close_tag($tabs_pane);
                ++ $k;
                $raw_out = array();
            }

            $tab_count = 0;
            if ((isset($this->field_tabs[$mode]) && $tabs_out) || ($this->nested_rendered && $this->config->nested_in_tab)) {
                $tab_total = count($this->field_tabs[$mode]) + count($this->nested_rendered);
                if ($tab_total < $this->opened_tab || ! $this->opened_tab) {
                    $old_val = $this->opened_tab;
                    $this->opened_tab = 1;
                }
            }
            if (isset($this->field_tabs[$mode]) && $tabs_out) {
                foreach ($this->field_tabs[$mode] as $key => $tabname) {
                    if (isset($tabs_out[$tabname])) {
                        $tab_count ++;
                        $class = '';
                        if ($this->opened_tab && $tab_count == $this->opened_tab) {
                            $class = $this->theme_config('tabs_first_element');
                        } else if (! $this->opened_tab && $k == 0) {
                            $class = $this->theme_config('tabs_first_element');
                        } else {
                            $class = '';
                        }

                        $tabid = 'tabid_' . base_convert(rand(), 10, 36);
                        $tabs_header .= $this->open_tag($tabs_row, $this->theme_config('tabs_header_cell') . ' ' . $class) . $this->open_tag($tabs_link, $this->theme_config('tabs_header_link') . ' ' . $class, array(
                            'href' => '#' . $tabid
                        )) . $tabname . $this->close_tag($tabs_link) . $this->close_tag($tabs_row);
                        $tabs_body .= $this->open_tag($tabs_pane, $this->theme_config('tabs_content_pane') . ' ' . $class, array(
                            'id' => $tabid
                        )) . $this->open_tag($container, $this->theme_config('details_container')) . implode('', $tabs_out[$tabname]) . $this->close_tag($container) . $this->close_tag($tabs_pane);
                        ++ $k;
                    }
                }
            }

            if ($this->nested_rendered && $this->config->nested_in_tab) {
                foreach ($this->nested_rendered as $tabname => $content) {
                    $tab_count ++;
                    $class = '';
                    if ($this->opened_tab && $tab_count == $this->opened_tab) {
                        $class = $this->theme_config('tabs_first_element');
                    } else if (! $this->opened_tab && $k == 0) {
                        $class = $this->theme_config('tabs_first_element');
                    } else {
                        $class = '';
                    }

                    $tabid = 'tabid_' . base_convert(rand(), 10, 36);
                    $tabs_header .= $this->open_tag($tabs_row, $this->theme_config('tabs_header_cell') . ' ' . $class) . $this->open_tag($tabs_link, $this->theme_config('tabs_header_link') . ' ' . $class, array(
                        'href' => '#' . $tabid
                    )) . $tabname . $this->close_tag($tabs_link) . $this->close_tag($tabs_row);
                    $tabs_body .= $this->open_tag($tabs_pane, $this->theme_config('tabs_content_pane') . ' ' . $class, array(
                        'id' => $tabid
                    )) . $content . $this->close_tag($tabs_pane);
                    ++ $k;
                    unset($this->nested_rendered[$tabname]);
                }
            }
            if (isset($old_val)) {
                $this->opened_tab = $old_val;
            }

            $out .= $tabs_header . $this->close_tag($tabs_head) . $tabs_body . $this->close_tag($tabs_content) . $this->close_tag($tabs_block);
        }

        if ($raw_out) {
            $out .= $this->open_tag($container, $this->theme_config('details_container')) . implode('', $raw_out) . $this->close_tag($container);
        }
        // $out .= implode('', $this->hidden_fields_output);
        return $out;
    }

    protected function render_tab($tab, $mode, $container = 'table', $row = 'tr', $label = 'td', $field = 'td', $tabs_block = 'div', $tabs_head = 'ul', $tabs_row = 'li', $tabs_link = 'a', $tabs_content = 'div', $tabs_pane = 'div')
    {
        $out = '';
        $tabs_out = array();
        $raw_out = array();
        foreach ($this->fields_output as $key => $item) {
            $row_class = $this->theme_config('details_row');
            if ($this->primary_key == $item['name']) {
                $row_class .= ' primary';
            }

            $row_rendered = $this->open_tag($row, $row_class) . $this->open_tag($label, $this->theme_config('details_label_cell')) . $item['label'] . $this->get_field_tooltip($item['name'], $mode) . $this->close_tag($label) . $this->open_tag($field, $this->theme_config('details_field_cell')) . $item['field'] . $this->close_tag($field) . $this->render_field_exception($item) . $this->close_tag($row);
            ;
            if (isset($this->fields[$item['name']]['tab']) && $this->fields[$item['name']]['tab']) {
                $tabs_out[$this->fields[$item['name']]['tab']][] = $row_rendered;
            } else {
                $raw_out[] = $row_rendered;
            }
        }
        if ((isset($this->field_tabs[$mode]) or $this->default_tab !== false) && ! $this->config->tabs_in_widgets) {
            $tabs_header = $this->open_tag($tabs_block, $this->theme_config('tabs_container'), array(
                'class' => 'cCrud-tabs'
            )) . $this->open_tag($tabs_head, $this->theme_config('tabs_header_row'));
            $tabs_body = $this->open_tag($tabs_content, $this->theme_config('tabs_content'));
            $k = 0;

            if ($this->default_tab !== false && $raw_out) {
                $tabid = 'tabid_' . base_convert(rand(), 10, 36);
                $tabs_header .= $this->open_tag($tabs_row, $this->theme_config('tabs_header_cell') . ($k == 0 ? ' ' . $this->theme_config('tabs_first_element') : '')) . $this->open_tag($tabs_link, $this->theme_config('tabs_header_link') . ($k == 0 ? ' ' . $this->theme_config('tabs_first_element') : ''), array(
                    'href' => '#' . $tabid
                )) . $this->default_tab . $this->close_tag($tabs_link) . $this->close_tag($tabs_row);
                $tabs_body .= $this->open_tag($tabs_pane, $this->theme_config('tabs_content_pane') . ($k == 0 ? ' ' . $this->theme_config('tabs_first_element') : ''), array(
                    'id' => $tabid
                )) . $this->open_tag($container, $this->theme_config('details_container')) . implode('', $raw_out) . $this->close_tag($container) . $this->close_tag($tabs_pane);
                ++ $k;
                $raw_out = array();
            }

            if (isset($this->field_tabs[$mode]) && $tabs_out) {
                foreach ($this->field_tabs[$mode] as $key => $tabname) {
                    if ($key == $tab) {
                        if (isset($tabs_out[$tabname])) {
                            $tabid = 'tabid_' . base_convert(rand(), 10, 36);
                            $tabs_header .= $this->open_tag($tabs_row, $this->theme_config('tabs_header_cell') . ($k == 0 ? ' ' . $this->theme_config('tabs_first_element') : '')) . $this->open_tag($tabs_link, $this->theme_config('tabs_header_link') . ($k == 0 ? ' ' . $this->theme_config('tabs_first_element') : ''), array(
                                'href' => '#' . $tabid
                            )) . $tabname . $this->close_tag($tabs_link) . $this->close_tag($tabs_row);
                            $tabs_body .= $this->open_tag($tabs_pane, $this->theme_config('tabs_content_pane') . ($k == 0 ? ' ' . $this->theme_config('tabs_first_element') : ''), array(
                                'id' => $tabid
                            )) . $this->open_tag($container, $this->theme_config('details_container')) . implode('', $tabs_out[$tabname]) . $this->close_tag($container) . $this->close_tag($tabs_pane);
                            ++ $k;
                        }
                    }
                }
            }
            $out .= $tabs_header . $this->close_tag($tabs_head) . $tabs_body . $this->close_tag($tabs_content) . $this->close_tag($tabs_block);
        }
        if ((isset($this->field_tabs[$mode]) or $this->default_tab !== false) && $this->config->tabs_in_widgets) {
            $k = 0;
            if (isset($this->field_tabs[$mode]) && $tabs_out) {
                foreach ($this->field_tabs[$mode] as $key => $tabname) {
                    if ($key == $tab) {
                        $class_widget_container = 'widget';
                        if ($this->config->widgets_open != 'all' && $this->config->widgets_open != 'none' && $k >= (int) $this->config->widgets_open)
                            $class_widget_container .= ' widget-closed';
                        else if ($this->config->widgets_open == 'none')
                            $class_widget_container .= ' widget-closed';
                        $widget .= $this->open_tag('div', $class_widget_container, array(
                            'class' => 'cCrud-widgets'
                        ));
                        $widget .= $this->open_tag('div', 'widget-header');
                        $widget .= $this->open_tag('h4');
                        $widget .= $this->open_tag('i', 'fas fa-bars') . $this->close_tag('i');
                        $widget .= $tabname;
                        $widget .= $this->close_tag('h4');

                        $widget .= $this->open_tag('div', 'toolbar');
                        $widget .= $this->open_tag('div', 'btn-group');
                        $widget .= $this->open_tag('span', 'btn btn-xs widget-collapse');
                        $widget .= $this->open_tag('i', 'fas fa-angle-down') . $this->close_tag('i');
                        $widget .= $this->close_tag('span');
                        $widget .= $this->close_tag('div') . $this->close_tag('div') . $this->close_tag('div');

                        $widget .= $this->open_tag('div', 'widget-content');
                        if (isset($tabs_out[$tabname])) {

                            $widget .= implode('', $tabs_out[$tabname]);
                            ++ $k;
                        }
                        $widget .= $this->close_tag('div');
                        $widget .= $this->close_tag('div');
                    }
                }
            }

            $out .= $widget;
        }

        if ($raw_out) {
            $out .= $this->open_tag($container, $this->theme_config('details_container')) . implode('', $raw_out) . $this->close_tag($container);
        }
        // $out .= implode('', $this->hidden_fields_output);
        return $out;
    }

    /**
     * table tooltip render
     */
    protected function get_table_tooltip()
    {
        $out = '';
        if ($this->table_tooltip) {
            $out .= ' ';
            $out .= $this->open_tag(array(
                'tag' => 'a',
                'href' => 'javascript:;',
                'class' => 'cCrud-tooltip cCrud-button-link',
                'title' => $this->table_tooltip['tooltip']
            ));
            $out .= $this->open_tag(array(
                'tag' => 'i',
                'class' => ($this->table_tooltip['icon'] ? $this->table_tooltip['icon'] : $this->theme_config('tooltip_icon'))
            ));
            $out .= $this->close_tag('i');
            $out .= $this->close_tag('a');
        }
        return $out;
    }

    /**
     * field tooltip render
     */
    protected function get_field_tooltip($field, $mode)
    {
        $out = '';
        if ($this->field_tooltip && isset($this->field_tooltip[$field])) {
            $out .= ' ';
            $out .= $this->open_tag(array(
                'tag' => 'a',
                'href' => 'javascript:;',
                'class' => 'cCrud-tooltip cCrud-button-link',
                'title' => $this->field_tooltip[$field]['tooltip']
            ));
            $out .= $this->open_tag(array(
                'tag' => 'i',
                'class' => ($this->field_tooltip[$field]['icon'] ? $this->field_tooltip[$field]['icon'] : $this->theme_config('tooltip_icon'))
            ));
            $out .= $this->close_tag('i');
            $out .= $this->close_tag('a');
        }
        return $out;
    }

    /**
     * column tooltip render
     */
    protected function get_column_tooltip($field)
    {
        $out = '';
        if ($this->column_tooltip && isset($this->column_tooltip[$field])) {
            $out .= ' ';
            $out .= $this->open_tag(array(
                'tag' => 'a',
                'href' => 'javascript:;',
                'class' => 'cCrud-tooltip cCrud-button-link',
                'title' => $this->column_tooltip[$field]['tooltip']
            ));
            $out .= $this->open_tag(array(
                'tag' => 'i',
                'class' => ($this->column_tooltip[$field]['icon'] ? $this->column_tooltip[$field]['icon'] : $this->theme_config('tooltip_icon'))
            ));
            $out .= $this->close_tag('i');
            $out .= $this->close_tag('a');
        }
        return $out;
    }

    /**
     *
     * @author Ariel Canal
     *         Compatibilização com os plugins JS
     *         search constructor and renderer
     */
    protected function search_button($search_label = false)
    {
        $attr = [
            'data-bs-toggle' => 'collapse',
            'href' => '#search_' . $this->table_name
        ];
        if ($this->search || $this->config->search_opened) {
            $attr['aria-expanded'] = 'true';
        } else {
            $attr['aria-expanded'] = 'false';
        }
        $out = '';
        $out .= $this->open_tag('button', 'cCrud-search-toggle ' . $this->theme_config('search_toggle'), $attr);
        $out .= $this->open_tag('i', $this->theme_config('search_toggle_icon')) . $this->close_tag('i');
        if (! empty($$search_label)) {
            $out .= $this->open_tag('span');
            $out .= $search_label;
            $out .= $this->close_tag('span');
        }
        $out .= $this->close_tag('button');
        return $out;
    }

    /**
     *
     * @author Ariel Canal
     *         Compatibilização com os plugins JS
     *         search constructor and renderer
     */
    /**
     * Renderiza o bloco de busca
     *
     * @return string
     */
    public function render_search()
    {
        $out = '';
        $phrase = '';
        $optlist = array();
        $fieldlist = array();
        $is_daterange = false;
        if (! is_int($this->search_lines)) {
            $this->search_lines = 1;
        }

        if ($this->is_search) {
            $container_tag = [
                'tag' => 'div',
                'class' => 'cCrud-search-form collapse',
                'id' => 'search_' . $this->table_name
            ];
            if ($this->search || $this->config->search_opened) {
                $container_tag['class'] = ' show';
            }
            // ABRE CONTAINER
            $out .= $this->open_tag($container_tag, $this->theme_config('search_container'));

            // LINES
            for ($i = 1; $i <= $this->search_lines; $i ++) {
                $fieldlist = array();
                if (isset($this->search_submit[$i]) && isset($this->search_submit[$i]['phrase']) && is_array($this->search_submit[$i]['phrase']) && isset($this->field_type[$this->search_submit[$i]['column']])) {
                    switch ($this->field_type[$this->search_submit[$i]['column']]) {
                        case 'timestamp':
                        case 'datetime':
                            $phrase = array(
                                'from' => $this->search_submit[$i]['phrase']['from'],
                                'to' => $this->search_submit[$i]['phrase']['to']
                            );
                            $is_daterange = true;
                            break;
                        case 'date':
                            $phrase = array(
                                'from' => $this->search_submit[$i]['phrase']['from'],
                                'to' => $this->search_submit[$i]['phrase']['to']
                            );
                            $is_daterange = true;
                            break;
                        case 'time':
                            $phrase = array(
                                'from' => $this->search_submit[$i]['phrase']['from'],
                                'to' => $this->search_submit[$i]['phrase']['to']
                            );
                            break;
                        default:
                            $phrase = '';
                            break;
                    }
                } else if (isset($this->search_submit[$i]['phrase'])) {
                    $phrase = $this->search_submit[$i]['phrase'];
                }
                $optlist = array();
                if ($this->config->search_all) {
                    $optlist[] = $this->open_tag('option', '', array(
                        'value' => ''
                    )) . $this->lang('all_fields') . $this->close_tag('option');
                    $fieldlist = $this->search_fieldlist('', $phrase, $fieldlist, $i);
                }
                if ($this->search_columns) {
                    foreach ($this->search_columns as $field => $tmp) {
                        if (in_array($field, array_keys($this->report))) {
                            continue;
                        }
                        if (isset($this->columns_names[$field])) {
                            $name = $this->columns_names[$field];
                        } else {
                            if (isset($this->labels[$field])) {
                                $name = \esc($this->labels[$field]);
                            } else {
                                $name = \esc($this->_humanize($tmp['field']));
                            }
                        }
                        $option = [
                            'tag' => 'option',
                            'value' => $field,
                            'data-type' => $this->field_type[$field]
                        ];
                        if ($field == $this->search_submit[$i]['column']) {
                            $option['selected'] = '';
                        }
                        $optlist[] = $this->open_tag($option) . $name . $this->close_tag($option);
                        $fieldlist = $this->search_fieldlist($field, $phrase, $fieldlist, $i);
                    }
                } elseif ($this->search_default !== false) { // not only 'all'
                    foreach ($this->columns_names as $field => $title) {
                        if (in_array($field, array_keys($this->report))) {
                            continue;
                        }
                        $option = [
                            'tag' => 'option',
                            'value' => $field,
                            'data-type' => $this->field_type[$field]
                        ];
                        if (isset($this->search_submit[$i]) && $field == $this->search_submit[$i]['column']) {
                            $option['selected'] = '';
                        }
                        $optlist[] = $this->open_tag($option) . $title . $this->close_tag($option);
                        $fieldlist = $this->search_fieldlist($field, $phrase, $fieldlist, $i);
                    }
                }
                // LINE CONTENT
                $line_container = [
                    'tag' => 'span'
                ];
                // $out .= $this->open_tag($line_container, 'line_container '.$this->theme_config('search_line_container'));
                $out .= implode('', $fieldlist);
                $name = 'search_submit][' . $i . '][column';
                if ($this->search_default === false && ! $this->search_columns) {
                    $input = [
                        'tag' => 'input',
                        'type' => 'hidden',
                        'name' => $name
                    ];
                    $out .= $this->single_tag($input, 'cCrud-columns-select cCrud-data ' . $this->theme_config('search_fieldlist'));
                } else {
                    $select = [
                        'tag' => 'select',
                        'name' => $name
                    ];
                    $out .= $this->open_tag($select, 'cCrud-columns-select cCrud-data ' . $this->theme_config('search_fieldlist'));
                    // $out .= $this->open_tag('option', '', array('value' => '')) .
                    // $this->lang('all_fields') . $this->close_tag('option');
                    $out .= implode('', $optlist);
                    $out .= $this->close_tag($select);
                }
                // $out .= $this->close_tag($line_container);
            }

            // BOTÕES
            $button_group = array(
                'tag' => 'span',
                'class' => $this->theme_config('search_button_group')
            );
            // $out .= $this->open_tag($button_group);

            $attr = array(
                'class' => 'cCrud-action',
                'href' => 'javascript:;',
                'data-search' => 1
            );
            $out .= $this->open_tag('button', $this->theme_config('search_go'), $attr);
            $out .= $this->lang('go');
            $out .= $this->close_tag('button');
            if ($this->search) {
                $attr = array(
                    'class' => 'cCrud-action',
                    'href' => 'javascript:;',
                    'data-search' => 0
                );
                $out .= $this->open_tag('button', $this->theme_config('search_reset'), $attr);
                $out .= $this->lang('reset') . $this->close_tag('button');
            }

            // $out .= $this->close_tag($button_group);

            // FECHA CONTAINER
            $out .= $this->close_tag($container_tag);
        }
        return $out;
    }

    /**
     * this creates unique field types for search
     */
    protected function search_fieldlist($field, $phrase, $fieldlist, $line = 1)
    {
        $attr_preset = array(
            'class' => 'cCrud-searchdata',
            'name' => 'search_submit][' . $line . '][phrase'
        );
        if ($field == @$this->search_submit[$line]['column']) {
            $class = 'cCrud-search-active';
        } else {
            $class = '';
            $attr_preset['style'] = 'display:none';
        }
        $attr = $attr_preset;
        $attr['data-type'] = $field ? $this->field_type[$field] : 'text';
        switch ($attr['data-type']) {
            case 'text':
            case 'textarea':
            case 'int':
            case 'float':
            default:
                if (! isset($fieldlist['default']) or $field == @$this->search_submit[$line]['column']) {
                    if (@! $this->search_submit[$line]['column']) {
                        $class = 'cCrud-search-active';
                        $attr['style'] = '';
                    }
                    $attr['data-fieldtype'] = 'default';
                    $attr['type'] = 'text';
                    $attr['value'] = (! is_array($phrase) && $field == @$this->search_submit[$line]['column'] or ! @$this->search_submit[$line]['column']) ? $phrase : '';
                    $fieldlist['default'] = $this->single_tag('input', $class . ' ' . $this->theme_config('search_phrase'), $attr);
                }
                break;
            case 'bool':
                if (! isset($fieldlist['bool']) or $field == @$this->search_submit[$line]['column']) {
                    $attr['name'] = 'search_submit][' . $line . '][phrase';
                    $attr['data-fieldtype'] = 'bool';
                    $attr['type'] = 'select';
                    $fieldlist['bool'] = $this->open_tag('select', $class . ' ' . $this->theme_config('search_phrase_dropdown'), $attr);
                    $attr = array(
                        'value' => 1
                    );
                    if ($phrase == 1) {
                        $attr['selected'] = '';
                    }
                    $fieldlist['bool'] .= $this->open_tag('option', '', $attr) . $this->lang('bool_on') . $this->close_tag('option');
                    $attr = array(
                        'value' => 0
                    );
                    if ($phrase == 0) {
                        $attr['selected'] = '';
                    }
                    $fieldlist['bool'] .= $this->open_tag('option', '', $attr) . $this->lang('bool_off') . $this->close_tag('option');
                    $fieldlist['bool'] .= $this->close_tag('select');
                }
                break;
            case 'date':
            case 'datetime':
            case 'time':
            case 'timestamp':
                if (! isset($fieldlist['date']) or $field == @$this->search_submit[$line]['column']) {
                    $attr['data-fieldtype'] = 'date';
                    $attr_range = array(
                        'class' => 'cCrud-daterange cCrud-searchdata ' . $this->theme_config('search_range'),
                        'name' => 'search_submit][' . $line . '][range',
                        'data-fieldtype' => 'date'
                    );
                    if ($field != @$this->search_submit[$line]['column']) {
                        $attr_range['style'] = 'display:none';
                    }

                    $fieldlist['date'] = $this->open_tag('select', $class, $attr_range);
                    $fieldlist['date'] .= $this->open_tag('option', '', array(
                        'value' => ''
                    )) . $this->lang('choose_range') . $this->close_tag('option');
                    if ($this->config->available_date_ranges) {
                        foreach ($this->config->available_date_ranges as $range) {
                            $attr_rs = array(
                                'value' => $range
                            );
                            if (isset($this->search_submit[$line]['range']) && $range == $this->search_submit[$line]['range']) {
                                $attr_rs['selected'] = '';
                            }
                            $curr_range = $this->get_range($range);
                            if ($curr_range) {
                                $attr_rs['data-from'] = $curr_range['from'];
                                $attr_rs['data-to'] = $curr_range['to'];
                                $fieldlist['date'] .= $this->open_tag('option', '', $attr_rs) . $this->lang($range) . $this->close_tag('option');
                            }
                        }
                    }
                    $fieldlist['date'] .= $this->close_tag('select');
                    $attr['type'] = 'text';
                    $attr['name'] = 'search_submit][' . $line . '][phrase][from';
                    $attr['value'] = ((isset($phrase['from']) && $field == @$this->search_submit[$line]['column']) ? $phrase['from'] : '');
                    // $fieldlist['date'] .= $this->open_tag('span', 'cCrud-range');
                    $fieldlist['date'] .= $this->single_tag('input', 'cCrud-datepicker-from ' . $class . ' ' . $this->theme_config('search_from'), $attr);
                    $attr['name'] = 'search_submit][' . $line . '][phrase][to';
                    $attr['value'] = (isset($phrase['to']) && $field == @$this->search_submit[$line]['column']) ? $phrase['to'] : '';
                    $fieldlist['date'] .= $this->single_tag('input', 'cCrud-datepicker-to ' . $class . ' ' . $this->theme_config('search_to'), $attr);
                }
                break;
            case 'select':
            case 'multiselect':
            case 'radio':
            case 'checkboxes':
                $attr['data-fieldtype'] = 'dropdown';
                $attr['data-fieldname'] = $field;
                $tmp = '';
                $tmp .= $this->open_tag('select', $class . ' ' . $this->theme_config('search_phrase_dropdown'), $attr);
                if (is_array($this->field_attr[$field]['values'])) {
                    foreach ($this->field_attr[$field]['values'] as $optkey => $opt) {
                        if (is_array($opt)) {
                            $tmp .= $this->open_tag(array(
                                'tag' => 'optgroup',
                                'label' => $optkey
                            ));
                            foreach ($opt as $k_key => $k_opt) {
                                $opt_tag = array(
                                    'tag' => 'option',
                                    'value' => $k_key
                                );
                                if ($k_key == $phrase && $field == $this->search_submit[$line]['column']) {
                                    $opt_tag['selected'] = '';
                                }
                                $tmp .= $this->open_tag($opt_tag) . \esc($k_opt) . $this->close_tag($opt_tag);
                            }
                            $tmp .= $this->close_tag('optgroup');
                        } else {
                            $opt_attr = array(
                                'value' => $optkey
                            );
                            if (isset($this->search_submit[$line]['column']) && $optkey == $phrase && $field == $this->search_submit[$line]['column']) {
                                $opt_attr['selected'] = '';
                            }
                            $tmp .= $this->open_tag('option', '', $opt_attr) . \esc($opt) . $this->close_tag('option');
                        }
                    }
                } else {
                    $opts = $this->parse_comma_separated($this->field_attr[$field]['values']);
                    foreach ($opts as $opt) {
                        $opt = trim($opt, '\'');
                        $opt_attr = array(
                            'value' => $opt
                        );
                        if ($opt == $phrase && $field == $this->column) {
                            $opt_attr['selected'] = '';
                        }
                        $tmp .= $this->open_tag('option', '', $opt_attr) . \esc($opt) . $this->close_tag('option');
                    }
                }
                $tmp .= $this->close_tag('select');
                $fieldlist[] = $tmp;
                break;
        }
        return $fieldlist;
    }

    protected function render_search_hidden()
    {
        $out = '';

        $tag = array(
            'tag' => 'input',
            'type' => 'hidden',
            'class' => 'cCrud-data'
        );
        if ($this->search) {
            if ($this->column) {
                switch ($this->field_type[$this->column]) {
                    case 'timestamp':
                    case 'datetime':
                    case 'date':
                    case 'time':
                        $out .= $this->single_tag($tag, '', array(
                            'name' => 'phrase][from',
                            'value' => (isset($this->phrase['from']) ? $this->phrase['from'] : '')
                        ));
                        $out .= $this->single_tag($tag, '', array(
                            'name' => 'phrase][to',
                            'value' => (isset($this->phrase['to']) ? $this->phrase['to'] : '')
                        ));
                        break;
                    default:
                        $out .= $this->single_tag($tag, '', array(
                            'name' => 'phrase',
                            'value' => (! is_array($this->phrase) ? $this->phrase : '')
                        ));
                        break;
                }
            } else {
                $out .= $this->single_tag($tag, '', array(
                    'name' => 'phrase',
                    'value' => (! is_array($this->phrase) ? $this->phrase : '')
                ));
            }

            $out .= $this->single_tag($tag, '', array(
                'name' => 'column',
                'value' => $this->column
            ));
            $out .= $this->single_tag($tag, '', array(
                'name' => 'range',
                'value' => $this->range
            ));
        }
        return $out;
    }

    /**
     * Renderiza o cabeçalho do grid
     *
     * @param array|null $row    Tag da linha
     * @param array|null $item   Tag da célula
     * @param array|null $arrows Ícones das setas
     *
     * @return string
     */
    public function render_grid_head($row = null, $item = null, $arrows = null)
    {
        $out = '';
        $row = $row ?? ['tag' => $this->theme_config('grid_head_row_tag')];
        $item = $item ?? ['tag' => $this->theme_config('grid_head_item_tag')];
        $arrows = $arrows ?? [
            'asc'  => $this->theme_config('grid_head_arrows_asc'),
            'desc' => $this->theme_config('grid_head_arrows_desc')
        ];
        $out .= $this->open_tag($row, 'cCrud-th');
        if (count($this->mass_actions)) {
            $out .= $this->open_tag($item).$this->open_tag('div',  'cCrud-mass-checkbox-container cCrud-mass-checkbox-header ' . $this->theme_config('mass_checkbox_header_container')) . $this->single_tag('input', 'cCrud-mass-checkbox cCrud-mass-checkbox-header ' . $this->theme_config('mass_checkbox_header_input'), array(
                'type' => 'checkbox',
                'id' => 'mass_checkbox_header_input'
            )) . $this->single_tag('label', $this->theme_config('mass_checkbox_header_label'), [
                'for' => 'mass_checkbox_header_input'
            ]);
            $out .= $this->close_tag('div') . $this->close_tag($item);
        }
        if ($this->is_numbers) {
            $out .= $this->open_tag($item, 'cCrud-num') . '&#35;' . $this->close_tag($item);
        }
        if (($this->is_edit || $this->is_remove || $this->is_view || $this->is_duplicate || $this->buttons || $this->grid_restrictions) && $this->task != 'print' && $this->buttons_position == 'left') {
            $out .= $this->open_tag($item, 'cCrud-actions') . '&nbsp;' . $this->close_tag($item);
        }
        foreach ($this->columns as $field => $fitem) {
            if (isset($this->field_type[$field]) && ($this->field_type[$field] == 'password' or $this->field_type[$field] == 'hidden'))
                continue;
            $fieldname = $this->columns_names[$field];
            $class = 'cCrud-column';
            $attr = array();
            if ($this->is_sortable) {
                $class .= ' cCrud-action';
                if ($this->primary_key == $field) {
                    $class .= ' cCrud-primary';
                }
                if ($this->order_column == $field) {
                    $class .= ' cCrud-current cCrud-' . $this->order_direct;
                    $attr['data-order'] = $this->order_direct == 'asc' ? 'desc' : 'asc';
                } else {
                    $attr['data-order'] = $this->order_direct;
                }
                $attr['data-orderby'] = $field;
            }
            if (isset($this->column_width[$field])) {
                $attr['style'] = 'width:' . $this->column_width[$field] . ';min-width:' . $this->column_width[$field] . ';max-width:' . $this->column_width[$field] . ';';
            }

            $out .= $this->open_tag($item, $class, $attr);
            if ($this->order_column == $field && $arrows && $this->is_sortable) {
                $out .= $arrows[$this->order_direct];
            }
            $out .= $fieldname;
            $out .= $this->get_column_tooltip($field);
            $out .= $this->close_tag($item);
        }
        if (($this->is_edit || $this->is_remove || $this->is_view || $this->is_duplicate || $this->buttons || $this->grid_restrictions) && $this->task != 'print' && $this->buttons_position == 'right') {
            $out .= $this->open_tag($item, 'cCrud-actions') . '&nbsp;' . $this->close_tag($item);
        }
        $out .= $this->close_tag($row);
        return $out;
    }

    /**
     *
     * @author Ariel Canal
     *         Renderização da classe cCrud-actions-fixed
     */
    /**
     * Renderiza o corpo do grid
     *
     * @param array|null $row_tag Tag da linha
     * @param array|null $item    Tag da célula
     *
     * @return string
     */
    public function render_grid_body($row_tag = null, $item = null)
    {
        $out = '';
        $row_tag = $row_tag ?? ['tag' => $this->theme_config('grid_body_row_tag')];
        $item    = $item    ?? ['tag' => $this->theme_config('grid_body_item_tag')];
        $i = 0;
        if ($this->result_list) {
            foreach ($this->result_list as $key => $row) {
                $j = 0;
                $row_color = false;
                $row_class = '';
                if (isset($this->highlight_row)) {
                    foreach ($this->highlight_row as $params) {
                        $params['value'] = $this->replace_text_variables($params['value'], $row, true);
                        if ($this->_compare($row[$params['field']], $params['operator'], $params['value'])) {
                            if ($params['color'])
                                $row_color = 'background-color:' . $params['color'] . ';';
                            if ($params['class'])
                                $row_class .= ' ' . $params['class'];
                        }
                    }
                }
                $out .= $this->open_tag($row_tag, 'cCrud-row cCrud-row-' . $i);
                if (count($this->mass_actions)) {
                    $out .= $this->open_tag($item) . $this->open_tag('div', 'cCrud-mass ' . $this->theme_config('mass_checkbox_row_container'));
                    if (! $this->table_ro && ($this->is_edit($row) || $this->is_remove($row))) {
                        $out .= $this->single_tag('input', 'cCrud-mass-checkbox cCrud-data ' . $this->theme_config('mass_checkbox_row_input'), array(
                            'type' => 'checkbox',
                            'name' => 'mass_list][' . $row['primary_key'],
                            'value' => $row['primary_key'],
                            'id' => 'mass_' . $row['primary_key']
                        )) . $this->single_tag('label', $this->theme_config('mass_checkbox_row_label'), [
                            'for' => 'mass_' . $row['primary_key']
                        ]);
                    }
                    $out .= $this->close_tag('div') . $this->close_tag($item);
                }
                if ($this->is_numbers) {
                    $out .= $this->open_tag($item, 'cCrud-num', $this->_cell_attrib(false, false, false, $row, false, $row_color, $row_class)) . $this->open_tag('span') . ($key + $this->start + 1) . $this->close_tag('span') . $this->close_tag($item);
                }
                if (($this->is_edit || $this->is_remove || $this->is_view || $this->buttons || $this->is_duplicate || $this->grid_restrictions) && $this->task != 'print' && $this->buttons_position == 'left') {
                    $out .= $this->open_tag($item, 'cCrud-actions' . (($this->config->fixed_action_buttons) ? ' cCrud-actions-fixed' : ''), $this->_cell_attrib(false, false, false, $row, false, $row_color, $row_class));
                    $out .= $this->_render_list_buttons($row);
                    $out .= $this->open_tag('span') . $this->close_tag($item);
                }
                foreach ($this->columns as $field => $fitem) {
                    $value = $row[$field];
                    if (isset($this->field_type[$field]) && ($this->field_type[$field] == 'password' or $this->field_type[$field] == 'hidden'))
                        continue;
                    $out .= $this->open_tag($item, '', $this->_cell_attrib($field, $value, $this->order_column, $row, false, $row_color, $row_class)) . $this->open_tag('span');
                    $out .= $this->_render_list_item($field, $value, $row['primary_key'], $row);
                    $out .= $this->open_tag('span') . $this->close_tag($item);
                }
                if (($this->is_edit || $this->is_remove || $this->is_view || $this->buttons || $this->is_duplicate || $this->grid_restrictions) && $this->task != 'print' && $this->buttons_position == 'right') {
                    $out .= $this->open_tag($item, 'cCrud-actions' . (($this->config->fixed_action_buttons) ? ' cCrud-actions-fixed' : '') . ($this->config->fixed_action_buttons ? ' cCrud-fix' : ''), $this->_cell_attrib(false, false, false, $row, false, $row_color, $row_class)) . $this->open_tag('span');
                    $out .= $this->_render_list_buttons($row);
                    $out .= $this->open_tag('span') . $this->close_tag($item);
                }
                $out .= $this->close_tag($row_tag);
                $i = 1 - $i;
            }
        } else {
            $j = count($this->columns); // colspan
            if (($this->is_edit || $this->is_remove || $this->is_view || $this->buttons || $this->is_duplicate || $this->grid_restrictions) && $this->task != 'print' && ($this->buttons_position == 'right' || $this->buttons_position == 'left')) {
                ++ $j;
            }
            if ($this->is_numbers) {
                ++ $j;
            }
            if (count($this->mass_actions)) {
                ++ $j;
            }
            $out .= $this->open_tag($row_tag, 'cCrud-row') . $this->open_tag($item, '', array(
                'colspan' => $j
            )) . $this->lang('table_empty') . $this->close_tag($item) . $this->close_tag($row_tag);
        }
        return $out;
    }

    /**
     * Renderiza o rodapé do grid
     *
     * @param array|null $row  Tag da linha
     * @param array|null $item Tag da célula
     *
     * @return string
     */
    public function render_grid_footer($row = null, $item = null)
    {
        $out = '';
        $row  = $row  ?? ['tag' => $this->theme_config('grid_footer_row_tag')];
        $item = $item ?? ['tag' => $this->theme_config('grid_footer_item_tag')];
        if ($this->sum && $this->result_list) {
            $out .= $this->open_tag($row, 'cCrud-tf');
            if (count($this->mass_actions)) {
                $out .= $this->open_tag($item) . $this->open_tag('div', 'cCrud-mass-checkbox-container cCrud-mass-checkbox-footer ' . $this->theme_config('mass_checkbox_footer_container'));
                $out .= $this->single_tag('input', 'cCrud-mass-checkbox cCrud-mass-checkbox-footer ' . $this->theme_config('mass_checkbox_footer_input'), [
                    'type' => 'checkbox',
                    'id' => 'mass_checkbox_footer_input'
                ]);
                $out .= $this->single_tag('label', $this->theme_config('mass_checkbox_footer_label'), [
                    'for' => 'mass_checkbox_footer_input'
                ]);
                $out .= $this->close_tag('div') . $this->close_tag($item);
            }
            if ($this->is_numbers) {
                $out .= $this->open_tag($item, 'cCrud-num') . '&Sigma;' . $this->close_tag($item);
            }
            if (($this->is_edit || $this->is_remove || $this->buttons || $this->is_view || $this->is_duplicate || $this->grid_restrictions) && $this->task != 'print' && $this->buttons_position == 'left') {
                $out .= $this->open_tag($item, 'cCrud-actions') . '&nbsp;' . $this->close_tag($item);
            }
            foreach ($this->columns as $field => $fitem) {
                if (isset($this->field_type[$field]) && ($this->field_type[$field] == 'password' or $this->field_type[$field] == 'hidden'))
                    continue;
                $out .= $this->open_tag($item, isset($this->sum[$field]) ? $this->sum[$field]['class'] : '', $this->_cell_attrib($field, isset($this->sum[$field]) ? $this->sum[$field] : null, $this->order_column, $this->sum, true));
                $out .= $this->render_sum_item($field);
                $out .= $this->close_tag($item);
            }
            if (($this->is_edit || $this->is_remove || $this->buttons || $this->is_view || $this->is_duplicate || $this->grid_restrictions) && $this->task != 'print' && $this->buttons_position == 'right') {
                $out .= $this->open_tag($item, 'cCrud-actions') . '&nbsp;' . $this->close_tag($item);
            }
            $out .= $this->close_tag($row);
        }
        return $out;
    }

    /**
     * Renderiza a lista de limites
     *
     * @param int|null $limit Limite de registros
     *
     * @return string
     */
    public function render_limitlist($limit = null)
    {
        if ($limit !== null) {
            $this->limit = $limit;
        }
        if ($this->is_limitlist) {
            return $this->getLimitList($this->limit);
        }
        return '';
    }

    /**
     * Renderiza a paginação
     *
     * @param int $numbers Quantidade de páginas
     * @param int $offsets Deslocamento
     *
     * @return string
     */
    public function render_pagination($numbers = 10, $offsets = 2)
    {
        if ($this->is_pagination) {
            return $this->_pagination($this->result_total, $this->start, $this->limit, $numbers, $offsets);
        }
        return '';
    }

    /**
     * Renderiza informações de desempenho
     *
     * @param array $tag Tag de contêiner
     *
     * @return string
     */
    public function render_benchmark($tag = array(
        'tag' => 'span'
    ))
    {
        if ($this->benchmark) {
            return $this->open_tag($tag, 'cCrud-benchmark') . $this->benchmark_end() . $this->close_tag($tag);
        }
        return '';
    }

    protected function render_control_fields()
    {
        $out = '';
        $tag = array(
            'tag' => 'input',
            'type' => 'hidden',
            'class' => 'cCrud-data'
        );
        $out .= $this->single_tag($tag, '', array(
            'name' => 'key',
            'value' => $this->key
        ));
        $out .= $this->single_tag($tag, '', array(
            'name' => 'orderby',
            'value' => $this->order_column
        ));
        $out .= $this->single_tag($tag, '', array(
            'name' => 'order',
            'value' => $this->order_direct
        ));
        $out .= $this->single_tag($tag, '', array(
            'name' => 'start',
            'value' => $this->start
        ));
        $out .= $this->single_tag($tag, '', array(
            'name' => 'limit',
            'value' => ($this->limit ? $this->limit : $this->config->limit)
        ));
        $out .= $this->single_tag($tag, '', array(
            'name' => 'instance',
            'value' => $this->instance_name
        ));
        $out .= $this->single_tag($tag, '', array(
            'name' => 'task',
            'value' => $this->task
        ));
        if ($this->primary_val) {
            $out .= $this->single_tag($tag, '', array(
                'name' => 'primary',
                'value' => $this->primary_val
            ));
        }

        $out .= $this->render_messages();
        $out .= implode('', $this->hidden_fields_output);

        return $out;
    }

    protected function render_custom_buttons()
    {
        $out = '';
        if (is_array($this->custom_buttons) && count($this->custom_buttons)) {
            foreach ($this->custom_buttons as $button) {
                $out .= $this->render_button($button);
            }
        }
        return $out;
    }

    /**
     * Renderiza um botão de ação
     *
     * @param string      $name    Nome do botão
     * @param string      $task    Tarefa executada
     * @param string      $after   Tarefa posterior
     * @param string|null $class   Classe CSS
     * @param string|null $icon    Ícone do botão
     * @param string      $mode    Modos permitidos
     * @param string      $primary Valor primário
     *
     * @return string
     */
    public function render_button($name = '', $task = '', $after = '', $class = null, $icon = null, $mode = '', $primary = '')
    {
        $out = '';
        $class = $class ?? $this->theme_config('grid_default');
        $icon  = $icon ?? $this->theme_config('grid_default_icon');
        if (is_array($name) && isset($this->custom_buttons[$name['label']])) {
            // custom_buttons
            $button = $name;

            $tag = array(
                'tag' => 'a',
                'href' => $button['link']
            );
            if (is_array($button['tag']) && count($button['tag'])) {
                $tag = $tag + $button['tag'];
            }
            if ($button['class']) {
                $tag['class'] = $button['class'];
            }
            $out .= $this->open_tag($tag);
            if ($button['icon']) {
                $out .= $this->open_tag(array(
                    'tag' => 'i',
                    'class' => $button['icon']
                )) . $this->close_tag('i') . '&nbsp;';
            }
            $out .= $this->open_tag(array(
                'tag' => 'span'
            )) . $button['label'] . $this->close_tag('span');
            $out .= $this->close_tag($tag);
            return $out;
        } elseif (isset($this->{'is_' . $after}) && ! $this->{'is_' . $after}) {
            return $out;
        }
        if (isset($this->{'is_' . $task}) && ! $this->{'is_' . $task}) {
            return $out;
        }
        if (! isset($this->hide_button[$name])) {
            if ($mode) {
                $mode = $this->parse_comma_separated($mode);
                if (! in_array($this->task, $mode)) {
                    return $out;
                }
            }
            $tag = array(
                'tag' => 'button',
                'data-task' => $task
            );
            if ($after) {
                $tag['data-after'] = $after;
            }
            if ($class) {
                $tag['class'] = $class;
            }
            if ($primary) {
                $tag['data-primary'] = $primary;
            } elseif ($this->primary_val) {
                $tag['data-primary'] = $this->primary_val;
            }
            if (isset($this->action[$task])) {
                if ($this->action[$task]['mode']) {
                    $this->action[$task]['mode'] = $this->parse_comma_separated($this->action[$task]['mode']);
                    if (! in_array($this->task, $this->action[$task]['mode'])) {
                        return $out;
                    }
                }
                if ($this->action[$task]['conditions']) {
                    foreach ($this->action[$task]['conditions'] as $cond) {
                        $f = $cond[0];
                        $o = $cond[1];
                        $v = $cond[2];
                        if (! strpos($f, '.'))
                            $f = $this->table . "." . $f;
                        if (! $this->_compare($this->result_row[$f], $o, $v)) {
                            return $out;
                        }
                    }
                }
                $tag['data-task'] = 'action';
                $tag['data-action'] = $task;
                if ($this->action[$task]['icon'] != "") {
                    $icon = $this->action[$task]['icon'];
                }
                if ($this->action[$task]['button_attr']) {
                    foreach ($this->action[$task]['button_attr'] as $k => $v) {
                        if (is_array($v)) {
                            foreach ($v as $k2 => $v2) {
                                $tag[$k2] = $v2;
                            }
                        } else {
                            $tag[$k] = $v;
                        }
                    }
                }
            }
            $out .= $this->open_tag($tag, 'cCrud-action');
            if ($icon) {
                $out .= $this->open_tag(array(
                    'tag' => 'i',
                    'class' => $icon
                )) . $this->close_tag('i') . ' ';
            }
            $out .= $this->open_tag(array(
                'tag' => 'span'
            )) . $this->lang($name) . $this->close_tag('span');
            $out .= $this->close_tag($tag);
        }
        return $out;
    }

    /**
     * Renderiza o botão de inclusão
     */
    public function add_button($class = null, $icon = null)
    {
        $class = $class ?? $this->theme_config('add_button_class');
        $icon  = $icon ?? $this->theme_config('add_button_icon');
        if ($this->is_create && ! isset($this->hide_button['add']) && ! $this->table_ro) {
            return $this->render_button('add', 'create', '', $class, $icon);
        }
    }

    /**
     * Renderiza o botão de exportação CSV
     */
    public function csv_button($class = null, $icon = null)
    {
        $class = $class ?? $this->theme_config('csv_button_class');
        $icon  = $icon ?? $this->theme_config('csv_button_icon');
        if ($this->is_csv && ! isset($this->hide_button['csv'])) {
            return $this->render_button('export_csv', 'csv', '', trim($class . ' cCrud-in-new-window'), $icon);
        }
    }

    /**
     * Renderiza o botão de impressão
     */
    public function print_button($class = null, $icon = null)
    {
        $class = $class ?? $this->theme_config('print_button_class');
        $icon  = $icon ?? $this->theme_config('print_button_icon');
        if ($this->is_print && ! isset($this->hide_button['print'])) {
            return $this->render_button('print', 'print', '', trim($class . ' cCrud-in-new-window'), $icon);
        }
    }

    /**
     * Renderiza o botão de atualização
     */
    public function refresh_button($class = null, $icon = null)
    {
        $class = $class ?? $this->theme_config('refresh_button_class');
        $icon  = $icon ?? $this->theme_config('refresh_button_icon');
        if ($this->task == "list") {
            return $this->render_button('refresh', 'list', '', $class, $icon);
        }
    }

    protected function get_image_folder($field)
    {
        if (isset($this->upload_folder[$field]))
            return $this->upload_folder[$field];
        $settings = $this->upload_config[$field];
        if (isset($settings['path'])) {
            $path = $this->check_folder($settings['path'], 'get_image_folder');
        } else {
            $path = $this->check_folder($this->config->upload_folder_def, 'get_image_folder');
        }
        $this->upload_folder[$field] = $path;
        return $path;
    }

    protected function check_file_folders($field)
    {
        $settings = $this->upload_config[$field];
        $path = $this->get_image_folder($field);
        if (! is_dir($path)) {
            $this->create_file_folders($path);
        }
        if (isset($settings['thumbs']) && is_array($settings['thumbs'])) {
            foreach ($settings['thumbs'] as $thumb) {
                if (isset($thumb['folder']) && ! is_dir($path . '/' . trim($thumb['folder'], '/'))) {
                    $this->create_file_folders($path . '/' . trim($thumb['folder'], '/'));
                }
            }
        }
    }

    protected function create_file_folders($path)
    {
        $path_array = explode('/', $path);
        array_pop($path_array);
        if (is_dir(implode('/', $path_array))) {
            if (! mkdir($path))
                // Não foi possível criar o diretório
                return Services::response()->setStatusCode(500)->setBody(lang('cCrud.cannot_create_directory', [$path]));
        } else {
            // Caminho do arquivo incorreto
            return Services::response()->setStatusCode(404)->setBody(lang('cCrud.file_path_incorrect'));
        }
    }

    protected function get_thumb_path($imgname, $field, $thumb_array)
    {
        $path = $this->get_image_folder($field);
        if (isset($thumb_array['folder']) && ! empty($thumb_array['folder'])) {
            $path .= '/' . trim($thumb_array['folder'], '/');
        }
        $marker = isset($thumb_array['marker']) ? $thumb_array['marker'] : '';
        return $path . '/' . $this->_thumb_name($imgname, $marker);
    }

    protected function safe_file_name($file, $field)
    {
        $ext = strtolower(strrchr($file['name'], '.'));
        if (isset($this->upload_config[$field]['not_rename']) && $this->upload_config[$field]['not_rename'] == true)
            $filename = $this->_clean_file_name($file['name']);
        else
            $filename = base_convert(str_replace(' ', '', microtime()) . rand(), 10, 36) . $ext;
        return $filename;
    }

    protected function get_ext($filename)
    {
        return strtolower(strrchr($filename, '.') + 1);
    }

    protected function save_file_to_tmp($file, $filename, $field)
    {
        $filename = substr($filename, 0, strrpos($filename, '.')) . '.tmp';
        $file_path = $this->get_image_folder($field) . '/' . $filename;
        move_uploaded_file($file['tmp_name'], $file_path);

        if ($this->after_upload) {
            $path = $this->check_file($this->after_upload['path'], 'save_file_to_tmp');
            include_once ($path);
            $callable = $this->after_upload['callable'];
            if (is_callable($callable)) {
                call_user_func_array($callable, array(
                    $field,
                    $filename,
                    $file_path,
                    $this->upload_config[$field],
                    $this
                ));
            }
        }

        return $filename;
    }

    /**
     * Salva o arquivo enviado no diretório configurado.
     *
     * @param array  $file     Dados do arquivo enviado
     * @param string $filename Nome do arquivo a ser salvo
     * @param string $field    Campo relacionado ao arquivo
     *
     * @return string Caminho completo do arquivo salvo
     */
    protected function save_file(array $file, string &$filename, string $field): string
    {
        $file_path = $this->get_image_folder($field) . '/' . $filename;
        $file_path = mb_convert_encoding($file_path, 'ISO-8859-1', 'UTF-8');
        move_uploaded_file($file['tmp_name'], $file_path);
        if ($this->after_upload) {
            $path = $this->check_file($this->after_upload['path'], 'save_file');
            include_once ($path);
            $callable = $this->after_upload['callable'];
            if (is_callable($callable)) {
                call_user_func_array($callable, array(
                    $field,
                    &$filename,
                    $file_path,
                    $this->upload_config[$field],
                    $this
                ));
            }
        }
        return $file_path;
    }

    protected function get_filename_noconfict($filename, $field)
    {
        $file_path = $this->get_image_folder($field) . '/' . $filename;
        if (is_file($file_path)) {
            $filename = substr_replace($filename, '_@cCrud' . base_convert(time() . rand(), 10, 36), strrpos($filename, '.'), 0);
        }
        return $filename;
    }

    protected function is_resize($field)
    {
        if (isset($this->upload_config[$field]['width']) or isset($this->upload_config[$field]['height'])
            /* or isset($this->upload_config[$field]['field'])*/ or (isset($this->upload_config[$field]['manual_crop']) && $this->upload_config[$field]['manual_crop'] == true) /* or (isset($this->upload_config[$field]['thumbs']) && count($this->
            upload_config[$field]['thumbs']))*/)
            return true;
        else
            return false;
    }

    protected function is_manual_crop($field)
    {
        if (isset($this->upload_config[$field]['manual_crop']) && $this->upload_config[$field]['manual_crop'] == true)
            return true;
        else
            return false;
    }

    protected function remove_tmp_image($filename, $field)
    {
        $tmp_filename = substr($filename, 0, strrpos($filename, '.')) . '.tmp';
        if (isset($this->upload_config[$field]['save_original']) && $this->upload_config[$field]['save_original'] == true) {
            if (isset($this->upload_config[$field]['original_marker']) && ! empty($this->upload_config[$field]['original_marker'])) {
                $orig_filename = $this->_thumb_name($filename, $this->upload_config[$field]['original_marker']);
            } else {
                $orig_filename = $this->_thumb_name($filename, '_orig');
            }
            rename($this->get_image_folder($field) . '/' . $tmp_filename, $this->get_image_folder($field) . '/' . $orig_filename);
        } else {
            $path = $this->get_image_folder($field);
            if (is_file($path . '/' . $tmp_filename))
                unlink($path . '/' . $tmp_filename);
        }

        if ($this->after_resize) {
            $path = $this->check_file($this->after_resize['path'], 'after_resize');
            include_once ($path);
            $callable = $this->after_resize['callable'];
            if (is_callable($callable)) {
                call_user_func_array($callable, array(
                    $field,
                    $filename,
                    $this->get_image_folder($field) . '/' . $filename,
                    $this->upload_config[$field],
                    $this
                ));
            }
        }
    }

    protected function remove_file($filename, $field)
    {
        $settings = $this->upload_config[$field];
        $path = $this->get_image_folder($field);
        if (is_file($path . '/' . $filename))
            unlink($path . '/' . $filename);
        if (isset($settings['thumbs']) && is_array($settings['thumbs'])) {
            foreach ($settings['thumbs'] as $thumb) {
                $thumb_file = $this->get_thumb_path($filename, $field, $thumb);
                if (is_file($thumb_file))
                    unlink($thumb_file);
            }
        }
        if (isset($this->upload_config[$field]['save_original']) && $this->upload_config[$field]['save_original'] == true) {
            if (isset($this->upload_config[$field]['original_marker']) && ! empty($this->upload_config[$field]['original_marker'])) {
                $orig_filename = $this->_thumb_name($filename, $this->upload_config[$field]['original_marker']);
            } else {
                $orig_filename = $this->_thumb_name($filename, '_orig');
            }
            if (is_file($path . '/' . $orig_filename))
                unlink($path . '/' . $orig_filename);
        }
    }

    /**
     * date ranges in unix timestamp
     */
    protected function get_range($name)
    {
        $range = array();
        $time = time() /* + 3600 * $this->config->local_time_correction*/;
        $week_day = date('w', $time) /* + $this->config->date_first_day*/;
        switch ($name) {
            default:
            case 'today':
                $range['from'] = gmmktime(0, 0, 0, date('n', $time), date('j', $time), date('Y', $time));
                $range['to'] = gmmktime(23, 59, 59, date('n', $time), date('j', $time), date('Y', $time));
                break;
            case 'next_year':
                $range['from'] = gmmktime(0, 0, 0, 1, 1, date('Y', $time) + 1);
                $range['to'] = gmmktime(23, 59, 59, 12, 31, date('Y', $time) + 1);
                break;
            case 'next_month':
                $range['from'] = gmmktime(0, 0, 0, date('n', $time) + 1, 1, date('Y', $time));
                $range['to'] = gmmktime(23, 59, 59, date('n', $time) + 2, - 1, date('Y', $time));
                break;
            case 'this_week_today':
                if ($week_day >= $this->config->date_first_day) {
                    $offset1 = $week_day - $this->config->date_first_day;
                } else {
                    $offset1 = 7 - ($this->config->date_first_day - $week_day);
                }
                $range['from'] = gmmktime(0, 0, 0, date('n', $time), date('j', $time) - $offset1, date('Y', $time));
                $range['to'] = gmmktime(23, 59, 59, date('n', $time), date('j', $time), date('Y', $time));
                break;
            case 'this_week_full':
                if ($week_day >= $this->config->date_first_day) {
                    $offset1 = $week_day - $this->config->date_first_day;
                } else {
                    $offset1 = 7 - ($this->config->date_first_day - $week_day);
                }
                $offset2 = 6 - $week_day + $this->config->date_first_day;
                $range['from'] = gmmktime(0, 0, 0, date('n', $time), date('j', $time) - $offset1, date('Y', $time));
                $range['to'] = gmmktime(23, 59, 59, date('n', $time), date('j', $time) + $offset2, date('Y', $time));
                break;
            case 'last_week':
                if ($week_day >= $this->config->date_first_day) {
                    $offset1 = $week_day - $this->config->date_first_day;
                } else {
                    $offset1 = 7 - ($this->config->date_first_day - $week_day);
                }
                $offset2 = 6 - $week_day + $this->config->date_first_day;
                $range['from'] = gmmktime(0, 0, 0, date('n', $time), date('j', $time) - $offset1 - 7, date('Y', $time));
                $range['to'] = gmmktime(23, 59, 59, date('n', $time), date('j', $time) + $offset2 - 7, date('Y', $time));
                break;
            case 'last_2weeks':
                if ($week_day >= $this->config->date_first_day) {
                    $offset1 = $week_day - $this->config->date_first_day;
                } else {
                    $offset1 = 7 - ($this->config->date_first_day - $week_day);
                }
                $offset2 = 6 - $week_day + $this->config->date_first_day;
                $range['from'] = gmmktime(0, 0, 0, date('n', $time), date('j', $time) - $offset1 - 14, date('Y', $time));
                $range['to'] = gmmktime(23, 59, 59, date('n', $time), date('j', $time) + $offset2 - 14, date('Y', $time));
                break;
            case 'this_month':
                $range['from'] = gmmktime(0, 0, 0, date('n', $time), 1, date('Y', $time));
                $range['to'] = gmmktime(23, 59, 59, date('n', $time), date('j', $time), date('Y', $time));
                break;
            case 'last_month':
                $range['from'] = gmmktime(0, 0, 0, date('n', $time) - 1, 1, date('Y', $time));
                $range['to'] = gmmktime(23, 59, 59, date('n', $time), date('j', $time) - 1, date('Y', $time));
                break;
            case 'last_3months':
                $range['from'] = gmmktime(0, 0, 0, date('n', $time) - 3, 1, date('Y', $time));
                $range['to'] = gmmktime(23, 59, 59, date('n', $time), date('j', $time) - 1, date('Y', $time));
                break;
            case 'last_6months':
                $range['from'] = gmmktime(0, 0, 0, date('n', $time) - 6, 1, date('Y', $time));
                $range['to'] = gmmktime(23, 59, 59, date('n', $time), date('j', $time) - 1, date('Y', $time));
                break;
            case 'this_year':
                $range['from'] = gmmktime(0, 0, 0, 1, 1, date('Y', $time));
                $range['to'] = gmmktime(23, 59, 59, date('n', $time), date('j', $time), date('Y', $time));
                break;
            case 'last_year':
                $range['from'] = gmmktime(0, 0, 0, 1, 1, date('Y', $time) - 1);
                $range['to'] = gmmktime(23, 59, 59, 12, 31, date('Y', $time) - 1);
                break;
        }
        return $range;
    }

    /* OPÇÃO PARA SUBSTITUIÇÃO DO TÍTULO */
    /**
     * Renderiza o nome da tabela conforme o modo informado.
     *
     * @param string      $mode          Modo de operação.
     * @param string      $tag           Tag HTML utilizada.
     * @param bool        $to_show       Indica se deve ser exibido.
     * @param bool|string $replace_title Texto alternativo.
     *
     * @return string
     */
    public function renderTableName(string $mode = 'list', $tag = 'h2', bool $to_show = false, $replace_title = false): string
    {
        $out = '';
        if ($this->is_title) {
            $attr = [];
            if (is_array($tag)) {
                $attr = $tag;
                $tag  = $attr['tag'] ?? 'h2';
            } elseif ($to_show) {
                $attr['class'] = 'cCrud-main-tab';
            }

            $title = $replace_title ?: $this->table_name;
            $out  .= $this->open_tag($tag, '', $attr);
            switch ($mode) {
                case 'create':
                    $out .= $title . '<small> - ' . $this->lang('add') . '</small>';
                    break;
                case 'edit':
                    $out .= $title . '<small> - ' . $this->lang('edit') . '</small>';
                    break;
                case 'view':
                    $out .= $title . '<small> - ' . $this->lang('view') . '</small>';
                    break;
                default:
                    $out .= $title . '<small> ' . $this->get_table_tooltip() . '</small>';
                    break;
            }
            $out .= $this->close_tag($tag);
        }
        return $out;
    }

    protected function get_id()
    {
        return 'id="xc_' . base_convert(time() + rand(), 10, 36) . '"';
    }

    /*
     * ALTERADO DO PADRÃO - Compatibilização com array de grid_restrictions
     */
    protected function is_edit(&$row)
    {
        if (! isset($this->grid_restrictions['edit'])) {
            return $this->is_edit;
        } else {
            $cond = array();
            foreach ($this->grid_restrictions['edit'] as $item) {
                $fdata = $this->_parse_field_names($item['field']);
                $fname = key($fdata) /*$fdata[0]['table'] . '.' . $fdata[0]['field']*/;
                $cond[] = $this->_compare($row[$fname], $item['operator'], $this->replace_text_variables($item['value'], $row));
            }
            if (($item['bool'] === true && in_array(true, $cond)) || ($item['bool'] === false && ! in_array(false, $cond))) {
                $this->is_edit = false;
                return false;
            }

            $this->is_edit = true;
            return true;
        }
    }

    /*
     * ALTERADO DO PADRÃO - Compatibilização com array de grid_restrictions
     */
    protected function is_remove(&$row)
    {
        if (! isset($this->grid_restrictions['remove'])) {
            return $this->is_remove;
        } else {
            $cond = array();
            foreach ($this->grid_restrictions['remove'] as $item) {
                $fdata = $this->_parse_field_names($item['field']);
                $fname = key($fdata) /*$fdata[0]['table'] . '.' . $fdata[0]['field']*/;
                $cond[] = $this->_compare($row[$fname], $item['operator'], $this->replace_text_variables($item['value'], $row));
            }
            if (($item['bool'] === true && in_array(true, $cond)) || ($item['bool'] === false && ! in_array(false, $cond))) {
                $this->is_remove = false;
                return false;
            }
            $this->is_remove = true;
            return true;
        }
    }

    /*
     * ALTERADO DO PADRÃO - Compatibilização com array de grid_restrictions
     */
    protected function is_duplicate(&$row)
    {
        if (! isset($this->grid_restrictions['duplicate'])) {
            return $this->is_duplicate;
        } else {
            $cond = array();
            foreach ($this->grid_restrictions['duplicate'] as $item) {
                $fdata = $this->_parse_field_names($item['field']);
                $fname = key($fdata) /*$fdata[0]['table'] . '.' . $fdata[0]['field']*/;
                $cond[] = $this->_compare($row[$fname], $item['operator'], $this->replace_text_variables($item['value'], $row));
            }
            if (($item['bool'] === true && in_array(true, $cond)) || ($item['bool'] === false && ! in_array(false, $cond))) {
                $this->is_duplicate = false;
                return false;
            }
            $this->is_duplicate = true;
            return true;
        }
    }

    /*
     * ALTERADO DO PADRÃO - Compatibilização com array de grid_restrictions
     */
    protected function is_view(&$row)
    {
        if (! isset($this->grid_restrictions['view'])) {
            return $this->is_view;
        } else {
            $cond = array();
            foreach ($this->grid_restrictions['view'] as $item) {
                $fdata = $this->_parse_field_names($item['field']);
                $fname = key($fdata) /*$fdata[0]['table'] . '.' . $fdata[0]['field']*/;
                $cond[] = $this->_compare($row[$fname], $item['operator'], $this->replace_text_variables($item['value'], $row));
            }
            if (($item['bool'] === true && in_array(true, $cond)) || ($item['bool'] === false && ! in_array(false, $cond))) {
                $this->is_view = false;
                return false;
            }
            $this->is_view = true;
            return true;
        }
    }

    /*
     * ALTERADO DO PADRÃO - Compatibilização com array de grid_restrictions
     */
    protected function is_button($name, &$row)
    {
        if (isset($this->grid_restrictions[$name])) {
            foreach ($this->grid_restrictions[$name] as $item) {
                $fdata = $this->_parse_field_names($item['field']);
                $fname = key($fdata) /*$fdata[0]['table'] . '.' . $fdata[0]['field']*/;

                $cond = $this->_compare($row[$fname], $item['operator'], $this->replace_text_variables($item['value'], $row));
                if (($item['bool'] === true && $cond) || ($item['bool'] === false && ! $cond)) {
                    return false;
                }
            }
            return true;
        } else {
            return true;
        }
    }

    protected function _call_action()
    {
        $name = $this->_post('action');
        if (isset($this->action[$name])) {
            $path = $this->check_file($this->action[$name]['path'], 'call_action');
            include_once ($path);
            if (is_callable($this->action[$name]['callable'])) {
                call_user_func_array($this->action[$name]['callable'], array(
                    $this
                ));
            }
        }
        $this->task = $this->after;
        $this->after = null;
        return $this->_run_task();
    }

    /**
     * Importa dados para a sessão do cCrud.
     *
     * @param array $data Dados a serem importados
     */
    public static function import_session($data)
    {
        Services::session()->set('cCrud_session', $data);
    }

    /**
     * Exporta os dados armazenados na sessão do cCrud.
     *
     * @return array|null Dados da sessão
     */
    public static function export_session()
    {
        return Services::session()->get('cCrud_session');
    }

    public function fieldname_encode($name = '')
    {
        if (! $this->config->encode_field_names) {
            return $name;
        }
        return str_replace(array(
            '=',
            '/',
            '+'
        ), array(
            '-',
            '_',
            ':'
        ), base64_encode($name));
    }

    public function fieldname_decode($name = '')
    {
        if (! $this->config->encode_field_names) {
            return $name;
        }
        return str_replace('`', '', base64_decode(str_replace(array(
            '-',
            '_',
            ':'
        ), array(
            '=',
            '/',
            '+'
        ), $name)));
    }

    protected function parse_mode($mode)
    {
        $modes = $this->parse_comma_separated($mode);
        if ($modes) {
            return (array_combine($modes, array_fill(0, count($modes), 1)));
        } else {
            return array(
                'list' => 1,
                'create' => 1,
                'edit' => 1,
                'view' => 1
            );
        }
    }

    /**
     *
     * @author Ariel Canal
     *         As propriedades readonly_on_* e disabled_on_* não existem na
     *         classe, portanto o backup delas, quando acionadas pelo método
     *         condition() não era feito.
     */
    protected function condition_backup($method, $field = null)
    {
        if (! isset($this->condition_backup[$method])) {
            if ($method == "readonly_on_create" || $method == "readonly_on_edit")
                $method = 'readonly';
            elseif ($method == "disabled_on_create" || $method == "disabled_on_edit")
                $method = 'disabled';
            if (property_exists($this, $method)) {
                $this->condition_backup[$method] = $this->{$method};
            } else {
                $this->condition_backup[$method] = false;
            }
        }
    }

    protected function condition_restore()
    {
        if ($this->condition_backup) {
            foreach ($this->condition_backup as $bak_key => $back_val) {
                $this->{$bak_key} = $back_val;
            }
            $this->condition_backup = array();
        }
    }

    public function load_core_class($name)
    {
        $path = CCRUD_PATH . '/core/' . $name . '.php';
        if (isset(self::$classes[$name])) {
            return self::$classes[$name];
        }
            if (is_file($path)) {
                require_once ($path);
                $class = 'cCrud_' . $name;
                if (class_exists($class)) {
                    self::$classes[$name] = new $class();
                    return self::$classes[$name];
                } else {
                    // Classe requisitada não existe
                    return Services::response()->setStatusCode(500)->setBody(lang('cCrud.class_not_exist', [$class]));
                }
            } else {
                // Arquivo requisitado não existe
                return Services::response()->setStatusCode(404)->setBody(lang('cCrud.file_not_exist', [$name]));
            }
    }

    /**
     *
     * @author Ariel Canal
     *         Formata o número no formato para usuário (não BD).
     */
    protected function cast_number_format($number, $field, $edit = false)
    {
        $out = '';

        if (! $number)
            return $out;

        $out .= \esc($this->field_attr[$field]['prefix']);
        $out .= number_format($number ? (float) $number : 0, $this->field_attr[$field]['decimals'], $this->field_attr[$field]['point'], \esc($this->field_attr[$field]['separator']));
        $out .= \esc($this->field_attr[$field]['suffix']);

        return $out;
    }

    protected function _alphabetical()
    {
        if ($this->alphabetical_field != '') {
            $table_join = $this->_build_table_join();
            $where = $this->_build_where(false);
            $db = $this->model->db;

            $query = $db->query("SELECT SUBSTRING(UPPER($this->alphabetical_field),1,1) as inicial \r\n FROM `{$this->table}`\r\n {$table_join}\r\n {$where}\r\n GROUP BY SUBSTRING(UPPER($this->alphabetical_field),1,1) ORDER BY inicial ASC");
            $this->alphabetical_index = array();
            foreach ($query->getResultArray() as $index) {
                if (in_array($index['inicial'], range('A', 'Z')))
                    $this->alphabetical_index[$index['inicial']] = $index['inicial'];
                else
                    $this->alphabetical_index['#'] = "#";
            }
        }
    }

    public function alphabetichal_filter($column_name)
    {
        $this->alphabetical_field = $column_name;
    }

    public function render_alphabetical_filter()
    {
        $out = '';
        if (count($this->alphabetical_index)) {
            $out .= $this->open_tag('ul', 'cCrud-alphabetical ' . $this->theme_config('alphabetical_container'));
            if (! isset($this->alphabetical_index[0]) || $this->alphabetical_index[0] != 'all')
                array_unshift($this->alphabetical_index, 'all');

            foreach ($this->alphabetical_index as $i) {
                if ($i == "all")
                    $attr['data-alphabetical'] = '';
                else
                    $attr['data-alphabetical'] = $i;

                if ($this->alphabetical_filter == $i || ($this->alphabetical_filter == "" && $i == 'all'))
                    $class = $this->theme_config('alphabetical_active');
                else
                    $class = $this->theme_config('alphabetical_item');

                $out .= $this->open_tag('li', 'cCrud-action ' . $class, $attr) . $this->lang($i) . $this->close_tag('li');
            }
            $out .= $this->close_tag('ul');
        }
        return $out;
    }

    public function custom_filter($filter_name, $filter_opts, $default = false, $hide_all_option = false)
    {
        if (! $hide_all_option && ! array_key_exists('all', $filter_opts)) {
            $arr = array_reverse($filter_opts, true);
            $arr['all'] = '1=1';
            $filter_opts = array_reverse($arr, true);
        }

        if (! @array_key_exists($default, $filter_opts))
            $default = false;

        $this->custom_filter[$filter_name] = $filter_opts;
        $this->custom_filter_active[$filter_name] = ($default) ? $default : 'all';
    }

    public function render_custom_filter($filter_name)
    {
        $out = '';
        if (isset($this->custom_filter[$filter_name])) {
            if (empty($this->custom_filter[$filter_name]['active'])) {
                $this->custom_filter[$filter_name]['active'] = 'all';
            }
            $out .= $this->open_tag('div', 'btn-group ' . $this->theme_config('custom_filter_container') . ' ' . $this->custom_filter[$filter_name]['container_class'], [
                "id" => "custom-filter-" . $filter_name,
                'role' => 'group'
            ]);
            $out .= $this->open_tag('button', 'btn ' . $this->theme_config('custom_filter_toggle') . ' ' . $this->custom_filter[$filter_name]['toggle_class'] . ' dropdown-toggle', [
                "data-bs-toggle" => "dropdown",
                "aria-expanded" => "false"
            ]);
            $out .= $this->lang($this->custom_filter[$filter_name]['active']);
            $out .= $this->close_tag('button');

            $out .= $this->open_tag('div', 'btn-group', [
                'role' => "group"
            ]);
            $out .= $this->open_tag('ul', 'dropdown-menu ' . $this->theme_config('custom_filter_menu'));
            $attr['data-filter'] = $filter_name;
            $attr['data-nomovepage'] = 'true';
            $attr['data-table'] = strtolower($this->table);
            $attr['href'] = "#";
            foreach ($this->custom_filter[$filter_name]['options'] as $label => $config) {
                $attr['data-label'] = $label;

                if ($this->custom_filter[$filter_name]['active'] == $attr['data-label']) {
                    $class = $this->theme_config('custom_filter_item_active') . ' active';
                } else {
                    $class = $this->theme_config('custom_filter_item');
                }

                $out .= $this->open_tag('a', 'cCrud-action dropdown-item ' . $class, $attr) . $this->lang($attr['data-label']) . $this->close_tag('a');

                if ($label == "all") {
                    $out .= $this->open_tag('div', 'dropdown-divider') . $this->close_tag('div');
                }
            }
            $out .= $this->close_tag('ul');
            $out .= $this->close_tag('div');
            $out .= $this->close_tag('div');
        }
        return $out;
    }

    public function totalizer($name, $where, $select = null, $icon = false, $icon_container = false, $replace_icon = false)
    {
        $this->totalizers[$name]['where'] = $where;
        $this->totalizers[$name]['select'] = $select;
        $this->totalizers[$name]['icon'] = $icon;
        $this->totalizers[$name]['icon_container'] = $icon_container;
        $this->totalizers[$name]['replace_icon'] = $replace_icon;
        foreach ($this->totalizers as $filter_name => $filter_opts)
            $filter[$filter_name] = $filter_opts['where'];

        $this->custom_filter('totalizer', $filter);
    }

    public function render_totalizers($render_view_all = true, $display = 'block')
    {
        if ($total_items = count($this->totalizers)) {
            if (! $display)
                $display = 'block';
            $out = '<div class="' . $this->theme_config('totalizer_container') . '" style="display:' . $display . ';">';

            if ($render_view_all) {
                $total_items ++;
                $width = round((12 / $total_items));

                $db = $this->model->db;
                $table_join = $this->_build_table_join();
                $where_tot = $this->_build_where(false, 'all');
                $result = $db->query("SELECT COUNT(*) AS `count` \r\n FROM `{$this->table}`\r\n {$table_join}\r\n {$where_tot}");
                $total = $result->getRowArray();
                $total = $total['count'];

                $icon = ($this->theme_config('totalizer_icon_all') != "") ? $this->theme_config('totalizer_icon_all') : $this->theme_config('totalizer_icon_default');
                $icon_container = ($this->theme_config('totalizer_icon_all_container') != "") ? $this->theme_config('totalizer_icon_all_container') : $this->theme_config('totalizer_icon_container');
                $out .= '<div class="col-md-' . $width . '"><div class="statbox widget box box-shadow"><div class="widget-content">';
                $out .= '<div class="' . $icon_container . '"><i class="' . $icon . '"></i></div>';
                $out .= '<div class="title">' . $this->lang('total') . '</div>';
                $out .= '<div class="value">' . $total . '</div>';
                $out .= '<a class="cCrud-action more" data-filter="totalizer" data-label="all">Visualizar <i class="pull-right fas fa-angle-right"></i></a>';
                $out .= "</div></div></div>";
            }
            foreach ($this->totalizers as $name => $params) {
                $out .= '<div class="col-xs-4"><div class="counter">';
                $out .= '<span class="counter-number font-weight-medium">' . $params['total'] . '</span>';
                $out .= '<div class="counter-label">' . $name . '</div>';
                $out .= '</div></div>';
            }
            $out .= "</div>";
            return $out;
        }
        return false;
    }

    public function nested_readonly_on_view($bool = true)
    {
        $this->nested_readonly_on_view = $bool;
    }

    public function table_always_edit_mode($bool = true)
    {
        $this->table_always_edit_mode = $bool;
    }

    public function get_table()
    {
        return $this->table;
    }

    /**
     * Define quais campos terão suas alterações registradas.
     *
     * @param string $fields Lista de campos separados por vírgula.
     * @param bool $reverse Quando verdadeiro, inverte a seleção dos campos.
     * @return void
     */
    public function record_changes($fields = '', $reverse = false)
    {
        if ($fields != '' && $reverse) {
            $fdata = $this->_parse_field_names($fields, 'fields');
            foreach ($this->fields_edit as $k => $data) {
                if (! in_array($k, array_keys($fdata))) {
                    $record_changes[$k] = $data;
                }
            }
        } elseif ($fields !== '' && ! $reverse) {
            $record_changes = $fdata;
        } elseif ($fields == '') {
            $record_changes = $this->fields_edit;
        }
        $this->record_changes = $record_changes;
    }

    /**
     * Aplica o registro das alterações realizadas.
     *
     * @param array $set Conjunto de dados atualizados.
     * @return void
     */
    private function apply_record_changes($set)
    {
        if (is_array($this->record_changes) && sizeof($this->record_changes)) {
            $old_task = $this->task;
            $this->task = 'view';
            $this->where_pri($this->primary_key, $this->primary_val);
            $select = $this->_build_select_details($this->task);
            $where = $this->_build_where();
            $table_join = $this->_build_table_join();
            $this->task = $old_task;

            $db = $this->model->db;
            $query = $db->query("SELECT {$select}\r\n FROM `{$this->table}`\r\n {$table_join}\r\n {$where}\r\n LIMIT 1");
            $result_row = (array) $query->getRowArray();

            foreach ($set as $k => $f) {
                $f = explode(' = ', str_replace(array(
                    '`',
                    "'"
                ), array(
                    '',
                    ''
                ), $f));
                if ($f[1] == "NULL")
                    $f[1] = null;
                $s[$f[0]] = $f[1];
            }
            $set = $s;

            foreach ($this->record_changes as $key => $fdata) {
                if ($this->field_type[$key] == "image") {
                    continue;
                }
                $val = $result_row[$key];
                if ($this->field_type[$key] == "datetime") {
                    if (! is_null($val) && $val !== '') {
                        $time = Time::createFromFormat('Y-m-d H:i:s', $val);
                        $val = $time->toDateTimeString() . ' (' . $time->humanize() . ')';
                    }
                    if (! is_null($set[$key]) && $set[$key] !== '') {
                        $time = Time::createFromFormat('Y-m-d H:i:s', $set[$key]);
                        $set[$key] = $time->toDateTimeString() . ' (' . $time->humanize() . ')';
                    }
                }
                if ($this->field_type[$key] == "date") {
                    if (! is_null($val) && $val !== '') {
                        $time = Time::createFromFormat('Y-m-d', $val);
                        $val = $time->toDateString() . ' (' . $time->humanize() . ')';
                    }
                    if (! is_null($set[$key]) && $set[$key] !== '') {
                        $time = Time::createFromFormat('Y-m-d', $set[$key]);
                        $set[$key] = $time->toDateString() . ' (' . $time->humanize() . ')';
                    }
                }
                if (array_key_exists($key, $set) && $set[$key] != $val) {
                    if (array_key_exists($key, $this->relation)) {
                        $val = $this->create_view_relation($key, $val);
                        $set[$key] = $this->create_view_relation($key, $set[$key]);
                    }
                    if ($this->labels[$key] == "") {
                        $this->labels[$key] = $key;
                    }
                    $changes[$this->labels[$key]] = "<span class='change-label'>" . $this->labels[$key] . "</span> <br/><span class='change-label'>De:</span> <span class='change-data'>" . $val . "</span><br/><span class='change-label'>Para:</span> <span class='change-data'>" . $set[$key] . "</span>";
                }
            }
            // var_dump($this->result_row);
            // var_dump($changes);
            if (sizeof($changes)) {
                // $this->ci->usuarios_model->registraAlteracao($this->table, $this->primary_val, implode(PHP_EOL . PHP_EOL, $changes));
            }
        }
    }

    public function strip_tags($bool = true)
    {
        $this->strip_tags = $bool;
    }

    protected function relation_search($name, $dependval)
    {
        if (! isset($this->relation[$name])) {
            return 'Restricted.';
        }
        $db = $this->model->db;
        $where_arr = array();
        $request  = Services::request();
        $security = Services::security();
        $q        = $security->clean($request->getPost('q'));
        $where_arr[] = $this->relation[$name]['rel_name'] . ' LIKE "%' . $q . '%"';
        if ($this->relation[$name]['rel_where']) {
            if (is_array($this->relation[$name]['rel_where'])) {
                foreach ($this->relation[$name]['rel_where'] as $field => $val) {
                    $val = $this->replace_text_variables($val, $this->result_row);
                    $fdata = $this->_parse_field_names($field, 'create_relation', $this->relation[$name]['rel_tbl']);
                    $fitem = reset($fdata);
                    $where_arr[] = $this->_where_field($fitem) . $this->_cond_from_where($field) . $db->escape($val);
                }
            } else {
                $where_arr[] = $this->replace_text_variables($this->relation[$name]['rel_where'], $this->result_row);
            }
        }
        if ($dependval !== false) {
            if (is_array($dependval)) {
                foreach ($dependval as $k => $v) {
                    $dependval[$k] = $db->escape($v);
                }
                $where_arr[] = $this->_field_from_where($this->relation[$name]['depend_field']) . ' IN (' . implode(',', $dependval) . ')';
            } else {
                $where_arr[] = $this->_field_from_where($this->relation[$name]['depend_field']) . $this->_cond_from_where($this->relation[$name]['depend_field']) . $db->escape($dependval);
            }
        }

        if ($where_arr)
            $where = 'WHERE ' . implode(' AND ', $where_arr);
        else
            $where = '';
        if (is_array($this->relation[$name]['rel_name'])) {
            $name_select = 'CONCAT_WS(' . $db->escape($this->relation[$name]['rel_separator']) . ',`' . implode('`,`', $this->relation[$name]['rel_name']) . '`) AS `name`';
        } else {
            $name_select = '`' . $this->relation[$name]['rel_name'] . '` AS `name`';
        }
        $result = $db->query('SELECT `' . $this->relation[$name]['rel_field'] . '` AS `field`,' . $name_select . $this->get_relation_tree_fields($this->relation[$name]) . ' FROM `' . $this->relation[$name]['rel_tbl'] . '` ' . $where . ' GROUP BY `field` ORDER BY ' . $this->get_relation_ordering($this->relation[$name]) . ' LIMIT ' . $this->config->relation_ajax);
        $options = $this->resort_relation_opts($result->getResultArray(), $this->relation[$name]);

        $results['items'] = array();
        if ($options) {
            foreach ($options as $opt) {
                $results['items'][] = array(
                    'id' => $opt['field'],
                    'text' => $opt['name']
                );
            }
        }
        echo json_encode($results);
        unset($options);
    }

    public function custom_lists($static = false, $default = false)
    {
        $this->custom_lists = true;
        $this->custom_lists_static = $static;
        $this->custom_filter_active['title'] = $default;
        return $this;
    }

    public function unset_custom_columns($fields)
    {
        if ($fields !== false) {
            $fdata = $this->_parse_field_names($fields, 'custom_lists');
            foreach ($fdata as $fitem) {
                $f[] = $fitem['table'] . '.' . $fitem['field'];
            }
        }
        $this->unset_custom_columns = $f;
    }

    /**
     * Configura as listas personalizadas do usuário.
     *
     * @return void
     */
    private function set_custom_lists(): void
    {
        $db = $this->model->db;
        if (! $db->tableExists('core_listagensPersonalizadas')) {
            return;
        }
        $userId = (int) $this->getSession()->get('usr_id');
        $result = $db->query(
            'SELECT * FROM core_listagensPersonalizadas WHERE lpe_entidade = "' . (($this->table != "contatos" ? $this->table : $this->table_name)) . '" AND (' . ($userId ? 'lpe_usuario = ' . $userId . ' OR ' : '') . 'lpe_usuario IS NULL)'
        );

        if (in_array($this->custom_filter_active['title'], array_keys($this->custom_lists_static))) {
            $this->columns($this->columns_default);
        }

        foreach ($result->getResultArray() as $list) {
            if ($this->custom_filter_active['title'] == $list['lpe_nome']) {
                $this->custom_lists_active = $list;
                unset($colunas);
                if ($list['lpe_colunas'] != "") {
                    $colunas = json_decode($list['lpe_colunas'], true);
                    if (count($colunas)) {
                        $this->columns(implode(',', $colunas), false, false);
                    } else {
                        $this->columns($this->columns_default);
                    }
                } else {
                    $this->columns($this->columns_default);
                }
            }

            $where = array();
            foreach (json_decode($list['lpe_filtros'], true) as $field => $c) {
                $w = '';
                if ($c[0] == "=") {
                    $w .= ' ' . $c[0] . ' "' . $c[1] . '"';
                } elseif ($c[0] == "maior") {
                    $w .= ' > "' . $c[1] . '"';
                } elseif ($c[0] == "menor") {
                    $w .= ' < "' . $c[1] . '"';
                } elseif ($c[0] == "maior_i") {
                    $w .= ' >= "' . $c[1] . '"';
                } elseif ($c[0] == "menor_i") {
                    $w .= ' <= "' . $c[1] . '"';
                } elseif (in_array($c[0], array('IS NULL'))) {
                    $w .= ' (' . $c[0] . ' OR ' . $c[0] . ' = "")';
                } elseif (in_array($c[0], array('IS NOT NULL'))) {
                    $w .= ' (' . $c[0] . ' OR ' . $c[0] . ' <> "")';
                } elseif (in_array($c[0], array('LIKE', 'NOT LIKE'))) {
                    $w .= ' ' . $c[0] . ' "%' . $c[1] . '%"';
                } elseif (in_array($c[0], array('IN', 'NOT IN'))) {
                    $w .= ' ' . $c[0] . ' ("' . implode('","', explode(',', $c[1])) . '")';
                }
                if ($w != "") {
                    $where[] = $field . $w;
                }
            }

            foreach (json_decode($list['lpe_filtrosAdicionais'], true) as $filtro => $value) {
                $w = '';
                if ($filtro == 'propostaProduto') {
                    $w .= '"' . $value . '" IN (SELECT fk_pro_id FROM propostas_itens WHERE fk_prp_id = prp_id)';
                }
                if ($w != "") {
                    $where[] = $w;
                }
            }

            $where = implode(' AND ', $where);
            if ($where == "") {
                $where = "1 = 1";
            }
            $filtro_titulo[$list['lpe_nome']] = $where;
            $line = true;
        }
        $this->custom_lists_temp = $filtro_titulo;
        foreach ($this->custom_lists_static as $label => $where)
            $filtro_titulo[$label] = $where;
        // var_dump($filtro_titulo);
        $this->custom_filter('title', $filtro_titulo, $this->custom_filter_active['title']);
    }

    public function render_title($mode = 'list', $tag = 'h2', $to_show = false, $icon = false, $replace_title = false)
    {
        $out = '';
        if ($this->is_title) {
            if ($replace_title)
                $title = $replace_title;
            else
                $title = $this->table_name;

            if ($this->custom_lists) {
                unset($all_fields);
                foreach ($this->field_type as $field => $type) {
                    if (! in_array($field, $this->unset_custom_columns)) {
                        $lbl = ($this->labels[$field] != "") ? str_replace("*", "", $this->labels[$field]) : $field;
                        $tbl = explode('.', $field);
                        if (in_array($tbl[0], array_keys($this->join)) && strpos($tbl[0], '_ext') === false) {
                            $lbl = " - " . $lbl;
                            $lbl = (($this->labels[$this->join[$tbl[0]]['table'] . '.' . $this->join[$tbl[0]]['field']] != "") ? str_replace("*", "", $this->labels[$this->join[$tbl[0]]['table'] . '.' . $this->join[$tbl[0]]['field']]) : $field) . $lbl;
                        } else {
                            $lbl = $this->table_name . " - " . $lbl;
                        }
                        $all_fields[$field] = $lbl;
                    }
                }
                asort($all_fields);
                $fdata = $this->_parse_field_names($this->columns_default, 'columns');
                foreach ($fdata as $fitem) {
                    $fields_default[] = $fitem['table'] . '.' . $fitem['field'];
                }
                $modals['customLists']['name'] = 'Nova Listagem Personalizada';
                $modals['customLists']['id'] = '';
                $modals['customLists']['cols'] = $fields_default;
                $modals['customLists']['filtros'];

                if (count($this->custom_lists_active)) {
                    $modals['customListsEdit']['name'] = $this->custom_lists_active['lpe_nome'];
                    $modals['customListsEdit']['id'] = $this->custom_lists_active['lpe_id'];
                    $modals['customListsEdit']['cols'] = json_decode($this->custom_lists_active['lpe_colunas'], true);
                    $modals['customListsEdit']['filtros'] = json_decode($this->custom_lists_active['lpe_filtros'], true);
                    $modals['customListsEdit']['filtrosAdicionais'] = json_decode($this->custom_lists_active['lpe_filtrosAdicionais'], true);
                }

                $options['0'] = 'Não Filtrar';
                $options['='] = 'Igual a';
                $options['maior'] = 'Maior que';
                $options['menor'] = 'Menor que';
                $options['maior_i'] = 'Maior ou igual a';
                $options['menor_i'] = 'Menor ou igual a';
                $options['LIKE'] = 'Contém';
                $options['NOT LIKE'] = 'Não contém';
                $options['IN'] = 'Está entre';
                $options['NOT IN'] = 'Não está entre';
                $options['IS NULL'] = 'É vazio ou nulo';
                $options['IS NOT NULL'] = 'Não é vazio ou nulo';

                $db = $this->model->db;
                if ($this->table == "propostas") {
                    $result = $db->query('SELECT * FROM core_produtos WHERE pro_ativo = 1 ORDER BY pro_nome ASC');
                    foreach ($result->getResultArray() as $pro) {
                        $produtos[$pro['pro_id']] = $pro['pro_nome'];
                    }
                }

                foreach ($modals as $tag_id => $cfg) {
                    $out .= $this->open_tag('div', 'modal fade', array(
                        'tabindex' => '-1',
                        'role' => 'dialog',
                        'aria-hidden' => 'true',
                        'id' => $tag_id
                    ));
                    $out .= $this->open_tag('div', 'modal-dialog modal-lg');
                    $out .= $this->open_tag('div', 'modal-content');
                    $out .= $this->open_tag('div', 'modal-header');
                    $out .= $this->open_tag('h4', 'modal-title');
                    $out .= $this->open_tag('i', $icon);
                    $out .= $this->close_tag('i');
                    $out .= "&nbsp;" . $this->table_name . "&nbsp;";
                    $out .= $this->open_tag('small') . $cfg['name'] . $this->close_tag('small') . "&nbsp;";
                    $out .= $this->close_tag('h4');
                    $out .= $this->close_tag('div');
                    $out .= $this->open_tag('div', 'modal-body');

                    $out .= $this->open_tag('div', '', array(
                        'id' => 'head-name'
                    ));
                    $out .= $this->open_tag('label') . 'Nome da Listagem' . $this->close_tag('label');
                    $out .= $this->open_tag('input', '', array(
                        'type' => 'hidden',
                        'id' => 'lpe_id',
                        'value' => $cfg['id']
                    ));
                    $out .= $this->open_tag('input', '', array(
                        'type' => 'hidden',
                        'id' => 'lpe_nome_original',
                        'value' => $cfg['name']
                    ));
                    $out .= $this->open_tag('input', '', array(
                        'type' => 'hidden',
                        'id' => 'lpe_entidade',
                        'value' => ($this->table != "contatos" ? $this->table : $this->table_name)
                    ));
                    $out .= $this->open_tag('input', 'form-control', array(
                        'type' => 'text',
                        'id' => 'name',
                        'value' => ($cfg['id'] != "") ? $cfg['name'] : ''
                    ));
                    $out .= $this->close_tag('div');
                    $out .= $this->open_tag('div', 'tabbable tabbable-custom');
                    $out .= $this->open_tag('ul', 'nav nav-tabs');
                    $out .= $this->open_tag('li', 'active') . $this->open_tag('a', '', array(
                        'data-toggle' => 'tab',
                        'href' => '#filtros_' . $tag_id
                    )) . 'Filtros' . $this->close_tag('a') . $this->close_tag('li');
                    if ($this->table == "propostas")
                        $out .= $this->open_tag('li', '') . $this->open_tag('a', '', array(
                            'data-toggle' => 'tab',
                            'href' => '#filtrosAdicionais_' . $tag_id
                        )) . 'Filtros Adicionais' . $this->close_tag('a') . $this->close_tag('li');
                    $out .= $this->open_tag('li', '') . $this->open_tag('a', '', array(
                        'data-toggle' => 'tab',
                        'href' => '#colunas_' . $tag_id
                    )) . 'Colunas' . $this->close_tag('a') . $this->close_tag('li');
                    $out .= $this->close_tag('ul');

                    $out .= $this->open_tag('div', 'tab-content');
                    $out .= $this->open_tag('div', 'tab-pane active', array(
                        'id' => 'filtros_' . $tag_id
                    ));
                    foreach ($all_fields as $field => $lbl) {
                        if (! in_array($field, array_keys($this->subselect))) {
                            $type = $this->field_type[$field];
                            if ($type == "textarea" || $type == "texteditor")
                                $type = "text";

                            $out .= $this->open_tag('div', 'row next-row form-horizontal col-md-6 pull-left filtrosFields', array(
                                'id' => $field,
                                'style' => 'margin-right: 15px; position: relative; margin-top: 25px;'
                            ));
                            $out .= $this->open_tag('label', 'col-md-12 pull-left') . $lbl . $this->close_tag('label');
                            $out .= $this->open_tag('div', 'col-md-12');
                            $out .= $this->open_tag('div', 'col-md-5 pull-left', array(
                                'style' => 'padding: 0px'
                            ));
                            $out .= $this->open_tag('select', 'form-control f');
                            foreach ($options as $value => $lbl) {
                                unset($atr);
                                $atr['value'] = $value;
                                if ($cfg['id'] != "" && isset($cfg['filtros'][$field]) && $cfg['filtros'][$field][0] == $value)
                                    $atr['selected'] = $value;
                                $out .= $this->open_tag('option', '', $atr) . $lbl . $this->close_tag('option');
                            }
                            $out .= $this->close_tag('select');
                            $out .= $this->close_tag('div');
                            $out .= $this->open_tag('div', 'col-md-7 pull-right inp', array(
                                'style' => 'padding: 0px'
                            ));
                            $func = 'create_' . $type;
                            unset($attr);
                            $attr['class'] = "col-md-8 pull-left cCrud-input";
                            $attr['multiple'] = "multiple";

                            $null_opt = $this->lists_null_opt;
                            $this->lists_null_opt = false;
                            if ($type == 'relation')
                                $val = explode(',', $cfg['filtros'][$field][1]);
                            else
                                $val = $cfg['filtros'][$field][1];
                            if (method_exists($this, $func))
                                $field = call_user_func_array(array(
                                    $this,
                                    $func
                                ), array(
                                    $field,
                                    $val,
                                    $attr
                                ));
                            else
                                $field = "";
                            $this->lists_null_opt = $null_opt;
                            $out .= $field;
                            $out .= $this->close_tag('div');
                            $out .= $this->open_tag('div', 'clearfix') . $this->close_tag('div');
                            $out .= $this->close_tag('div');
                            $out .= $this->close_tag('div');
                        }
                    }
                    $out .= $this->open_tag('div', 'clearfix') . $this->close_tag('div');
                    $out .= $this->close_tag('div');

                    if ($this->table == "propostas") {
                        $out .= $this->open_tag('div', 'tab-pane ', array(
                            'id' => 'filtrosAdicionais_' . $tag_id
                        ));
                        $out .= $this->open_tag('div', 'row next-row form-horizontal col-md-12');
                        $out .= $this->open_tag('label') . 'Propostas que possuam o produto abaixo entre os itens' . $this->close_tag('label');
                        $out .= $this->open_tag('select', 'form-control filtroAd', array(
                            'id' => 'propostaProduto'
                        ));
                        $out .= $this->open_tag('option', '', array(
                            'value' => 0
                        )) . 'Selecione' . $this->close_tag('option');
                        foreach ($produtos as $value => $lbl) {
                            unset($atr);
                            $atr['value'] = $value;
                            if ($cfg['id'] != "" && isset($cfg['filtrosAdicionais']['propostaProduto']) && $value == $cfg['filtrosAdicionais']['propostaProduto'])
                                $atr['selected'] = $value;
                            $out .= $this->open_tag('option', '', $atr) . $lbl . $this->close_tag('option');
                        }
                        $out .= $this->close_tag('select');
                        $out .= $this->close_tag('div');
                        $out .= $this->close_tag('div');
                    }

                    $out .= $this->open_tag('div', 'tab-pane', array(
                        'id' => 'colunas_' . $tag_id
                    ));

                    $out .= $this->open_tag('div', 'col-md-6 dd', array(
                        'id' => 'nestable_list_1'
                    ));
                    $out .= $this->open_tag('h3') . 'Colunas Disponíveis' . $this->close_tag('h3');
                    $out .= $this->open_tag('ol', 'dd-list');
                    foreach ($all_fields as $field => $lbl) {
                        if (! in_array($field, $cfg['cols'])) {
                            $out .= $this->open_tag('li', 'dd-item', array(
                                'data-id' => $field
                            ));
                            $out .= $this->open_tag('div', 'dd-handle');
                            $out .= $lbl;
                            $out .= $this->close_tag('div');
                        }
                    }
                    $out .= $this->close_tag('ol');
                    $out .= $this->close_tag('div');
                    $out .= $this->open_tag('div', 'col-md-6 dd', array(
                        'id' => 'nestable_list_2'
                    ));
                    $out .= $this->open_tag('h3') . 'Colunas Selecionadas' . $this->close_tag('h3');
                    $out .= $this->open_tag('input', '', array(
                        'type' => 'hidden',
                        'id' => 'cols'
                    ));
                    $out .= $this->open_tag('ol', 'dd-list');
                    foreach ($cfg['cols'] as $field) {
                        if (! in_array($field, $this->unset_custom_columns)) {
                            $out .= $this->open_tag('li', 'dd-item', array(
                                'data-id' => $field
                            ));
                            $out .= $this->open_tag('div', 'dd-handle');
                            $out .= $all_fields[$field];
                            $out .= $this->close_tag('div');
                            $out .= $this->close_tag('li');
                        }
                    }
                    $out .= $this->close_tag('ol');
                    $out .= $this->close_tag('div');

                    $out .= $this->close_tag('div');

                    $out .= $this->close_tag('div');
                    $out .= $this->close_tag('div');
                    $out .= $this->close_tag('div');

                    $out .= $this->open_tag('div', 'modal-footer');
                    $out .= $this->open_tag('button', 'btn btn-default', array(
                        'type' => 'button',
                        'data-dismiss' => 'modal',
                        'aria-hidden' => 'true'
                    )) . $this->lang('Cancelar') . $this->close_tag('button');
                    if ($cfg['id'] != "")
                        $out .= $this->open_tag('button', 'btn btn-danger remove', array(
                            'type' => 'button'
                        )) . $this->lang('remove') . $this->close_tag('button');
                    $out .= $this->open_tag('button', 'btn btn-primary save', array(
                        'type' => 'button'
                    )) . $this->lang('save') . $this->close_tag('button');
                    $out .= $this->close_tag('div');
                    $out .= $this->close_tag('div');
                    $out .= $this->close_tag('div');
                    $out .= $this->close_tag('div');
                }

                $out .= $this->open_tag('a', 'dropdown-toggle', array(
                    'data-toggle' => 'dropdown'
                ));
                $out .= $this->open_tag($tag);
                $out .= $this->open_tag('span');
                $out .= $this->open_tag('i', $icon);
                $out .= $this->close_tag('i');
                $out .= "&nbsp;" . $this->table_name . "&nbsp;";
                if ($this->custom_filter_active['title'] != "") {
                    $out .= $this->open_tag('small');
                    $out .= $this->custom_filter_active['title'] != 'all' ? $this->custom_filter_active['title'] : "";
                    $out .= $this->close_tag('small') . "&nbsp;";
                }
                $out .= $this->open_tag('i', 'fas fa-chevron-down');
                $out .= $this->close_tag('i');
                $out .= $this->close_tag('span');
                $out .= $this->close_tag($tag);
                $out .= $this->close_tag('a');
                $out .= $this->open_tag('ul', 'dropdown-menu');

                if ($this->custom_lists_static) {
                    foreach ($this->custom_lists_static as $label => $where) {
                        $filtro_titulo[$label] = $where;
                        $out .= $this->open_tag('li');
                        $out .= $this->open_tag('a', 'cCrud-action custom_list', array(
                            'data-filter' => 'title',
                            'data-label' => $label,
                            'data-preferencia' => 'custom_list_' . (($this->table != "contatos" ? $this->table : $this->table_name)),
                            'href' => '#'
                        ));
                        $out .= $this->open_tag('i', 'fas fa-list-alt fa-fw');
                        $out .= $this->close_tag('i');
                        $out .= $label . $this->close_tag('a');
                        $out .= $this->close_tag('li');
                    }
                } else {
                    $filtro_titulo[$this->lang('default_list')] = '1=1';
                    $out .= $this->open_tag('li');
                    $out .= $this->open_tag('a');
                    $out .= $this->open_tag('i', 'fas fa-list-alt fa-fw');
                    $out .= $this->close_tag('i');
                    $out .= $this->lang('default_list') . $this->close_tag('a');
                    $out .= $this->close_tag('li');
                }

                $out .= $this->open_tag('li', 'divider') . $this->close_tag('li');
                foreach ($this->custom_lists_temp as $label => $where) {
                    $line = true;
                    $filtro_titulo[$label] = $where;
                    $out .= $this->open_tag('li');
                    $out .= $this->open_tag('a', 'cCrud-action custom_list', array(
                        'data-filter' => 'title',
                        'data-label' => $label,
                        'data-preferencia' => 'custom_list_' . (($this->table != "contatos" ? $this->table : $this->table_name)),
                        'href' => '#'
                    ));
                    $out .= $this->open_tag('i', 'fas fa-filter fa-fw');
                    $out .= $this->close_tag('i');
                    $out .= $label . $this->close_tag('a');

                    if ($label == $this->custom_filter_active['title']) {
                        $out .= $this->open_tag('i', 'btn btn-xs btn-default fas fa-pencil-alt edit', array(
                            'data-toggle' => 'modal',
                            'data-target' => '#customListsEdit'
                        ));
                        $out .= $this->close_tag('i');
                    }

                    $out .= $this->close_tag('li');
                }
                $this->custom_filter('title', $filtro_titulo, ($this->custom_filter_active['title'] != "") ? $this->custom_filter_active['title'] : $default_list, true);

                if ($line)
                    $out .= $this->open_tag('li', 'divider') . $this->close_tag('li');
                $out .= $this->open_tag('li');
                $out .= $this->open_tag('a', '', array(
                    'data-toggle' => 'modal',
                    'href' => '#customLists'
                ));
                $out .= $this->open_tag('i', 'fas fa-plus fa-fw');
                $out .= $this->close_tag('i');
                $out .= $this->lang('add_custom_list') . $this->close_tag('a');
                $out .= $this->close_tag('li');
                $out .= $this->close_tag('ul');
            } else {
                $out = $this->renderTableName($mode, $tag, $to_show, $icon, $replace_title);
            }
            return $out;
        }
        return $out;
    }
    
    public function mass_remove(bool $bool = true)
    {
        if ($bool) {
            $this->mass_actions['delete'] = $bool;
        } else {
            unset($this->mass_actions['delete']);
        }
        return $this;
    }
    
    public function mass_merge(string|bool $callable, string $path)
    {
        if ($callable) {
            $this->mass_actions['merge'] = [
                'callable' => $callable,
                'path' => "/../../helpers/" . $path
            ];
        } else {
            unset($this->mass_actions['merge']);
        }
        return $this;
    }
    
    public function mass_edit(array|string|bool $fields)
    {
        if (! is_bool($fields)) {
            $fdata = $this->_parse_field_names($fields, 'mass_edit');
            foreach ($fdata as $fitem) {
                $this->mass_actions['edit'][$fitem['table'] . '.' . $fitem['field']] = $fitem;
            }
        } else if (! $fields) {
            unset($this->mass_actions['edit']);
        }
        return $this;
    }
    
    
    public function render_mass_actions()
    {
        if (count($this->mass_actions)) {
            $out = $this->open_tag('div', $this->theme_config('mass_container_class'));
            $out .= $this->open_tag('select', 'cCrud-mass-select not_select2 not_select2 cCrud-data ' . $this->theme_config('mass_select'), array(
                'name' => 'mass_task'
            ));
            $out .= $this->open_tag('option', '', array(
                'value' => 0
            )) . $this->lang('mass_action') . $this->close_tag('option');
            if (array_key_exists('edit',$this->mass_actions)) {
                $out .= $this->open_tag('option', '', array(
                    'value' => 'edit'
                )) . $this->lang('mass_edit') . $this->close_tag('option');
            }
            if (array_key_exists('merge',$this->mass_actions)) {
                $out .= $this->open_tag('option', '', array(
                    'value' => 'merge'
                )) . $this->lang('mass_merge') . $this->close_tag('option');
            }
            if (array_key_exists('remove',$this->mass_actions)) {
                $out .= $this->open_tag('option', '', array(
                    'value' => 'remove'
                )) . $this->lang('mass_remove') . $this->close_tag('option');
            }
            $out .= $this->close_tag('select');
            $out .= $this->open_tag('a', 'cCrud-action ' . $this->theme_config('mass_button'), array(
                'data-task' => 'mass',
                'href' => 'javascript:;',
                'data-after' => 'list',
                'data-confirm' => $this->lang('mass_apply_confirm')
            )) . $this->lang('mass_apply') . $this->close_tag('a');
            $out .= $this->close_tag('div');
            return $out;
        }
    }

    public function render_mass_edit_form($container = 'table', $row = 'tr', $label = 'td', $field = 'td', $tabs_block = 'div', $tabs_head = 'ul', $tabs_row = 'li', $tabs_link = 'a', $tabs_content = 'div', $tabs_pane = 'div')
    {
        $out = "";
        if (count($this->mass_actions)) {
            $out .= $this->open_tag('div', 'cCrud-mass-form ' . $this->theme_config('mass_form_container'));
            foreach ($this->mass_edit as $field => $fitem) {
                $type = $this->field_type[$field];
                $lbl = $this->labels[$field];
                if ($type == "textarea" || $type == "texteditor") {
                    $type = "text";
                }
                $out .= $this->open_tag('div', 'cCrud-mass-form-group ' . $this->theme_config('mass_form_field_group'), array(
                    'id' => $field
                ));
                $out .= $this->open_tag('label', $this->theme_config('mass_form_label'), array(
                    'for' => $field
                )) . $lbl . $this->close_tag('label');
                $func = 'create_' . $type;
                if (method_exists($this, $func))
                    $field = call_user_func_array(array(
                        $this,
                        $func
                    ), array(
                        $field,
                        null,
                        [
                            'class' => 'cCrud-input ' . $this->theme_config('mass_form_field')
                        ]
                    ));

                $out .= $field;
                $out .= $this->close_tag('div');
            }
            $out .= $this->close_tag('div');
            return $out;
        }
    }

    public function _mass_action()
    {
        if ($this->table_ro)
            // Operação não permitida em tabela somente leitura
            return Services::response()->setStatusCode(403)->setBody(lang('cCrud.forbidden'));
            
            $this->set_custom_lists();
            $this->_set_field_types('list');
            $this->_list(false);
            if ($this->_post('mass_task') == 'edit') {
                $postdata = $this->_post('postdata');
                $postdata = $this->check_postdata($postdata, true);
                $pd = new PostData($postdata, $this);
                $postdata = $pd->toArray();
                // Validação dos dados em massa utilizando o Model
                if (! $this->model->validate($postdata)) {
                    foreach ($this->model->errors() as $field => $error) {
                        $this->set_exception_fields($field, $error);
                    }
                }
                if ($this->exception) {
                    return $this->call_exception($postdata);
                }
                $this->_set_field_types('edit', true);
                foreach ($this->result_list as $key => $row) {
                    if (in_array($row['primary_key'], $this->_post('mass_list', array())) && $this->is_edit($row)) {
                        $this->_update($postdata, $row['primary_key']);
                    }
                }
                unset($postdata);
            } else if ($this->_post('mass_task') == 'merge' && isset($this->mass_actions['merge'])) {
                $this->_set_field_types('edit', true);
                $path = $this->check_file($this->mass_actions['merge']['path'], 'merge');
                include_once ($path);
                if (is_callable($this->mass_actions['merge']['callable'])) {
                    call_user_func_array($this->mass_actions['merge']['callable'], array(
                        $this->_post('mass_list', array()),
                        $this
                    ));
                    if ($this->exception) {
                        return $this->call_exception($postdata);
                    }
                }
            } else if ($this->_post('mass_task') == 'delete') {
                foreach ($this->result_list as $key => $row) {
                    if (in_array($row['primary_key'], $this->_post('mass_list', array())) && $this->is_remove($row)) {
                        if ($this->replace_remove) {
                            $path = $this->check_file($this->replace_remove['path'], 'replace_remove');
                            include_once ($path);
                            if (is_callable($this->replace_remove['callable'])) {
                                call_user_func_array($this->replace_remove['callable'], array(
                                    $this->_post('mass_list', array()),
                                    $this
                                ));
                            }
                        }
                    }
                }
            }
            $this->_set_field_types('list');
            $this->_list();
            return $this->_render_list();
    }

    function opened_tab($id = false)
    {
        $this->opened_tab = $id;
    }

    function nested_default_render($mode = 'list', $primary = false)
    {
        $this->nested_default_render = $mode;
        $this->nested_default_render_primary = $primary;
    }

    function join_relation($fields = '', $rel_tbl = '', $rel_field = '', $rel_name = '', $rel_where = array(), $order_by = false, $rel_concat_separator = ' ', $depend_field = '', $depend_on = '', $rel_additional_fields = false, $default_data_create = null, $default_data_edit = null, $tag_support = false)
    {
        $this->relation($fields, $rel_tbl, $rel_field, $rel_name, $rel_where, $order_by, false, $rel_concat_separator, false, $depend_field, $depend_on);
        $fdata = $this->_parse_field_names($fields, 'join_relation');

        $rel_additional_fields = $this->_parse_field_names($rel_additional_fields, 'join_relation', $rel_tbl);
        foreach ($this->_parse_field_names($rel_name, 'join_relation', $rel_tbl) as $f => $fd) {
            unset($rel_additional_fields[$f]);
        }
        foreach ($fdata as $fitem) {
            $this->join_relation[$fitem['table'] . '.' . $fitem['field']] = array(
                'rel_table' => $rel_tbl,
                'rel_additional_fields' => $rel_additional_fields,
                'default_data_create' => $default_data_create,
                'default_data_edit' => $default_data_edit,
                'tag_support' => $tag_support
            );
        }
        return $this;
    }

    function join_relation_data($field, $value)
    {
        if (! $field) {
            return;
        }
        if (array_key_exists($field, $this->relation) && array_key_exists($field, $this->join_relation)) {
            foreach ($this->join_relation[$field]['rel_additional_fields'] as $k => $v) {
                $select[] = $k . ' as "' . $k . '"';
            }
            $select = implode(',', $select);
            $db = $this->model->db;
            // echo 'SELECT ' . $select . ' FROM `' . $this->relation[$field]['rel_tbl'] . '` WHERE `' . $this->relation[$field]['rel_field'] . '` = "' . $value . '"';exit;
            $query = $db->query('SELECT ' . $select . ' FROM `' . $this->relation[$field]['rel_tbl'] . '` WHERE `' . $this->relation[$field]['rel_field'] . '` = "' . $value . '"');
            $data = $query->getRowArray();
            $mode = 'create';
            foreach ($this->join_relation[$field]['rel_additional_fields'] as $fld => $fdata) {
                $func = 'create_' . $this->field_type[$fld];
                if (isset($this->field_callback[$fld]) && $mode != 'view') {
                    $path = $this->check_file($this->field_callback[$fld]['path'], 'field_callback');
                    include_once ($path);
                    if (is_callable($this->field_callback[$fld]['callback'])) {
                        $field_rendered = call_user_func_array($this->field_callback[$fld]['callback'], array(
                            $this->result_row[$fld],
                            $fld,
                            $mode,
                            $this->result_row,
                            $this
                        ));
                        if (! $field_rendered)
                            $field_rendered = call_user_func_array(array(
                                $this,
                                $func
                            ), array(
                                $fld,
                                $data[$fld],
                                $attr
                            ));
                        $return[$this->fieldname_encode($fld)] = $field_rendered;
                    }
                } else {
                    $attr = $this->get_field_attr($fld, $mode);
                    $attr['class'] .= ' jr_additional';
                    $attr['jr-parent'] = $this->fieldname_encode($field);
                    if (! method_exists($this, $func)) {
                        continue;
                    }
                    if ($this->field_type[$fld] == "relation" && $this->relation[$fld]['depend_on'] && isset($data[$this->relation[$fld]['depend_on']])) {
                        $return[$this->fieldname_encode($fld)] = call_user_func_array(array(
                            $this,
                            $func
                        ), array(
                            $fld,
                            $data[$fld],
                            $attr,
                            $data[$this->relation[$fld]['depend_on']]
                        ));
                    } else {
                        $return[$this->fieldname_encode($fld)] = call_user_func_array(array(
                            $this,
                            $func
                        ), array(
                            $fld,
                            $data[$fld],
                            $attr
                        ));
                    }
                }
            }
            print json_encode($return);
            exit();
        }
    }

    function join_relation_tag($postdata)
    {
        foreach ($this->join_relation as $key => $params) {
            if ($params['tag_support'] === true) {
                $val = $postdata[$key];
                $db = $this->model->db;
                $builder = $db->table($this->relation[$key]['rel_tbl']);
                $row = $builder->where($this->relation[$key]['rel_field'], $val)->get()->getRowArray();
                $set = array();
                if (is_array($this->join_relation[$key]['rel_additional_fields'])) {
                    foreach ($this->join_relation[$key]['rel_additional_fields'] as $k => $fdata) {
                        $set[str_replace($this->relation[$key]['rel_tbl'] . '.', '', $k)] = $postdata[$k];
                    }
                }

                if (! count($row)) {
                    if (is_array($this->join_relation[$key]['default_data_create'])) {
                        foreach ($this->join_relation[$key]['default_data_create'] as $k => $v) {
                            $set[$k] = $v;
                        }
                    }
                    $set[$this->relation[$key]['rel_name']] = $val;

                    // inserir o campo do depend_on
                    if ($this->relation[$key]['depend_on'] != "" && $this->relation[$key]['depend_field'] != "") {
                        $set[$this->relation[$key]['depend_field']] = $postdata[$this->relation[$key]['depend_on']];
                    }

                    $db->table($this->relation[$key]['rel_tbl'])->insert($set);
                    $postdata[$key] = $db->insertID();
                } else {
                    if (is_array($this->join_relation[$key]['default_data_edit'])) {
                        foreach ($this->join_relation[$key]['default_data_edit'] as $k => $v) {
                            $set[$k] = $v;
                        }
                    }
                    $db->table($this->relation[$key]['rel_tbl'])->update($set, [$this->relation[$key]['rel_field'] => $row[$this->relation[$key]['rel_field']]]);
                }
            }
            unset($set);
        }
        return $postdata;
    }

    public function parameter($name, $field, $operator = '=')
    {
        if (! in_array($operator, array(
            '=',
            'between',
            'IN',
            'FIND_IN_SET'
        ))) {
            return false;
        }
        $field = $this->_parse_field_names($field, 'parameter');
        foreach ($field as $fname => $fdata) {
            $this->parameters[$name] = array(
                'field' => $fname,
                'operator' => $operator
            );
            $this->fields_report[$fname] = $fdata;
            $this->fields_names[$fname] = \esc($name);
        }
        return $this;
    }

    protected function _report($postdata = array())
    {
        foreach ($this->parameters as $name => $attr) {
            if ($attr['operator'] == 'between') {
                $this->create_field($attr['field'] . '_to', $this->field_type[$attr['field']], null, array(
                    'data-rangestart' => $this->fieldname_encode($attr['field'])
                ));
                $this->field_attr[$attr['field']] = array(
                    'data-rangeend' => $this->fieldname_encode($attr['field'] . '_to')
                );
                $this->parameters[$attr['field'] . '_to'] = array(
                    'field' => $attr['field'] . '_to',
                    'operator' => '_to'
                );
                $this->fields_names[$attr['field'] . '_to'] = $attr['field'] . '_to';
            } elseif ($attr['operator'] == 'IN' && $this->field_type[$attr['field']] == 'relation') {
                $this->relation[$attr['field']]['multi'] = true;
            }
        }
        $this->lists_null_opt = false;
        return $this->_render_report();
    }

    protected function _render_report()
    {
        $mode = 'report';
        foreach ($this->parameters as $name => $attr) {
            $field = $attr['field'];
            $operator = $attr['operator'];

            if ($operator == 'between' && ! in_array($this->field_type[$field], array(
                'date',
                'datetime',
                'int'
            ))) {
                continue;
            }

            $func = 'create_' . $this->field_type[$field];

            if (isset($this->field_callback[$field])) {
                $path = $this->check_file($this->field_callback[$field]['path'], 'field_callback');
                include_once ($path);
                if (is_callable($this->field_callback[$field]['callback'])) {
                    $attr = $this->get_field_attr($field, 'create');
                    $field_default = call_user_func_array(array(
                        $this,
                        $func
                    ), array(
                        $field,
                        null,
                        $attr
                    ));
                    $field_rendered = call_user_func_array($this->field_callback[$field]['callback'], array(
                        null,
                        $field,
                        'create',
                        null,
                        $this,
                        $field_default
                    ));
                    if (! $field_rendered)
                        $field_rendered = call_user_func_array(array(
                            $this,
                            $func
                        ), array(
                            $field,
                            $this->result_row[$field],
                            $attr
                        ));
                    $this->fields_output[$field] = array(
                        'label' => $this->fields_names[$field],
                        'field' => $field_rendered,
                        'name' => $field
                    );
                }
            } else {
                $attr = $this->get_field_attr($field, 'create');
                if (! method_exists($this, $func))
                    continue;
                $this->fields_output[$field] = array(
                    'label' => $this->fields_names[$field],
                    'field' => call_user_func_array(array(
                        $this,
                        $func
                    ), array(
                        $field,
                        null,
                        $attr
                    )),
                    'name' => $field
                );
            }
        }

        $view_file = __DIR__ . '/views/' . $this->views[$mode];
        $view_file = $this->check_file($view_file, 'render');
        $viewPath = dirname($view_file);
        $renderer = Services::renderer($viewPath);
        $content = $renderer->setData([
            'cCrud' => $this,
            'mode' => $mode,
            'title' => $this->get_var('title')
        ])->render(basename($view_file));

        $content = $this->render_search_hidden() . $content;

        $output = $this->render_output($content);
        return $this->set_output($output);
    }

    private function _save_report_values()
    {
        $postdata = $this->_post('postdata');
        if (! $postdata)
            return;
        foreach ($postdata as $k => $v) {
            if (trim($postdata[$k]) == "") {
                unset($postdata[$k]);
            }
        }
        $postdata = $this->check_postdata($postdata, 0);
        foreach ($postdata as $field => $val) {
            $this->report_values[$field] = $val;
        }
    }

    public function search_lines($lines = 1)
    {
        $this->search_lines = (int) $lines;
    }

    /**
     * Encaminha chamadas desconhecidas para o Model associado.
     *
     * @param string $name Nome do método chamado.
     * @param array<int,mixed> $arguments Argumentos do método.
     *
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->model, $name)) {
            return $this->model->$name(...$arguments);
        }

        throw new \BadMethodCallException("Método {$name} não encontrado");
    }
}

