<?php 
	$wo = $viewModel;
?>
<div class="row">
	<div class="col-xs-12" >
		<h1 class="margin-sm pull-left">Din Workout <span class="hidden"><?php echo $wo->name; ?></span></h1>
		<button class="btn workoutbtn btn-xxl btn-success pull-right">Start Workout</button>
		<div class="pull-left clear-left">
			<p>Fokus: <?php echo MapperHelper::focusToString($wo->focus); ?></p>
			<p>Cardio/Styrke: <?php echo MapperHelper::diffToString($wo->diff); ?></p>
			<p class="hidden">Beskrivelse: <?php echo $wo->descr; ?></p>
		</div>
		
	</div>

	<div class="col-xs-12" >
		<div class="mt-panel">
			<h2 class="margin-sm">Program: <?php echo $wo->protocol->name; ?></h2>
			<div><?php echo htmlspecialchars_decode($wo->protocol->descr, ENT_HTML5); ?></div>		
		</div>
	</div>

	<div class="col-xs-12" >
		<div class="workout-exercises mt-panel">
			<h2 class="margin-sm">Øvelser</h2>
			
			<div class="row small-gutters">

<?php 
	foreach ($wo->exercises as $ex ) {
?>
	<div class="col-sm-2">
		<div class='exercise thumbnail' data-id='<?php echo $ex->id; ?>'>
			<div class="imageViewer">
				<?php 
					$current = true;
					foreach($ex->images->items as $item){ 
				?>
					<img class="<?php if ($current == true){ echo 'current'; } ?>" src="<?php URLHelper::renderURL("assets/uploads/$item->imageName") ?>" />
				<?php 
					$current = false;
				}?>
			</div>
			<button class="btn-play btn btn-xs btn-primary"><i class="_glyphicon _glyphicon-arrow-left"></i>></button>
			<button class="btn-next btn btn-xs btn-primary"><i class="_glyphicon _glyphicon-arrow-left"></i>-></button>
			<button class="btn-prev btn btn-xs btn-primary"><i class="_glyphicon _glyphicon-arrow-left"></i><-</button>  
			<div>
				<h1 class="margin-sm"><?php echo $ex->name; ?></h1>
				<p><?php echo MapperHelper::focusToString($ex->focus); ?></p>
				<p><?php echo MapperHelper::diffToString($ex->diff); ?></p>
				<div class="description"><?php echo htmlspecialchars_decode($ex->descr, ENT_HTML5); ?></div>
			</div >
		</div>
	</div>
<?php 
	}
?>
			</div>
		</div>        
	</div>
</div>