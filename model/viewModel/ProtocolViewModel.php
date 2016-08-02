<?php

class ProtocolViewModel extends BaseViewModel{
	public $protocol;

	function __construct()
	{
		$this->protocol = new ProtocolModel();
	}	
}

?>