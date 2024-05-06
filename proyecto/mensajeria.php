<?php

include("conect.php");

$conexion = conectar();

session_start();
if(!isset($_SESSION['idUser'])){
	header("location: index.php");
}

//identificador del usuario en la base de datos
$idSesion = $_SESSION['idUser'];

//Selecciona datos del usuario
$usuarioInfo = "SELECT Nombre, Usuario, tipo FROM usuarios WHERE id = '$idSesion'";
$respuestaInfo = mysqli_query($conexion, $usuarioInfo);

$info = mysqli_fetch_assoc($respuestaInfo);

$nombreUser = $info['Nombre'];
$UserName = $info['Usuario'];

//Fecha actual
$fechaActual = date("d/m/Y");

//condicion que trae los datos del formulario luego de apretar el boton
if(isset($_POST['enviarMensaje'])){

	$fecha = date('Y/m/d');
	$emisor = $_POST['Emisor'];
	$receptor = $_POST['Receptor'];
	$mensaje = $_POST['Msj'];

	//Inserta los datos en la tabla de la base de datos
	$guardarMensaje = "INSERT INTO 
								chat(Emisor, Receptor, Mensaje, Fecha) 
					   VALUES
					   (
					   		'$emisor',
					   		'$receptor',
					   		'$mensaje',
					   		'$fecha'
						)";

	$InsertarMensaje = mysqli_query($conexion, $guardarMensaje);

	if ($InsertarMensaje > 0) {
		echo "
			<script>
				alert('Mensaje Enviado exitosamente');
			</script>
		";
	}else{
		echo "
			<script>
				alert('Error al enviar mensaje');
			</script>
		";
	}
}

if (isset($_POST['responder'])){

	$Fecha = date('Y/m/d');
	$Emisor = $_POST['emisor'];
	$Receptor = $_POST['receptor'];
	$Mensaje = $_POST['msjR'];


	$guardarRespuesta = "INSERT INTO 
								chat(Emisor, Receptor, Mensaje, Fecha) 
					   VALUES
					   (
					   		'$Emisor',
					   		'$Receptor',
					   		'$Mensaje',
					   		'$Fecha'
						)";

	$InsertarRespuesta = mysqli_query($conexion, $guardarRespuesta);

	if ($InsertarRespuesta > 0) {
		echo "
			<script>
				alert('Mensaje Enviado exitosamente');
			</script>
		";
	}else{
		echo "
			<script>
				alert('Error al enviar mensaje');
			</script>
		";
	}

}

?>


<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/estiloMenu.css">
	<link rel="stylesheet" type="text/css" href="css/mensajeria.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300&family=Roboto+Condensed:wght@300&display=swap" rel="stylesheet">
	
	<title> Mensajeria </title>
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

				//Verifica que el usuario sea administrador segun el campo 'tipo' de la tabla usuarios
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

	<div class="mensajes">

		<div class="recibidos" id="Recibidos">

			<div class="CambiarBandejaR">
				<h3 class="tituloMsj"> Recibidos </h3>
				<button id="NuevoMsj" class="btnMensaje"> Nuevo mensaje </button>
				<button id="BandejaE" class="btnEnviados"> Enviados </button>
			</div>
			
			<table id="miTabla" class="Brecibidos">

			<?php
			//se traen los datos de la tabla de chat de la base de datos que sean destinados al usuario

				$sqlMensajesR = "SELECT * FROM chat INNER JOIN usuarios ON usuarios.id = chat.Emisor WHERE Receptor = $idSesion ";
				$respuestaMsjR = mysqli_query($conexion, $sqlMensajesR);
				while($mensajeR = mysqli_fetch_assoc($respuestaMsjR)){
					$id = $mensajeR['ChatID'];

			?>

				<tr>
					<td>De: <?php echo $mensajeR['Nombre']; ?> </td>
					<td>Dice: <?php echo $mensajeR['Mensaje']; ?> </td>
					<td>Enviado: <?php echo $mensajeR['Fecha']; ?></td>
					<td> <a href="responder.php?id=<?php echo $id ?>" class="responder" name="responder"  id="IdMensajeR" > Responder </a>  </td>
				</tr>
			<?php
				}
			?>
			</table>

		</div>

		<div class="enviados" id="Enviados">

			<div class="CambiarBandejaE">
				<h3 class="tituloMsj"> Enviados </h3>
				<button id="BandejaR" class="btnRecibidos"> Recibidos </button>
			</div>

			<table class="Benviados">
			
			<?php

				//se traen los datos de la tabla de chat de la base de datos que el usuario haya enviado

				$sqlMensajesE = "SELECT * FROM chat INNER JOIN usuarios ON usuarios.id = chat.Emisor WHERE Emisor = $idSesion ";
				$respuestaMsjE = mysqli_query($conexion, $sqlMensajesE);
				while($mensajeE = mysqli_fetch_assoc($respuestaMsjE)){

			?>

				<tr>
					<td class="codigoMsj"> <?php echo $mensajeE['ChatID']; ?> </td>
					<td>De: <?php echo $mensajeE['Nombre']; ?> </td>
					<td>Dice: <?php echo $mensajeE['Mensaje']; ?> </td>
					<td>Enviado: <?php echo $mensajeE['Fecha']; ?></td>
				</tr>

			<?php
				}
			?>

			</table>
		</div>

		<!-- Modal con el formulario para enviar un msj nuevo -->

		<dialog id="modal">
			
			<div class="formNuevoMsj">
				<form  method="POST" action="<?php $_SERVER['PHP_SELF']; ?>" >
					<h2 class="tituloFormMsj"> Mensaje Nuevo </h2>
					<input type="hidden" name="Emisor" value="<?php echo $idSesion; ?>">
					<p>
						Para:
						<select name="Receptor">  

							<?php 
								$sqlUserMensaje = "SELECT id, Nombre, Usuario FROM usuarios WHERE id != $idSesion";
								$respuestaMsj = mysqli_query($conexion, $sqlUserMensaje);
								while($user = mysqli_fetch_assoc($respuestaMsj)){
							?>

							<option disabled selected hidden> Seleccione: </option>
							<option value="<?php echo $user['id']; ?>"> <?php echo $user['Nombre'] .": ". $user['Usuario'] ?> </option>

							<?php
								}
							?>

						</select>
					</p>
					<p>Mensaje: <br>
						<textarea name="Msj" id="msj" cols="120" rows="8"  required> </textarea>
					</p>
					<button class="EnviarMsj" name="enviarMensaje"> Enviar </button>
				</form>
				<button id="btnVolver"> Volver </button>
			</div>
		</dialog>

	</div>

	<script type="text/javascript" src="js/sidebar.js"></script>
	<script type="text/javascript" src="js/mensajeria.js"></script>

</body>
</html>