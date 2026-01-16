<?php
namespace Dompdf;
require_once 'require/dompdf/autoload.inc.php';

$dompdf = new Dompdf();

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use app\assets\AppAsset;

use yii\helpers\Url;

//$rows = Yii::$app->db->createCommand("SELECT * FROM facturas")->queryAll(); 

session_start();

///conexion
$config = include("../config/db.php");
$dsn = $config['dsn'];
preg_match('/dbname=([^;]*)/', $dsn, $matches);
$dbname = $matches[1];
preg_match('/host=([^\;]+)/', $dsn, $matches2);
$host = $matches2[1];
$conexion= mysqli_connect($host,  $config['username'],  $config['password']) or die(mysqli_error());
//$conexion= mysqli_connect('127.0.0.1',  $config['username'],  $config['password']) or die(mysqli_error());
mysqli_select_db($conexion, $dbname) or die(mysqli_error($conexion));
//fin conexion

	

$css='<style>
	html {
		margin: 0px;
	}
	.seccionRedonda{
		border:solid 1px #C7C3C3; border-radius: 15px; padding: 15px; vertical-align: top;
		
	}
	.seccionConceptos{
		padding: 15px 27px; vertical-align: top;
	}
	.sellos{
		padding-left:40px;
		padding-right:27px;
		vertical-align: top;
	}
	hr{
		border: solid 0px; border-top:dotted 1px;
	}
	.factura{
		border:solid 0px; font-family: Arial, Helvetica, sans-serif; font-size:11px;
	}
	.conceptos{
		font-size:12px;
		font-family: Arial;
	}
	.encabezado{
		border: solid 1px;
		background-color: #C4C1C1;
		font-weight:bold;
	}
	.encabezado td{
		padding: 5px;
		font-size:11px;
	}
	.totales{
		text-align: right;
	}
	.totales td{
		padding-top:0px;
	}
	.footer{
	position: absolute; bottom:0px; width:100%; padding-bottom:12px;
	font-family: Arial; font-size:11px;
	}
	.modoPrueba{
		color:red;
		font-size:18px;
		position:absolute;
		right: 125px;
	}
	.estadoFactura{
		font-size:13px;
		position:absolute;
		right: 45px;
	}
	.colorRojo{
		color:red;
	}
</style>';
// 

$consulta= "select f.*, e.rfcEstablecimiento as rfcEmpresa, e.razonSocialEstablecimiento, e.aliasEstablecimiento, rf.nombreRegimenFiscal, rf.descripcionRegimenFiscal, fp.formaPago as nombreFormaPago, fp.descripcion as descripcionFormaPago, mp.metodoPago as nombreMetodoPago, 
mp.descripcion as descripcionMetodoPago, f.modo, f.clienteID, c.clienteRFC as rfcCliente, c.clienteRazonSocial as razonSocialCliente, uc.usoCFDI as nombreUsoCFDI, uc.descripcion as descripcionUsoCFDI, f.fechaPago
		from Facturas f 
		inner join Establecimientos e on e.establecimientoID=f.establecimientoID
		inner join Clientes c on f.clienteID=c.clienteID
		left join FERegimenFiscal rf on e.regimenFiscalID=rf.regimenFiscalID
		left join FEFormaPago fp on f.formaPagoID=fp.formaPagoID
		left join FEUsoCFDI uc on f.usoCfdiID=uc.usoCfdiID
		left join FEMetodoPago mp on f.metodoPagoID=mp.metodoPagoID
		where f.facturaID=".$_GET['id'];

		/*
		SELECT f.facturaID, f.establecimientoID as empresaID, e.rfcEstablecimiento as rfcEmpresa, c.rfc as rfcCliente, totalFactura, uuid, numeroIntentosCancelacion, activoFactura, c.razonSocial as razonSocialCliente, serie, folio, smp.metodoPago as nombreMetodoPago, smp.descripcion as descripcionMetodoPago 
	FROM Facturas f 
	INNER JOIN Establecimientos e on f.establecimientoID = e.establecimientoID 
	INNER JOIN Clientes c on f.clienteID = c.clienteID 
	inner join FEMetodoPago smp on f.metodoPagoID=smp.metodoPagoID
	where f.establecimientoID=
		*/

