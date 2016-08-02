'use strict'

module.exports = {
	post: function(url, data, cb){
		var request = new XMLHttpRequest();
		request.addEventListener("load", function(){ cb(this.status, this.responseText) });
		request.open("POST", url);
		if (typeof data == 'string'){ request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');}
		request.send(data);
		return request;	
	},
	get: function(url, cb){
		var request = new XMLHttpRequest();
		request.addEventListener("load", function(){ cb(this.status, this.responseText) });
		request.open("GET", url);
		request.send();
		return request;
	}
}
