<?php

class URLHelper{
	public static function renderURL($url = ""){
		if (substr($url, 0, 1) === '/') {
			echo @constant('SITEROOT') . $url;
		}
		else {
			echo @constant('APP_BASE_PATH') . '/' . $url;
		}
	}
	public static function getURL($url){
		return @constant('APP_BASE_PATH') . '/' . ltrim($url, '/');
	}
	public static function getBasePath(){
		return @constant('APP_BASE_PATH');
	}
}
?>