<?php
class ModelFactory{
	protected $dbhandle = null;
	//protected $auth;

	public function __construct($dbhandle){
		$this->dbhandle = $dbhandle;
		//$this->auth = new Auth($this->dbhandle);
	}
	public function buildObject($name){
		$instance = new $name();
		return $instance;
	}
	public function buildMapper($name){
		$instance = new $name($this->dbhandle);
		return $instance;
	}
}
?>