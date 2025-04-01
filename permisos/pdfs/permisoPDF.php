<?php
include "../conect.php";
require("../fpdf/phpqrcode/qrlib.php");

$conexion = conectar();

session_start();

if (!isset($_SESSION['id_usuario'])) {
	header("Location:index.php");
}

setlocale(LC_TIME, 'spanish');

$id = $_GET['id'];

$traerPermiso = "SELECT * FROM permisos WHERE id = '$id' ";
$respuesta = mysqli_query($conexion, $traerPermiso);
$datos = mysqli_fetch_assoc($respuesta);

$fechaEmision = date($datos['Emision']);
$fecha = strtotime($fechaEmision);
$añoEmision = date("Y", $fecha);
$mesEmision = date("m", $fecha);
$diaEmision = date("d", $fecha);

$nombreMes = date_create_from_format("!m", $mesEmision);
$mes = strftime("%B", $nombreMes->getTimestamp());

$nombre = $datos['Nombre'];
$cedula = $datos['Cedula'];
$empresa = $datos['Empresa'];
$actividad = $datos['Actividad'];
$direccion = $datos['Direccion'];
$fechaEmision = $datos['Emision'];
$desde = $datos['Desde'];
$hasta = $datos['Hasta'];
$nro = $datos['Nro'];
$correo = $datos['Correo'];
$renovacion = $datos['Renovacion'];

if($renovacion == 1){
	$desde = $datos['DesdeR'];
	$hasta = $datos['HastaR'];
	$fechaEmision = $datos['EmisionR'];
}

use phpmailer\phpmailer\PHPMailer;
use phpmailer\phpmailer\SMTP;
use phpmailer\phpmailer\Exception;

require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = 0;                      
    $mail->isSMTP();                                          
    $mail->Host       = 'smtp.gmail.com';                    
    $mail->SMTPAuth   = true;               
    $mail->SMTPSecure = 'ssl';                    
    $mail->Username   = 'lossaliasdat@gmail.com';               
    $mail->Password   = 'dquqpukmsfwcqblm';                               
    $mail->Port       = '465';       
    $mail->AddEmbeddedImage('../img/logoNuevoComp.jpg', 'logoN');                            

    //Recipients
    $mail->setFrom('lossaliasdat@gmail.com', 'Direccion Administracion Tributaria');
    $mail->addAddress($correo);     

   
    $mail->isHTML(true);                                 
    $mail->Subject = 'FROM: lossaliasdat@gmail.com';
    $mail->Body    = "<img src='cid:logoN' alt='Logo' width='150' height='100'/>"."<br><br>". "Saludos, sr(a) contribuyente ".$nombre.", se ha emitido un PERMISO TEMPORAL para uso ".$actividad.", ubicado en ".$direccion.", el dia ".$fechaEmision.", válido desde el dia ".$desde." hasta ".$hasta." por un período de 3 MESES por la Dirección de Administración Tributaria de la Alcaldia del Municipio Los Salias". "<br><br>" ."Dirección de Administración Tributaria de la Alacaldia del Municipio Los Salias". "<br>". "lossaliasdat@gmail.com";

    $mail->send();
   
} catch (Exception $e) {
    echo "Error al enviar: {$mail->ErrorInfo}";
}

// $logo_path = '../img/logoNuevo.jpg';
// $type = pathinfo($logo_path, PATHINFO_EXTENSION);
// $image_contents = file_get_contents($logo_path);
// $image64 = 'data:image/' . $type . ';base64,' . base64_encode($image_contents);

// $to = "$correo";
// $subject = "Emisión de permiso temporal";
// $message ="<img src='../img/logoNuevo.jpg'/>"."\n". "Saludos, sr(a) contribuyente ".$nombre.", se ha emitido un PERMISO TEMPORAL para uso ".$actividad.", ubicado en ".$direccion.", el dia ".$fechaEmision.", válido desde el dia ".$desde." hasta ".$hasta." por un período de 3 MESES por la Dirección de Administración Tributaria de la Alcaldia del Municipio Los Salias". "\n\n" ."Dirección de Administración Tributaria de la Alacaldia del Municipio Los Salias". "\n". "lossaliasdat@gmail.com";
// $headers = "FROM: lossaliasdat@gmail.com";
// $headers.= "MIME-Version: 1.0";
// $headers.= "Content-Type: text/html; charset=UTF-8";

//mail($to, $subject, $message, $headers);

$contenido  = 'BEGIN:VCARD'."\n";
$contenido .= 'VERSION:2.1'."\n";
$contenido .= 'FN:'."El contributente: ".$nombre."\n";
$contenido .= 'FN:'."que representa a: ".$empresa."\n";
$contenido .= 'FN:'."perteneciente al nro de C.I.: ".$cedula."\n";
$contenido .= 'FN:'."emite un permisos temporal desde: ".$desde."\n";
$contenido .= 'FN:'."hasta: ".$hasta."\n";
$contenido .= 'FN:'."actividad comercial: ".$actividad."\n";
$contenido .= 'FN:'."Nro: ".$nro."\n";
$contenido .= 'END:VCARD';

$codigoQR = QRcode::png($contenido, "../Qrs/QrPermisoNro".$id.".png", QR_ECLEVEL_L, 3);

require("../fpdf/WriteTag.php");

$pdf=new PDF_WriteTag();

