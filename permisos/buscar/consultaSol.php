<?php

include "../conect.php";
$conexion = conectar();

session_start();
if (!isset($_SESSION['id_usuario'])) {
	header("Location:index.php");
}

$columnas = ["id", "Tipo", "Nombres", "Direccion", "FechaActual", "Periodo", "FechaDesde", "FechaHasta", "Uso"];

$id = "id";

$tabla = "solvencias";

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
		$nombres = $fila['Nombres'];
		$salida["data"] .= "<tr>";
		$salida["data"] .= "<td>".$fila['Tipo']."</td>";
		$salida["data"] .= "<td>".$fila['Nombres']."</td>";
		$salida["data"] .= "<td>".$fila['Direccion']."</td>";
		$salida["data"] .= "<td>".$fila['FechaActual']."</td>";
		$salida["data"] .= "<td>".$fila['Periodo']."</td>";
		$salida["data"] .= "<td>".$fila['FechaDesde']."</td>";
		$salida["data"] .= "<td>".$fila['FechaHasta']."</td>";
		$salida["data"] .= "<td>".$fila['Uso']."</td>";


			$verificarCorreo = "SELECT 
											correos.correo, 
											correos.nombre, 
											solvencias.Nombres 
									FROM 
											correos 
											INNER JOIN solvencias ON correos.nombre = solvencias.Nombres 
									WHERE 
											correos.nombre = '$nombres' ";

				$respuestaCorreos = mysqli_query($conexion, $verificarCorreo);
				$Correo = mysqli_fetch_assoc($respuestaCorreos);

				if ($Correo['correo'] != null) {
					$salida["data"] .= '<td> 
								<a id="correo" style="display:none;" class="enlace" href="/permisos/correoAgg.php?id='.$id.'"> <abbr title="Agregar correo"> <i class="bi bi-envelope-paper"> </i> </abbr> </a>';
								$salida["data"] .='
								<a id="correo" class="enlace" href="/permisos/correoEdit.php?id='.$id.'"> <abbr title="Editar correo"> 
								<i class="bi bi-envelope-arrow-up"></i> </abbr> </a>';
				}else{
					$salida["data"] .='<td> 
								<a id="correo" class="enlace" href="/permisos/correoAgg.php?id='.$id.'"> <abbr title="Agregar correo"> 
								<i class="bi bi-envelope-plus"></i> </abbr> </a>';
				}

					$salida["data"].= ' 
								<a class="enlace" href="/permisos/pdfs/solvenciaPDF.php?id='.$id.'"> <abbr title="Imprimir"> <i class="bi bi-printer"> </i> </abbr> </a> 
								<a class="enlace" href="eliminar/eliminarSol.php?id='.$id.'" > <abbr title="Eliminar"> <i class="bi bi-trash"></i> </abbr> </a> 
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