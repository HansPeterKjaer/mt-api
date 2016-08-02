'use strict'

var doc = document;
var win = window;

var autoCompleteData = [];
var currentRequest = null;
var autoComplete;
var url;

var awesomplete = require('./third/awesomplete.js');
var ajax = require('./ajax.js');

exports.init = function(inputField, baseUrl, subQuery, showThumb){  
		url = baseUrl;

		var config = {minChars: 1, autoFirst: true};
		if (showThumb) config.item = itemCB;

		autoComplete = new awesomplete(inputField, config);
		
		if (subQuery) inputField.addEventListener('keydown', subQueryCB);
		else inputField.addEventListener('focus', dataCB);
}

function dataCB(evt){		
	var queryUrl = url;
	currentRequest = ajax.get(queryUrl, fetchDataCB); 
	function fetchDataCB(status, data){
		data = JSON.parse(data);
		var result = data.map( function(a) { return a.name; });
		autoComplete.list = result;
		autoComplete.open();
	}
}

function subQueryCB(evt){
		var term = this.value
		var firstChar = term.charAt(0);

		if (term == '' || event.keyCode < 65 || event.keyCode > 90) return; // return if not key is nonalphabetic or term is empty string

		if (autoCompleteData[firstChar]){
				autoComplete.list = autoCompleteData[firstChar];
		}
		else {
				if(currentRequest) currentRequest.abort();
		
				var queryUrl = url + '&firstLetter=' + firstChar;
				currentRequest = ajax.get(queryUrl, fetchDataCB); 
		}//https://github.com/LeaVerou/awesomplete/pull/16774
		function fetchDataCB(status, data){
				data = JSON.parse(data);
				var result = data.map( function(a) { return a.imageName; });
				autoCompleteData[firstChar] = result;
				autoComplete.list = result;
				autoComplete.open();
		}
}

function itemCB(item, input) {
		var html = '<img src="' +  MT.BasePath + '/assets/uploads/' + item + '"/>' + item.replace(RegExp(awesomplete.$.regExpEscape(input.trim()), 'gi'), '<mark>$&</mark>');
		return awesomplete.$.create('li', {'innerHTML': html, 'aria-selected': 'false'});
}