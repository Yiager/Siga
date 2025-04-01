let botonera = document.getElementById("botonera");
let formC = document.getElementById("formularioC");
let formP = document.getElementById("formularioP");
let tabla = document.getElementById("tabla");
let btnVer = document.getElementById("ver");
let btnAgg = document.getElementById("agregar");
let btnC = document.getElementById("Catastro");
let btnP = document.getElementById("Patente");


btnAgg.addEventListener("click", function(){

	botonera.style.display = "block";
	formC.style.display = "none";
	formP.style.display = "none";
	tabla.style.display = "none";

})

btnC.addEventListener("click", function(){

	botonera.style.display = "none";
	formC.style.display = "block";
	formP.style.display = "none";
	tabla.style.display = "none";

})

btnP.addEventListener("click", function(){

	botonera.style.display = "none";
	formC.style.display = "none";
	formP.style.display = "block";
	tabla.style.display = "none";

})


btnVer.addEventListener("click", function(){

	botonera.style.display = "none";
	formC.style.display = "none";
	formP.style.display = "none";
	tabla.style.display = "block";

})

