<?php
	$wo = $viewModel->workout;
	$formData = $viewModel->formData;
?>

<div class="row top-margin no-gutters">
	<div class="col-xs-12 col-sm-6 extendable" >
		<div class="mt-panel mt-panel--light generator clearfix">
			<h1 class="margin-sm">Generer workout</h1>
			<form class="form-horizontal">
				<div class="form-group">
					<label class="col-xs-4">Cardio/Styrke</label>
					<div class="col-xs-4">
						<input class="diff-range" type="range" name="diff" min="1" max="5" step="1" <?php if ($formData) { echo "value='{$formData['diff']}'"; }; ?> />
					</div>
					<div class="col-xs-4">
						<span id="diff-value" class="diff"><?php if ($formData) { echo "(" . (6 - intval($formData['diff'])) . "/{$formData['diff']})"; }; ?></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-4" for="input-focus">Fokus</label>
					<div class="col-sm-4">
						<select class="form-control " id="input-focus" name="focus" >
						  <option value="1" <?php if($formData && $formData['focus']==1){echo "selected ";} ?>><?php echo MapperHelper::focusToString(1); ?></option>
						  <option value="2" <?php if($formData && $formData['focus']==2){echo "selected ";} ?>><?php echo MapperHelper::focusToString(2); ?></option>
						  <option value="3" <?php if($formData && $formData['focus']==3){echo "selected ";} ?>><?php echo MapperHelper::focusToString(3); ?></option>
						  <option value="4" <?php if($formData && $formData['focus']==4){echo "selected ";} ?>><?php echo MapperHelper::focusToString(4); ?></option>
						  <option value="5" <?php if($formData && $formData['focus']==5){echo "selected ";} ?>><?php echo MapperHelper::focusToString(5); ?></option>
						</select>
					</div>
				</div>
				<div class="form-group">					
					<label class="col-xs-4" for="time">Tid</label>
					<div class="col-sm-4">
						<select class="form-control" name="time">
							<option value="all" <?php if($formData && $formData['time']=='all'){echo "selected ";} ?> >Alle</option>
							<option value="short" <?php if($formData && $formData['time']=='short'){echo "selected ";} ?> >Under 15 min</option>
							<option value=">long" <?php if($formData && $formData['time']=='>long'){echo "selected ";} ?>>Over 15 min</option>
						</select>
					</div>
				</div>
				<button class="btn btn-lg btn-success pull-right">Generer Workout</button>
			</form>
		</div>
	</div>
	<div class="col-xs-12 col-sm-6 extendable" >
		<div class="mt-panel mt-panel--medium workout">
<?php 
	if($wo != null){
		ViewHelper::renderPartial("appViews/_workout", $wo);
	}
	else if ($formData != null){
		ViewHelper::renderPartial("appViews/_noWorkoutFound", null);
	}
	else{
		ViewHelper::renderPartial("appViews/_dummyWorkout", null);
	}
?>
		</div>
	</div>	
	<div class="col-xs-12 col-sm-6 extendable" >
		<div class="mt-panel mt-panel--dark exercise-panel">
			<div class="row">
				<div class="col-xs-12" >
					<h1 class="margin-sm">Øvelse</h1>
					<p class="placeholder">Generer en workout ud fra dine parametre og klik start for at komme igang!</p>
				</div>
			</div>
		</div>		
	</div>
</div>

