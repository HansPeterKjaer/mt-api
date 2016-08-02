'use strict'

var doc = document;
var win = window;

//win.MT = {}; // is this used anyehaere
//win.MT.BasePath = (location.hostname  == 'localhost') ? '/mt' : '';

//var ajaxPanel = require('./modules/ajaxPanel.js');
//var imageUploader = require('./modules/uploadImage.js');
//var alertMsg = require('./modules/alertMsg.js');
//var autocomplete = require('./modules/autocomplete.js');
//var ajax = require('./modules/ajax.js');
//var findAncestor = require('./modules/utils/findAncestor.js');
//var ajaxFormSubmit = require('./modules/ajaxFormSubmit.js');


	var exerciseThumbs = doc.querySelectorAll('.workout-exercises .exercise');
	var startBtn =  doc.querySelector('.workoutbtn');
	var diffRange = doc.querySelector('.diff-range');

	diffRange.addEventListener('input', function(evt){
		var diffValueElm = doc.querySelector('#diff-value');
		diffValueElm.textContent = '(' + (6-this.value) + '/' + this.value + ')';
	});

	startBtn && startBtn.addEventListener('click', function(evt){
		var nextExercise = null
		if (doc.querySelector('.workout-exercises .exercise.selected')) {
			nextExercise = doc.querySelector('.workout-exercises .exercise.selected').parentNode.nextElementSibling.childNodes[1];
			console.log(nextExercise);
		}
		if (nextExercise == null) nextExercise = doc.querySelector('.workout-exercises .exercise');
		selectExercise(nextExercise);
		startBtn.textContent = 'Næste Øvelse';
	});

	for (var i = 0; i < exerciseThumbs.length; ++i) {
		exerciseThumbs[i].addEventListener('click', function(evt){
			selectExercise(this);			
		});
	}

	function selectExercise(target){
		var exercise = target.cloneNode(true);
		doc.querySelector('.workout-exercises .exercise.selected') && doc.querySelector('.workout-exercises .exercise.selected').classList.remove('selected');
		target.classList.add('selected');
		var exercisePanel = doc.querySelector('.exercise-panel');

		var currentExercise = exercisePanel.querySelector('.exercise');
		currentExercise && exercisePanel.removeChild(currentExercise);
		exercisePanel.appendChild(exercise);
		var placeholder = exercisePanel.querySelector('.placeholder');
		placeholder && placeholder.classList.add('hidden');
		player(exercise);
	}
	function player(exercise){
		var imageViewer = exercise.querySelector('.imageViewer');
		var exerciseImages = imageViewer.querySelectorAll('img');
		var currentImage = imageViewer.querySelector('img.current');
		var exerciseTimer = null;

		var playBtn = exercise.querySelector('.btn-play');
		var prevBtn = exercise.querySelector('.btn-prev');
		var nextBtn = exercise.querySelector('.btn-next');

		playBtn.addEventListener('click', play);
		nextBtn.addEventListener('click', next);
		prevBtn.addEventListener('click', prev);

		function play(evt){
			if (exerciseTimer) {
				clearInterval(exerciseTimer);
				exerciseTimer = null;
			}else{
				exerciseTimer = setInterval(playCallback, 1000);
			}
		}
		
		function playCallback(){
		next();
		}

		function next(evt){
		var nextImage = currentImage.nextElementSibling;
		if (nextImage == null) nextImage = exerciseImages[0];
		nextImage.classList.add('current');
		currentImage.classList.remove('current');
		currentImage = nextImage;
		}

		function prev(evt){
		var prevImage = currentImage.previousElementSibling;
		if (prevImage == null) prevImage = exerciseImages[exerciseImages.length-1];
		prevImage.classList.add('current');
		currentImage.classList.remove('current');
		currentImage = prevImage;
		}	
	}