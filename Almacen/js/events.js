let btnAgregar = document.getElementById("Agregar");
let tabla = document.getElementById("table");
let formulario = document.getElementById("form");
let btnVolver = document.getElementById("Volver");
let idForm = document.getElementById("formulario");

btnAgregar.addEventListener("click", function(){

	formulario.style.display = "block";
	tabla.style.display = "none";

})

btnVolver.addEventListener("click", function(){

	formulario.style.display = "none";
	idForm.noValidate = true;
	idForm.reset();

	tabla.style.display = "block";

})