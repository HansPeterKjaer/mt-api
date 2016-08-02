<?php

class WorkoutModel{
	public $id;
	public $name;
	public $descr;
	public $diff;
	public $focus;
	public $protocol;
	public $exercises = [];	

	public function __construct() {
		$this->protocol = new ProtocolModel(); 
	}
}

?>