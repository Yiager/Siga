<?php
//incluir variable de conexion y variable de sesion del usuario
include '../head.php';

//identificador de el producto
$id = $_GET['id'];

//conexion a la base de datos para elimninar el producto
$sqlVerificar = $conexion->prepare("SELECT 
					*
				 FROM 
				 	inventario
				 WHERE 
				 	EXISTS
				 	(
				 		SELECT 
				 			idProducto.*,
                        	entradadetalle.*, 
							salidadetalle.*
				 		FROM 
				 			productos 
				 			INNER JOIN salidadetalle ON salidadetalle.Objeto = productos.idProducto
				 			INNER JOIN entradadetalle ON entradadetalle.CodigoProducto = productos.idProducto
				 		WHERE 
				 			entradadetalle.CodigoProducto = ?
				 			OR salidadetalle.Objeto = ?
				 			OR inventario.Producto = ?
				 	) ");
$sqlVerificar->bind_param("iii", $id, $id, $id);
$sqlVerificar->execute();
$datos = $sqlVerificar->get_result()
$sqlVerificar->close();

$producto = $datos->fetch_assoc();

//verificar que no se este usando la clave de producto
if($producto == null){
	$sql = $conexion->prepare("DELETE FROM productos WHERE idProducto = ? ");
	$sql->bind_param("i", $id);
	$sql->execute();
	$sql->close();
	
	echo "<script>
			alert('¡Eliminado exitosamente!');
			window.location.href = '../Productos.php';
		</script>";
}else{
	echo "<script> 
			alert('No se puede eliminar, clave en uso');
			window.location.href = '../Productos.php';
		</script>";
}

?>

