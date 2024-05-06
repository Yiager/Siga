<?php

include("conect.php");

$conexion = conectar();

session_start();
if(!isset($_SESSION['idUser'])){
	header("location: index.php");
}

$idSesion = $_SESSION['idUser'];

$usuarioInfo = "SELECT Nombre, Usuario, tipo FROM usuarios WHERE id = '$idSesion'";
$respuestaInfo = mysqli_query($conexion, $usuarioInfo);

$info = mysqli_fetch_assoc($respuestaInfo);

$nombreUser = $info['Nombre'];
$UserName = $info['Usuario'];

$fechaActual = date("d/m/Y");

if(isset($_POST['Registrar'])){

	$nombre_CC = $_POST['nombre'];
	$situr = $_POST['situr'];
	$situr_nuevo = $_POST['siturN'];
	$estado = $_POST['estado'];
	$municipio = $_POST['municipio'];
	$parroquia = $_POST['parroquia'];
	$tipo = $_POST['TipoConcejo'];
	$comuna = $_POST['nombreCom'];
	$fmayores = $_POST['fmayores'];
	$fmenores = $_POST['fmenores'];
	$hmayores = $_POST['hmayores'];
	$hmenores = $_POST['hmenores'];
	$habitantes = $fmayores + $fmenores + $hmayores + $hmenores;
	$situacion = "";
	$Latitud = $_POST['Latitud'];
	$Longitud = $_POST['Longitud'];
	$elecciones = $_POST['fechaVoce'];
	$vencimiento = date("Y-m-d",strtotime($elecciones."+ 3 year"));
	$participantes = $fmayores + $hmayores;

	if($elecciones < $fechaActual){
		$situacion = "Vencida";
	}else{
		$situacion = "Vigente";
	}

	$cuaderno = $_POST['cuaderno'];
	$actaNombre = $_FILES["acta"]['name'];
	$actaTmp = $_FILES["acta"]['tmp_name'];
	$NombreActa = str_replace(' ', '_', $actaNombre);

	if(file_exists('./actas/')){
		move_uploaded_file($actaTmp, './actas/'.$NombreActa);
	}

	$rutaActa = '/actas/'.$NombreActa;
	$verificarConcejo = "SELECT nombre_cc FROM ccomunal WHERE nombre_cc = '$nombre_CC' ";
	$respuestaConcejo = mysqli_query($conexion, $verificarConcejo);

	if($verificarConcejo > 0){

		echo "<script> 
				alert('El concejo comunal ya existe!');
				back();
			  </script>";

	}else{

		$InsertarConcejo = "INSERT INTO 
									ccomunal(nombre_cc, situr, situr_nuevo, estado, municipio, parroquia, tipo_cc, comuna, situacion, habitantes, prop_elecciones, vencimiento, cuaderno, participantes, acta, Mmayores, Mmenores, Hmayores, Hmenores) 
							VALUES
								(
									'$nombre_CC', 
									'$situr', 
									'$situr_nuevo', 
									'$estado', 
									'$municipio', 
									'$parroquia', 
									'$tipo', 
									'$comuna', 
									'$situacion', 
									'$habitantes',
									'$elecciones', 
									'$vencimiento', 
									'$cuaderno', 
									'$participantes', 
									'$rutaActa',
									'$fmayores',
									'$fmenores',
									'$hmayores',
									'$hmenores'
								) ";

		$RegistrarCC = mysqli_query($conexion, $InsertarConcejo);

		echo "
				<script>
						alert('Concejo comunal registrado Exitosamente!');
						window.location = 'ConcejoC.php'
				</script>
		";
	}
}
$registros_por_pagina = 2;

//Datos para el paginador de la tabla (Contar registros)
$sqlTotalCC = "SELECT * FROM ccomunal";
$respuestaTotal = mysqli_query($conexion, $sqlTotalCC);
$registrosTotal = $respuestaTotal->num_rows;

$paginas = ceil($registrosTotal/$registros_por_pagina);

//Variable que indica desde que registro se debe iniciar en cada pagina
$Iniciar_desde = ($_GET['pagina']-1)*$registros_por_pagina;


if(!$_GET){
	header('Location: ConcejoC.php?pagina=1');
}

if($_GET['pagina']>$paginas || $_GET['pagina'] <= 0){
	header('Location: ConcejoC.php?pagina=1');

}




