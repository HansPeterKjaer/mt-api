<?php

class URLHelper{
	public static function renderURL($url = ""){
		echo @constant('APP_BASE_PATH') . '/' . ltrim($url, '/');
	}
	public static function getURL($url){
		return @constant('APP_BASE_PATH') . '/' . ltrim($url, '/');
	}
	public static function getBasePath(){
		return @constant('APP_BASE_PATH');
	}
}
?>