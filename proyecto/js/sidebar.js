//constante que trae el id de la barra de navegacion
const btn = document.querySelector('.toggle-btn');

//Evento que abre y cierra la barra de navegacion cambiando su clase
btn.addEventListener("click", function(){

	document.getElementById("sidebar").classList.toggle("active")


})