<?php
include("conect.php");

$conexion = conectar();

session_start();
if(!isset($_SESSION['idUser'])){
	header("location: index.php");
}

$idSesion = $_SESSION['idUser'];

$usuarioInfo = "SELECT Nombre, Usuario FROM usuarios WHERE id = '$idSesion'";
$respuestaInfo = mysqli_query($conexion, $usuarioInfo);

$info = mysqli_fetch_assoc($respuestaInfo);

$nombreUser = $info['Nombre'];
$UserName = $info['Usuario'];

$fechaActual = date("d/m/Y");

$idCC = $_GET['id'];



if(isset($_POST['RegistrarVocero'])){
	
	$idConcejo = $_POST['id_cc'];
	$nombre = $_POST['name'];
	$apellido = $_POST['apellido'];
	$cedula = $_POST['cedula'];
	$tlf = $_POST['tlf'];
	$votos = $_POST['votos'];
	$comite = $_POST['comite'];
	$comiteNuevo = $_POST['Cnuevo'];
	$Unidad = $_POST['unidad'];	
	$tipo = $_POST['tipo'];

	if($Unidad != 'Unidad Ejecutiva'){
		$comite = 'N/A';

	}

	if($comite == 'Otro'){
		$comite = $comiteNuevo;

	}
	
	//comprueba que el vocero no exista en la base de datos

	$comprobarVocero = "SELECT Cedula, Comite FROM voceros WHERE Cedula = '$cedula' AND Comite = '$comite' ";
	$comprobarConsulta = mysqli_query($conexion, $comprobarVocero);
	$row = $comprobarConsulta->num_rows;
	if($row > 0){

		echo "

			<script>

					alert('El vocero ya esta asignado a un comite');

			</script>";

	}else{
		//Inserta los datos del vocero en la tabla
		$insertarVocero = "INSERT INTO 
									voceros(id_cc, Nombre, Apellido, Cedula, Tlf, Unidad, Comite, Tipo, Votos) 
						   VALUES
						   (
						   		'$idConcejo', 
						   		'$nombre', 
						   		'$apellido', 
						   		'$cedula', 
						   		'$tlf', 
						   		'$Unidad',
						   		'$comite',
						   		'$tipo',
						   		'$votos'
						   	)";

		$ValidarConsulta = mysqli_query($conexion, $insertarVocero);

		echo "<script>

					alert('Vocero registrado con exito!');
					window.location = 'DatosConcejo.php?id=$idCC';

			</script>";

	}
}

