<?php
	$formData = $viewModel->formData;
	$focus = ($formData) ? $formData['focus'] : 1;
	$diff = ($formData) ? intval($formData['diff']) : 3;
	$wId = ($viewModel->workout) ? $viewModel->workout->id : null;
?>

<h1 class="mt-panel__heading">Generer workout</h1>
<form class="form-horizontal">
	<div class="form-group">
		<label class="col-xs-4">
			Cardio/Styrke
		</label>
		<div class="col-xs-4">
			<input class="diff-range swiper-no-swiping" type="range" name="diff" min="1" max="5" step="0.001" value="<?php echo $diff; ?>" />
		</div>
		<div class="col-xs-4">
			<?php ViewHelper::renderPartial('appViews/_diff-icon', ['size' =>'sm', 'diff' => $diff ] ); ?>
			<span id="diff-value" class="diff hidden"><?php echo "(" . (6 - $diff) . "/". $diff ." )"; ?></span>
		</div>
	</div>
	<div class="form-group">
		<label class="col-xs-4" for="input-focus">Fokus</label>
		<div class="col-sm-4">
			<select class="form-control swiper-no-swiping" id="input-focus" name="focus" >
			  <option value="1" <?php if($focus==1){echo "selected ";} ?>><?php echo MapperHelper::focusToString(1); ?></option>
			  <option value="2" <?php if($focus==2){echo "selected ";} ?>><?php echo MapperHelper::focusToString(2); ?></option>
			  <option value="3" <?php if($focus==3){echo "selected ";} ?>><?php echo MapperHelper::focusToString(3); ?></option>
			  <option value="4" <?php if($focus==4){echo "selected ";} ?>><?php echo MapperHelper::focusToString(4); ?></option>
			  <option value="5" <?php if($focus==5){echo "selected ";} ?>><?php echo MapperHelper::focusToString(5); ?></option>
			</select>
		</div>
	</div>
	<div class="form-group">					
		<label class="col-xs-4" for="time">Tid</label>
		<div class="col-sm-4">
			<select class="form-control swiper-no-swiping" name="time">
				<option value="all" <?php if($formData && $formData['time']=='all'){echo "selected ";} ?> >Alle</option>
				<option value="short" <?php if($formData && $formData['time']=='short'){echo "selected ";} ?> >Under 15 min</option>
				<option value=">long" <?php if($formData && $formData['time']=='>long'){echo "selected ";} ?>>Over 15 min</option>
			</select>
		</div>
	</div>
	<?php if($wId) echo "<input name='c_wid' type='hidden' value='$wId' />"?>
	<button class="btn btn-lg btn-success pull-right">Generer Workout</button>
</form>