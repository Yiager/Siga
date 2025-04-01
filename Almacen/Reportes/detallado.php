<?php

include('../login.php');
include('../fpdf/pdf_mc_table.php');

$conexion = conectar();

//Fecha, almacen del detalle de entrada
$Fecha = $_GET['fecha'];
$Almacen = $_GET['almacen'];
//Fecha actual que se solicita el reporte
$FechaReporte = date("j/n/Y");

$pdf = new PDF_MC_table();

$pdf->AddPage();
$pdf->SetFont('Arial', '', '12');

$pdf->Image('../img/logoNuevo.jpg' , 5 ,5, 50);

//******************************Selecionar las sumas totales de la tabla entradadetalle******************************
$ConexionSQL = $conexion->prepare("SELECT 
					entradadetalle.ID,
					entradadetalle.CodigoEntrada,
					entradadetalle.CodigoProducto,
					entradadetalle.Unidades,
					entradas.Codigo,
					entradas.FechaFactura,
					entradas.Almacen,
					entradas.Estado,
					salidas.Deposito,
					salidas.Fecha,
					salidas.Estado,
					salidadetalle.CodigoSalida,
					salidadetalle.IdSalida,
					salidas.SalidaID,
					salidadetalle.Objeto,
					salidadetalle.Deposit,
					salidadetalle.Cantidad,
					almacen.id,
					almacen.Almacen,
					productos.idProducto,
					productos.Articulo,
					productos.id_medida,
					medidas.Medida,
					medidas.id,
					IFNULL((
						SELECT
							SUM(entradadetalle.Unidades) 
						FROM 
							entradadetalle 
							INNER JOIN entradas ON entradas.Codigo = entradadetalle.CodigoEntrada 
						WHERE 
							entradas.Almacen = ?
							AND entradadetalle.CodigoProducto = productos.idProducto
							AND entradas.Estado = 'Procesada'
					), 0) AS UnidadesD, 
					IFNULL((
						SELECT
							SUM(salidadetalle.Cantidad) 
						FROM 
							salidadetalle 
							INNER JOIN salidas ON salidas.SalidaID = salidadetalle.CodigoSalida
						WHERE 
							salidadetalle.Objeto = productos.idProducto
							AND salidadetalle.Deposit = ?
							AND salidas.Estado = 'Procesada'
					), 0) AS salidasD, 
					IFNULL((
						SELECT 
							SUM(salidadetalle.Cantidad) 
						FROM 
							salidadetalle 
							INNER JOIN salidas ON salidas.SalidaID = salidadetalle.CodigoSalida
						WHERE 
							salidas.Estado = 'Procesada' 
							AND salidadetalle.Deposit = ?
							AND salidas.Fecha <= ?
					), 0) AS totalSalida, 

					IFNULL((
						SELECT 
							SUM(entradadetalle.Unidades) 
						FROM 
							entradadetalle 
							INNER JOIN entradas ON entradas.Codigo = entradadetalle.CodigoEntrada 
						WHERE 
							entradas.Almacen = ?
							AND entradas.FechaFactura <= ?
							AND entradas.Estado = 'Procesada'
					), 0) AS totalUnidades 
				FROM 
					entradas 
					INNER JOIN almacen ON entradas.Almacen = almacen.id 
					INNER JOIN entradadetalle ON entradas.Codigo = entradadetalle.CodigoEntrada 
					INNER JOIN productos ON entradadetalle.CodigoProducto = productos.idProducto 
					INNER JOIN medidas ON productos.id_medida = medidas.id 
					INNER JOIN salidas ON entradas.Almacen = salidas.Deposito 
					INNER JOIN salidadetalle ON salidadetalle.CodigoSalida = salidas.SalidaID 
				WHERE 
					entradas.FechaFactura <= ? 
					AND entradas.Estado = 'Procesada' 
					AND entradas.Almacen = ?
					AND salidas.Fecha <= ?
					AND salidas.Estado = 'Procesada' 
				GROUP BY 
					productos.idProducto");
$ConexionSQL->bind_param("iiisissis", $Almacen, $Almacen, $Almacen, $Fecha, $Almacen, $Fecha, $Fecha, $Almacen, $Fecha); 
$ConexionSQL->execute();
$datos = $ConexionSQL->get_result();
$ConexionSQL->close();
	
	$pdf->SetFont('Arial', '', '12');
	$pdf->Cell(130);
	$pdf->Cell(60, 10, 'Fecha de reporte: '.$FechaReporte, 1, 1, 'C', 0);
	$pdf->Ln(15);
	$pdf->Cell(90);
	$pdf->SetFont('Arial', 'B', '14');
	$pdf->Cell(10, 10, 'Inventario detallado', 0, 1, 'C', 0);
	$pdf->Ln(5);
	$pdf->SetWidths(Array(50,25,30,20,20,25));
	//Centrar contenido de celda
	$pdf->SetAligns(Array('C','C','C','C','C','C','C','C','C','C','C'));
	$pdf->SetLineHeight(5);
	$pdf->Cell(10);
	$pdf->SetFont('Arial','B',12);
	$pdf->Cell(50, 10, 'Almacen', 1, 0, 'C', 0);
	$pdf->Cell(25, 10, 'Producto', 1, 0, 'C', 0);
	$pdf->Cell(30, 10, 'Unid. Medida', 1, 0, 'C', 0);
	$pdf->Cell(20, 10, 'Entrada', 1, 0, 'C', 0);
	$pdf->Cell(20, 10, 'Salida', 1, 0, 'C', 0);
	$pdf->Cell(25, 10, 'Existencia', 1, 1, 'C', 0);

	while($traer = $datos->fetch_assoc()){

		$totalUni = $traer['totalUnidades'];
		$totalSal = $traer['totalSalida'];

		$pdf->Cell(10);
		$pdf->SetFont('Arial','',12);

		$pdf->Row(Array(

			$traer['Almacen'],
			$traer['Articulo'],
			utf8_decode($traer['Medida']),
			$traer['UnidadesD'],
			$traer['salidasD'],
			$traer['UnidadesD'] - $traer['salidasD']
		));
	}

	$pdf->SetLineHeight(5);
	$pdf->Cell(10);
	$pdf->SetFont('Arial','B',12);
	$pdf->Cell(105, 10, 'Total', 1, 0, 'C', 0);
	$pdf->Cell(20, 10, $totalUni, 1, 0, 'C', 0);
	$pdf->Cell(20, 10, $totalSal, 1, 0, 'C', 0);
	$pdf->Cell(25, 10, $totalUni-$totalSal, 1, 1, 'C', 0);

$pdf->Output();

?>