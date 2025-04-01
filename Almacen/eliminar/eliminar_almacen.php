<?php
//incluir variable de conexion y variable de sesion del usuario
include '../head.php';

//id del almacen a eliminar
$id = $_GET['id'];

//conexion a base de datos para eliminar el alamcen que no este en uso
$sqlVerificar = $conexion->prepare("SELECT 
					entradas.*, 
					salidas.*, 
					salidadetalle.*, 
					inventario.* 
				 FROM 
				 	entradas 
				 	INNER JOIN salidas ON salidas.Deposito = entradas.Almacen 
				 	INNER JOIN salidadetalle ON salidadetalle.Deposit = entradas.Almacen 
				 	INNER JOIN inventario ON inventario.Almacen = entradas.Almacen 
				 WHERE 
				 	EXISTS
				 	(
				 			SELECT 
				 				* 
				 			FROM 
				 				almacen 
				 			WHERE 
				 				entradas.Almacen = ? 
				 				OR salidas.Deposito = ? 
				 				OR salidadetalle.Deposit = ? 
				 				OR inventario.Almacen = ?
				 	)");
$sqlVerificar->bind_param("iiii", $id, $id, $id, $id);
$sqlVerificar->execute();
$datos = $sqlVerificar->get_result();
$sqlVerificar->close();

$almacen = $datos->fetch_assoc();

//sentencia para verificar que no este en uso el almacen
if($almacen == null){
	$sql = $conexion->prepare("DELETE FROM almacen WHERE id = ? ");
	$sql->bind_param("i", $id);
	$sql->execute();
	$sql->close();
	
	echo "<script> 
			alert('¡Eliminado exitosamente!');
			window.location.href = '../Almacen.php';
		</script>";
}else{
	echo "<script> 
			alert('No se puede eliminar, clave en uso');
			window.location.href = '../Almacen.php';
		</script>";
}


?>

