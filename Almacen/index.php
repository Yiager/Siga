<?php

include("login.php");
//Variable de conexion
$conexion = conectar();

//Verificar identificador de inicio de sesion
session_start();
if(isset($_SESSION['id_usuario'])){
	header("location: Menu.php");
}

//datos del login principal
if(!empty($_POST)){
	$usuario = $_POST['name'];
	$password = $_POST['pass'];
	$password_encriptada = sha1($password);
	$sql = $conexion->prepare("SELECT 
					id 
			 FROM 
			 	usuarios 
			 WHERE 
			 	usuario = ?
			 	AND password = ? ");
	$sql->bind_param("ss", $usuario, $password_encriptada);
	$sql->execute();
	$sql->store_result();
	$rows = $sql->num_rows;
	$sql->close();

		if($rows > 0){
			$sqlS = $conexion->prepare("SELECT id  FROM usuarios");
			$sqlS->execute();
			$row = $sqlS->get_result()->fetch_assoc();
			$sqlS->close();
			$_SESSION['id_usuario'] = $row["id"];
			header("Location: Menu.php");
		}else{

			echo "<script >
					alert('Usuario o Contraseña Incorrectos');
					window.location = 'index.php';
				</script>";
		}
	}

?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" >
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta charset="utf-8">
	<title> SIGA </title>
	<link rel="stylesheet" type="text/css" href="css/estiloIndex.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="icon" href="img/SIGA.png">
	<link href="https://fonts.googleapis.com/css2?family=Kdam+Thmor+Pro&family=Pixelify+Sans&family=Roboto:wght@100&display=swap" rel="stylesheet">
</head>

<body>
	<div class="login" >
		<div class="logo">
			<img src="img/SIGA.png" alt="Logo SIGA" class="logoPrincipal">
		</div>
		<h4 > Sistema de Gestion de Almacen </h4> 
		<!-- Menu de inicio de Sesion -->
		<form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>">
			<!-- *****    Usuario   ******** -->
			<input type="text" name="name" placeholder="Usuario"  required> 
			<!-- *****   Contraseña   ******** -->
			<input type="password" name="pass"  placeholder="Contraseña" required> 
			<!-- *********    Boton   *********** -->
			<p class="botonEnviar">
			<input type="submit" name="enter" value="Entrar"  > 
			</p>
		</form> 		
	</div>
</body>
</html>