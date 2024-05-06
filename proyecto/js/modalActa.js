//Botones para mostrar y cerrar el modal
const btnModalActa = document.querySelector('#btnModalActa');
const btnVolverActa = document.querySelector('#btnVolverActa');
//Constantes que identifica al modal
const modalActa = document.querySelector('#modalActa');

//eventos que abren y cierran el modal
btnModalActa.addEventListener('click', function(){
	modalActa.showModal();
});


btnVolverActa.addEventListener('click', function(){
	modalActa.close();
});