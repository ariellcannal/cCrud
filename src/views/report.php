<?php
/**
 * View de relatório com layout inspirado no Notion.
 *
 * @var object $cCrud Objeto principal de controle
 * @var string $mode  Modo da view
 * @var string $title Título exibido
 */
if ($cCrud->get_var('custom_head') != false) {
    require APPPATH . 'Views/' . ltrim($cCrud->get_var('custom_head'), '/');
}
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <?php echo $cCrud->renderTableName($mode, ['tag'=>'h1','class'=>'h4 mb-0'], false, $title); ?>
        <div class="btn-group">
            <?php echo $cCrud->render_button('make_report','make_report','','btn btn-primary',''); ?>
        </div>
    </div>
    <p class="mb-3">Preencha os parâmetros abaixo:</p>
    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">
            <div class="cCrud-view">
                <?php
                /* DEFAULTS */
                $container = 'div';
                $row = 'div';
                $label = ['tag'=>'label','class'=>'col-md-4 col-form-label'];
                $field = ['tag'=>'div','class'=>'col-md-8'];
                $tabs_block = 'div';
                $tabs_head = 'ul';
                $tabs_row = 'li';
                $tabs_link = 'a';
                $tabs_content = 'div';
                $tabs_pane = 'div';
                if ($mode == 'view') {
                    $container = ['tag'=>'table','class'=>'table table-borderless align-middle'];
                    $row = 'tr';
                    $label = 'th';
                    $field = 'td';
                }
                $tabs_head = ['tag'=>'ul','data-plugin'=>'nav-tabs','role'=>'tablist'];
                $tabs_row = ['tag'=>'li','role'=>'presentation'];
                $tabs_link = ['tag'=>'a','data-toggle'=>'tab','role'=>'tab'];
                echo $cCrud->render_fields_list($mode,$container,$row,$label,$field,$tabs_block,$tabs_head,$tabs_row,$tabs_link,$tabs_content,$tabs_pane);
                ?>
            </div>
            <div class="cCrud-nav mt-3">
                <?php echo $cCrud->render_benchmark(); ?>
            </div>
        </div>
    </div>
</div>
