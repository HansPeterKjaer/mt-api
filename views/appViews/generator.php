<?php
	$wo = $viewModel->workout;
	$formData = isset($viewModel->formData);
?>
<div class="row swiper-container swiper-container-h">
	<div class="swiper-wrapper top-margin no-gutters">
		
		<div class="col-xs-12 col-sm-6 col-lg-4 swiper-slide" >
			<div class="mt-panel clearfix mt-panel--light -mt-panel--flex generator-panel">
				<?php ViewHelper::renderPartial("appViews/_generatorForm", $viewModel); ?>
			</div>
		</div>


		<div class="col-xs-12 col-sm-6 col-lg-4 swiper-slide <?php if ($formData == null){ echo 'hidden'; } ?> " >
			<div class="swiper-container swiper-container-v2">
				<div class="swiper-wrapper">
					<div class="swiper-slide mt-panel clearfix mt-panel--medium workout-panel">
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
				<div class="swiper-scrollbar swiper-scrollbar-v2"></div>
			</div>
		</div>	
		

		<div class="col-xs-12 col-sm-6 col-lg-4 swiper-slide hidden" >
			<div class="swiper-container swiper-container-v3">
				<div class="swiper-wrapper">
					<div class="swiper-slide mt-panel clearfix mt-panel--dark exercise-panel">	
						<div class="exercise"></div>
					</div>		
				</div>
				<div class="swiper-scrollbar swiper-scrollbar-v3"></div>
			</div>
		</div>
	
	</div>
	<div class="swiper-scrollbar swiper-scrollbar-h"></div>
</div>
