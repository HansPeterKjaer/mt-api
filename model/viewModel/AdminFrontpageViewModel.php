<?php

class AdminFrontpageViewModel extends BaseViewModel{
	public $exercises;
	public $workouts;
	public $protocols;

	public function __construct() {
		$this->workouts = new WorkoutListModel();
		$this->protocols = new ProtocolListModel();
		$this->exercises = new ExerciseListModel(); 
	}
}

?>