<?php

class XssSecureHelper{
	public function xssSecure($array){
		foreach ($array as $k => $v) {
			if (is_array($v)){
				$array[$k] = $this->xssSecure($v);	
			}
			else{
				$array[$k] = htmlspecialchars($v, ENT_QUOTES);
			}	
		}
		return $array;
	}
}

?>