<?php 

include '../head.php';

	$id = $_GET['id'];

	$sql = $conexion->prepare("SELECT * FROM usuarios WHERE id = ? ");
	$sql->bind_param("i", $id);
	$sql->execute();
	$datos = $sql->get_result();
	$sql->close();

	while ($mostrar1 = $datos->fetch_assoc()) {
?>

<!DOCTYPE html>
<html>
	<title> Usuario: Editar </title>
<body class="fondo">

<div id="formEditar" >
	<form  action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" >	
		<h3 class="bloque"> Editar Usuario </h3>
		<input type="hidden" name="textid" value="<?= $mostrar1['id'] ?>">			
		<div >
			<label for="Nombre"> Nombre y Apellido: </label> 
			<input type="text" name="nombre" id="Nombre" value="<?= $mostrar1['nombre'] ?>"> 
		</div>  
		<div class="input form-group">
			<label for="Correo"> Correo electronico </label>
			<input type="email" name="correo" id="Correo" value="<?= $mostrar1['correo'] ?>"> 
		</div>  
		<div class="input form-group">
			<label for="usuario"> Nombre de usuario: </label>
			<input type="text" name="Usuario" id="usuario" value="<?= $mostrar1['usuario'] ?>"> 
		</div>  
		<div class="input form-group">
			<label for="tipo"> Tipo de usuario: </label> 
			<input type="text" name="Tipo" id="tipo" value="<?= $mostrar1['Tipo'] ?>"> 
		</div>  
		<div class="bloque">
			<button class="agregar" name="actualizar"> Actualizar </button>
			<button class="Volver" id="Volver" name="Volver" > Volver </button>
		</div>
	</form>
	<?php } ?>
</div>    

</body>
</html>
<?php

if(isset($_POST['actualizar'])){

	$idUser = $_POST['textid'];
	$Nombre = $_POST['nombre'];
	$Correo = $_POST['correo'];
	$Usuario = $_POST['Usuario'];
	$Cuenta = $_POST['Tipo'];

	$sql2 = $conexion->prepare("UPDATE 
				usuarios 
			 SET 
			 	nombre = ?, 
			 	correo = ?, 
			 	usuario = ?, 
			 	Tipodecuenta = ?
			 WHERE  
			 	id = ? ");
	$sql2->bind_param("ssssi", $Nombre, $Correo, $Usuario, $Cuenta, $idUser);
	$sql2->execute();
	$sql2->close();
		
	echo "<script>
			alert('Actualizacion Exitosa');
			window.location = '../usuarios.php';
		</script>";
}

if(isset($_POST['Volver'])){
	header('location:../usuarios.php');
}

?>