$res = mysqli_query($conexion, $consulta);
if(mysqli_num_rows($res)>0){
	$k=0;
	$row = mysqli_fetch_array($res);
	//var_dump($row);
	$facturaID= $row['facturaID'];
	if($row['aliasEstablecimiento']!="")
		$empresa= $row['razonSocialEstablecimiento']." (".$row['aliasEstablecimiento'].")";
	else 
		$empresa= $row['razonSocialEstablecimiento'];
	$rfcEmisor= $row['rfcEmpresa'];
	$regimenFiscalEmisorCodigo= "(".$row['nombreRegimenFiscal'].")";
	$regimenFiscalEmisor= $row['descripcionRegimenFiscal'];
	$fechaEmision= $timestamp = strtotime($row['fechaFactura']);
	$certificado= $row['certificado'];
	$folio= $row['serie']." ".$row['folio'];
	$tipoComprobante= $row['tipoComprobante'];
	$folioFiscal= $row['uuid'];
	$formaPago= "(".$row['nombreFormaPago'].") ".$row['descripcionFormaPago'];
	$metodoPago= "(".$row['nombreMetodoPago'].") ".$row['descripcionMetodoPago'];
	$sello= $row['sello'];
	$selloSAT= $row['selloSAT'];
	$cadenaoriginal= $row['cadenaoriginal'];
	$fechaTimbrado= $timestamp = strtotime($row['fechaTimbrado']);
	$subTotalFactura= $row['subTotalFactura'];
	$ivaFactura= $row['ivaFactura'];
	$totalFactura= number_format($row['totalFactura'], 2);
	$importeImagenQR= number_format("0.00", 2);
	$lugarExpedicion= $row['lugarExpedicion'];
	$rfcCliente= $row['rfcCliente'];
	$razonSocialCliente= $row['razonSocialCliente'];
	$nombreUsoCFDI= $row['nombreUsoCFDI'];
	$descripcionUsoCFDI= $row['descripcionUsoCFDI'];
	$direccionCliente= $row['direccionCliente'];
	$fechaPago= $row['fechaPago'];
	$activoFactura= $row['activoFactura'];
	
	$tipoComprobanteNombre= $tipoComprobante;
	if($tipoComprobante=="I") $tipoComprobanteNombre= "Ingreso";
	if($tipoComprobante=="E") $tipoComprobanteNombre= "Egreso";
	if($tipoComprobante=="P") $tipoComprobanteNombre= "Pago";
	
	
	$observaciones='';
	if($row['observaciones'] != ""){
		$observaciones='<strong>Observaciones: </strong> '.utf8_encode($row['observaciones']);
	}
	
	
	//para dividir los sellos
	$sello2= "";
	$selloSAT2= "";
	$cadenaoriginal2= $cadenaoriginal;
	$contarSello=0;
	if(strlen($sello)>122){
		$contarSello= strlen($sello)/3;
		$sello2= substr($sello, 0, $contarSello)."<br />".substr($sello, $contarSello, $contarSello)."<br />".substr($sello, $contarSello*2, $contarSello)."<br />".substr($sello, $contarSello*3, $contarSello);
	}
	if(strlen($selloSAT)>122){
		$contarSello= strlen($selloSAT)/3;
		$selloSAT2= substr($selloSAT, 0, $contarSello)."<br />".substr($selloSAT, $contarSello, $contarSello)."<br />".substr($selloSAT, $contarSello*2, $contarSello)."<br />".substr($selloSAT, $contarSello*3, $contarSello);
	}
	if(strlen($cadenaoriginal)>122){
		//$cadenaoriginal= base64_encode($cadenaoriginal);
		$contarSello= strlen($cadenaoriginal)/3;
		$cadenaoriginal2= substr($cadenaoriginal, 0, 56).(substr($cadenaoriginal, 56, ($contarSello-56)))."<br />".
			(substr($cadenaoriginal, $contarSello, $contarSello))."<br />".
			(substr($cadenaoriginal, $contarSello*2, $contarSello))."<br />".
			substr($cadenaoriginal, $contarSello*3, $contarSello);
	}
	//fin para dividir los sellos
	$modoTimbrado='';
	if($row['modo']=="Pruebas" or $row['modo']=="PRUEBAS") {
		$modoTimbrado='<label class="modoPrueba">Modo Pruebas</label>';
	}
	
	$colorRojo= "";
	if($activoFactura=="Cancelada")
		$colorRojo= "colorRojo";
	$activoFactura= '<label class="estadoFactura '.$colorRojo.'">'.$activoFactura.'</label>';
	
	include("imagen.php");
	
	$html= $css.'
	<table width="613" align="center" class="factura" style="background-image: url(require/img/bannerCody.jpg); background-repeat: no-repeat; border: solid 0px; margin:-1px 0 0 -1px; padding:0px;">
		<tr>
			<td colspan="2" align="left" style="height:130px; padding:20px 25px 0 25px; line-height:17px; font-size:11px;">
				<div style="margin-bottom: 10px; border: solid 0px;"><b>Datos del emisor:</b></div>
				<div style="position: relative;">
					'.$empresa.'
					<br />
					Rfc: '.$rfcEmisor.'
					<br />
					Régimen fiscal: '.$regimenFiscalEmisorCodigo.'
					<br />
					'.$regimenFiscalEmisor.'
					<br />
					<div style="position: absolute; top:-20px; right: 50%; transform: translateX(50px); color: black;">
						<img class="inline-block img-fluid" src="logos/footer_1_20240625055043.png" alt="Logo">
					</div>
					<div style="position: absolute; top:20px; right: 20px; color: black;">
						<label style="margin-right: 13px;">
							<strong>Folio:</strong> '.$folio.'
						</label>
						<br />
						<label>
							<strong>Fecha de emisión:</strong> '.date('d-m-Y h:i:s', $fechaEmision).'
						</label>
						<br />
						<label>
							<strong>Certificado:</strong>
							'.$certificado.'
						</label>
					</div>
				</div>
			</td>
		</tr>
		<tr style="line-height:20px;">
			<td valign="top" width="50%">
				<div class="seccionRedonda" style="margin-left:25px; height:135px;">
					<strong>Datos del receptor</strong>
					<hr>
					<span><strong>RFC:</strong> '.$rfcCliente.'</span>
					<br>
					<span><strong>Nombre receptor:</strong> '.$razonSocialCliente.'</span>
				</div>
			</td>
			<td valign="top">
				<div class="seccionRedonda" style="margin-right:25px;">
					<strong>Datos del pago</strong>
					'.$modoTimbrado.'
					'.$activoFactura.'
					<hr>

					<span><strong>Tipo de comprobante:</strong> ('.$tipoComprobante.') '.$tipoComprobanteNombre.'</span>
					<br />
					<span><strong>Folio fiscal:</strong>
					'.$folioFiscal.'</span>
					<br />
					<span><strong>Forma de pago:</strong> '.$formaPago.'</span>
					<br />
					<span><strong>Fecha del pago:</strong> '.$fechaPago.'</span>
					<br />
					<span><strong>Monto:</strong> $'.$totalFactura.' &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <strong>Moneda:</strong> Peso mexicano</span>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="seccionConceptos">
				<strong>Conceptos</strong>
				<hr>
				<table width="570" class="conceptos" style="border:solid 0px;">
					<tr class="encabezado">
						<td width="60">
							Clave producto y/o servicio
						</td>
						<td width="60">
							Clave de unidad
						</td>
						<td>
							Descripción
						</td>
						<td width="35">
							Cantidad
						</td>
						<td width="70" align="right">
							PU
						</td>
						<td width="70" align="right">
							Importe
						</td>
					</tr>
					<tr>
						<td>
							43232408
						</td>
						<td>
							ACT
						</td>
						<td>
							Pago
						</td>
						<td>
							1
						</td>
						<td align="right">
							$'.number_format("0.00", 2).'
						</td>
						<td align="right">
							$'.number_format("0.00", 2).'
						</td>
					</tr>
					<tr>
						<td colspan="4">

						</td>
						<td colspan="2">
							<hr>
						</td>
					</tr>
					<tr class="totales">
						<td colspan="5"><strong>Subtotal</strong></td>
						<td align="right">$<span id="total_sub">'.number_format($subTotalFactura, 2).'</span> 
						</td>
					</tr>
					<tr>
						<td colspan="5" align="right"><strong>Total</strong></td>
						<td align="right">$<span id="total_suma">'.number_format("0.00", 2).'</span>
						</td>
					</tr>
				</table>
				<hr />
				<strong>Información del pago</strong>';
	$resDet = mysqli_query($conexion, "select d.* from FacturasDetalles d inner join Facturas f on d.facturaID=f.facturaID where d.facturaID=".$facturaID);
	
	$html.='<table width="570">';
	while($rowsDet = mysqli_fetch_array($resDet)){
		$resFactRelacionada = mysqli_query($conexion, "select f.*, mp.metodoPago as nombreMetodoPago, mp.descripcion as descripcionMetodoPago from Facturas f 
		left join FEMetodoPago mp on f.metodoPagoID=mp.metodoPagoID where f.facturaID=".$rowsDet['facturaRelacionadaID']);
		if(mysqli_num_rows($resFactRelacionada)>0){
			$rowFactRelacionada = mysqli_fetch_array($resFactRelacionada);
			$uuidRelacionada= $rowFactRelacionada['uuid'];
			$html.='
				<tr style="background-color: #c1c1c1;">
					<td width="220">
						<strong>Documento relacionadoPP</strong>
						<br />
						'.$uuidRelacionada.'
					</td>
					<td width="180">
						<strong>Moneda del documento relacionado</strong>
						<br />
						Peso Mexicano
					</td>
					<td>
						<strong>Folio del documento relacionado</strong>
						<br />
						'.$rowFactRelacionada['serie'].' '.$rowFactRelacionada['folio'].'
					</td>
				</tr>
				<tr>
					<td>
						<strong>Método de pago del documento relacionado:</strong>
						<br />
						('.$rowFactRelacionada['nombreMetodoPago'].') '.$rowFactRelacionada['descripcionMetodoPago'].'
					</td>
					<td colspan="2">
						<strong>Número parcialidad</strong>
						<br />
						'.$rowsDet['numeroParcialidad'].'
					</td>
				</tr>
				<tr>
					<td>
						<strong>Importe de saldo anterior:</strong>
						<br />
						$'.number_format($rowsDet['saldoAnterior'], 2).'
					</td>
					<td>
						<strong>Importe pagado</strong>
						<br />
						$'.number_format($rowsDet['importePagado'], 2).'
					</td>
					<td>
						<strong>Importe de saldo insoluto</strong>
						<br />
						$'.number_format($rowsDet['saldoInsoluto'], 2).'
					</td>
				</tr>';
		}
	}
	$html.='</table>
				<hr />
			
			</td>
		</tr>
		<tr>
			<td class="sellos" colspan="2" width="500" style="hyphens: auto; word-wrap: break-word; word-break: break-word; font-size:11px;">
				<strong>Sello digital del CFDI:</strong>
				<br />
				'.$sello2.'
				<br />
				<strong>Sello digital del SAT:</strong>
				<br />
				'.$selloSAT2.'
			</td>
		</tr>
		<tr>
			<td colspan="2" valign="top">
				<table class="sellos">
					<tr>
						<td width="80" style="border: solid 0px;" valign="top">
							<img src="require/images/QRCode/'.$_GET['id'].'.png" width="100" />
						</td>
						<td style="hyphens: auto; word-wrap: break-word; word-break: break-word; font-size:11px;">
							<strong>Cadena Original del complemento de certificación digital del SAT:</strong>
							<br />
							'.$cadenaoriginal2.'
							<br />
							<br />
							<strong>RFC del proveedor de certificación:</strong> PPD101129EA3
							&nbsp; &nbsp; &nbsp; &nbsp; 
							<strong>Fecha y hora de certificación:</strong> '.date('d-m-Y h:i:s', $fechaTimbrado).'
							<br />
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="left" style="padding-top: 5px; padding-left:45px;" class="seccionConceptos">
				'.$observaciones.'
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center" style="padding-top: 20px;">

			</td>
		</tr>
	</table>
	<div class="footer" align="left" style="padding-left: 110px;">
		<strong>Expedido en:</strong> '.$lugarExpedicion.'
	</div>
	<div class="footer" align="right" style="background-image: url(require/img/bannercodyfooter.jpg); background-repeat: no-repeat; height:70px; background-position: right; padding-right:25px; font-family: Arial, Helvetica, sans-serif;" valign="bottom">
	<br />
	<br />
	<br />
	<br />
	<strong>Este documento es una representación impresa de un CFDI</strong>
	</div>';
}
else{
	$html= $css.'Error al consultar la factura seleccionada<br />'.$consulta;
}

//echo $html;


$dompdf->loadHtml($html);
$dompdf->setPaper('L', 'Letter');
//$pdf = new PDF_HTML('L', 'mm', 'Letter');
$dompdf->render();
//$dompdf->set_option('defaultFont', 'Arial');
//$dompdf->stream("mi_archivo.pdf");
$dompdf->stream("",array("Attachment" => false));
exit(0);/**/
?>