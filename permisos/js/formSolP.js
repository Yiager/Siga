$(obtener_registrosP());

function obtener_registrosP(licencias){

	$.ajax({
		url: "buscar/formSolvenciaP.php",
		type : "POST",
		data : {licencias: licencias},
	})

	.done(function(resultadoP){
		$("#formP").html(resultadoP);
	})

}

$(document).on('keyup', '#codigoP', function(){
	let valorBusquedaP = $(this).val();
	if(valorBusquedaP != ""){
		obtener_registrosP(valorBusquedaP);
	}else{
		obtener_registrosP();
	}
})
