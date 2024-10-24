// var a = "Hellow World!";
// alert(a);

var animacjaTestowa = document.getElementById("animacjaTestowa1");

console.log(animacjaTestowa);

//animacjaTestowa.addEventListener("click", function() {
$("#animacjaTestowa1").on("click", function() {
	$(this).animate({
		width: "500px",
		opacity: 0.4,
		fontsize: "3em",
		borderwidth: "10px"
	}, 1500);	
});
//<script src="scripts/jquery-3.7.1.min.js"></script>
//<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

$("#animacjaTestowa2").on({
	"mouseover" : function() {
		$(this).animate({
			width: 300
		}, 800);
	},
	"mouseout" : function() {
		$(this).animate({
			width: 200
		}, 800);
	}
});

$("#animacjaTestowa3").on("click", function() {
	var currentHeight = $(this).height();
	if(currentHeight < 490  && !$(this).is(":animated")) {
		$(this).animate({
			width: "+=" + 17,
			height: "+=" + 26,
			//opacity: "-=" + 0.1,
		}, 3000);
	}
});


$("#animacjaHover1").on({
	"mouseover" : function() {
		$(this).animate({
			height: 380,
			width: 270
		}, 800);
	},
	"mouseout" : function() {
		$(this).animate({
			height: 160,
			width: "100%"
		}, 800);
	}
});

$("#animacjaHover2").on({
	"mouseover" : function() {
		$(this).animate({
			height: 135,
			width: 220
		}, 800);
	},
	"mouseout" : function() {
		$(this).animate({
			height: "100%",
			width: 140
		}, 800);
	}
});

var counter = 0;

$(".zwiekszLubZmniejsz").on("click", function() {
	var currentHeight = $(this).height();
	var currentWidth = $(this).width();
	if(counter == 0) {
		$(this).animate({
			scale: 1.3
		}, 1500);
		counter++;	
	} else {
		$(this).animate({
			scale: 1.0
		}, 1500);
		counter--;
	}
});