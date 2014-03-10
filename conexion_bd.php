<?php

$serverMysql = "localhost";
$usuarioMysql = "root";
$passMysql = "";
$base_datos = "tultest";
$tabla = "recurso";

//Accesos a mysql
$conexion = mysql_connect($serverMysql, $usuarioMysql, $passMysql);  
    
//Error de conexión
if (!$conexion) {  
    die('No pudo conectarse: ' . mysql_error() );  
}  

$bd_seleccionada = mysql_select_db($base_datos,$conexion);

//Error al escoger la BD
if (!$bd_seleccionada) {
    die ('No se puede usar la base de datos '. $base_datos . ':' . mysql_error());  
} 

//Sentencia SQL
$sql = "SELECT r.rec_id, r.rec_titulo_largo, r.rec_estatus, r.rec_url 
		FROM $tabla as r";
$resultado = mysql_query($sql);

//Error en la consulta
if (!$resultado) {
    echo "No se pudo ejecutar con exito la consulta ($sql) en la BD: " . mysql_error();
    exit;
}

//Arreglo con los recursos
$recursos = array();
$links = array();
$contador = 0;
$contadorPrincipal = 0;
$contadorSecundario = 0;

//Iteramos para llenar arreglo con valores deseados
while ($recurso = mysql_fetch_assoc($resultado)) {
	$recursos[$contador]["id"] = $recurso["rec_id"];
	$recursos[$contador]["titulo"] = $recurso["rec_titulo_largo"];
	$recursos[$contador]["entidad"] = "ENTIDAD"; //$recurso["rec_entidad"];
	$recursos[$contador]["estado"] = $recurso["rec_estatus"];
	$recursos[$contador]["url"] = $recurso["rec_url"];
	if ($contadorSecundario >= 1000) {
		$contadorPrincipal++;
		$contadorSecundario = 0;
	}
	$links[$contadorPrincipal][$contadorSecundario] = $recurso["rec_url"];

	$contadorSecundario++;
	$contador++;
}

$sqlCount = "SELECT COUNT(*) FROM  `recurso` ";
$consultaCOUNT = mysql_query($sqlCount);
$elementos = mysql_fetch_row($consultaCOUNT);

if (!$elementos) {
    echo "No se pudo ejecutar con exito la consulta ($sql) en la BD: " . mysql_error();
    exit;
}

//Liberamos memoria de la consulta
mysql_free_result($resultado);
  
//Cerramos la conexión
mysql_close($conexion);