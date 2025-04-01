let btnResumen = document.getElementById("Resumen");
let tabla = document.getElementById("table");
let formularioRes = document.getElementById("formResumen");
let btnVolverRes = document.getElementById("VolverRes");
let btnDetallado = document.getElementById('Detallado');
let formularioDet = document.getElementById('formDetallado');
let btnVolverDet = document.getElementById("VolverDet");
let idFormD = document.getElementById('formularioD');
let idFormR = document.getElementById('formularioR');

btnResumen.addEventListener("click", function(){

	formularioRes.style.display = "block";

	tabla.style.display = "none";

	formularioDet.style.display = "none"
	idFormD.noValidate = true;
	idFormD.reset();

})

btnVolverRes.addEventListener("click", function(){

	formularioDet.style.display = "none";
	idFormD.noValidate = true;
	idFormD.reset();

	formularioRes.style.display = "none";
	idFormR.noValidate = true;
	idFormR.reset();

	tabla.style.display = "block";

})

btnDetallado.addEventListener("click", function(){

	formularioRes.style.display = "none";
	idFormR.noValidate = true;
	idFormR.reset();

	tabla.style.display = "none";

	formularioDet.style.display = "block"

})

btnVolverDet.addEventListener("click", function(){

	formularioDet.style.display = "none"
	idFormD.noValidate = true;
	idFormD.reset();

	formularioRes.style.display = "none";
	idFormR.noValidate = true;
	idFormR.reset();

	tabla.style.display = "block";

})