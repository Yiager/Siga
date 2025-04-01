let btnAgg = document.getElementById("agregar");
let btnVer = document.getElementById("ver");
let form = document.getElementById("formulario");
let tabla = document.getElementById("tabla");

btnAgg.addEventListener('click', function(){

	form.style.display = "block";
	tabla.style.display = "none";

});


btnVer.addEventListener('click', function(){

	form.noValidate = true;
	form.style.display = "none";
	tabla.style.display = "block";

});