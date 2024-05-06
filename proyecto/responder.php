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

// identificador del chat
$idChat = $_GET['id'];

//consulta a la base de datos para traer todos los datos que concuerden con el identificador

$SeleccionarChat = "SELECT * FROM chat INNER JOIN usuarios ON usuarios.id = chat.Emisor WHERE ChatID = '$idChat' ";
$respuestaSql = mysqli_query($conexion, $SeleccionarChat);
//Inicio de ciclo para iterar los valores existentes en la base de datos y mostrarlos en los respectivos campos del formulario
while ($traerChat = mysqli_fetch_assoc($respuestaSql)) {
	



if (isset($_POST['responder'])){

	$Fecha = date('Y/m/d');
	$Emisor = $idSesion;
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
				alert('Respuesta Enviada exitosamente');
				window.location.href='mensajeria.php';
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
<body style="background:black;">


	<div class="contenedorResponder">		
		<div class="formNuevoMsj">
			<form  method="POST" action="<?php $_SERVER['PHP_SELF']; ?>" >
				<h2 class="tituloFormMsj"> Responder a: <?php echo $traerChat['Nombre']; ?> </h2>

				<p>Dice: 
					<textarea cols="40" rows="4" readonly> <?php echo $traerChat['Mensaje']; ?> </textarea>
				</p>

				<input type="hidden" name="receptor" value="<?php echo $traerChat['Emisor']; ?>">	
				<?php
					}
				?>
				
				<p>Respuesta: <br>
								
					<textarea name="msjR" id="msjRes" cols="40" rows="4"  required> </textarea>
				</p>
							
				<button class="EnviarMsjR" name="responder"> Responder </button>
			</form>

			<a href="mensajeria.php" class="btnVolverR"> Volver </a>
		</div>
	<div>


</body>
</html>