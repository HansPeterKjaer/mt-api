<?php 
	$wo = $viewModel;
?>
<div class="row">
	<div class="col-xs-12" >
		<h1 class="margin-sm mt-panel__heading">Din Workout <span class="hidden"><?php echo $wo->name; ?></span></h1>
		<!--button class="btn workoutbtn btn-xxl btn-success pull-right">Start Workout</button>
		<div class="pull-left clear-left">
			<p>Fokus: <?php echo MapperHelper::focusToString($wo->focus); ?></p>
			<p>Cardio/Styrke: <?php echo MapperHelper::diffToString($wo->diff); ?></p>
			<p class="hidden">Beskrivelse: <?php echo $wo->descr; ?></p>
		</div-->
		
	</div>

	<div class="col-xs-12" >
			<h2 class="margin-sm">Forløb: <?php echo $wo->protocol->name; ?></h2>
			<div><?php echo htmlspecialchars_decode($wo->protocol->descr, ENT_HTML5); ?></div>		
	</div>

	<div class="col-xs-12" >
		
		<h2 class="margin-sm">Øvelser:</h2>
		
		<div class="workout__exercises">

			<div class="row small-gutters">

<?php 
	foreach ($wo->exercises as $key=>$ex ) {
		$ex->index = $key;
?>
	<div class="col-sm-3">
		<?php ViewHelper::renderPartial("appViews/_exercise", $ex); ?>
	</div>
<?php 
	}
?>
			</div>
		</div>        
	</div>
</div>