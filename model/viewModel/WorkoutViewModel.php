<?php

class WorkoutViewModel extends BaseViewModel{
	public $workout;
	public $protocols;

	function __construct()
	{
		$this->workout = new WorkoutModel();
		$this->protocols = new ProtocolListModel();
	}	
}

?>