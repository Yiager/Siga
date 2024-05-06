<?php

//Archivo que contiene variables de conexion a la DB
include("conect.php");

//Variable de conexion con la base de datos
$conexion = conectar();

//Variable de inicio de sesion
session_start();

if(isset($_SESSION['idUser'])){
	header("location: menu.php");
}


//condicional que verificar si los elementos enviados con post estan no vacios
if(!empty($_POST)){
	$correo = $_POST["correo"];
	$pass = $_POST["pass"];
	$passEncript = sha1($pass);

	//Busca en la base de datos si existe el correo y la contraseña
	$sqlUser = "SELECT id FROM usuarios WHERE correo = '$correo' AND pass = '$passEncript' ";
	$respuesta = mysqli_query($conexion, $sqlUser);
	
	$fila = $respuesta->num_rows;
		if($fila > 0){
			$row = $respuesta->fetch_assoc();
			$_SESSION['idUser'] = $row["id"];
			header("Location: menu.php");
		}else{
			echo "<script>
				alert('Usuario o contraseña incorrectos');
				window.location = 'index.php';
			</script>
			";
		}

}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/estilo1.css">
	<!-- *********** GOOGLE  FOTNS  *************-->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300&family=Roboto+Condensed:wght@300&display=swap" rel="stylesheet">
	<title> Login </title>
</head>
<body>

	<!--****************************** Contenedor principal ********************************-->

	<div class="formulario" >

		<img src="img/LogoAlcaldia.png" alt="logoPrincipal" class="logo">

		<h1 class="titulo"> SIGESCO </h1>
		<h3 class="subTitulo"> Sistema de Gestion de Concejos Comunales</h3>

		<!--****************************** Formulario de inicio de sesion ********************************-->

		<form method="POST" action="<?php $_SERVER['PHP_SELF'] ?>">
		
		<!--****************************** Usuario ********************************-->
			<label for="Correo"> Correo electronico </label>
			<input type="text" name="correo" id="Correo" class="input-camp" placeholder="Correo"> 

		<!--****************************** Contraseña ********************************-->
			<label for="Pass"> Contraseña </label>
			<input type="password" name="pass" id="Pass" class="input-camp" placeholder="Contraseña"> 

		<!--****************************** Boton para validar entrada ********************************-->
			<input type="submit" name="enviar" value="Entrar" class="btn">
		</form>

	<div>

</body>
</html>