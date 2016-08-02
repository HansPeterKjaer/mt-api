'use strict'

var doc = document;
var win = window;

exports.init = function(message, elm, style, positionBefore){
	var style = (typeof style !== 'undefined') ? style : 'alert-danger';
	var errorWrapper = document.createElement('div');
	var button = document.createElement('button');

	errorWrapper.classList.add('errormsg', 'alert', 'alert-dismissible', 'margin-top', style);
	button.classList.add('close');
	button.setAttribute('aria-label', 'Close');
	button.textContent = 'x';

	errorWrapper.textContent = message;
	errorWrapper.appendChild(button);

	button.addEventListener('click', function cb(evt){ errorWrapper.remove(); }, false)
	win.setTimeout(function(){ errorWrapper.remove(); }, 5000);

	if(positionBefore !== true) 
		elm = elm.nextSibling;
	
	elm.parentNode.insertBefore(errorWrapper, elm);
}

