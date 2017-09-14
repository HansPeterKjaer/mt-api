<?php 
abstract class BaseController{
	protected $modelFactory;
	protected $view;

	public function __construct(ModelFactory $modelFactory){
		$this->modelFactory = $modelFactory;
	}
	public function action($actionName, $parms){
		$this->view = new View();
		return call_user_func_array(array($this, $actionName), $parms);			
	}
}
?>