if (isset($_POST['ActualizarActa'])) {
	
	$actaNombre = $_FILES['acta']['name'];
	$actaTmp = $_FILES['acta']['tmp_name'];
	$NombreActa = str_replace(' ', '_', $actaNombre);

	if(file_exists('./actas/')){
		move_uploaded_file($actaTmp, './actas/'.$NombreActa);
	}

	$rutaActa = '/actas/'.$NombreActa;

	$ActualizarActa = "UPDATE ccomunal SET acta = '$rutaActa' WHERE id_cc = $idCC ";
	$respuestaActa = mysqli_query($conexion, $ActualizarActa);
	echo "

		<script>
				alert('Acta actualizada exitosamente!');
				window.location = 'DatosConcejo.php?id=$idCC';
		</script>

	";
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/estiloMenu.css">
	<link rel="stylesheet" type="text/css" href="css/estiloDatos.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300&family=Roboto+Condensed:wght@300&display=swap" rel="stylesheet">
	<title> Datos Concejo </title>
</head>
<body>

	<header>
		<div class="fecha">   
			<p> <?php echo $fechaActual ?> </p>
		</div>

		<div class="information">
			<h3> <?php echo $nombreUser ?> </h3>
			<h4> <?php echo $UserName ?> </h4>
		</div>
	</header>

	<?php

	$sqlConcejo = "SELECT * FROM ccomunal WHERE id_cc = '$idCC' ";
	$respuestaSql = mysqli_query($conexion, $sqlConcejo);

	//Inicio de bucle para iterar los datos de el concejo comunal en la pagina
	while($traerCC = mysqli_fetch_assoc($respuestaSql)){
		$latitud = $traerCC['Latitud'];
		$lat = (float) $latitud;
		$longitud = $traerCC['Longitud'];
		$lon = (float) $longitud;
		$nombre = $traerCC['nombre_cc'];

	?>

	<div class="contenedorPrincipal"> 
		<div class="contenedor1">
			<span> Concejo Comunal: <?php echo $traerCC['nombre_cc'] ?> </span>
			<span> Codigo SITUR: <?php echo $traerCC['situr'] ?> </span> 
			<span> Nuevo: <?php echo $traerCC['situr_nuevo']; ?> </span>
		</div>

		<div class="contenedor2">
			
			<h3 > Situacion actual </h3>
			<div class="Situacion">
				<div class="bloqueSituacion">

					<table class="tablaDatos">

						<tr>
							<td>Fecha de elecciones:</td>
							<td><?php echo $traerCC['prop_elecciones'] ?></td>
						</tr>
						<tr>
							<td>Fecha vencimiento</td>
							<td><?php echo $traerCC['vencimiento'] ?></td>
						</tr>
						<tr>
							<td>Situacion</td>
							<td><?php echo $traerCC['situacion'] ?></td>
						</tr>
						<tr>
							<td>Habitantes</td>
							<td><?php echo $traerCC['habitantes'] ?></td>
						</tr>
						<tr>
							<td>Participantes cuaderno</td>
							<td><?php echo $traerCC['cuaderno'] ?></td>
						</tr>
					</table>
				</div>


				<div class="bloqueSituacion">

					<table class="tablaDatos">

						<tr>
							<td>Participantes</td>
							<td><?php echo $traerCC['participantes'] ?></td>
						</tr>
						<tr>
							<td>Mujeres +15 años</td>
							<td><?php echo $traerCC['Mmayores'] ?></td>
						</tr>
						<tr>
							<td>Mujeres -15 años</td>
							<td><?php echo $traerCC['Mmenores'] ?></td>
						</tr>
						<tr>
							<td>Hombres +15 años</td>
							<td><?php echo $traerCC['Hmayores'] ?></td>
						</tr>
						<tr>
							<td>Hombres -15 años</td>
							<td><?php echo $traerCC['Hmenores'] ?></td>
						</tr>
					</table>
				</div>
			</div>
		<div class="enlaces">
			<a class="enlaceActa btn-acta" href="/proyecto<?php echo $traerCC['acta'] ?>"> Ver acta </a> 
			<button id="btnModalActa" class="editActa"> Editar acta </button>
		</div>
		</div> 

		<div class="contenedor3">
			
			<div class="Ubicacion">
				<h3 class="tituloDatos"> Ubicacion : </h3>

				<table class="tablaDatos">

						<tr>
							<td>Estado</td>
							<td><?php echo $traerCC['estado'] ?></td>
						</tr>
						<tr>
							<td>Municipio</td>
							<td><?php echo $traerCC['municipio'] ?></td>
						</tr>
						<tr>
							<td>Parroquia</td>
							<td><?php echo $traerCC['parroquia'] ?></td>
						</tr>
						<tr>
							<td>Tipo de Concejo Comunal</td>
							<td> <?php echo $traerCC['tipo_cc'] ?></td>
						</tr>
						<tr>
							<td>Comuna</td>
							<td><?php echo $traerCC['comuna'] ?></td>
						</tr>
					</table>
			</div>
	<?php
		}
	?>
			<!-- Codigo para mostrar la localizacion mediante el API Streetmaps -->
			<div class="localizacion">
				<h3 class="tituloDatos"> Coordenadas : </h3>

				<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
				<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

				<div id="mapa"></div>


				<script>
					//Se ejecuta la funcion del mapa y se traen los valores de latitud y longitud de la base de datos
					let map = L.map('mapa').setView([<?php echo $lat ?>, <?php echo $lon ?>], 18)

					L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
					    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
					}).addTo(map);

					//se añade un marcador que muestra la latitud y longitud con un mensaje
					L.marker([<?php echo $lat ?>, <?php echo $lon ?>]).addTo(map).bindPopup('<?php echo $lat. "<br>". $lon ?>');
					
				</script>

			</div>
		</div>

	<?php
		// ************************************* UNIDAD EJECUTIVA ************************************************
		//Seleccionamos los voceros de la tabla de voceros
		$sqlVoceros = "SELECT * FROM voceros WHERE id_cc = $idCC AND Unidad = 'Unidad Ejecutiva' ";
		$traerVoceros = mysqli_query($conexion, $sqlVoceros);
		$totalVoceros = $traerVoceros->num_rows;

		//En caso de no existir registros de algun vocero
		if($totalVoceros > 0){

			echo "

				<div class='contenedor4'>	
					<div class='cabeceraVocero'>
						<h3> Voceros: Unidad Ejecutiva</h3> 
						<button class='ComiteNuevo' id='btnModal'> Agregar </button>
					</div>
					<table>

					<thead>
						<tr>
							
							<td>Nombres</td>
							<td>Apellidos</td>
							<td>Cedula</td>
							<td>Tlf</td>
							<td>Unidad</td>
							<td>Comite</td>
							<td>Tipo</td>
							<td>Votos</td>
							<td> Acciones </td>

						</tr>
					</thead>

			";

			//si existen voceros se inicia el bucle que trae todos las filas dela tabla
			while($Voceros = mysqli_fetch_assoc($traerVoceros)){

				$id_vocero = $Voceros['id_vocero'];
				$Nombre = $Voceros['Nombre'];
				$Apellido = $Voceros['Apellido'];
				$Cedula = $Voceros['Cedula'];
				$Tlf = $Voceros['Tlf'];
				$Comite = $Voceros['Comite'];
				$Votos = $Voceros['Votos'];
				$Unidad = $Voceros['Unidad'];
				$Tipo = $Voceros['Tipo'];

				?>
							<tr>
								<td> <?php echo $Nombre ?></td>
								<td> <?php echo $Apellido ?></td>
								<td> <?php echo $Cedula ?></td>
								<td> <?php echo $Tlf ?></td>
								<td> <?php echo $Unidad ?></td>
								<td> <?php echo $Comite ?></td>
								<td> <?php echo $Tipo ?></td>
								<td> <?php echo $Votos ?></td>
								<td> 
									<a href='editar/editarVocero.php?CI=<?php echo $Cedula ?>&id=<?php echo $idCC ?>&V=<?php echo $id_vocero ?>' class='enlace'> Editar </a> 
									<button class='btnEliminar' onclick='Eliminar();'> Eliminar </button>
								</td>

								<script>

										function Eliminar(){
											let pregunta = confirm('¿Estas seguro que deseas eliminar este registro?');
											if(pregunta == true){
												window.location.href = 'eliminar/eliminarVocero.php?CI=$Cedula&id=$idCC'
											}
										}

								</script>

							</tr>
						<?php
							}
						?>

				</table>
			</div>
	<?php
			
		}else{
			echo 
				"<div class='contenedor4'>	
					<div class='cabeceraVocero'>
						<h3> Voceros: Unidad Ejecutiva</h3> 
						<button class='ComiteNuevo' id='btnModal'> Agregar </button>
					</div>
					<table>

					<thead>
						<tr>
							
							<td>Nombres</td>
							<td>Apellidos</td>
							<td>Cedula</td>
							<td>Tlf</td>
							<td>Unidad</td>
							<td>Comite</td>
							<td>Tipo</td>
							<td>Votos</td>
							<td> Acciones </td>

						</tr>
					</thead>
					</tr>		

				</table>
			</div>";
		}
		
	?>

	<?php
		// ************************************* UNIDAD ADMINOISTRATIVA Y FINANCIERA ************************************************
		//Seleccionamos los voceros de la tabla de voceros
		$sqlVoceros = "SELECT * FROM voceros WHERE id_cc = $idCC AND Unidad = 'Unidad Administrativa y financiera' ORDER BY Votos ASC LIMIT 10";
		$traerVoceros = mysqli_query($conexion, $sqlVoceros);
		$totalVoceros = $traerVoceros->num_rows;

		//En caso de no existir registros de algun vocero
		if($totalVoceros > 0){

			echo "

				<div class='contenedor4'>	
					<div class='cabeceraVocero'>
						<h3> Voceros: Unidad Administrativa y Financiera </h3> 
					</div>
					<table>

					<thead>
						<tr>
							
							<td>Nombres</td>
							<td>Apellidos</td>
							<td>Cedula</td>
							<td>Tlf</td>
							<td>Unidad</td>
							<td>Comite</td>
							<td>Tipo</td>
							<td>Votos</td>
							<td> Acciones </td>

						</tr>
					</thead>

			";

			//si existen voceros se inicia el bucle que trae todos las filas dela tabla
			while($Voceros = mysqli_fetch_assoc($traerVoceros)){

				$id_vocero = $Voceros['id_vocero'];
				$Nombre = $Voceros['Nombre'];
				$Apellido = $Voceros['Apellido'];
				$Cedula = $Voceros['Cedula'];
				$Tlf = $Voceros['Tlf'];
				$Comite = $Voceros['Comite'];
				$Votos = $Voceros['Votos'];
				$Unidad = $Voceros['Unidad'];
				$Tipo = $Voceros['Tipo'];

				?>
							<tr>
								<td> <?php echo $Nombre ?></td>
								<td> <?php echo $Apellido ?></td>
								<td> <?php echo $Cedula ?></td>
								<td> <?php echo $Tlf ?></td>
								<td> <?php echo $Unidad ?></td>
								<td> <?php echo $Comite ?></td>
								<td> <?php echo $Tipo ?></td>
								<td> <?php echo $Votos ?></td>
								<td> 
									<a href='editar/editarVocero.php?CI=<?php echo $Cedula ?>&id=<?php echo $idCC ?>&V=<?php echo $id_vocero ?>' class='enlace'> Editar </a> 
									<button class='btnEliminar' onclick='Eliminar();'> Eliminar </button>
								</td>

								<script>

										function Eliminar(){
											let pregunta = confirm('¿Estas seguro que deseas eliminar este registro?');
											if(pregunta == true){
												window.location.href = 'eliminar/eliminarVocero.php?CI=$Cedula&id=$idCC'
											}
										}

								</script>

							</tr>
						<?php
							}
						?>

				</table>
			</div>
	<?php
			
		}else{
			echo 
				"<div class='contenedor4'>	
					<div class='cabeceraVocero'>
						<h3> Voceros: Unidad Administrativa y Financiera</h3> 
					</div>
					<table>

					<thead>
						<tr>
							
							<td>Nombres</td>
							<td>Apellidos</td>
							<td>Cedula</td>
							<td>Tlf</td>
							<td>Unidad</td>
							<td>Comite</td>
							<td>Tipo</td>
							<td>Votos</td>
							<td> Acciones </td>

						</tr>
					</thead>
					</tr>		

				</table>
			</div>";
		}
		
	?>

	<?php
		// ************************************* UNIDAD CONTRALORIA SOCIAL ************************************************
		//Seleccionamos los voceros de la tabla de voceros
		$sqlVoceros = "SELECT * FROM voceros WHERE id_cc = $idCC AND Unidad = 'Unidad de Contraloria Social Comunal' ORDER BY Votos ASC LIMIT 10 ";
		$traerVoceros = mysqli_query($conexion, $sqlVoceros);
		$totalVoceros = $traerVoceros->num_rows;

		//En caso de no existir registros de algun vocero
		if($totalVoceros > 0){

			echo "

				<div class='contenedor4'>	
					<div class='cabeceraVocero'>
						<h3> Voceros: Unidad Contraloria Social </h3> 
					</div>
					<table>

					<thead>
						<tr>
							
							<td>Nombres</td>
							<td>Apellidos</td>
							<td>Cedula</td>
							<td>Tlf</td>
							<td>Unidad</td>
							<td>Comite</td>
							<td>Tipo</td>
							<td>Votos</td>
							<td> Acciones </td>

						</tr>
					</thead>

			";

			//si existen voceros se inicia el bucle que trae todos las filas dela tabla
			while($Voceros = mysqli_fetch_assoc($traerVoceros)){

				$id_vocero = $Voceros['id_vocero'];
				$Nombre = $Voceros['Nombre'];
				$Apellido = $Voceros['Apellido'];
				$Cedula = $Voceros['Cedula'];
				$Tlf = $Voceros['Tlf'];
				$Comite = $Voceros['Comite'];
				$Votos = $Voceros['Votos'];
				$Unidad = $Voceros['Unidad'];
				$Tipo = $Voceros['Tipo'];

				?>
							<tr>
								<td> <?php echo $Nombre ?></td>
								<td> <?php echo $Apellido ?></td>
								<td> <?php echo $Cedula ?></td>
								<td> <?php echo $Tlf ?></td>
								<td> <?php echo $Unidad ?></td>
								<td> <?php echo $Comite ?></td>
								<td> <?php echo $Tipo ?></td>
								<td> <?php echo $Votos ?></td>
								<td> 
									<a href='editar/editarVocero.php?CI=<?php echo $Cedula ?>&id=<?php echo $idCC ?>&V=<?php echo $id_vocero ?>' class='enlace'> Editar </a> 
									<button class='btnEliminar' onclick='Eliminar();'> Eliminar </button>
								</td>

								<script>

										function Eliminar(){
											let pregunta = confirm('¿Estas seguro que deseas eliminar este registro?');
											if(pregunta == true){
												window.location.href = 'eliminar/eliminarVocero.php?CI=$Cedula&id=$idCC'
											}
										}

								</script>

							</tr>
						<?php
							}
						?>

				</table>
			</div>
	<?php
			
		}else{
			echo 
				"<div class='contenedor4'>	
					<div class='cabeceraVocero'>
						<h3> Voceros: Unidad Contraloria Social </h3> 
					</div>
					<table>

					<thead>
						<tr>
							
							<td>Nombres</td>
							<td>Apellidos</td>
							<td>Cedula</td>
							<td>Tlf</td>
							<td>Unidad</td>
							<td>Comite</td>
							<td>Tipo</td>
							<td>Votos</td>
							<td> Acciones </td>

						</tr>
					</thead>
					</tr>		

				</table>
			</div>";
		}
		
	?>

	<?php
		// ************************************* UNIDAD ELECTORAL ************************************************
		//Seleccionamos los voceros de la tabla de voceros
		$sqlVoceros = "SELECT * FROM voceros WHERE id_cc = $idCC AND Unidad = 'Comision Electoral' ORDER BY Votos ASC LIMIT 10 ";
		$traerVoceros = mysqli_query($conexion, $sqlVoceros);
		$totalVoceros = $traerVoceros->num_rows;

		//En caso de no existir registros de algun vocero
		if($totalVoceros > 0){

			echo "

				<div class='contenedor4'>	
					<div class='cabeceraVocero'>
						<h3> Voceros: Unidad Electoral </h3> 
					</div>
					<table>

					<thead>
						<tr>
							
							<td>Nombres</td>
							<td>Apellidos</td>
							<td>Cedula</td>
							<td>Tlf</td>
							<td>Unidad</td>
							<td>Comite</td>
							<td>Tipo</td>
							<td>Votos</td>
							<td> Acciones </td>

						</tr>
					</thead>

			";

			//si existen voceros se inicia el bucle que trae todos las filas dela tabla
			while($Voceros = mysqli_fetch_assoc($traerVoceros)){

				$id_vocero = $Voceros['id_vocero'];
				$Nombre = $Voceros['Nombre'];
				$Apellido = $Voceros['Apellido'];
				$Cedula = $Voceros['Cedula'];
				$Tlf = $Voceros['Tlf'];
				$Comite = $Voceros['Comite'];
				$Votos = $Voceros['Votos'];
				$Unidad = $Voceros['Unidad'];
				$Tipo = $Voceros['Tipo'];

				?>
							<tr>
								<td> <?php echo $Nombre ?></td>
								<td> <?php echo $Apellido ?></td>
								<td> <?php echo $Cedula ?></td>
								<td> <?php echo $Tlf ?></td>
								<td> <?php echo $Unidad ?></td>
								<td> <?php echo $Comite ?></td>
								<td> <?php echo $Tipo ?></td>
								<td> <?php echo $Votos ?></td>
								<td> 
									<a href='editar/editarVocero.php?CI=<?php echo $Cedula ?>&id=<?php echo $idCC ?>&V=<?php echo $id_vocero ?>' class='enlace'> Editar </a> 
									<button class='btnEliminar' onclick='Eliminar();'> Eliminar </button>
								</td>

								<script>

										function Eliminar(){
											let pregunta = confirm('¿Estas seguro que deseas eliminar este registro?');
											if(pregunta == true){
												window.location.href = 'eliminar/eliminarVocero.php?CI=$Cedula&id=$idCC'
											}
										}

								</script>

							</tr>
						<?php
							}
						?>

				</table>
			</div>
	<?php
			
		}else{
			echo 
				"<div class='contenedor4'>	
					<div class='cabeceraVocero'>
						<h3> Voceros: Unidad Electoral </h3> 
					</div>
					<table>

					<thead>
						<tr>
							
							<td>Nombres</td>
							<td>Apellidos</td>
							<td>Cedula</td>
							<td>Tlf</td>
							<td>Unidad</td>
							<td>Comite</td>
							<td>Tipo</td>
							<td>Votos</td>
							<td> Acciones </td>

						</tr>
					</thead>
					</tr>		

				</table>
			</div>";
		}
		
	?>



		<a href="ConcejoC.php" class="btn-Regresar"> Volver </a>
		
	</div>


	<dialog id="modal">

	<!-- Formulario para agregar un nuevo vocero -->	
	<div class="formVoceros">
		<form  method="POST" action="<?php $_SERVER['PHP_SELF']; ?>" >
			<h2 class="tituloFormV"> Registrar vocero </h2>

			<input type="hidden" name="id_cc" value="<?php echo $idCC ?>">

			<p>
				<label for="Nombre"> Nombres: </label>
				<input type="text" name="name" id="Nombre" placeholder="Ej: Juan, Jose..." required>
			</p>
			<p>
				<label for="Apellido"> Apellidos: </label>
				<input type="text" name="apellido" id="Apellido" placeholder="Ej: Perez, Silva..." required>
			</p>
			<p>
				<label for="CI"> Cedula: </label>
				<input type="text" name="cedula" id="CI" placeholder="Ej 98.765.432" required>
			</p>
			<p>
				<label for="Tlf"> Telefono: </label>
				<input type="text" name="tlf" id="Tlf" placeholder="Ej: 0414 123 4567" required>
			</p>
			<p>
				Unidad: 
				<select name="unidad" id="Unidades" required> 
					<option disabled selected hidden> Seleccione: </option>
					<option value="Unidad Ejecutiva"> Unidad Ejecutiva </option>
					<option value="Unidad Administrativa y financiera"> Unidad Administrativa y financiera </option>
					<option value="Unidad de Contraloria Social Comunal"> Unidad de Contraloria Social Comunal </option>
					<option value="Comision Electoral"> Comision Electoral </option>
				</select>
			</p>
			<p>
				<label for="Votos"> Votos: </label>
				<input type="text" name="votos" id="Votos" required>
			</p>

			<p id="Campo-comites">
				Comite: 
				<select name="comite" id="comites" > 
					<option disabled selected hidden> Seleccione: </option>
					<option value="Alimentacion y Defensa al consumidor"> Alimentacion y Defensa al consumidor </option>
					<option value="Comunitario de personas con discapacidad"> Comunitario de personas con discapacidad </option>
					<option value="Economia Comunal"> Economia Comunal </option>
					<option value="Ecosocialismo"> Ecosocialismo </option>
					<option value="Educacion, Cultura y formacion ciudadana"> Educacion, Cultura y formacion ciudadana </option>
					<option value="Familia e igualdad de genero"> Familia e igualdad de genero </option>
					<option value="Mesa tecnica de agua"> Mesa tecnica de agua </option>
					<option value="Medios alternativos comunitarios"> Medios alternativos comunitarios </option>
					<option value="Mesa tecnica de energia y gas"> Mesa tecnica de energia y gas </option>
					<option value="Planificacion comunal"> Planificacion comunal </option>
					<option value="Proteccion social de niños, niños y adolescentes"> Proteccion social de niños, niños y adolescentes</option>
					<option value="Recreacion y deporte"> Recreacion y deporte </option>
					<option value="Salud integral"> Salud integral </option>
					<option value="Seguridad y defensa integral"> Seguridad y defensa integral </option>
					<option value="Tierra urbana"> Tierra urbana </option>
					<option value="Transporte"> Transporte </option>
					<option value="Turismo"> Turismo </option>
					<option value="Vivienda y habitad"> Vivienda y habitad </option>
					<option value="Ambiente y demarcacion de tierra en los habitad indigenas"> Ambiente y demarcacion de tierra en los habitad indigenas </option>
					<option value="Medicina tradicional indigena"> Medicina tradicional indigena </option>
					<option value="Educacion propia, educacion intercultural bilingue e idiomas indigenas"> Educacion propia, educacion intercultural bilingue e idiomas indigenas </option>
					<option value="Otro"> Otro </option>
				</select>
			</p>

			<p id="NuevoComite">
				<label for="NuevoC"> Nuevo comite: </label>
				<input type="text" name="Cnuevo" id="NuevoC" >
			</p>

				<script type="text/javascript">
					
					document.getElementById('comites').addEventListener('change', function(){

						if(this.value == 'Otro'){
							document.getElementById('NuevoComite').style.display = 'block' ;
						}else{
							document.getElementById('NuevoComite').style.display = 'none' ;
						}

					});

					document.getElementById('Unidades').addEventListener('change', function(){

						if(this.value == 'Unidad Ejecutiva'){
							document.getElementById('Campo-comites').style.display = 'block' ;
						}else{
							document.getElementById('Campo-comites').style.display = 'none' ;
						}

					});

				</script>

			<p>
				Tipo: 
				<select name="tipo" required> 
					<option disabled selected hidden> Seleccione: </option>
					<option value="Principal"> Principal </option>
					<option value="Suplente"> Suplente </option>
				</select>
			</p>
			
			<button class="RegistrarVocero" name="RegistrarVocero"> Registrar </button>
		</form>

		<button id="btnVolver"> Volver </button>
	</div>
	</dialog>


	<dialog id="modalActa">
	<!-- Formulario para actualizar acta de concejos comunales -->	
	<div class="formActa">
		<form  method="POST" action="<?php $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
			<h2 class="tituloFormActa"> Actualizar acta </h2>

			<input type="hidden" name="id_cc" value="<?php echo $idCC ?>">

			<p>
				<span> Acta de concejo: </span>
				<input type="file" name="acta" required>
			</p>
			
			<button class="ActualizarActa" name="ActualizarActa"> Actualizar acta </button>
		</form>

		<button id="btnVolverActa"> Volver </button>
	</div>
	</dialog>

	<script type="text/javascript" src="js/modal.js"></script>
	<script type="text/javascript" src="js/modalActa.js"></script>

</body>
</html>