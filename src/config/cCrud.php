<?php 
namespace cCrud\Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Configurações padrão do cCrud.
 */
class cCrud extends BaseConfig
{
    // scripts
    public $load_bootstrap = false; // turn on, if you want to load bootstrap via cCrud
    public $load_googlemap = false; // loads google map api for 'POINT' type. Turn off, if your site already uses it.
    public $load_jquery = false; // loads jQuery, turn it off if you already have jQuery on your page. jQuery version must be at least 1.7. If your jQuery loads in the bottom of page, you must activate $manual_load and use  cCrud::load_css() & cCrud::load_js() on your page.
    public $load_jquery_ui = false; // jQueryUI, turn it on if you already have jQueryUI on your page (datepicker and slider widgets are mandatory).
    public $load_jcrop = false; // disable, if your page already uses jCrop
    public $jquery_no_conflict = false; // Includes jQuery.noConflict(). Use according to jQuery documentation.
    public $manual_load = false; // Allows you to disable cCruds css and js output, but you can use cCrud::load_css() & cCrud::load_js() in your code manually.

    
    // editor
    public $editor_url = '/plugins/ckeditor/ckeditor.js'; // URL path to editor script, if you want to use the visual editor.
    //public $editor_url = 'assets/js/plugins/tinymce/tinymce.min.js'; // URL path to editor script, if you want to use the visual editor.
    public $editor_init_url = ''; //  URL path to your custom initialization file for editor.
    public $force_editor = false; // Forced initialization of editor, even if the path is not specified. Check this if you're already using editor on your page.
    public $auto_editor_insertion = true; // inserts visual editor on textarea fields.
    
    
    // grid settings
    public $show_primary_ai_field = false; // Show primary auto-increment field in create/edit view.
    public $show_primary_ai_column = false; // Show primary auto-increment column in list view.
    public $can_minimize = false; // allows 'minimize' arrow in grid
    public $start_minimized = false; // Start all cCrud instances minimized.
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
    
    // alert settings
    public $email_from = 'mailer@example.com'; // email from address
    public $email_from_name = ''; // email from name
    public $email_enable_html = true; // enables html in email letters

    
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
    
    
    // cCrud folder url
    public $scripts_url = ''; // URL to the cCrud folder, not real path, without a trailing slash, can be relative, e.g. 'some_folder/cCrud' or absolute, e.g. 'http://www.your_site.com/some_folder/cCrud'. If empty - will be detected automatically
    public $urls2abs = true; // makes relative urls to absolute. Turn off if you have some troubles with relative urls.
    
    
    // system integration options. NO ANY TRAILING SLASHES!
    // urls (relative to $scripts_url or cCrud's folder, if $scripts_url is not defined)
    public $plugins_uri = 'plugins'; // scripts and libraries
    public $lang_uri = 'application/language'; // js files
    public $ajax_uri = 'ajax'; // main ajax file ou url
    // paths (relative to cCrud's folder)
    public $lang_path = '../../language/'; // ini files
    // external session
    public $external_session = false; // use only when you use integration with externall session
    // loading events
    public $before_construct = false; // callable param, runs before instance creation
    public $after_render = false; // callable param, runs after instance was rendered
    
    
    // system
    public $performance_mode = false; // experimental, disables {field_tags} features
    public $autoclean_timeout = 3600; // in seconds. Do not change, if not sure. cCrud clears old instances in session when you reload browser tab or open new tab with cCrud. In this case cCrud can't work in two tabs in the same time. You can increase timeout on your risk.
    
    
    // anti XSS
    public $auto_xss_filtering = false; // enable all cCrud's POST and GET data filtering
    public $xss_disalowed_attibutes = array('on\w*', /*'style',*/ 'xmlns', 'formaction'); // Remove bad attributes such as style, onclick and xmlns
    public $xss_naughty_html = 'alert|applet|audio|basefont|base|behavior|bgsound|blink|body|embed|expression|form|frameset|frame|head|html|ilayer|input|isindex|layer|link|meta|object|plaintext|script|textarea|title|video|xml|xss'; // If a tag containing any of the words in the list below is found, the tag gets converted to entities.
    public $xss_naughty_scripts = 'alert|cmd|passthru|eval|exec|expression|system|fopen|fsockopen|file|file_get_contents|readfile|unlink'; // imilar to above, only instead of looking for tags it looks for PHP and JavaScript commands that are disallowed.  Rather than removing the code, it simply converts the parenthesis to entities rendering the code un-executable.


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
