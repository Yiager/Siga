<?php

include('../login.php');
include('../fpdf/pdf_mc_table.php');

$conexion = conectar();

//Identificador de la entrada
$id = $_GET['id'];

$pdf = new PDF_MC_table();

$pdf->AddPage();
$pdf->SetFont('Arial', '', '12');

$pdf->Image('../img/logoNuevo.jpg' , 5 ,5, 50);
$pdf->Ln(30);

//*********************Seleccionar datos principales de la entrada******************************
$ConexionSQL = $conexion->prepare("SELECT 
					entradas.*, 
					almacen.*, 
					almacen.Almacen AS Almacen2, 
					prove.*  
				FROM 
					entradas 
					INNER JOIN almacen ON entradas.Almacen = almacen.id 
					INNER JOIN prove ON entradas.Proveedor = prove.id
				WHERE 
					Codigo = ? ");
$ConexionSQL->bind_param("i", $id);
$ConexionSQL->execute();
$datos = $ConexionSQL->get_result();
$ConexionSQL->close();

$pdf->SetFont('Arial', '', '12');
while($traer = $datos->fetch_assoc()){

$pdf->Cell(30);
$pdf->Cell(20, 10, 'Fecha de compra: '.$traer['FechaCompra'], 0, 0, 'C', 0);
$pdf->Cell(80);
$pdf->Cell(20, 10, 'Nro de compra: '.$traer['NroCompra'], 0, 1, 'C', 0);
$pdf->Cell(30);
$pdf->Cell(20, 10, 'Fecha de factura: '.$traer['FechaFactura'], 0, 0, 'C', 0);
$pdf->Cell(80);
$pdf->Cell(20, 10, 'Nro de factura: '.$traer['NroFactura'], 0, 1, 'C', 0);
$pdf->Cell(40);
$pdf->Cell(110, 10, 'Proveedor: '.$traer['Empresa'], 0, 1, 'C', 0);
$pdf->Cell(65);
$pdf->SetFont('Arial', 'B', '14');
$pdf->Cell(70, 10, 'Entrada Nro '.$id, 0, 1, 'C', 0);

$pdf->SetWidths(Array(20,35,45,35,25,20));
//Centrar contenido de celda
$pdf->SetAligns(Array('C','C','C','C','C','C'));
$pdf->SetLineHeight(5);

	$pdf->Cell(5);
	$pdf->SetFont('Arial','B',12);
	$pdf->Cell(20, 10, 'Codigo', 1, 0, 'C', 0);
	$pdf->Cell(35, 10, 'Fecha', 1, 0, 'C', 0);
	$pdf->Cell(45, 10, 'Almacen', 1, 0, 'C', 0);
	$pdf->Cell(35, 10, 'Tipo de entrada', 1, 0, 'C', 0);
	$pdf->Cell(25, 10, 'Estado', 1, 0, 'C', 0);
	$pdf->Cell(20, 10, 'Inicial', 1, 1, 'C', 0);

	$pdf->Cell(5);
	$pdf->SetFont('Arial','',12);

	$pdf->Row(Array(

		$traer['Codigo'],
		$traer['Fecha'],
		$traer['Almacen2'],
		$traer['TipoEntrada'],
		$traer['Estado'],
		$traer['InvInicial']

	));
}

$pdf->Ln(5);
$pdf->Cell(90);
$pdf->SetFont('Arial', 'B', '14');
$pdf->Cell(20, 10, 'Detalles', 0, 1, 'C', 0);

$pdf->SetWidths(Array(20,20,40,15,15,15,15,20,20));
//Centrar contenido de celda
$pdf->SetAligns(Array('C','C','C','C','C','C','C','C','C'));
$pdf->SetLineHeight(5);

	$pdf->Cell(5);
	$pdf->SetFont('Arial','B',12);
	$pdf->Cell(20, 10, 'Detalle', 1, 0, 'C', 0);
	$pdf->Cell(20, 10, 'Entrada', 1, 0, 'C', 0);
	$pdf->Cell(40, 10, 'Producto', 1, 0, 'C', 0);
	$pdf->Cell(15, 10, 'Cant', 1, 0, 'C', 0);
	$pdf->Cell(15, 10, 'Unid', 1, 0, 'C', 0);
	$pdf->Cell(15, 10, 'Salidas', 1, 0, 'C', 0);
	$pdf->Cell(15, 10, 'Exis', 1, 0, 'C', 0);
	$pdf->Cell(20, 10, 'Precio', 1, 0, 'C', 0);
	$pdf->Cell(20, 10, 'MontoT', 1, 1, 'C', 0);

//***********************************************************************************
//Seleccionar los totales de las cantidades en unidades y montos de entrada y salida*
//***********************************************************************************
$ConexionSQLDetalle = $conexion->prepare("SELECT 
							entradadetalle.*, 
							productos.*, 
							(
								SELECT 
									IFNULL(SUM(entradadetalle.Unidades),0) 
								FROM 
									entradadetalle 
								WHERE 
									entradadetalle.CodigoEntrada = ?
							) AS TotalUni,

							(
								SELECT 
									IFNULL(SUM(entradadetalle.Cantidad),0)
								FROM 
									entradadetalle 
								WHERE 
									entradadetalle.CodigoEntrada = ?
							) AS TotalCant,

							(
								SELECT 
									IFNULL(SUM(entradadetalle.Salidas),0)
								FROM 
									entradadetalle 
								WHERE 
									entradadetalle.CodigoEntrada = ?
							) AS TotalSal,

							(
								SELECT 
									IFNULL(SUM(entradadetalle.Precio),0) 
								FROM 
									entradadetalle 
								WHERE 
									entradadetalle.CodigoEntrada = ?
							) AS TotalPrecio,

							(
								SELECT 
									IFNULL(SUM(entradadetalle.MontoT),0)
								FROM 
									entradadetalle 
								WHERE 
									entradadetalle.CodigoEntrada = ?
							) AS TotalMonto
						FROM 
							entradadetalle 
							INNER JOIN productos ON entradadetalle.CodigoProducto = productos.idProducto 
						WHERE 
							entradadetalle.CodigoEntrada = ? ");
$ConexionSQLDetalle->bind_param("iiiiii", $id, $id, $id, $id, $id, $id);
$ConexionSQLDetalle->execute();
$datos = $ConexionSQLDetalle->get_result();
$ConexionSQLDetalle->close();
							
$pdf->SetFont('Arial', '', '12');
while($traerD = $datos->fetch_assoc()){
	$totalUni = $traerD['TotalUni'];
	$totalSal = $traerD['TotalSal'];
	$totalCant = $traerD['TotalCant'];
	$TotalPrecio = $traerD['TotalPrecio'];
	$TotalMonto = $traerD['TotalMonto'];

	$pdf->Cell(5);
	$pdf->Row(Array(

		$traerD['ID'],
		$traerD['CodigoEntrada'],
		$traerD['Articulo'],
		$traerD['Cantidad'],
		$traerD['Unidades'],
		$traerD['Salidas'],
		$traerD['Existencia'],
		$traerD['Precio'],
		$traerD['MontoT']

	));
}

	$pdf->Ln(5);
	$pdf->Cell(5);
	$pdf->SetFont('Arial','B',12);
	$pdf->Cell(80, 10, 'Total', 1, 0, 'C', 0);
	$pdf->Cell(15, 10, $totalCant, 1, 0, 'C', 0);
	$pdf->Cell(15, 10, $totalUni, 1, 0, 'C', 0);
	$pdf->Cell(15, 10, $totalSal, 1, 0, 'C', 0);
	$pdf->Cell(15, 10, $totalUni-$totalSal, 1, 0, 'C', 0);
	$pdf->Cell(20, 10, $TotalPrecio, 1, 0, 'C', 0);
	$pdf->Cell(20, 10, $TotalMonto, 1, 1, 'C', 0);

$pdf->Output();

?>