<?php

include("../conect.php");

$conexion = conectar();

session_start();

if(!isset($_SESSION['idUser'])){
	header("Location: index.php");
}
$fechaActual = date("d/m/Y");

$idCC = $_GET['id'];

$sqlCC = "SELECT * FROM ccomunal WHERE id_cc = '$idCC' ";
$respuestaCC = mysqli_query($conexion, $sqlCC);

//Inicio de ciclo para iterar los valores existentes en la base de datos y mostrarlos en los respectivos campos del formulario
while($traerCC = mysqli_fetch_assoc($respuestaCC)){

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="../css/estiloMenu.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300&family=Roboto+Condensed:wght@300&display=swap" rel="stylesheet">
	<title> Editar usuarios </title>
</head>
<body style="background-color: black;">

	<div class="contenedor">

		<div class="Form FormEdit">

			<h2 class="tituloForm"> Registro nuevo</h2>
				
			<form method="POST" action="<?php $_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data">

			<input type="hidden" name="id" value="<?php echo $traerCC['id_cc']; ?>" >
				
			<p>	
				<label for="NombreC"> Concejo Comunal: </label>
				<input type="text" name="nombre" class="input-camp" id="NombreC" value="<?php echo $traerCC['nombre_cc']; ?>" required>
			</p>
			<p>
				<label for="Situr"> Codigo Situr: </label>
				<input type="text" name="situr" class="input-camp" id="Situr" value="<?php echo $traerCC['situr']; ?>" required>
			</p>
			<p>
				<label for="SiturN"> Codigo Situr Nuevo: </label>
				<input type="text" name="siturN" class="input-camp" id="SiturN" value="<?php echo $traerCC['situr_nuevo']; ?>" required>
			</p>
			<p>
				<label for="Estado"> Estado: </label>
				<input type="text" name="estado" class="input-camp" id="Estado" value="<?php echo $traerCC['estado']; ?>" required>
			</p>
			<p>
				<label for="Municipio"> Municipio: </label>
				<input type="text" name="municipio" class="input-camp" id="Municipio" value="<?php echo $traerCC['municipio']; ?>" required>
			</p>
			<p>
				<label for="Parroquia"> Parroquia: </label>
				<input type="text" name="parroquia" class="input-camp" id="Parroquia" value="<?php echo $traerCC['parroquia']; ?>" required>
			</p>
			<p>
				Tipo de Concejo comunal: 
				<select name="TipoConcejo" class="input-camp" id="TipoConcejo" required> 
					<option value="<?php echo $traerCC['tipo_cc']; ?>" selected > <?php echo $traerCC['tipo_cc']; ?> </option>
					<option value="Rural"> Rural </option>
					<option value="Urbano"> Urbano </option>
					<option value="Mixto"> Mixto </option>
					<option value="Indigena"> Indigena </option>
				</select>
			</p>	

			<p>
				<label for="NombreCom"> Nombre de la comuna: </label>
				<input type="text" name="nombreCom" class="input-camp" id="NombreCom" value="<?php echo $traerCC['comuna']; ?>" required>
			</p>
			<p>
				<label for="Fmayores"> Mujeres + 15: </label>
				<input type="text" name="fmayores" class="input-camp" id="Fmayores" value="<?php echo $traerCC['Mmayores']; ?>" required>
			</p>	
			<p>
				<label for="Fmenores"> Mujeres - 15: </label>
				<input type="text" name="fmenores" class="input-camp" id="Fmenores" value="<?php echo $traerCC['Mmenores']; ?>" required>
			</p>	
			<p>
				<label for="Hmayores"> Hombres + 15: </label>
				<input type="text" name="hmayores" class="input-camp" id="Hmayores" value="<?php echo $traerCC['Hmayores']; ?>" required>
			</p>	
			<p>
				<label for="Hmenores"> Hombres - 15: </label>
				<input type="text" name="hmenores" class="input-camp" id="Hmenores" value="<?php echo $traerCC['Hmenores']; ?>" required>
			</p>	
			<p>
				<label for="FechaVoce"> Fecha elecciones de vocerias del CC: </label>
				<input type="date" name="fechaVoce" class="input-camp" id="FechaVoce" onchange="Vence()" required>
			</p>
			<p>
				<label for="Cuaderno"> Cuaderno: </label>
				<input type="text" name="cuaderno" class="input-camp" id="Cuaderno" value="<?php echo $traerCC['cuaderno']; ?>" required>
			</p>	
			<p>
				<label for="Lat"> Latitud: </label>
				<input type="text" name="Latitud" class="input-camp" id="Lat" value="<?php echo $traerCC['Latitud']; ?>" required>
			</p>	
			<p>
				<label for="Long"> Longitud: </label>
				<input type="text" name="Longitud" class="input-camp" id="Long" value="<?php echo $traerCC['Longitud']; ?>" required>
			</p>	

			<?php
				} 
			?>

			<button name="Actualizar" class="btn-Enviar bloque">
				Actualizar
			</button>

	
			<a href="../ConcejoC.php" class="btn-Volver bloque">
				Volver
			</a>
			

			</form>

		</div>

	</div>

	<?php
			//al presionar el boton se recogen todos los datos del formulario
			if(isset($_POST['Actualizar'])){

				$idCC = $_POST['id'];
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
					$situacion = "Vigente";
				}else{
					$situacion = "Vencida";
				}

				$cuaderno = $_POST['cuaderno'];
				$actaNombre = $_FILES["acta"]['name'];
				$actaTmp = $_FILES["acta"]['tmp_name'];
				$NombreActa = str_replace(' ', '_', $actaNombre);

				if(file_exists('./proyecto/actas/')){
					move_uploaded_file($actaTmp, '/proyecto/actas/'.$actaNombre);
				}

				$rutaActa = '/actas/'.$actaNombre;
				//se verifica que ningun dato este repetido con otro registro de la base de datos
				$VerificarConcejo = "SELECT id_cc, nombre_cc FROM ccomunal WHERE nombre_cc = '$nombre_CC' AND id_cc != '$idCC' ";
				$verificar = mysqli_query($conexion, $VerificarConcejo);
				$buscarConcejo = $verificar->num_rows;

				if($buscarConcejo > 0){

					echo "<script> 

							alert('El concejo comunal ya existe!');
							back();

					</script>";

				}else{

					//se actualizan los datos en la base de datos
					$ActualizarConcejo = "UPDATE 
											ccomunal 
										SET 
											nombre_cc = '$nombre_CC', 
											situr = '$situr', 
											situr_nuevo = '$situr_nuevo', 
											estado = '$estado', 
											municipio = '$municipio', 
											parroquia = '$parroquia', 
											tipo_cc = '$tipo', 
											comuna = '$comuna', 
											situacion = '$situacion', 
											habitantes = '$habitantes', 
											prop_elecciones = '$elecciones', 
											vencimiento = '$vencimiento', 
											cuaderno = '$cuaderno', 
											participantes = '$participantes', 
											Mmayores = '$fmayores', 
											Mmenores = '$fmenores', 
											Hmayores = '$hmayores', 
											Hmenores = '$hmenores',
											acta = '$rutaActa',
											Latitud = '$Latitud',
											Longitud = '$Longitud'
										WHERE 
											id_cc = '$idCC' ";	

					$ActualizarCC = mysqli_query($conexion, $ActualizarConcejo);

					echo "
							<script>

									alert('Concejo comunal registrado Exitosamente!');
									window.location = '../ConcejoC.php';

							</script>

					";
				}
			}
	?>

</body>
</html>