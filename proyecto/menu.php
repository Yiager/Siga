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
	
	<title> Menu principal </title>
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

	<div class="bloqueAyuda">
		
		<div class="bloque1">
			<div>
				<p> Bienvenido al Sistema de Gestion de Concejos Comunales <strong> SIGESCO </strong>, para navegar por el sistema comencemos por abrir la barra de navegacion presionando el boton de menu como se ve en la imagen acontinuacion </p>
			</div>
			<div class="img1"></div>
			<div>
				<p> Ahora solo selecciona a la pagina que deseas para comenzara trabajar. Cualquier duda contacta al administrador del sistema o tambien puedes revisar nuestra seccion de ayuda para aclarar dudas. Buena Gestion! </p>
			</div>
			<div class="img2"></div>
		</div>

	</div>

	<script type="text/javascript" src="js/sidebar.js"></script>

</body>
</html>