$pdf->SetFont('courier','',12);
$pdf->AddPage();

// Stylesheet
$pdf->SetStyle("p","arial","N",10,"0,0,0");
$pdf->SetStyle("h1","times","N",18,"102,0,102",0);
$pdf->SetStyle("a","times","BU",9,"0,0,255");
$pdf->SetStyle("pers","times","I",0,"255,0,0");
$pdf->SetStyle("place","arial","B",10,"0,0,0");
$pdf->SetStyle("vb","arial","UB",10,"0,0,0");

// Title
$pdf->SetLineWidth(0.5);
$pdf->SetFillColor(255,255,204);
$pdf->SetDrawColor(102,0,102);


$txt = "
<p>
Otorgar, al ciudadano (a) <place>".$nombre."</place> titular de la cedula de identidad Nro. <place>".$cedula."</place> 
en su caracter de representante legal de <place>".$empresa."</place> previsto el Capitulo III, seccion segunda de la Ordenanza de
Impuestos sobre Actividades Economicas de fecha veintiocho (28) de Abril 2010, LICENCIA TEMPORAL para ejercer actividades economicas
</p>";

$txt2 = "
<p>
Se autoriza al ciudadano anteriomente identificado a su calidad de representante lega de la empresa identificada Ut Supra, al ejercicio de 
la actividad comercial: <place>".$actividad."</place> desde <place>".$desde."</place> hasta <place>".$hasta."</place> Con 
<vb>LICENCIATEMPORAL</vb>identificada con el <place>Nro: ".$nro."</place>
</p>";

$txt3 = "
<p> 
<place>NOTA:</place> la presente LICENCIA TEMPORAL podra ser revocada en cualquier momento por esta Direccion de Administracion Tributaria, su revocatoria 
NO dara derecho a exigir el reintegro del costo de su emision
</p>";

$txt4 = "
<p>
Con la recepcion de esta LICENCIA TEMPORAL, el beneficiario declara su conformidad a todos los extremos antes expuestos. Es todo. En San Antonio de los Altos, a los  <place>".$diaEmision."</place>  dias del mes de  <place>". $mes ."</place>  de  <place>".$añoEmision."</place>
</p>";

$pdf->Image("../img/logoNuevo.jpg", 10, 5, 50, 30, 'JPG');
$pdf->Image("../Qrs/QrPermisoNro".$id.".png", 170, 3, 35, 35, 'PNG');
$pdf->SetFont("Arial", "B", 12);
$pdf->Ln(10);
$pdf->Cell(220, 5,"REPUBLICA BOLIVARIANA DE VENEZUELA",0,1,"C");
$pdf->Cell(220, 5,"ESTADO MIRANDA",0,1,"C");
$pdf->Cell(220, 5,"ALCALDIA DEL MUNICIPIO LOS SALIAS",0,1,"C");
$pdf->Cell(220, 5,"DIRECCION DE ADMINISTRACION TRIBUTARIA",0,1,"C");
$pdf->Ln(10);
$pdf->SetFont('Arial','B',10);
$pdf->MultiCell(190, 5,"En uso de las atribuciones legales que le confiere la vigente ordenanza de impuestos, sobre actividades economicas, de fecha 28 de abril 2010, la direccion de Administracion Tributaria del municipio Los Salias:");
$pdf->Ln(5);
$pdf->Cell(200, 5, "RESUELVE", 0,1,"C");
$pdf->Ln(5);
$pdf->SetFont("Arial", "", 10);
$pdf->WriteTag(0,5,$txt,0,"J",0,0);
$pdf->Ln(10);
$pdf->WriteTag(0,5,$txt2,0,"J",0,0);
$pdf->Ln(10);
$pdf->SetFont("Arial", "", 10);
$pdf->MultiCell(190,5, "Esta licencia temporal se emite para ejercicio de actividad comercial descrita en este documento. Tiene una duracion unica e impropagable de noventa (90) dias continuos a partir de la fecha de su otorgamiento y es de caracter intuitu personae, por lo cual es intransferible a cualquier otra persona natural o juridica");
$pdf->Ln(10);
$pdf->WriteTag(0,5,$txt3,0,"J",0,0);
$pdf->Ln(10);
$pdf->WriteTag(0,5,$txt4,0,"J",0,0);
$pdf->Ln(10);
$pdf->SetFont("Arial", "B", 10);
$pdf->Cell(10, 5, "OBSERVACION:");
$pdf->Ln(30);
$pdf->SetFont("Arial", "B", "12");
$pdf->Cell(200, 5,"SANTIAGO A. SANTANA ORTA",0,1,"C");
$pdf->Cell(200, 5,"Director de Administracion Tributaria",0,1,"C");
$pdf->Cell(200, 5,"Segun Gaceta Municipal Nro. 05/01 publicada en",0,1,"C");
$pdf->Cell(200, 5,"Fecha 12/01/2022",0,1,"C");
$pdf->Ln(20);
$pdf->SetFont("Arial", "B", "10");
$pdf->MultiCell(190, 5, 'OBLIGATORIO: "ESTA LICENCIA TEMPORAL DEBE SER COCLOCADA EN UN LUGAR VISIBLE DENTRO DEL ESTABLECIMINETO COMERCIAL". ');
$pdf->Ln(3);
$pdf->Cell(200, 1, "www.alcaldialossalias.gob.ve", 0,1,"C");
$pdf->Output();


?>