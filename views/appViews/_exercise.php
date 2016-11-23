<?php 
	$ex = $viewModel;
?>
<div class="exercise exercise--thumbnail" data-id="<?php echo $ex->id; ?>">
	<div class="exercise__imageviewer imageviewer">
		<?php 
			$current = true;
			foreach($ex->images->items as $item){ 
		?>
			<img class="imageviewer__image <?php if ($current == true){ echo 'current'; } ?>" src="<?php URLHelper::renderURL("mtassets/uploads/$item->imageName") ?>" />
		<?php 
			$current = false;
		}?>
	</div>
	<div class="exercise__controls">
		<button class="exercise__btn btn-play btn btn-xs btn-primary"><i class="_glyphicon _glyphicon-arrow-left"></i>></button>
		<button class="exercise__btn btn-next btn btn-xs btn-primary"><i class="_glyphicon _glyphicon-arrow-left"></i>-></button>
		<button class="exercise__btn btn-prev btn btn-xs btn-primary"><i class="_glyphicon _glyphicon-arrow-left"></i><-</button>  
	</div>
	<div class="exercise__textwrapper">
		<h1 class="margin-sm"><?php echo $ex->name; ?></h1>
		<p><?php echo MapperHelper::focusToString($ex->focus); ?> <?php echo MapperHelper::diffToString($ex->diff); ?></p>
		<div class="exercise__description"><?php echo htmlspecialchars_decode($ex->descr, ENT_HTML5); ?></div>
	</div >
</div>

