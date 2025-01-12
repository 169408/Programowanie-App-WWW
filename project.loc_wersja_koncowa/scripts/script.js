// Funkcja wykonuje się po załadowaniu całego dokumentu HTML.
// Pobiera aktualny URL strony i porównuje go z adresami href w menu.
// Dopasowanemu linkowi dodaje klasę "active", a pozostałym ją usuwa.
document.addEventListener("DOMContentLoaded", function() {
	const currentUrl = window.location.href;

	const menuLinks = document.querySelectorAll("ul.menu li a");

	menuLinks.forEach(link => {
		link.classList.remove("active");
		console.log(link.getAttribute("href"));

		// Jeśli URL linku pasuje do obecnego URL, dodaj klasę "active"
		if (currentUrl.includes(link.getAttribute("href"))) {
			link.classList.add("active");
		}

		// Jeśli URL zawiera "?", a href linku nie, usuń klasę "active"
		if(currentUrl.includes("?") && !link.getAttribute("href").includes("?")){
			link.classList.remove("active");
		}

	});
});

var animacjaTestowa = document.getElementById("animacjaTestowa1");

console.log(animacjaTestowa);

// Funkcja animuje element po kliknięciu, zmieniając jego szerokość, przezroczystość, rozmiar czcionki i grubość ramki.
// Czas trwania animacji to 1500 ms.
$("#animacjaTestowa1").on("click", function() {
	$(this).animate({
		width: "500px",
		opacity: 0.4,
		fontsize: "3em",
		borderwidth: "10px"
	}, 1500);	
});

// Funkcja dodaje animację dla elementu przy najechaniu i opuszczeniu kursora.
// Podczas najechania szerokość elementu rośnie do 300px, a po opuszczeniu zmniejsza się do 200px.
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


// Funkcja zwiększa wysokość i szerokość elementu po kliknięciu.
// Jeśli element nie przekroczył wysokości 490px i nie jest aktualnie animowany, następuje powiększenie.
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

// Funkcja zmienia rozmiar elementu po najechaniu i opuszczeniu kursora.
// Na hover zwiększa rozmiary, a na opuszczenie resetuje je do pierwotnych wartości.
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
// Funkcja podobna do animacjaHover1, ale z innymi wartościami dla zmiany wysokości i szerokości.
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


// Funkcja zmienia skalę elementu na większą lub mniejszą po kliknięciu.
// Cykl powiększenia i zmniejszenia sterowany jest przez licznik.
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

