<?php if ($cCrud->get_var('custom_head') != false)
        require APPPATH . 'Views/' . ltrim($cCrud->get_var('custom_head'), '/'); ?>
<?php if($cCrud->is_inner)
        include 'nested/cCrud_list_view.php';
else{
?>
<div class="d-flex justify-content-between align-items-center mb-3">
        <?php echo $cCrud->renderTableName('list',[ 'tag'=>'h1','class'=>'page-title mb-0']); ?>
        <div>
                        <?php echo $cCrud->render_totalizers(false)?>
                        <?php echo $cCrud->render_custom_filter('direita')?>
        </div>
</div>
                <div class="container-fluid">
                        <div class="card">
                                <div class="card-header">
                                        <?php if ($cCrud->is_create or $cCrud->is_csv or $cCrud->is_search or $cCrud->is_print){
?>
                                        <div class="cCrud-top-actions">
                                                        <div class="float-start">
                                                                <?php echo $cCrud->add_button();?>
                                                                <?php echo $cCrud->render_mass_actions();?>
                                                </div>
                                                        <div class="float-end">
                                                        <?php echo $cCrud->render_search(); ?>
                                                    <div class="btn-group">
                                                        <?php
                                                                        echo $cCrud->print_button();
                                                                        echo $cCrud->csv_button();
                                                                        echo $cCrud->refresh_button();
                                                                        ?>
                                                    </div>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                </div>
                                        <?php } ?>
                                </div>
                                <div class="float-start col-md-10">
                                        <?php echo $cCrud->render_mass_edit_form();?>
                                        <?php echo $cCrud->render_alphabetical_filter();?>
                                </div>
                                <div class="float-end col-md-2">
                                        <?php echo $cCrud->renderColumnsSelect();?>
                                </div>
                                <div class="cCrud-list-container table-responsive">
                                <table class="cCrud-list table table-striped table-hover table-sm" data-selectable="selectable" data-row-selectable="true">
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
                                <div class="card-footer">
                                <div class="float-end ms-2"><?php echo $cCrud->render_pagination(7,1); ?></div>
                                        <div class="float-end"><?php echo $cCrud->render_limitlist(); ?></div>
                                        <div class="clearfix"></div>
                                </div>
                        </div>
                </div>
<?php
}?>
