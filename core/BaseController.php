<?php 
abstract class BaseController{
	protected $modelFactory;
	protected $auth;
	protected $view;

	public function __construct(ModelFactory $modelFactory, $auth){
		$this->modelFactory = $modelFactory;
		$this->auth = $auth;
	}
	public function action($actionName, $parms){
		$this->view = new View();
		return call_user_func_array(array($this, $actionName), $parms);			
	}
}
?>