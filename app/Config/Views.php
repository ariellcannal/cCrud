<?php
namespace cCrud\Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Configurações das classes CSS utilizadas pelas views do cCrud.
 */
class Views extends BaseConfig
{
    /**
     * Mapeamento das classes padrão para os componentes visuais.
     *
     * @var array<string,string>
     */
    public array $classes = [
        // upload
        'upload_button' => 'btn btn-success',
        'upload_button_icon' => 'fas fa-file-upload',
        'remove_button' => 'btn btn-danger',
        'remove_button_icon' => 'fa fa-times',
        'image' => 'img-thumbnail',
        'file_name' => 'btn btn-info',
        'no_file' => 'btn btn-warning',

        // containers e abas
        'details_container' => 'form-horizontal',
        'details_row' => 'mb-3 row',
        'details_label_cell' => 'col-form-label',
        'details_field_cell' => 'col-sm-10',
        'tabs_container' => '',
        'tabs_header_cell' => 'nav-item',
        'tabs_header_link' => 'nav-link',
        'tabs_content' => 'tab-content',
        'tabs_content_pane' => 'tab-pane pt-3',
        'tabs_first_element' => 'active',

        // campos
        'int_field' => 'form-control',
        'float_field' => 'form-control',
        'text_field' => 'form-control',
        'password_field' => 'form-control',
        'textarea_field' => 'form-control',
        'texteditor_field' => 'form-control',
        'price_field' => 'form-control',
        'coord_field' => 'form-control',
        'address_field' => 'form-control',
        'date_field' => 'form-control',
        'datetime_field' => 'form-control',
        'time_field' => 'form-control',
        'timestamp_field' => 'form-control',
        'select_field' => 'form-select',
        'year_field' => 'form-control',
        'multiselect_field' => 'form-control',
        'relation_field' => 'form-control',
        'validation_error_field' => 'is-invalid',
        'validation_error_text' => 'invalid-feedback',

        // campos booleanos e rádio
        'bool_container' => 'form-check form-switch',
        'bool_field' => 'form-check-input',
        'bool_label' => 'form-check-label',
        'radio_container' => 'form-check',
        'radio_field' => 'form-check-input',
        'radio_label' => 'form-check-label',

        // campos remotos e coordenadas
        'remote_image_field' => 'form-control',
        'remote_image' => '',
        'point_map' => '',
        'point_field' => 'form-control',
        'point_search' => 'form-control',

        // elementos de busca
        'search_container' => 'me-2 justify-content-end input-group input-group-sm w-100',
        'search_line_container' => '',
        'search_go' => 'btn btn-sm btn-primary',
        'search_reset' => 'btn btn-sm btn-secondary',
        'search_add' => 'btn btn-success',
        'search_toggle' => 'btn btn-sm btn-secondary',
        'search_toggle_icon' => 'btn-icon fas fa-search',
        'search_phrase' => 'form-control form-control-sm',
        'search_from' => 'form-control form-control-sm',
        'search_to' => 'form-control form-control-sm',
        'search_range' => 'form-control form-control-sm',
        'search_fieldlist' => 'form-control form-control-sm w-25',
        'search_phrase_dropdown' => 'form-control form-control-sm',
        'search_button_group' => 'btn-group ',

        // botões do grid
        'grid_default' => 'btn btn-default btn-sm',
        'grid_default_icon' => 'glyphicon glyphicon-globe',
        'grid_edit' => 'btn btn-warning btn-sm',
        'grid_edit_icon' => 'fas fa-pencil-alt',
        'grid_remove' => 'btn btn-danger btn-sm',
        'grid_remove_icon' => 'fas fa-trash',
        'grid_view' => 'btn btn-info btn-sm',
        'grid_view_icon' => 'fas fa-ellipsis-h',
        'grid_duplicate' => 'btn btn-inverse btn-info btn-sm',
        'grid_duplicate_icon' => 'fas fa-clone',
        'grid_button_group' => 'btn-group',

        // paginação
        'pagination_container' => 'pagination pagination-sm ms-2',
        'pagination_item' => 'page-item',
        'pagination_link' => 'page-link',
        'pagination_active' => 'page-item active',
        'pagination_dots' => 'page-item disabled',

        // limite de registros
        'limit_list_container' => '',
        'limit_list_button' => 'btn btn-secondary btn-sm',
        'limit_list_menu' => 'dropup',
        'limit_list_item' => '',

        // diversos
        'tooltip_icon' => 'fas fa-info-circle',
        'modal_icon' => 'fas fa-arrows-alt',
        'slide_down_icon' => 'fas fa-chevron-down',
        'slide_up_icon' => 'fas fa-chevron-up',

        // alfabético
        'alphabetical_container' => 'btn-group btn-group-sm btn-toolbar bg-default',
        'alphabetical_item' => 'btn btn-default btn-squared',
        'alphabetical_active' => 'btn btn-primary btn-squared active',

        // filtro personalizado
        'custom_filter_container' => '',
        'custom_filter_toggle' => 'btn-sm btn-outline-secondary',
        'custom_filter_menu' => '',
        'custom_filter_item' => 'btn-sm',
        'custom_filter_item_active' => '',

        // edição em massa
        'mass_container_class' => 'input-group',
        'mass_select' => 'form-control',
        'mass_button' => 'btn btn-primary',
        'mass_form_container' => 'd-flex justify-content-start',
        'mass_form_field_group' => 'my-3 me-1',
        'mass_form_label' => '',
        'mass_form_field' => '',
        'mass_checkbox_header_container' => '',
        'mass_checkbox_header' => '',
        'mass_checkbox_container' => '',
        'mass_checkbox' => '',

        // totalizadores
        'totalizer_container' => 'row no-space width-250 totalizers hidden-xs',
        'totalizer_icon_default' => 'fas fa-filter',
        'totalizer_icon_container' => 'visual cyan',
        'totalizer_icon_all_container' => 'visual blue',
        'totalizer_icon_all' => 'fas fa-list-alt',
    ];
}
