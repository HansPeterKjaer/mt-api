<?php

class MediaListViewModel extends BaseViewModel{
	public $MediaItems;

	public function __construct() {
		$this->MediaItems = new MediaListModel(); 
	}
}

?>