<?php

abstract class BaseModelMapper {
	protected $dbhandle;

	public function __construct($dbhandle) {
		$this->dbhandle = $dbhandle;
	}
}

?>