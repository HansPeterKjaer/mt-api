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
var findAncestor = require('./modules/utils/findAncestor.js');
//var ajaxFormSubmit = require('./modules/ajaxFormSubmit.js');
//require('smoothscroll-polyfill').polyfill();
var Swiper = require('swiper');

	var hSwiper = new Swiper ('.swiper-container-h', {
    	// Optional parameters
    	direction: 'horizontal',
    	loop: false,
     
	    // Navigation arrows
	    //nextButton: '.swiper-button-next',
	    //prevButton: '.swiper-button-prev',
	    
	    // And if we need scrollbar
	    scrollbar: '.swiper-scrollbar-h',
	    scrollbarHide: false,
	    scrollbarDraggable: true,
	    scrollbarSnapOnRelease: true,
        slidesPerView: 'auto',
        //centeredSlides: false,
        nextButton: '.swiper-nav-button--next',
        prevButton: '.swiper-nav-button--prev',
        buttonDisabledClass: 'swiper-nav-button--disabled',
        grabCursor: true
  	})   
	var v2Swiper = new Swiper('.swiper-container-v2', {
        scrollbar: '.swiper-scrollbar-v2',
        direction: 'vertical',
        slidesPerView: 'auto',
        mousewheelControl: true,
        freeMode: true,
        touchMoveStopPropagation: false
    });
   	var v3Swiper = new Swiper('.swiper-container-v3', {
        scrollbar: '.swiper-scrollbar-v3',
        direction: 'vertical',
        slidesPerView: 'auto',
        mousewheelControl: true,
        freeMode: true,
        touchMoveStopPropagation: false
    });     

    var exercisePanel = doc.querySelector('.exercise-panel');
    if(!exercisePanel.classList.contains('hidden')){
    	setTimeout(function(){ hSwiper.slideTo(2, 1000);}, 300);
    }

	var exerciseThumbs = doc.querySelectorAll('.workout__exercises .exercise');
	//var startBtn =  doc.querySelector('.workoutbtn');
	var diffRange = doc.querySelector('.diff-range');
	var strengthCardioIcon = doc.querySelector('.strength-cardio-icon'); 

	diffRange.addEventListener('input', function(evt){
		var diffValueElm = doc.querySelector('#diff-value');
		diffValueElm.textContent = '(' + Math.round(6-this.value) + '/' + Math.round(this.value) + ')';
		strengthCardioIcon.querySelector('.bump').style.animationDelay = ((this.value-1)*-0.2) + "s";
		strengthCardioIcon.querySelector('.heart').style.animationDelay = ((this.value-1)*-0.2) + "s";
	});

	/*startBtn && startBtn.addEventListener('click', function(evt){
		var nextExercise = null
		if (doc.querySelector('.workout__exercises .exercise.selected')) {
			nextExercise = doc.querySelector('.workout__exercises .exercise.selected').parentNode.nextElementSibling.childNodes[1];
			console.log(nextExercise);
		}
		if (nextExercise == null) nextExercise = doc.querySelector('.workout__exercises .exercise');
		selectExercise(nextExercise);
		startBtn.textContent = 'Næste Øvelse';
	});*/

	for (var i = 0; i < exerciseThumbs.length; ++i) {
		exerciseThumbs[i].addEventListener('click', function(evt){
			selectExercise(this);			
		});
	}

	function selectExercise(target){
		var newExercise = target.cloneNode(true);
		doc.querySelector('.workout__exercises .exercise.selected') && doc.querySelector('.workout__exercises .exercise.selected').classList.remove('selected');
		target.classList.add('selected');
		var exercisePanel = doc.querySelector('.exercise-panel');

		var exercise = exercisePanel.querySelector('.exercise');
		//currentExercise && exercisePanel.removeChild(currentExercise);

		//while (exercise.firstChild) { exercise.removeChild(exercise.firstChild); }
		newExercise.classList.remove('exercise--thumbnail');
		exercise.parentNode.replaceChild(newExercise, exercise);
		//exercisePanel.querySelector('.mt-panel__heading .index').textContent = newExercise.dataset.index;
		//exercisePanel.querySelector('.mt-panel__heading .name').textContent = newExercise.dataset.name;
		var swiperSlide = findAncestor(exercisePanel, 'swiper-slide')
		swiperSlide.classList.remove('hidden');

		hSwiper.update(true);
		v3Swiper.update(true);
		hSwiper.slideNext();
		player(newExercise);

		doc.querySelector('.main-content').classList.add('extended-grid');

		//win.scrollTo({top: 0, left: 2000, behavior: 'smooth' });
	}

	function player(exercise){
		var imageViewer = exercise.querySelector('.exercise__imageviewer');
		var exerciseImages = imageViewer.querySelectorAll('.imageviewer__image');
		var currentImage = imageViewer.querySelector('.imageviewer__image.current');
		var exerciseTimer = null;

		var playBtn = exercise.querySelector('.btn-play');
		var pauseBtn = exercise.querySelector('.btn-pause');
		var stopBtn = exercise.querySelector('.btn-stop');
		var prevBtn = exercise.querySelector('.btn-prev');
		var nextBtn = exercise.querySelector('.btn-next');

		playBtn.addEventListener('click', play);
		pauseBtn.addEventListener('click', pause);
		stopBtn.addEventListener('click', stop);
		nextBtn.addEventListener('click', function(){ 
			if (exerciseTimer) {
				clearInterval(exerciseTimer);
				exerciseTimer = null;
			} 
			next(); 
		});
		prevBtn.addEventListener('click', prev);

		function play(evt){
			if (exerciseTimer) {
				clearInterval(exerciseTimer);
				exerciseTimer = null;
			}else{
				exerciseTimer = setInterval(next, 1000);
			}
		}

		function pause(evt){
			if (exerciseTimer) {
				clearInterval(exerciseTimer);
				exerciseTimer = null;
			}
		}
		
		function stop(evt){
			if (exerciseTimer) {
				clearInterval(exerciseTimer);
				exerciseTimer = null;
			}
			exerciseImages[0].classList.add('current');
			currentImage.classList.remove('current');
			currentImage = exerciseImages[0];
		}

		function next(evt){
			var nextImage = currentImage.nextElementSibling;
			if (nextImage == null) nextImage = exerciseImages[0];
			nextImage.classList.add('current');
			currentImage.classList.remove('current');
			currentImage = nextImage;
		}

		function prev(evt){
			if (exerciseTimer) {
				clearInterval(exerciseTimer);
				exerciseTimer = null;
			}
			var prevImage = currentImage.previousElementSibling;
			if (prevImage == null) prevImage = exerciseImages[exerciseImages.length-1];
			prevImage.classList.add('current');
			currentImage.classList.remove('current');
			currentImage = prevImage;
		}	
	}
