//Se crea la constante para el boton que mostrara el formulario de los concejos comunales y ocultara la tabla
//Mediente un evento

const botonTablaCC = document.getElementById("botonTablaCC");

botonTablaCC.addEventListener('click', function(){

	let tablaCC = document.getElementById('TablaCC');
	tablaCC.style.display = 'none';

	let FormCC = document.getElementById('FormCC');
	FormCC.style.display = 'block';


} )

//Se crea la constante para el boton que mostrara las tablas de los concejos comunales y ocultara el formulario
//Mediente un evento
const botonVolverForm = document.getElementById('Form-Volver');

botonVolverForm.addEventListener( 'click', function(){

	let tablaCC = document.getElementById('TablaCC');
	tablaCC.style.display = 'block';

	let FormCC = document.getElementById('FormCC');
	let FormConcejoC = document.getElementById('formConcejoC');
	FormConcejoC.noValidate = true;
	FormCC.style.display = 'none';


} )