//Botones para mostrar y cerrar el modal
const btnModal = document.querySelector('#btnModal');
const btnVolver = document.querySelector('#btnVolver');
//Constantes que identifica al modal
const modal = document.querySelector('#modal');

//eventos que abren y cierran el modal
btnModal.addEventListener('click', function(){
	modal.showModal();
});


btnVolver.addEventListener('click', function(){
	modal.close();
});