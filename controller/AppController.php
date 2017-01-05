<?php 
class generatorController extends BaseController {
	public function GetWorkoutAction($wo_id){
		$viewModel = null;

		if ($wo_id !== null){
			$mapper = $this->modelFactory->buildMapper('WorkoutModelMapper');
			$viewModel = $this->modelFactory->buildObject('WorkoutViewModel');
			//$workoutModel = $this->modelFactory->buildObject('WorkoutModel');
			$status = $mapper->fetchById($viewModel->workout, $wo_id);
		}

		$this->view->output('/appViews/generator', $viewModel, false);
	}

	public function workoutAction($wid = null, $diff = null, $focus = null, $time = null, $c_wid = null){
		$workoutListModel = $this->modelFactory->buildObject('WorkoutListModel');
		$viewModel = $this->modelFactory->buildObject('WorkoutViewModel');
		$workoutModelMapper = $this->modelFactory->buildMapper('WorkoutModelMapper');

		if($wid != null){
			$workoutModelMapper->fetchById($viewModel->workout, $wid);
		}
    	else if($diff != null){
    		$diff = round($diff);
    		$workoutModelMapper->search($workoutListModel, 1, 1, null, null, 'rand', $diff, $focus, $c_wid);
    		$viewModel->workout = (count($workoutListModel->workouts) > 0) ? $workoutListModel->workouts[0] : null;
			$viewModel->formData = ['diff'=>$diff, 'focus'=>$focus, 'time'=>$time];    // maybe not formdata?		
    	}
    	else{
    		$viewModel->formData = null;
    	}
		$this->view->output('/appViews/generator', $viewModel);
	}

	/*public function getExerciseByIdAction($wo_id){
		$mapper = $this->modelFactory->buildMapper('ExerciseModelMapper');
		$model = $this->modelFactory->buildObject('ExerciseModel');
		
		if($mapper->fetchById($model, $wo_id)){
			$this->view->outputJSON(get_object_vars($model));
		}
		else{ 
			// something meaningfull		
		}
	}
	public function searchExerciseAction($term, $diff, $focus){
		$mapper = $this->modelFactory->buildMapper('ExerciseModelMapper');
		$models = []; // b$this->modelFactory->buildObject('ExerciseModel');

		$result = $mapper->fetchBySearchData($models, $term, $diff, $focus);

		if($result['success'] == true){
			$this->view->outputJSON(['status'=>'success', 'data' => $models, 'msg'=> null]);
		}
		else{ 
			$this->view->outputJSON(['status'=>'error', 'data' => null, 'msg'=> $result['msg']]);
		}
	}
	public function searchWorkoutAction($term, $diff, $focus){
		$mapper = $this->modelFactory->buildMapper('WorkoutModelMapper');
		$models = []; // b$this->modelFactory->buildObject('ExerciseModel');

		$result = $mapper->fetchBySearchData($models, $term, $diff, $focus);

		if($result['success'] == true){
			$this->view->outputJSON(['status'=>'success', 'data' => $models, 'msg'=> null]);
		}
		else{ 
			$this->view->outputJSON(['status'=>'error', 'data' => null, 'msg'=> $result['msg']]);
		}
	}*/
}
?>