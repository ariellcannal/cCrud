<?php
/**
 * Container principal das views do cCrud.
 *
 * @var object $cCrud   Objeto de controle principal
 * @var string $content Conteúdo interno da view
 */
?>
<div class="cCrud container-fluid">
    <?php echo $cCrud->renderTableName(false, 'div', true) ?>
    <div class="cCrud-container position-relative">
        <div class="cCrud-ajax">
            <?php echo $content ?>
        </div>
        <div class="cCrud-overlay"></div>
    </div>
</div>
