<?php

include "../login.php";
$conexion = conectar();

session_start();
if (!isset($_SESSION['id_usuario'])) {
	header("Location:index.php");
}

$columnas = ["productos.idProducto", "productos.Articulo", "productos.id_medida", "productos.Tipo", "medidas.Medida"];

$id = "idProducto";

$tabla = "productos";

$buscador = isset($_POST['buscar']) ? $conexion->real_escape_string($_POST['buscar']) : null;

$where = "";

if ($buscador != null) {
	$where .= "WHERE (";

	$contador = count($columnas);
	for ($i=0; $i < $contador; $i++) { 
		$where .= $columnas[$i] . " LIKE '%".$buscador."%' OR ";
	}

	$where = substr_replace($where, " ", -3);

	$where.= ")";
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

$sql = "SELECT SQL_CALC_FOUND_ROWS productos.idProducto, productos.id_medida, productos.Articulo, productos.Tipo, productos.rutaImagen, medidas.id, medidas.Medida FROM $tabla INNER JOIN medidas ON medidas.id = productos.id_medida $where $sqlLimit";
$respuesta = $conexion->query($sql);
$filas = $respuesta->num_rows;

$sqlFiltro = "SELECT FOUND_ROWS()";
$resFiltro = $conexion->query($sqlFiltro);
$filaFiltro = $resFiltro->fetch_array();
$totalFiltro = $filaFiltro[0];

$sqlTotal = "SELECT COUNT($id) FROM $tabla";
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
		$id = $fila['idProducto'];
		$tipo = $fila['Tipo'];
		$Medida = $fila['id_medida'];
		$rutaImagen = $fila['rutaImagen'];

			if($tipo == "Si"){				
				$tipo = "Unidad";
			}else{
				$tipo = "Unidades";
			}

		if ($Medida == false) {
			$Medida = "N/A";
		}

		$salida["data"].= "<tr>";
		$salida["data"].='<td>'.$fila['Articulo'] .'</td>';
		$salida["data"].='<td>'.$Medida .'</td>';
		$salida["data"].='<td>'.$tipo .'</td>';
		$salida["data"].='<td> <img class="imagen" src="'.$rutaImagen.'" width="40px" height="40px"> </td>';

						$salida["data"].='<td> <a class="enlace" href="/Almacen/editar/editar_Producto.php?id='.$id.'"> <abbr title="Editar"><span class="icono">&#9998;</span></abbr></a>  ';
					

					$salida["data"].= '
								<a class="enlace" href = "/Almacen/eliminar/eliminar_Producto.php?id='.$id.'" > <abbr title="Eliminar"> <span class="icono">&#10006;</span> </abbr> </a> 
							  </td>';

				$salida["data"] .= "</tr>";
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