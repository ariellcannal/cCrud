<?php
/**
 * Container principal das views do cCrud.
 *
 * @var object $cCrud   Objeto de controle principal
 * @var string $content Conteúdo interno da view
 */
?>
<div class="cCrud container-fluid">
	<div class="cCrud-ajax">
    	<?=$content ?>
    </div>
	<div class="cCrud-overlay">
		<div class="spinner-border" style="width: 3rem; height: 3rem;" role="status">
			<span class="visually-hidden">Loading...</span>
		</div>
	</div>
</div>
