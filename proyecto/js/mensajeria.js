/***************** EVENTOS DE CAMBIO DE BANDEJAS **************************/

const btnRecibidos = document.getElementById('BandejaR');
const btnEnviados = document.getElementById('BandejaE');
const BandejaR = document.getElementById('Recibidos');
const BandejaE = document.getElementById('Enviados');

btnRecibidos.addEventListener('click', function(){

	BandejaR.style.display = 'block';
	BandejaE.style.display = 'none';

});

btnEnviados.addEventListener('click', function(){

	BandejaR.style.display = 'none';
	BandejaE.style.display = 'block';

});

/***************** MODAL MENSAJES **************************/

const btnModalMsj = document.querySelector('#NuevoMsj');
const btnVolver = document.querySelector('#btnVolver');
const modal = document.querySelector('#modal');

btnModalMsj.addEventListener('click', function(){
	modal.showModal();
});


btnVolver.addEventListener('click', function(){
	modal.close();
});

