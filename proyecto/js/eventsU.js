const TablaU = document.getElementById("botonFormU");

TablaU.addEventListener( 'click', function(){

	let tablaUser = document.getElementById('TablaU');
	tablaUser.style.display = 'block';

	let FormUser = document.getElementById('FormU');
	let formU = document.getElementById('formUser');
	formU.noValidate = true;
	FormUser.style.display = 'none';



} )

const FormU = document.getElementById("botonTablaU");

FormU.addEventListener( 'click', function(){

	let tablaUser = document.getElementById('TablaU');
	tablaUser.style.display = 'none';

	let FormUser = document.getElementById('FormU');
	FormUser.style.display = 'block';


} )