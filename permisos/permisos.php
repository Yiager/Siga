<?php

include "conect.php";
$conexion = conectar();
session_start();
if (!isset($_SESSION['id_usuario'])) {
	header("Location:index.php");
}

$fechaActual = date("d-m-Y");

if (isset($_POST['Agregar'])) {
	
	$cedula = $_POST['ci'];
	$nombre = $_POST['nombre'];
	$empresa = $_POST['empresa'];
	$direccion = $_POST['direccion'];
	$correo = $_POST['correo'];
	$actividad = $_POST['actividad'];
	$emision = $fechaActual;
	$desde = $_POST['desde'];
	$hasta = $_POST['hasta'];
	$renovacion = 0;


	$ComprobarNumero = $conexion->prepare("SELECT * FROM permisos");
	$ComprobarNumero->execute();
	$ComprobarNumero->store_result();
	$totalNumero = $ComprobarNumero->num_rows;
	$ComprobarNumero->close();

	if ($totalNumero == 0) {
		$nro = 1;
	}else{
		$nro = $totalNumero + 1;
	}

	$verificarPermiso = $conexion->prepare("SELECT cedula FROM permisos WHERE cedula = ? ");
	$verificarPermiso->bind_param("s", $cedula);
	$verificarPermiso->execute();
	$verificarPermiso->store_result();
	$contarPermisos = $verificarPermiso->num_rows;
	$verificarPermiso->close();

	if ($contarPermisos > 0) {	
		echo "<script> alert('Ya existe un permiso con la cedula'".$cedula.") </script>";
	}else{

		$insertarPermiso = $conexion->prepare("INSERT INTO 
								permisos(Cedula, Nombre, Empresa, Direccion, Actividad, Desde, Hasta, Emision, DesdeR, HastaR, EmisionR,  Renovacion, Nro) 
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
									'No',
									'No',
									'No',
									?,
									?
								) ");
		$insertarPermiso->bind_param("ssssssssss", $cedula, $nombre, $empresa, $direccion, $actividad, $desde, $hasta, $emision, $renovacion, $nro);
		$insertarPermiso->execute();
		$insertarPermiso->close();

		echo "<script> alert('Registro insertado exitosamente!') </script>";

	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/estiloPer.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans&family=Roboto:wght@100&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
	<title> Permisos </title>
</head>
<body>

	<header>
		<div class="titulo"> <h3> Permisos </h3> </div>
		<div class="fecha"> <p> <?= $fechaActual; ?> </p> </div>
	</header>

	<div class="barraNav">
		<ul>
			<li> <button id="agregar"> Agregar </button></li>
			<li> <button id="ver"> Ver </button></li>
		</ul>
	</div>
	
	<div class="formularioPer" id="formulario">
		<form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>" >
			<h2 class="bloque"> Agregar permiso </h2>
			<p>
				<label for="Cedula"> Cedula: </label>
				<input type="text" name="ci" id="Cedula" required>
			</p>
			<p>
				<label for="Nombre"> Nombre: </label>
				<input type="text" name="nombre" id="Nombre" required>
			</p>
			<p>
				<label for="Empresa"> Empresa: </label>
				<input type="text" name="empresa" id="Empresa" required>
			</p>
			<p>
				<label for="Direccion"> Direccion: </label>
				<input type="text" name="direccion" id="Direccion" required>
			</p>
			<p>
				<label for="Correo"> Correo: </label>
				<input type="text" name="correo" id="Correo" required>
			</p>
			<p>
				<label for="Actividad"> Actividad: </label>
				<input type="text" name="actividad" id="Actividad" required>
			</p>
			<p>
				<label for="Desde"> Desde: </label>
				<input type="date" name="desde" id="Desde" required onchange="fechas()">
			</p>
			<p>
				<label for="Hasta"> Hasta: </label>
				<input type="text" name="hasta" id="Hasta" readonly>
			</p>

			<script>

						function fechas(){

							let desde = new Date(document.getElementById('Desde').value);
							let hasta = document.getElementById('Hasta');

							let dia = desde.getDate() + 1;
							let mes = desde.getMonth() + 4;
							let año = desde.getFullYear();

							if(mes == 2 && dia == 31){
								dia -= 3;
							}

							if(mes % 2 == 0 && dia == 31 && mes != 2){
								dia -= 1
							}else{
								dia += 1
							}

							hasta.value = año+'-'+mes+'-'+dia;

						}

					</script>

			<input type="hidden" name="emision" value="<?= $fechaActual; ?>" required>
			
			<input type="submit" name="Agregar" value="Agregar" class="bloque">
			
		</form>
	</div>

	<div class="tablaPer" id="tabla">

		<input type="text" name="buscar" id="buscar" placeholder="Buscar..." class="buscador"> 

		<table>
			<thead>
				<tr>
					<th>CI</th>
					<th>Nombre</th>
					<th>Empresa</th>
					<th>Direccion</th>
					<th>Correo</th>
					<th>Actividad</th>
					<th>Desde</th>
					<th>Hasta</th>
					<th>Emision</th>
					<th>Renovacion</th>
					<th>Nro</th>
					<th>Acciones</th>
				</tr>
			</thead>

			<tbody id="tablaPermisos">
					
			</tbody>
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
				let contenido = document.getElementById("tablaPermisos");
				let pagina = document.getElementById('pagina').value;

				if (pagina == null) {
					pagina = 1
				}

				let url = "buscar/consultaPer.php";
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

<script  type="text/javascript" src="js/Events.js"></script>
</body>
</html>
