let btnAgregar = document.getElementById("Agregar");
let tabla = document.getElementById("table");
let formulario = document.getElementById("form");
let btnVolver = document.getElementById("Volver");
let btnEntregas = document.getElementById('Entregas');
let formularioEntregas = document.getElementById('formEntrega');
let btnVolverEntrega = document.getElementById("VolverEntrega");
let idForm = document.getElementById('formulario');
let idFormE = document.getElementById('formularioE');

btnAgregar.addEventListener("click", function(){

	formulario.style.display = "block";

	tabla.style.display = "none";

	formularioEntregas.style.display = "none"
	idFormE.noValidate = true;
	idFormE.reset();

})

btnVolver.addEventListener("click", function(){

	formulario.style.display = "none";
	idForm.noValidate = true;
	idForm.reset();

	tabla.style.display = "block";

	formularioEntregas.style.display = "none"
	idFormE.noValidate = true;
	idFormE.reset();

})

btnEntregas.addEventListener("click", function(){

	formulario.style.display = "none";
	idForm.noValidate = true;
	idForm.reset();

	tabla.style.display = "none";

	formularioEntregas.style.display = "block"

})

btnVolverEntrega.addEventListener("click", function(){

	formulario.style.display = "none";
	idForm.noValidate = true;
	idForm.reset();

	tabla.style.display = "block";

	formularioEntregas.style.display = "none";
	idFormE.noValidate = true;
	idFormE.reset();

})