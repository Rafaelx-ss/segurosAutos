<?php
namespace Dompdf;
require_once 'require/dompdf/autoload.inc.php';

$dompdf = new Dompdf();

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use app\assets\AppAsset;

use yii\helpers\Url;

//$rows = Yii::$app->db->createCommand("SELECT * FROM facturas")->queryAll(); 


///conexion
/*error_reporting(E_ALL ^ E_DEPRECATED);
$conexion= mysqli_connect('localhost', 'root', 'r00t123') or die(mysqli_error());
mysqli_select_db($conexion, 'codye043_facturacion') or die(mysqli_error($conexion));*/
$config = include("../config/db.php");
$dsn = $config['dsn'];
preg_match('/dbname=([^;]*)/', $dsn, $matches);
$dbname = $matches[1];
preg_match('/host=([^\;]+)/', $dsn, $matches2);
$host = $matches2[1];
$conexion= mysqli_connect($host,  $config['username'],  $config['password']) or die(mysqli_error());
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
		right: 105px;
	}
	.estadoFactura{
		font-size:13px;
		position:absolute;
		right: 45px;
	}
	.colorRojo{
		color:red;
	}
	.spaceLineNormal {
		margin-top: 5px;
	}
</style>';
// 

$res = mysqli_query($conexion, "select *from Cotizaciones c inner join Clientes cc on c.clienteID=cc.clienteID where c.cotizacionID=".$_GET['id']);
if(mysqli_num_rows($res)>0){
	$k=0;
	$row = mysqli_fetch_array($res);
	$empresa= "Limpieza y Servicios de Mantenimiento en General";
	$rfcEmisor="LSMG123456QW";
	$correoEmisor="cotizacioneslsmg@gmail.com";
	$telefonoEmisor="999 9 85 31 14 - 999 9 45 91 14";
	//var_dump($row);
	$folio= "F-00".$row['cotizacionID'];
	$formaPago= "(".$row['nombreFormaPago'].") ".$row['descripcionFormaPago'];
	$metodoPago= "(".$row['nombreMetodoPago'].") ".$row['descripcionMetodoPago'];
	$razonSocialCliente= $row['razonSocial'];
	$referencia= $row['direccionCliente'];
	$comentario= $row['comentario'];
	$fechaVigencia= $row['fechaVigencia'];
	//$fechaCotizacion= $row['fechaVigencia'];
	
	$tipoComprobanteNombre= $tipoComprobante;
	if($tipoComprobante=="I") $tipoComprobanteNombre= "Ingreso";
	if($tipoComprobante=="E") $tipoComprobanteNombre= "Egreso";
	if($tipoComprobante=="P") $tipoComprobanteNombre= "Pago";
	
	$tipoRelacion= "";
	if($row['tipoRelacionID'] != ""){
		$resTipoRel = mysqli_query($conexion, "SELECT * FROM sattiporelacion where tipoRelacionID=".$row['tipoRelacionID']);
		if(mysqli_num_rows($resTipoRel)>0){
			$rowTipoRel = mysqli_fetch_array($resTipoRel);
			$tipoRelacion= "<strong>Tipo de relación:</strong> (".$rowTipoRel['noombreTipoRelacion'].") ".$rowTipoRel['descripcionTipoRelacion'];
		}
	}
	$cfdiRelacionados= "";
	if($row['cfdiRelacionados'] != ""){
		$cadenaCfdi= explode(",", $row['cfdiRelacionados']);
		if(count($cadenaCfdi)>0){
			$cfdiRelacionados .="<strong>Cfdi relacionados: </strong>";
			foreach ($cadenaCfdi as $index) {
				$cfdiRelacionados .= " ".$index."<br />";
			}
		}
		//$cfdiRelacionados= count($cadenaCfdi)."<strong>Cfdi relacionados:</strong> ".$row['cfdiRelacionados'];
	}
	
	$observaciones='';
	if($row['observaciones'] != "")
		$observaciones='<strong>Observaciones: </strong> '.utf8_encode($row['observaciones']);
	
	
	
	$imagenEmpresa4='<img class="inline-block img-fluid" src="logos/footer_1_20240625055043.png" alt="Logo">';
	$colorTextHeader="white";
	$styleCodyFondoHeader= 'background-image: url(require/img/bannerCody.jpg); background-repeat: no-repeat; border: solid 0px; margin:-1px 0 0 -1px; padding:0px;';
	
	$html= $css.'
	<table width="613" align="center" class="factura" style="'.$styleCodyFondoHeader.' border:solid 0px; margin-top:30px;">
		<tr>
			<td colspan="2" align="left" style="height:100px; padding:0px 25px 0 25px; color:#000; line-height:17px; font-size:11px;border:solid 0px;">
				<table width="100%" class="factura">
				<tr>
					<td width="40">
						'.$imagenEmpresa4.'
					</td>
					<td align="center" style="padding-right:80px;">
						<div class="spaceLineNormal"><b>'.$empresa.'</b></div>
						<div class="spaceLineNormal">'.$rfcEmisor.'</div>
						<div class="spaceLineNormal">'.$correoEmisor.'</div>
						<div class="spaceLineNormal">'.$telefonoEmisor.'</div>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr style="line-height:20px;">
			<td colspan="2" valign="top">
				<div class="seccionRedonda" style="margin-left:25px;margin-right:25px;">
					<strong>Datos del documento</strong>
					<hr>
					<table width="100%" class="factura">
					<tr>
						<td width="50%">
							<div class="spaceLineNormal"><b>Folio del documento:</b> '.$folio.'</div>
							<div class="spaceLineNormal"><b>Nombre del cliente:</b> '.$razonSocialCliente.'</div>
							<div class="spaceLineNormal"><b>Referencia:</b>'.$referencia.'</div>
						</td>
						<td valign="top">
							<div class="spaceLineNormal"><b>Fecha Cotización:</b> '.date('d-m-Y').' 
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
							<b>Vigencia al:</b> '.date('d-m-Y', strtotime($fechaVigencia)).'</div>
							<div class="spaceLineNormal"><b>Observaciones:</b></div>
							'.$comentario.'
						</td>
					</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="seccionConceptos">
				<strong>Conceptos</strong>
				<hr>
				<table width="570" class="conceptos" style="border:solid 0px;">
					<tr class="encabezado">
						<td>
							Descripción
						</td>
						<td width="35">
							Cantidad
						</td>
						<td width="60">
							Unidad
						</td>
						<td width="70" align="right">
							PU
						</td>
						<td width="70" align="right">
							Importe
						</td>
					</tr>';
					$res = mysqli_query($conexion, "select *from Cotizaciones c inner join CotizacionesDetalle d on c.cotizacionID=d.cotizacionID where c.cotizacionID=".$_GET['id']." order by d.cotizacionDetalleID");
					while ($rows = mysqli_fetch_array($res)) {
						$html.='
						<tr>
							<td>
								'.$rows['nombreProducto'].'
							</td>
							<td>
								'.$rows['cantidad'].'
							</td>
							<td>
								'.$rows['unidad'].'
							</td>
							<td align="right">
								$'.number_format($rows['precio'], 2).'
							</td>
							<td align="right">
								$'.number_format($rows['precio'] * $rows['cantidad'], 2).'
							</td>
						</tr>';
						$subTotalFactura += $rows['total'];
					}
	$html.='
					<tr>
						<td colspan="3">
						</td>
						<td colspan="2">
							<hr>
						</td>
					</tr>
					<tr class="totales">
						<td colspan="3">
						</td>
						<td><strong>Subtotal</strong></td>
						<td align="right">$<span id="total_sub">'.number_format($subTotalFactura  , 2).'</span> 
						</td>
					</tr>
					<tr>
						<td colspan="3" align="left">
							'.$tipoRelacion.'
						</td>
						<td align="right"><strong>Iva</strong></td>
						<td align="right">$<span id="total_iva">'.number_format(($subTotalFactura*0.16), 2).'</span>
						</td>	
					</tr>
					<tr>
						<td colspan="3" align="left">
						</td>
						<td align="right" valign="top"><strong>Total</strong></td>
						<td align="right" valign="top">$<span id="total_suma">'.number_format(( $subTotalFactura) + ($subTotalFactura*0.16) , 2).'</span>
						</td>
					</tr>
				</table>
				<hr />
			</td>
		</tr>
		<tr>
			<td colspan="2" align="left" style="padding-top: 5px; padding-left:45px;" class="seccionConceptos">
				<!--'.$observaciones.'-->
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center" style="padding-top: 20px;">

			</td>
		</tr>
	</table>
	<div class="footer" align="center">
		Estos precios pueden sufrir cambios sin previo aviso.
		<br />
		Los precios solo tendrán como máximo 10 días de vigencia apartir de la fecha de envío de la siguiente cotización.
	</div>';
}
else{
	$html= $css.'Error al consultar la factura seleccionada';
}

//echo $html;


$dompdf->loadHtml($html);
$dompdf->setPaper('L', 'Letter');
//$pdf = new PDF_HTML('L', 'mm', 'Letter');
$dompdf->render();
//$dompdf->set_option('defaultFont', 'Arial');
//$dompdf->stream("mi_archivo.pdf");
$folio = "F-" . str_pad($_GET['id'], 4, "0", STR_PAD_LEFT);
$dompdf->stream($folio, array("Attachment" => false));
exit(0);/**/
?>