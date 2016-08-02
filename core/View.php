<?php

class View{

	public function __construct(){

	}
	public function output($view, $viewModel, $template = 'shared/maintemplate'){
		$viewFile = "views/{$view}.php";

		if(!file_exists($viewFile)){ 
			throw new Exception("Error: Bad viewFile $viewFile", 1);
		}

		if($template){
			$templateFile = "views/{$template}.php";
			if(file_exists($templateFile)) {
                require($templateFile);
            } else {
            	throw new Exception("Error: Bad templatefile $templateFile", 1);
            }
		}
		else{
			require($viewFile);
		}
	}
	public function outputJSON($model){
		header('Content-Type: application/json');
		echo json_encode($model);
	}
	public function outputJSONString($json){
		json_decode($json);
 		
 		if (json_last_error() != JSON_ERROR_NONE){
 			//$json = '{"status": false, "msg": "JSON DATA ERROR"}';
 			throw new Exception("Error: Invalid JSON Format", 1);
 		}

		header('Content-Type: application/json');
		echo $json;	
	}
	public function outputFile($content, $filename = 'download.txt'){
		header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
        //header("Cache-Control: public"); // needed for i.e.
        header("Content-Type: text/x-sql");
        header("Content-Transfer-Encoding: Binary");
        header("Content-Length:".strlen(utf8_decode($content)));
        header("Content-Disposition: attachment; filename=$filename");
        
        header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' ); 
		header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' ); 
		header( 'Cache-Control: no-store, no-cache, must-revalidate' ); 
		header( 'Cache-Control: post-check=0, pre-check=0', false ); 
		header( 'Pragma: no-cache' ); 
        
        echo $content;	
	}
}

?>