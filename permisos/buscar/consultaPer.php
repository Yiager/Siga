<?php

include "../conect.php";
$conexion = conectar();

session_start();
if (!isset($_SESSION['id_usuario'])) {
	header("Location:index.php");
}

$columnas = ["id", "Cedula", "Nombre", "Empresa", "Direccion","Correo", "Actividad", "Desde", "Hasta", "Emision", "DesdeR", "HastaR", "EmisionR", 
"Renovacion", "Nro"];

$id = "id";

$tabla = "permisos";

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

$limit = 5;

$pagina = isset($_POST['pagina']) ? $conexion->real_escape_string($_POST['pagina']) : 0;

if ($pagina == 0) {
	$inicio = 0;
	$pagina = 1;
}else{
	$inicio = ($pagina-1) * $limit;
}

$sqlLimit = "LIMIT $inicio, $limit";

$sql = "SELECT SQL_CALC_FOUND_ROWS ". implode(",", $columnas)." FROM $tabla $where $sqlLimit";
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
		$id = $fila['id'];
		$desde = $fila['Desde'];
		$hasta = $fila['Hasta'];
		$emision = $fila['Emision'];
		$renovacion = $fila['Renovacion'];
		$fechaRenovacionDesde = $fila['DesdeR'];
		$fechaRenovacionHasta = $fila['HastaR'];
		$fechaRenobvacionEmision = $fila['EmisionR'];

		if ($renovacion == 1) {
			$renovacion = "Si";
			$desde = $fechaRenovacionDesde;
			$hasta = $fechaRenovacionHasta;
			$emision = $fechaRenobvacionEmision;

		}else{
			$renovacion = "No";
		}

		$salida["data"].= "<tr>";
		$salida["data"].='<td>'.$fila['Cedula'] .'</td>';
		$salida["data"].='<td>'.$fila['Nombre'] .'</td>';
		$salida["data"].='<td>'.$fila['Empresa'] .'</td>';
		$salida["data"].='<td>'.$fila['Direccion'] .'</td>';
		$salida["data"].='<td>'.$fila['Correo'] .'</td>';
		$salida["data"].='<td>'.$fila['Actividad'] .'</td>';
		$salida["data"].='<td>'.$desde .'</td>';
		$salida["data"].='<td>'.$hasta .'</td>';
		$salida["data"].='<td>'.$emision.'</td>';
		$salida["data"].='<td>'.$renovacion .'</td>';
		$salida["data"].='<td>'.$fila['Nro'] .'</td>';


			if ($renovacion == "Si") {
						$salida["data"].='<td> <a class="enlace" style="display:none;" href="/permisos/renovar.php?id='.$id.'"><abbr title="Renovar"><i class="bi bi-arrow-clockwise"></i></abbr></a>  ';
					}else{
						$salida["data"].='<td> <a class="enlace" href="/permisos/renovar.php?id='.$id.'"> <abbr title="Renovar"><i class="bi bi-arrow-clockwise"></i></abbr></a>  ';
					}

					$salida["data"].= '
								<a class="enlace" href="/permisos/pdfs/permisoPDF.php?id='.$id.'"> <abbr title="Imprimir"> <i class="bi bi-printer"> </i> </abbr> </a>

								<a class="enlace" href="/permisos/editar/editarPer.php?id='.$id.'"> <abbr title="Editar"> <i class="bi bi-pencil-square"> </i> </abbr> </a> 
								<a class="enlace" href = "/permisos/eliminar/eliminarPer.php?id='.$id.'" > <abbr title="Eliminar"> <i class="bi bi-trash"></i> </abbr> </a> 
							  </td>';

				$salida["data"] .= "</tr>";
	}
}else{
	$salida["data"].= "<tr> <td> Sin Resultados </td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td> 
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