?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/estiloMenu.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300&family=Roboto+Condensed:wght@300&display=swap" rel="stylesheet">
	
	<title> Concejos comunales </title>
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
	<div id="sidebar" >
		<div class="toggle-btn">
			<span>&#9776;</span>
		</div>
		<ul>
			<li>
				<img src="img/user.png" alt="logo" class="logo">
			</li>
			<li> <a href="menu.php"> Menu principal </a> </li>
			<li> <a href="ConcejoC.php"> Registros </a> </li>

			<?php
				if($info['tipo'] != 3){
					echo "<li style='display:none;'> <a  href='User.php'> Usuarios </a> </li>";
				}else{
					echo "<li> <a href='User.php'> Usuarios </a> </li>";
				}
			?>
			<li> <a href="mensajeria.php"> Mensajes </a> </li>
			<li> <a href="ayuda.php"> Ayuda </a> </li>
			<li> <a href="salir.php"> Salir </a> </li>
		</ul>
	</div>

	<div class="contenedor">
		<div class="Form FormCC" id="FormCC">
			<h2 class="tituloForm"> Registro nuevo</h2>	
			<form method="POST" action="<?php $_SERVER["PHP_SELF"] ?>" id="formConcejoC" enctype="multipart/form-data" >
			<p>	
				<label for="NombreC"> Concejo Comunal: </label>
				<input type="text" name="nombre" class="input-camp" id="NombreC" required>
			</p>
			<p>
				<label for="Situr"> Codigo Situr: </label>
				<input type="text" name="situr" class="input-camp" id="Situr" required>
			</p>
			<p>
				<label for="SiturN"> Codigo Situr Nuevo: </label>
				<input type="text" name="siturN" class="input-camp" id="SiturN" required>
			</p>
			<p>
				<label for="Estado"> Estado: </label>
				<input type="text" name="estado" class="input-camp" id="Estado" required>
			</p>
			<p>
				<label for="Municipio"> Municipio: </label>
				<input type="text" name="municipio" class="input-camp" id="Municipio" required>
			</p>
			<p>
				<label for="Parroquia"> Parroquia: </label>
				<input type="text" name="parroquia" class="input-camp" id="Parroquia" required>
			</p>
			<p>
				Tipo de Concejo comunal: 
				<select name="TipoConcejo" class="input-camp" id="TipoConcejo" required> 
					<option value="" disabled selected hidden> Seleccione </option>
					<option value="Rural"> Rural </option>
					<option value="Urbano"> Urbano </option>
					<option value="Mixto"> Mixto </option>
					<option value="Indigena"> Indigena </option>
				</select>
			</p>	
			<p>
				<label for="NombreCom"> Nombre de la comuna: </label>
				<input type="text" name="nombreCom" class="input-camp" id="NombreCom" required>
			</p>
			<p>
				<label for="Fmayores"> Mujeres + 15: </label>
				<input type="text" name="fmayores" class="input-camp" id="Fmayores" required>
			</p>	
			<p>
				<label for="Fmenores"> Mujeres - 15: </label>
				<input type="text" name="fmenores" class="input-camp" id="Fmenores" required>
			</p>	
			<p>
				<label for="Hmayores"> Hombres + 15: </label>
				<input type="text" name="hmayores" class="input-camp" id="Hmayores" required>
			</p>	
			<p>
				<label for="Hmenores"> Hombres - 15: </label>
				<input type="text" name="hmenores" class="input-camp" id="Hmenores" required>
			</p>	
			<p>
				<label for="FechaVoce"> Fecha elecciones de vocerias del CC: </label>
				<input type="date" name="fechaVoce" class="input-camp" id="FechaVoce"  required>
			</p>
			<p>
				<label for="Cuaderno"> Cuaderno: </label>
				<input type="text" name="cuaderno" class="input-camp" onchange="cambio()" id="Cuaderno" required>
			</p>	
			<p>
				<label for="Lat"> Latitud: </label>
				<input type="text" name="Latitud" class="input-camp" id="Lat" required>
			</p>	
			<p>
				<label for="Long"> Longitud: </label>
				<input type="text" name="Longitud" class="input-camp" id="Long" required>
			</p>	
			<p>
				<span> Acta de concejo: </span>
				<input type="file" name="acta" accept="application/pdf" required>
			</p>	

			<button name="Registrar" id="btnRegistrarCC" class="btn-Enviar bloque">
				Registrar
			</button>
			<script>
				
			function cambio(){
				let mujeresM = Number(document.getElementById('Fmayores').value);
				let hombresM = Number(document.getElementById('Hmayores').value);
				let cuaderno = Number(document.getElementById('Cuaderno').value);
				let btn = document.getElementById('btnRegistrarCC');
				let Total = mujeresM + hombresM;
				let porcentaje = (30 * Total)/100;

				function habilitar(){
					btn.classList.remove("btn-Inhabilitado");
					btn.classList.toggle("btn-Enviar");
				}

				function deshabiitar(){
					btn.classList.remove("btn-Enviar");
					btn.classList.toggle("btn-Inhabilitado");
				}

				if(cuaderno < porcentaje){
					deshabiitar();
					alert("El numero de participantes es menor al registrado en el cuaderno");
				}else{
					habilitar();
				
				}
			}

			</script>

			<button class="btn-Volver bloque" id="Form-Volver"> 
				Volver 
			</button>
			
			</form>
		</div>
	</div>

	<div class="tablaCon" id="TablaCC">
		
		<div class="cabecera">
			<h2 class="tituloUsers"> Concejos comunales </h2>
			<button class="btn-incluir" id="botonTablaCC"> Incluir </button>
		</div>
		<table>
			<thead>
				<th> Concejo Comunal </th>
				<th> Codigo Situr </th>
				<th> Estado </th>
				<th> Municipio </th>
				<th> Situacion </th>
				<th> Acciones </th>
			</thead>
			<tbody>
				<tr>
					<?php
						//Datos de la tabla

						$sqlCcomunal = "SELECT * FROM ccomunal LIMIT  $Iniciar_desde, $registros_por_pagina";
						$sqlRespuesta = mysqli_query($conexion, $sqlCcomunal);

						while($traerCC = mysqli_fetch_assoc($sqlRespuesta)){

						$idCC = $traerCC['id_cc'];
						$FechaVence = $traerCC['vencimiento'];
						$FechaElecciones = $traerCC['prop_elecciones'];
						$PorVencer = date("Y-m-d", strtotime($FechaVence. "- 3 month"));
						$FechaHoy = date("Y-m-d");

					?>
					<td> <a class="enlace" href="DatosConcejo.php?id=<?php echo $idCC; ?>"> <?php echo $traerCC['nombre_cc']; ?> </a> </td>
					<td> <?php echo $traerCC['situr']; ?> </td>
					<td> <?php echo $traerCC['estado']; ?> </td>
					<td> <?php echo $traerCC['municipio']; ?> </td>
					<?php		
						if($FechaHoy >= $FechaElecciones && $FechaHoy < $FechaVence){
							
							if($FechaHoy > $PorVencer && $FechaHoy < $FechaVence){
								echo '<td><abbr title="El concejo comunal esta por vencer"><div class="porVencer"></div></abbr></td>';
							}else{
								echo '<td><abbr title="El concejo comunal esta vigente"><div class="vigente"></div></abbr></td>';
							}

						}else{
							echo '<td><abbr title="El concejo comunal esta vencido"><div class="vencido"></div></abbr></td>';
						}
					?>

					<?php
						if($info['tipo'] == 1){
							echo"
							<td > <a style='display:none;' class='acciones' href='editar/editarCC.php?id=$idCC'> Editar </a> 
							<button style='display:none;' onclick='Eliminar()' class='acciones' > Eliminar </button> </td>";
							
						}else if ($info['tipo'] == 2) {
							echo"
							<td > <a  class='acciones' href='editar/editarCC.php?id=$idCC'> Editar </a> 
							<button style='display:none;' onclick='Eliminar()' class='acciones' > Eliminar </button> </td>";
						}else{
							echo"
							<td> <a class='acciones' href='editar/editarCC.php?id=$idCC'> Editar </a> 
							<button onclick='Eliminar()' class='acciones' > Eliminar </button> </td>";
						}
					?>
					
						<?php
							echo "<script>
										function Eliminar(){
											let pregunta = confirm('¿Estas seguro que deseas eliminar este registro?');
											if(pregunta == true){
												window.location.href = 'eliminar/eliminarCC.php?id=$idCC'
											}
										}
								</script>
							";
						?>
				</tr>
			</tbody>
					<?php
						}
					?>
		</table>

		<ul class="paginador">
			
			<li id="<?php echo $_GET['pagina']==1 ? 'disable' : '' ?>"> 
				<a href="ConcejoC.php?pagina=<?php echo ($_GET['pagina'])-1 ?>"> Anterior </a> </li>

			<?php for($i=0; $i<$paginas; $i++){ ?>
				<li id="<?php echo $_GET['pagina']==$i+1 ? 'active' : '' ?>"> 
					<a href="ConcejoC.php?pagina=<?php echo $i+1 ?>"> <?php echo $i+1 ?> </a></li>
			<?php } ?>

			<li id="<?php echo $_GET['pagina']>=$paginas ? 'disable' : '' ?>"> 
				<a href="ConcejoC.php?pagina=<?php echo ($_GET['pagina'])+1 ?>"> Siguiente </a> </li>

		</ul>

	</div>
	<script type="text/javascript" src="js/sidebar.js"></script>
	<script type="text/javascript" src="js/eventsCC.js"></script>
</body>
</html>