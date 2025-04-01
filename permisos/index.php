<?php

include "conect.php";
$con = conectar();

session_start();
if(isset($_SESSION['id_usuario']))
{
	header("Location: menu.php");
}

if(!empty($_POST))
{
	$user = $_POST["usuario"];
	$pass = $_POST["contraseña"];
	$passEncrip = sha1($pass);

	$verificar_usuario = $con->prepare("SELECT id FROM usuarios WHERE Usuario = ? AND Contraseña = ? ");
	$verificar_usuario->bind_param("ss", $user, $passEncrip);
	$verificar_usuario->execute();
	$verificar_usuario->store_result();
	$filas = $verificar_usuario->num_rows;
	$verificar_usuario->close();

	if ($filas > 0) {
		$sql = $con->prepare("SELECT * FROM usuarios");
		$sql->execute();
		$datos = $sql->get_result();
		$sql->close();
		$fila = $datos->fetch_assoc();
		$_SESSION["id_usuario"] = $fila["id"];
		header("Location: menu.php");
	}else{
		echo "<script> 
					alert('Usuario y/o contraseña invalidos');
					window.location = 'index.php';
			  </script>";

	}
}


?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/estiloIndex.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans&family=Roboto:wght@100&display=swap" rel="stylesheet">
	<title> Permisos </title>
</head>
<body>
	
	<div class="contenedor">
		<form class="formLogin" action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">

			<img src="img/logoNuevo.jpg">

			<h2> Permisos y Solvencias </h2>
			
				<label for="Usuario"> </label>
				<input type="text" name="usuario" id="Usuario" placeholder="Usuario" class="input-camp">
		
				<label for="Contraseña"> </label>
				<input type="password" name="contraseña" id="Contraseña" placeholder="Contraseña" class="input-camp">
			
				<input type="submit" name="Entrar" value="Entrar" class="input-camp"> 
			
		</form>
	</div>
</body>
</html>