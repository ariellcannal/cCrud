<?php
/**
 * View de listagem com layout inspirado no Notion.
 *
 * @var object $cCrud Objeto principal de controle
 */
if ($cCrud->get_var('custom_head') != false) {
    require APPPATH . 'Views/' . ltrim($cCrud->get_var('custom_head'), '/');
}

if ($cCrud->is_inner) {
    include 'nested/cCrud_list_view.php';
} else {
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <?php echo $cCrud->renderTableName('list', ['tag' => 'h1', 'class' => 'h4 mb-0']); ?>
    <div>
        <?php echo $cCrud->render_totalizers(false) ?>
        <?php echo $cCrud->render_custom_filter('direita') ?>
    </div>
</div>
<div class="card">
    <?php if ($cCrud->is_create or $cCrud->is_csv or $cCrud->is_search or $cCrud->is_print) { ?>
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="d-flex">
            <?php echo $cCrud->add_button(); ?>
            <?php echo $cCrud->render_mass_actions(); ?>
        </div>
        <div class="d-flex align-items-center">
            <?php echo $cCrud->render_search(); ?>
            <div class="btn-group ms-2">
                <?php
                    echo $cCrud->print_button();
                    echo $cCrud->csv_button();
                    echo $cCrud->refresh_button();
                ?>
            </div>
        </div>
    </div>
    <?php } ?>
    <div class="card-body">
        <div class="row">
            <div class="col-md-10">
                <?php echo $cCrud->render_mass_edit_form(); ?>
                <?php echo $cCrud->render_alphabetical_filter(); ?>
            </div>
            <div class="col-md-2 text-md-end">
                <?php echo $cCrud->renderColumnsSelect(); ?>
            </div>
        </div>
        <div class="cCrud-list-container table-responsive mt-3">
            <table class="cCrud-list table table-borderless table-hover align-middle" data-selectable="selectable" data-row-selectable="true">
                <thead>
                    <?php echo $cCrud->render_grid_head(); ?>
                </thead>
                <tbody>
                    <?php echo $cCrud->render_grid_body(); ?>
                </tbody>
                <tfoot>
                    <?php echo $cCrud->render_grid_footer(); ?>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-end">
        <div class="me-2"><?php echo $cCrud->render_limitlist(); ?></div>
        <div><?php echo $cCrud->render_pagination(7,1); ?></div>
    </div>
</div>
<?php }
?>
