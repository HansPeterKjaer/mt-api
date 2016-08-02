<?php

class ExerciseModel implements JsonSerializable{

	public $id;
	public $name;
	public $diff;
	public $focus;
	public $descr;
	public $images;

	public function __construct() {
		$this->images = new MediaListModel(); 
	}
	public function jsonSerialize() {
        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }
}
?>