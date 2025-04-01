$(obtener_registrosC());

function obtener_registrosC(inmuebles){

	$.ajax({
		url: "buscar/formSolvenciaC.php",
		type : "POST",
		data : {inmuebles: inmuebles},
	})

	.done(function(resultadoC){
		$("#formC").html(resultadoC);
	})

}

$(document).on('keyup', '#codigoC', function(){
	let valorBusquedaC = $(this).val();
	if(valorBusquedaC != ""){
		obtener_registrosC(valorBusquedaC);
	}else{
		obtener_registrosC();
	}
})
