'use strict'

var doc = document;
var win = window;

var ajax = require('./ajax.js');
var alertMsg = require('./alertMsg.js');
var parent = require('./utils/parent.js');

var url;
var callback;
var exControls;
var id = null;

exports.init = function(fileInput, cb){
	url = fileInput.getAttribute('data-target-url');
	id = fileInput.getAttribute('data-target-id');
	exControls = fileInput.getAttribute('data-ex-controls') ? fileInput.getAttribute('data-ex-controls') : false ;
	callback = cb;
	fileInput.addEventListener('change', upload);
}

function upload(evt){
	evt.preventDefault();

	if(evt.target.files.length == 0) return;

	var file = evt.target.files[0]

	if (!file.type.match(/image.*/)) {
		var target = this;
		target = document.querySelector('.upload-status');

		alertMsg.init('The selected file appears not to be an image!', target);
		return
	}

	var p1 = resize(file, 500, 500);
	var p2 = resize(file, 50, 50)

	Promise.all([p1, p2]).then(function(images) { 
	  
		var fd = new FormData();
		fd.append('image', images[0], file.name);
		fd.append('thumb', images[1], file.name);	
		fd.append('controls', exControls);
		if(id) fd.append('id', id);

		if (callback === undefined){
			callback = function(status, response){
				console.log(status);
				console.log(response);
			}
		}
		
		ajax.post(url, fd, callback);

	}, function(reason) {
	  console.log(reason);
	});

	
}

function resize(file, w, h){
	var blob;
	var tmpImage = doc.createElement('img');
	var canvas = doc.createElement('canvas');
	var ctx = canvas.getContext('2d');
	var reader = new FileReader();

	canvas.width = w;
	canvas.height = h;

	reader.readAsDataURL(file);

	// Promises:
	var promiseReaderLoad = new Promise( function(resolve, reject){
		reader.addEventListener("load", function(evt) { 
			resolve(evt.target.result); 
		}, false);
	});

	var promiseImageLoad = new Promise(function(resolve, reject){
		tmpImage.addEventListener("load", function(){ 
			resolve();
		}, false);	
	});

	var promiseCanvasToBlob = new Promise(function(resolve, reject) {
		promiseImageLoad.then(function(val){
			ctx.drawImage(tmpImage, 0, 0, w, h);			
			canvas.toBlob(function(b){ 
				resolve(b);
			});
		});
	});		

	promiseReaderLoad.then(function(val){
		tmpImage.src = val; 
	});

	return promiseCanvasToBlob;
}

