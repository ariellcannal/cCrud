<div class="cCrud<?php echo $this->is_rtl ? ' cCrud_rtl' : ''?>">
    <?php echo $this->renderTableName(false, 'div', true)?>
    <div class="cCrud-container"<?php echo ($this->start_minimized) ? ' style="display:none;"' : '' ?>>
        <div class="cCrud-ajax">
            <?php echo $this->render_view() ?>
        </div>
        <div class="cCrud-overlay"></div>
    </div>
</div>