'use strict'
var doc = document;
var win = window;

var ajax = require('./ajax.js');
var alertMsg = require('./alertMsg.js');

module.exports = function (form){
	form.addEventListener('submit', function(evt) {
		evt.preventDefault();
		var fd = new FormData(form);
		var url = this.getAttribute('action');
	  	ajax.post(url, fd, function(status, data){
			data = JSON.parse(data);
			var msgClass = (status == 200 && data.status == true) ? 'alert-success' : 'alert-danger';
			var target = doc.querySelector(data.input) || doc.querySelector('.form-msg') || form;			
			alertMsg.init(data.msg, target, msgClass, true); 
	  	});
	}, false);
}