<?php

class BaseViewModelMapper extends BaseModelMapper{

	public function init(BaseViewModel &$model){
		$this->fetch($model);		
	}	
	public function fetch(BaseViewModel &$model){
	}
}

?>