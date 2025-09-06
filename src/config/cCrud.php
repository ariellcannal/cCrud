<?php 
namespace cCrud\Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Configurações padrão do cCrud.
 */
class cCrud extends BaseConfig
{
    public bool $manual_load = false; // permite desativar a saída automática de CSS e JS

    /**
     * Bibliotecas CSS a serem carregadas via CDN.
     *
     * @var string[]
     */
    public array $css_libs = [
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
        'https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.min.css',
        'https://cdnjs.cloudflare.com/ajax/libs/Jcrop/0.9.15/jquery.Jcrop.min.css',
        'https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css',
        'https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css',
    ];

    /**
     * Bibliotecas JavaScript a serem carregadas via CDN.
     *
     * @var string[]
     */
    public array $js_libs = [
        'https://code.jquery.com/jquery-3.7.1.min.js',
        'https://code.jquery.com/ui/1.13.2/jquery-ui.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/Jcrop/0.9.15/jquery.Jcrop.min.js',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
        'https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js',
        'https://maps.googleapis.com/maps/api/js',
        'https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js',
    ];

    /**
     * URI base utilizada pelo cCrud para processar requisições.
     *
     * @var string
     */
    public string $request_uri = 'c';

    
    // editor
    public bool $force_editor = false; // força a inicialização do editor visual
    public bool $auto_editor_insertion = true; // insere automaticamente o editor em textareas
    
    
    // grid settings
    public $show_primary_ai_field = false; // Show primary auto-increment field in create/edit view.
    public $show_primary_ai_column = false; // Show primary auto-increment column in list view.
    public $remove_confirm = true; // Show confirmation dialog on remove action.
    public $column_cut = 100; // Sets the maximum number of characters in the column.
    public $limit = 25; // default limit of rows per page
    public $limit_list = array('25', '50', '100', '150', '200'); // default limits list
    public $clickable_list_links = false; // make all links, emails clikable in list view
    public $clickable_filenames = true; // makes filenames clikable in list view
    public $fixed_action_buttons = true; // it allows to fix the action buttons on the right side of the table. Appears when you hover on row.
    public $images_in_grid = true; // shows images in list view
    public $images_in_grid_height = 55; // maximal height of thumbnails in list view
    public $button_labels = false; // displays button labels in grid
    public $strip_tags = false; // remove all tags from data in grid view. This is not affected to user patterns or other custom.
    public $safe_output = false; // encodes special characters to html-entities in grid view
    
    
    // print
    public $print_all_fields = false; // print all fields and rows of table or only visible.
    public $print_full_texts = false; // print grid without cutting
    
    // csv export
    public $csv_delimiter = ';'; // default delimiter in CSV file.
    public $csv_enclosure = '"'; // default enclosure in CSV file.
    public $csv_all_fields = false; // export all fields and rows of table or only visible.
    public $csv_limit = 5000; // número máximo de linhas que podem ser exportadas.

    
    // editing
    public $make_checkbox = true; // display TINYINT(1),BIT(1),BOOL(1),BOOLEAN(1) fields like checkboxes
    public $lists_null_opt = true; // display null(empty) option in all dropdowns and multiselects
    public $enum_as_radio = false; // shows ENUM field as radiobox, dropdown by default
    public $set_as_checkboxes = false; // shows SET field as checkboxes, multiselect by default
    public $upload_folder_def = '../../uploads/'; // Default uploads folder on your site, relative to cCrud folder or absolute path required. Folder is must exist.
    public $not_null_is_required = true; // makes not null fields required
    public $encode_field_names = false;
    
    
    // features
    public $enable_printout = true; // show print button
    public $enable_search = true; // show searck block
    public $enable_pagination = true; // show pagination
    public $enable_csv_export = true; // show csv export button
    public $enable_table_title = true; // show table title and toggle button
    public $enable_numbers = false; // show row numbers in grid
    public $enable_limitlist = true; // show row numbers in grid
    public $enable_sorting = true; // alows to sort by column
    public $benchmark = false; // Displays information about the performance in the lower right corner.
    public $nested_readonly_on_view = true; // turn of editing nested tables when viewing parent (can edit only when editing parent)
    public $default_tab = false; // Sets name of tab for fields which not assigned with any tab. This tab will be created automatically. Tab will not be created when is FALSE.
    public $nested_in_tab = true; // Nested will be displayed in tab if tabs are active
    public $relation_ajax = 5000; // number of register to trasnform relation in ajax.
    
    // remote request options (call_page() methods)
    public $use_browser_info = false; // allow to use your browser cookie, referer, user agent for http request to some file or url. BE CAREFUL: DON'T USE IT FOR REQUESTS TO EXTERNAL SITES!!!

    
    // date
    public $date_first_day = 0; // 0 - Sunday, 1 - Monday etc. Uses in datepicker and search ranges
    public $date_format = 'dd/mm/yy'; // jqueryui date format
    public $time_format = 'HH:mm'; // jqueryui time format
    public $php_date_format = 'd/m/Y'; // php date format
    public $php_time_format = 'H:i'; // php time format
    
    // search
    public $search_all = true; // enables -all- option for search
    public $available_date_ranges = array( // available date ranges, can be translated in language file
        'next_year',
        'next_month',
        'today',
        'this_week_today',
        'this_week_full',
        'last_week',
        'last_2weeks',
        'this_month',
        'last_month',
        'last_3months',
        'last_6months',
        'this_year',
        'last_year');
    public $search_pattern = array('%','%'); // uses for LIKE operator in SQL request
    public $search_opened = true; // make search always opened
    
    
    // map
    public $default_point = '35.6894875,139.69170639999993';
    public $default_text = 'your_position';
    public $default_zoom = 8;
    public $default_width = 500;
    public $default_height = 300;
    public $default_coord = true;
    public $default_search = true;
    public $default_search_text = 'search_here';
    public $maps_api_key = '';
    
    
    public $urls2abs = true; // makes relative urls to absolute. Turn off if you have some troubles with relative urls.

  // loading events
    public $before_construct = false; // callable param, runs before instance creation
    public $after_render = false; // callable param, runs after instance was rendered
    
    
    // system
    public $performance_mode = false; // experimental, disables {field_tags} features
    public $autoclean_timeout = 3600; // in seconds. Do not change, if not sure. cCrud clears old instances in session when you reload browser tab or open new tab with cCrud. In this case cCrud can't work in two tabs in the same time. You can increase timeout on your risk.
    
    /**
     * Instância da configuração carregada.
     *
     * @var self|null
     */
    private static ?self $instance = null;

    /**
     * Retorna a instância das configurações, aplicando sobrecargas
     * definidas pelo consumidor do pacote.
     *
     * @return self Configurações do cCrud
     */
    public static function instance(): self
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        // Inicia com a configuração base
        $config = new self();

        // Carrega configurações personalizadas da aplicação, se existirem
        if (defined('APPPATH')) {
            $path = rtrim(APPPATH, '\\/') . '/cCrud.php';
            if (is_file($path)) {
                $appConfig = require $path;
                if (is_array($appConfig)) {
                    foreach ($appConfig as $key => $value) {
                        if (property_exists($config, $key)) {
                            $config->$key = $value;
                        }
                    }
                }
            }
        }

        self::$instance = $config;

        return self::$instance;
    }

}
