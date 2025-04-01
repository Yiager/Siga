<?php
//incluir variable de conexion y variable de sesion del usuario
include '../head.php';
//identificador del proveedor
$id = $_GET['id'];

//conexion a la base de datos para elimninar el proveedor
$sqlVerificar = $conexion->prepare("SELECT 
					* 
				FROM 
					entradas 
				WHERE 
					EXISTS
					(
						SELECT 
							id
						FROM 
							prove 
						WHERE 
							entradas.Proveedor = ?
					) ");
$sqlVerificar->bind_param("i", $id);
$sqlVerificar->execute();
$datos = $sqlVerificar->get_result();
$sqlVerificar->close();

$proveedor = $datos->fetch_assoc();
//condicion para verificar que la clave del proveedor no esta en uso
if($proveedor == null){
	//eliminar proveedor seleccionado de la base de datos
	$sql = $conexion->prepare("DELETE FROM prove WHERE id = ? ");
	$sql->bind_param("i", $id);
	$sql->execute();
	$sql->close();

	echo "<script> 
			alert('¡Eliminado exitosamente!');
			window.location.href = '../Proveedores.php';
		</script>";
}else{
	echo "<script> 
			alert('No se puede eliminar, clave en uso');
			window.location.href = '../Proveedores.php';
		</script>";
}

?>