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
	
	<title> Ayuda </title>
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
				<p> Para comenzar a cargar los datos de los concejos comunales, en la barra de navegacion en la opcion <strong> Registros </strong> lo llevara a la seccion para gestionar los concejos comunales, e ir a la opcion para incliur un nuevo concejo </p>
			</div>
			<div class="ayuda1"></div>
			<div>
				<p> Luego se le desplegara el formulario para ingresar los datos </p>
			</div>
			<div class="ayuda2"></div>
			<div>
				<p> Debe asegurarse de ingresar los datos en todos los campos del formulario, todos son obligatorios </p>
			</div>
			<div class="ayuda3"></div>
			<div>
				<p> Por ultimo al presionar el boton registrar en el formulario, si ingreso todos los datos correctamente se le desplegara el siguiente aviso a continuacion:  </p>
			</div>
			<div class="ayuda4"></div>
			<div>
				<p> <strong> NOTA: </strong> Para los campos numericos (Mujeres u hombres mayores y menores a 15 años deben ser datos numericos, no en letras) Ej: 15, 16, 28. No: quince, veinte, treinta y cinco <br>
				Por ultimo, debe tener en cuenta que el campo <strong> Cuaderno </strong> representa los participantes que asistieron a la votacion del concejo comunal y debe ser <strong> mayor al 30% </strong> de la suma de los participantes (Mujeres y hombres mayores a 15 años) </p>
			</div> 
			<div>
				<p> El mismo procedimiento se cumple para registrar los voceros en sus respectivos concejos comunales </p>
			</div><br>
		</div>

	</div>

	<script type="text/javascript" src="js/sidebar.js"></script>

</body>
</html>