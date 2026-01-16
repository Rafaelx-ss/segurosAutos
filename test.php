<?php
$queryValor = 'metodoApiID,apiID';
$valores = explode(",", $queryValor);
$controlQuery = "Select MetodosApis.metodoApiID,CONCAT(Apis.nombreApi,' ',MetodosApis.tipoMetodoApi) as metodo from MetodosApis,Apis where MetodosApis.apiID='?1' and MetodosApis.metodoApiID = '?2'";
if(count($valores) == 1){
	$nQry = str_replace("'?1'", "'\".\$model['".$valores[0]."'].\"'", $controlQuery);
}else{
	$inc = 1;
	$nQry = $controlQuery;
	foreach($valores as $dataVal){
		$nQry = str_replace("'?".$inc."'", "'\".\$model['".trim($dataVal)."'].\"'", $nQry);
		$inc++;
	}
}
echo $nQry;
//$nQry = str_replace("'?'", "'\".\$model['".$indexCampos['nombreCampo']."'].\"'", $indexCampos['controlQuery']);
?>