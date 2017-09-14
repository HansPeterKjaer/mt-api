<?php 
class ApiController extends BaseController {

	// /mt/api/workout?id=4
	/*public function workoutAction($id){

		$mapper = $this->modelFactory->buildMapper('WorkoutModelMapper');
		$model = $this->modelFactory->buildObject('WorkoutModel');

		$status = $mapper->fetchById($model, $id);

		
		if($status){
			$this->view->outputJSON(get_object_vars($model));
		}
		else{
			// something meaningful
		}
	}
*/
	// /mt/api/exercise?id=4
	public function exerciseAction($id){

		$mapper = $this->modelFactory->buildMapper('ExerciseModelMapper');
		$model = $this->modelFactory->buildObject('ExerciseModel');
		
		if($mapper->fetchById($model, $id)){
			$this->view->outputJSON(['success' => true, 'message' => '', 'data' => get_object_vars($model)]);
		}
		else{ 
			$this->view->outputJSON(['success' => false, 'message' => 'No workout found']);		
		}
	}

	public function workoutAction($id){

		$mapper = $this->modelFactory->buildMapper('WorkoutModelMapper');
		$model = $this->modelFactory->buildObject('WorkoutModel');

		$status = $mapper->fetchById($model, $id);

		
		if($status){
			$this->view->outputJSON(['success' => true, 'message' => '', 'data' => get_object_vars($model)]);
		}
		else{
			$this->view->outputJSON(['success' => false, 'message' => 'No workout found']);
			// something meaningful
		}
	}
	public function searchWorkoutAction($diff = null, $focus = null, $time = null, $c_wid = null){
		$workoutListModel = $this->modelFactory->buildObject('WorkoutListModel');
		$workoutModelMapper = $this->modelFactory->buildMapper('WorkoutModelMapper');
		$workout;

    	if($diff != null){
    		$diff = round($diff);
    		$workoutModelMapper->search($workoutListModel, 1, 1, null, null, 'rand', $diff, $focus, $c_wid);
    		$workout =  (count($workoutListModel->workouts) > 0) ? $workoutListModel->workouts[0] : null;
    	}
    	if($workout){
			$this->view->outputJSON(['success' => true, 'message' => '', 'data' => get_object_vars($workout)]);
    	}
    	else{
    		$this->view->outputJSON(['success' => false, 'message' => '']);
    	}
	}
}
?>