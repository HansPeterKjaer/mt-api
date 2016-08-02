<?php 
class AppController extends BaseController {
	public function GetWorkoutAction($wo_id){
		$viewModel = null;

		if ($wo_id !== null){
			$mapper = $this->modelFactory->buildMapper('WorkoutModelMapper');
			$viewModel = $this->modelFactory->buildObject('WorkoutViewModel');
			//$workoutModel = $this->modelFactory->buildObject('WorkoutModel');
			$status = $mapper->fetchById($viewModel->workout, $wo_id);
		}

		$this->view->output('/appViews/demo', $viewModel, false);
	}

	public function WorkoutGeneratorAction($diff = null, $focus = null, $time = null){
		$workoutListModel = $this->modelFactory->buildObject('WorkoutListModel');
		$viewModel = $this->modelFactory->buildObject('WorkoutViewModel');

    	if($diff != null){
    		$workoutModelMapper = $this->modelFactory->buildMapper('WorkoutModelMapper');
    		$workoutModelMapper->search($workoutListModel, 1, 1, null, null, 'rand', $diff, $focus);
			$viewModel->formData = ['diff'=>$diff, 'focus'=>$focus, 'time'=>$time];    		
    	}
    	else{
    		$viewModel->formData = null;
    	}

    	$viewModel->workout = (count($workoutListModel->workouts) > 0) ? $workoutListModel->workouts[0] : null;

		$this->view->output('/appViews/demo', $viewModel);
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