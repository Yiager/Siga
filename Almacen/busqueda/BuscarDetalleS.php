<?php

include "../login.php";
$conexion = conectar();

session_start();
if (!isset($_SESSION['id_usuario'])) {
	header("Location:index.php");
}

$id = "IdSalida";

$tabla = "salidadetalle";

$buscador = isset($_POST['buscar']) ? $conexion->real_escape_string($_POST['buscar']) : null;
$Codigo = isset($_POST['Codigo']) ? $conexion->real_escape_string($_POST['Codigo']) : 0;
$EstadoS = isset($_POST['Estado']) ? $conexion->real_escape_string($_POST['Estado']) : null;
$AlmacenS = isset($_POST['Almacen']) ? $conexion->real_escape_string($_POST['Almacen']) : 0;

$where = "";

if ($buscador != null) {
	$where.= "WHERE salidadetalle.IdSalida LIKE '%$buscador%' 
											OR salidadetalle.CodigoSalida LIKE '%$buscador%' 
											OR salidadetalle.Cantidad LIKE '%$buscador%' 
											OR salidadetalle.Precio LIKE '%$buscador%'
											OR salidadetalle.Total LIKE '%$buscador%'
											OR salidadetalle.Observacion LIKE '%$buscador%'
											OR productos.Articulo LIKE '%$buscador%'
											OR almacen.Almacen LIKE '%$buscador%' 
									 		AND salidadetalle.CodigoSalida = $Codigo ";
}else{
	$where.= "WHERE salidadetalle.CodigoSalida = $Codigo ";
}

$limit = 8;

$pagina = isset($_POST['pagina']) ? $conexion->real_escape_string($_POST['pagina']) : 0;

if ($pagina == 0) {
	$inicio = 0;
	$pagina = 1;
}else{
	$inicio = ($pagina-1) * $limit;
}

$sqlLimit = "LIMIT $inicio, $limit";

$sql = "SELECT SQL_CALC_FOUND_ROWS salidadetalle.*, salidas.SalidaID, salidas.Estado, productos.idProducto, productos.Articulo, almacen.id, almacen.Almacen 
										FROM $tabla 
											INNER JOIN salidas ON salidadetalle.CodigoSalida =  salidas.SalidaID 
											INNER JOIN productos ON salidadetalle.Objeto = productos.idProducto 
											INNER JOIN almacen ON salidadetalle.Deposit = almacen.id  $where $sqlLimit";

$respuesta = $conexion->query($sql);
$filas = $respuesta->num_rows;
$sqlFiltro = "SELECT FOUND_ROWS()";
$resFiltro = $conexion->query($sqlFiltro);
$filaFiltro = $resFiltro->fetch_array();
$totalFiltro = $filaFiltro[0];

$sqlTotal = "SELECT COUNT($id) FROM $tabla WHERE CodigoSalida = $Codigo";
$resTotal = $conexion->query($sqlTotal);
$filaTotal = $resTotal->fetch_array();
$total = $filaTotal[0];

$salida = [];
$salida["total"] = $total;
$salida["totalFiltro"] = $totalFiltro;
$salida["data"] = "";
$salida["paginacion"] = ""; 

if ($filas > 0) {
	while($fila = $respuesta->fetch_assoc()){
		$Codigo2 = $fila['IdSalida'];
		$Estado = $fila['Estado'];
		$Codigo1 = $fila['SalidaID'];
		$Almacen = $fila['Deposit'];

		$salida["data"].= "<tr>";
		$salida["data"].='<td>'.$fila['IdSalida'] .'</td>';
		$salida["data"].='<td>'.$fila['CodigoSalida'] .'</td>';
		$salida["data"].='<td>'.$fila['Almacen'] .'</td>';
		$salida["data"].='<td>'.$fila['Articulo'] .'</td>';
		$salida["data"].='<td>'.$fila['Cantidad'] .'</td>';
		$salida["data"].='<td>'.$fila['Precio'] .'</td>';
		$salida["data"].='<td>'.$fila['Total'] .'</td>';
		$salida["data"].='<td>'.$fila['Observacion'].'</td>';

						if($Estado == "Procesada"){

									$salida["data"].= "<td > <a style='display: none;' href='/Almacen/editar/editarSalidaD.php?id=".$Codigo1."&Codigo=".$Codigo2."&Estado=".$Estado."&Almacen=".$Almacen."' > <i class='fas fa-edit'> </i> </a>  
								 	<a style='display: none;' href='/Almacen/eliminar/eliminarSalidaD.php?id=".$Codigo1."&Codigo=".$Codigo2."&Estado=".$Estado."&Almacen=".$Almacen."' > 
								 	<i class='fas fa-trash-alt'> </i> </a> </td>";


								}else{
									$salida["data"].= "<td> <a href='/Almacen/editar/editarSalidaD.php?id=".$Codigo1."&Codigo=".$Codigo2."&Estado=".$Estado."&Almacen=".$Almacen."'> <abbr title='Editar detalle de salida'> <span class='icono'>&#9998;</span> </abbr> </a> 
									<a class=btnEliminar href = '/Almacen/eliminar/eliminarSalidaD.php?id=".$Codigo1."&Codigo=".$Codigo2."&Estado=".$Estado."&Almacen=".$Almacen."'> <abbr title='Eliminar detalle de salida'> <span class='icono'>&#10006;</span></abbr>  </a> </td>";
								}

				$salida["data"].= "</tr>";
	}
}else{
	$salida["data"].= "<tr> <td> Sin Resultados </td>
				<td></td>
				<td></td>
			 </tr>";
}


if ($salida["total"] > 0) {
	
	$totalPaginas = ceil($salida["total"]/$limit);

	$salida["paginacion"].= "<ul class='paginador'>";

	$numeroInicio = 1;

	if ($pagina - 4 > 1) {

		$numeroInicio = $pagina - 4;
	}

	$numeroFin = $numeroInicio + 9;

	if ($numeroFin > $totalPaginas) {
		$numeroFin = $totalPaginas;
	}

	for ($i=$numeroInicio; $i <= $numeroFin ; $i++) { 
		if ($pagina == $i) {
			$salida["paginacion"].="<li > <a class='activa' href='#'>".$i." </a> </li>";
		}else{
			$salida["paginacion"].="<li> <a href='#' onclick='siguientePagina(".$i.")'>".$i." </a> </li>";
		}
		
	}

	$salida["paginacion"].= "</ul>";

}

echo json_encode($salida, JSON_UNESCAPED_UNICODE);

?>