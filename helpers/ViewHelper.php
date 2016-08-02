<?php

class ViewHelper{
	public static function renderPartial($view, $viewModel){
		$model = $viewModel;
		$viewFile = "views/{$view}.php";
		
		if(!file_exists($viewFile)){ 
			throw new Exception("Error: Bad viewFile $viewFile", 1);
		}
		
		require($viewFile);
	}
}
?>