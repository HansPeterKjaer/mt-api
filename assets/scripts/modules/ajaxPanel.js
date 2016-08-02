'use strict'

var doc = document;
var win = window;

var ajax = require('./ajax.js');

var filterElms, searchInput, searchBtn, loadMoreBtn, panel;
var searchTerm = '', filterValue='', sortValue = '', page = 1, baseUrl = MT.BasePath + '/Media/searchImages';

var currentRequest = null;

module.exports = function (elm){

	if (typeof elm === 'undefined') return;
	panel = elm; 

	baseUrl = panel.getAttribute('data-url');

	filterElms = doc.getElementsByClassName(panel.getAttribute('data-filter-class'));
	var sortElms = doc.getElementsByClassName(panel.getAttribute('data-sort-class'));
	searchInput = doc.getElementById(panel.getAttribute('data-search-id'));
	searchBtn = doc.getElementById(panel.getAttribute('data-search-btn-id'));
	loadMoreBtn = doc.getElementById(panel.getAttribute('data-load-more-btn-id'));

	//win.console && console.log(panel);
	//win.console && console.log(filterElms);
	//win.console && console.log(searchInput);
	//win.console && console.log(searchBtn);

	searchBtn && searchInput && searchBtn.addEventListener('click', search, false);
	loadMoreBtn && loadMoreBtn.addEventListener('click', loadMore, false);
	
	for (var i = 0; i < filterElms.length; i++){
		filterElms[i].addEventListener('click', filter, false);
	}
	for (var i = 0; i < sortElms.length; i++){
		if(sortElms[i].tagName == 'SELECT'){
			sortElms[i].addEventListener('change', function(evt){ 
				this.setAttribute('data-sort-val', this.value);
				var boundSetSort = setSort.bind(this);
				boundSetSort(evt);
			}, false);	
		}
		else{
			sortElms[i].addEventListener('click', setSort, false);
		}
	}
}

function search(evt){
	evt.preventDefault();
	searchTerm = searchInput.value;
	resetContainer();
	fetchContent();
}

function filter(evt){
	evt.preventDefault();
	var newFilterValue = this.getAttribute('data-filter-val');
	
	var prev = this.parentNode.querySelector('.active');
	if (prev) prev.classList.remove('active');

	if (newFilterValue == filterValue){
		filterValue = "";
	}
	else {
		filterValue = newFilterValue;
		this.classList.add('active');
	}

	resetContainer();
	fetchContent();
}

function setSort(evt){
	evt.preventDefault();

	var newSortValue = this.getAttribute('data-sort-val');
	
	if (newSortValue == filterValue){
		return;
	}
	else {
		sortValue = newSortValue;
	}

	resetContainer();
	fetchContent();
}

function loadMore(){
 	if(currentRequest == null || currentRequest.readyState == 4){ 
 		page++;
 		fetchContent() 
 	};
}

function resetContainer(){
	page = 1;
	panel.innerHTML = '';
}

function fetchContent(){
	var url = baseUrl + '?term=' + searchTerm + '&filter=' + filterValue + '&page=' + page + '&sort=' + sortValue;

	loadMoreBtn && loadMoreBtn.classList.remove('hidden');
	if(currentRequest && currentRequest.status !== 4){
		currentRequest.abort();
	}
	currentRequest = ajax.get(url, fetchContentCallback);
}

function fetchContentCallback(status, response){
	if(status == 200){
		response = JSON.parse(response);
		panel.insertAdjacentHTML('beforeend', response.html);
		if (page >= response.pages) loadMoreBtn.classList.add('hidden');
	}
	else{
		panel.insertAdjacentHTML('beforeend', '<p class="error">An error occured!</p>');	
	}
}