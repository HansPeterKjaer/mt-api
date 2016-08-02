<?php

class MediaViewModel extends BaseViewModel{
	public $MediaItem;

	public function __construct() {
		$this->MediaItem = new MediaModel(); 
	}
}

?>