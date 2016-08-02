<?php

class Logger {
	private $dev = true;
	private static $_instance;

 	private function __construct($dev){
 		if ($dev == false) error_reporting(0);
 		$this->dev = $dev; 
 	}
 	public static function initLogger($dev){
		
 		if(!isset(self::$_instance)) {
            self::$_instance = new Logger($dev);
        }
        return self::$_instance;
 	}
 	public static function log($output){
 		if(self::$_instance->dev){
 			if(is_array($output)){
 				var_dump($output);
 			}
 			else{
 				echo($output);
 			}
 		}
 	}
}

?>