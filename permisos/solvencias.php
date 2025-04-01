<!DOCTYPE html>
<?php
echo "<script>

	if(window.history.replaceState){
   		 window.history.replaceState(null, 'solvencias','solvencias.php');
	}	

</script>";

include "conect.php";
$conexion = conectar();
// ***************************** ARCHIVO CSV CON LA BASE DE DATOS **************************************
$archivo = fopen("dbf/tbpropie.csv", "r");

$fechaActual = date("d-m-Y");

session_start();
if (!isset($_SESSION['id_usuario'])) {
	header("Location:index.php");
}

//****************Agregar datos del formulario de solvencias de catastro *****************************
if (isset($_POST['AgregarC'])) {
	
	$tipoC = $_POST['tipoC'];
	$nombresC = $_POST['nombresC'];
	$direccionC = $_POST['direccionC'];
	$usoC = $_POST['UsoC'];
	$PeriodoC = $_POST['PeriodoC'];
	$FechaEmisionC = $_POST['fechaActualC'];
	$fechaDesdeC = $_POST['desdeC'];
	$fechaHastaC = $_POST['hastaC'];
	$fechaDesdeFormateadaC = date("Y-m-d", strtotime($fechaDesdeC));
	$fechaHastaFormateadaC = date("Y-m-d", strtotime($fechaHastaC));
	$codigoC = $_POST["CodigoC"];

	$verificar_catastro = $conexion->prepare("SELECT Nombres FROM solvencias WHERE Nombres = ? AND FechaHasta >= ? ");
	$verificar_catastro->bind_param("ss", $nombresC, $fechaDesdeFormateadaC);
	$verificar_catastro->execute();
	$verificar_catastro->store_result();
	$contar_registrosC = $verificar_catastro->num_rows;
	$verificar_catastro->close();

	$NombresC = $conexion->prepare("SELECT nombre FROM correos WHERE nombre = ? ");
	$NombresC->bind_param("s", $nombresC);
	$NombresC->execute();
	$NombresC->store_result();
	$contarNombresC = $NombresC->num_rows;
	$NombresC->close();

	if ($contar_registrosC > 0 ) {
		echo "<script> alert('Ya existe una solvencia a nombre de la persona: ".$nombresC." ') </script>";
	}else{

		$insertar_registroC = $conexion->prepare("INSERT INTO 
									solvencias(Tipo, codigo, Nombres, Direccion, FechaActual, Periodo, FechaDesde, FechaHasta, Uso) 
							  VALUES 
							  	(
							  		?,
							  		?,
							  		?,
							  		?,
							  		?,
							  		?,
							  		?,
							  		?,
							  		?
							  	) ");
		$insertar_registroC->bind_param("sisssssss", $tipoC, $codigoC, $nombresC, $direccionC, $FechaEmisionC, $PeriodoC, $fechaDesdeC, $fechaHastaFormateadaC, $usoC);
		$insertar_registroC->execute();
		$insertar_registroC->close();


		if ($contarNombresC <= 0) {
			$InsertarNombreC = $conexion->prepare("INSERT INTO correos (nombre) VALUES (?)");
			$InsertarNombreC->bind_param("s", $NombresC);
			$InsertarNombreC->execute();
			$InsertarNombreC->close();
		}

		echo "<script>
					alert('Agregado exitosamente!');
			  </script>
		";
	}
}

//****************Agregar Datos del fomrulario de solvencias de licencias *****************************

if (isset($_POST['AgregarP'])) {
	
	$tipo = $_POST['tipoP'];
	$nombres = $_POST['nombresP'];
	$direccion = $_POST['direccionP'];
	$uso = $_POST['UsoP'];
	$Periodo = $_POST['PeriodoP'];
	$FechaEmision = $_POST['fechaActualP'];
	$fechaDesde = $_POST['desdeP'];
	$fechaHasta = $_POST['hastaP'];
	$fechaDesdeFormateada = date("Y-m-d", strtotime($fechaDesde));
	$fechaHastaFormateada = date("Y-m-d", strtotime($fechaHasta));
	$codigoP = $_POST["CodigoP"];

	$verificar_Patente = $conexion->prepare("SELECT Nombres,FechaHasta FROM solvencias WHERE Nombres = ? AND FechaHasta >= ? ");
	$verificar_Patente->bind_param("ss", $nombres, $fechaDesdeFormateada);
	$verificar_Patente->execute();
	$verificar_Patente->store_result();
	$contar_registros = $verificar_Patente->num_rows;
	$verificar_Patente->close();

	$Nombres = $conexion->prepare("SELECT nombre FROM correos WHERE nombre = ? ");
	$Nombres->bind_param("s", $nombres);
	$Nombres->execute();
	$Nombres->store_result();
	$contarNombres = $Nombres->num_rows;
	$Nombres->close();

	if ($contar_registros > 0) {
		echo "<script> alert('Ya existe una solvencia en ese periodo a nombre de la persona: ".$nombres." ') </script>";
	}else{

		$insertar_registro = $conexion->prepare("INSERT INTO 
									solvencias(Tipo, codigo, Nombres, Direccion, FechaActual, Periodo, FechaDesde, FechaHasta, Uso) 
							  VALUES 
							  	(
							  		?,
							  		?,
							  		?,
							  		?,
							  		?,
							  		?,
							  		?,
							  		?,
							  		?
							  	) ");
		$insertar_registro->bind_param("sisssssss", $tipo, $codigoP, $nombres, $direccion, $FechaEmision, $Periodo, $fechaDesde, $fechaHastaFormateada, $uso);
		$insertar_registro->execute();
		$insertar_registro->close();

		if ($contarNombres <= 0) {
			$InsertarNombre = $conexion->prepare("INSERT INTO correos (nombre) VALUES (?)");
			$InsertarNombre->bind_param("s", $nombres);
			$InsertarNombre->close();

		}

		echo "<script>
					alert('Agregado exitosamente!');
			  </script>
		";
	}
}

?>

<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/estiloSol.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans&family=Roboto:wght@100&display=swap" rel="stylesheet">
	<script defer src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
	<script defer type="text/javascript" src="js/solEvents.js"></script>
	<script defer type="text/javascript" src="js/formSolP.js"></script>
	<script defer type="text/javascript" src="js/formSolC.js"></script>
	<title> Solvencias </title>
</head>
<body>

	<header>
		<div class="titulo"> <h3> Solvencias </h3> </div>
		<div class="fecha"> <p> <?= $fechaActual; ?> </p> </div>
	</header>
	<div class="barraNav">
		<ul>
			<li> <button id="agregar"> Agregar </button></li>
			<li> <button id="ver"> Ver </button></li>
		</ul>
	</div>
	<div class="barraBtn" id="botonera">
		<ul>
			<li> <button id="Catastro"> Catastro </button></li>
			<li> <button id="Patente"> Patente </button></li>
		</ul>
	</div>
	
	<!-- /////////////////////////// FORMULARIO PATENTE ///////////////////////////// -->

	<div class="formularioSolP" id="formularioP">
		<h2 class="titulo"> Nueva solvencia: Patente </h2>
		<input type="text" name="CodigoP" id="codigoP" placeholder="Codigo...">
		<form method='POST' action="<?= $_SERVER['PHP_SELF']; ?>" id="formP"></form>
	</div>

	<!-- /////////////////////////// FORMULARIO CATASTRO ///////////////////////////// -->

	<div class="formularioSolC" id="formularioC">
		<h2 class="titulo"> Nueva solvencia: Catastro </h2>	
		<input type="text" name="CodigoC" id="codigoC" placeholder="Codigo...">
		<form method='POST' action="<?= $_SERVER['PHP_SELF']; ?>" id="formC"></form>
	</div>

	<!-- /////////////////////////// TABLA SOLVENCIAS ///////////////////////////// -->

	<div class="tablaSol" id="tabla">

			<input type="text" name="buscar" placeholder="Buscar..." id="buscar" class="buscador"> 
		
		<table>
			<thead>
				<tr>
					<th>Solvencia</th>
					<th>Nombres</th>
					<th>Direccion</th>
					<th>F.Emision</th>
					<th>Periodo</th>
					<th>Desde</th>
					<th>Hasta</th>
					<th>Uso</th>
					<th>Acciones</th>
				</tr>		
			</thead>

			<tbody id="tablaSolvencias"></tbody>
		</table>		

		<div class="total">
			<label id="total"> </label>
		</div>

		<div id="paginacion"></div>

		<input type="hidden" id="pagina" value="1">

		<script>
			
			getData();

			document.getElementById("buscar").addEventListener("keyup", function(){
				getData();
			});

			function getData(){

				let valorBusqueda = document.getElementById("buscar").value;
				let contenido = document.getElementById("tablaSolvencias");
				let pagina = document.getElementById('pagina').value;

				if (pagina == null) {
					pagina = 1
				}

				let url = "buscar/consultaSol.php";
				let formaData = new FormData();
				formaData.append("buscar", valorBusqueda);
				formaData.append("pagina", pagina);

				fetch(url, {
						method: "POST",
						body: formaData
				}).then(response => response.json())
				.then(data => {
					contenido.innerHTML = data.data
					document.getElementById("total").innerHTML = "Mostrando "+data.totalFiltro +" de "+ data.total + " registros totales"
					document.getElementById("paginacion").innerHTML = data.paginacion
				}).catch(err => console.log(err))
			}

			function siguientePagina(pagina){
				document.getElementById('pagina').value = pagina;
				getData();
			}

		</script>

	</div>
	<a href="menu.php" class="btnVolver"> Volver </a>

</body>
</html>
