<?php

class MapperHelper{
	public static function focusToString($id){
		$string = "";
		switch ($id) {
			case 1:
				$string = "Arme";
				break;
			case 2:
				$string = "Ben";
				break;
			case 3:
				$string = "Mave";
				break;
			case 4:
				$string = "Ryg";
				break;
			case 5:
				$string = "Helkrop";
				break;
		}
		return $string; 
	}
	public static function diffToString($id){
		$string = "";
		switch ($id) {
			case 1:
				$string = "(5/1)";
				break;
			case 2:
				$string = "(4/2)";
				break;
			case 3:
				$string = "(3/3)";
				break;
			case 4:
				$string = "(2/4)";
				break;
			case 5:
				$string = "(1/5)";
				break;
		}
		return $string; 
	}
	public static function legacyDiffToString($id){
		$string = "";
		switch ($id) {
			case 1:
				$string = "Cardio";
				break;
			case 2:
				$string = "Let-Cardio";
				break;
			case 3:
				$string = "Cardio-Styrke";
				break;
			case 4:
				$string = "Let-Styrke";
				break;
			case 5:
				$string = "Styrke";
				break;
		}
		return $string; 
	}
}
?>