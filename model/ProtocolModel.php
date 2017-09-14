<?php

class ProtocolModel implements JsonSerializable{
	public $id;
	public $name;
	public $descr;

	public function jsonSerialize() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'descr' => htmlspecialchars_decode($this->descr)
        ];
    }
}

?>