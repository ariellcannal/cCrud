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

<div class="container-fluid notion-table">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <?php echo $cCrud->renderTableName('list', ['tag' => 'h2', 'class' => 'fs-4 mb-0 fw-semibold']); ?>
        <div class="d-flex align-items-center gap-2">
            <?php echo $cCrud->add_button(); ?>
            <?php echo $cCrud->render_mass_actions(); ?>
            <?php echo $cCrud->render_search(); ?>
            <div class="btn-group" role="group">
                <?php
                    echo $cCrud->render_custom_buttons('list');
                    echo $cCrud->print_button();
                    echo $cCrud->csv_button();
                    echo $cCrud->refresh_button();
                ?>
            </div>
        </div>
    </div>
    <div class="card cCrud-table-card shadow-none">
        <div class="table-responsive">
            <table class="cCrud-list table table-sm table-hover align-middle mb-0" data-selectable="selectable" data-row-selectable="true">
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
    <div class="d-flex justify-content-end mt-3 gap-2">
        <div><?php echo $cCrud->render_limitlist(); ?></div>
        <div><?php echo $cCrud->render_pagination(7,1); ?></div>
    </div>
</div>
<?php }
?>
