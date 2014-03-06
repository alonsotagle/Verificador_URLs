<!DOCTYPE html>
<html>
<head>
	<title>Analizar Links</title>
	<meta charset="utf-8"/>
	<meta name="description" content="Analizador Links"/>
	<meta name="author" content="Alonso Tagle"/>
	<link rel="stylesheet" type="text/css" href="estilos.css">
</head>
<body>

	<div>No. de Links analizados:</div>
	<p><?php
	require "conexion_bd.php";
	echo $elementos[0];
	?></p>

	<table class="tables">
		<thead>
			<tr>
				<th>Título</th>
				<th>Entidad</th>
				<th>Estatus del recurso</th>
				<th>URL</th>
				<th>Estatus del link</th>
			</tr>
		</thead>
		<tbody>
<?php

//Función que verifica la url
function verifica($url){

	//Inicializo CURL
	$curl = curl_init($url);
    //Tiempo para conexión
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
	//Para regresar el HEAD
	curl_setopt($curl, CURLOPT_HEADER, true);
	//Sólo petición HEAD sin BODY
	curl_setopt($curl, CURLOPT_NOBODY, true);
	//Para que curl_exec() pueda ser asignado a una variable
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	//Incluir páginas https
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

	//Obtengo una respuesta
	$response = curl_exec($curl);
	//print_r($response);

	//Mostrar información de la respuesta
	$info = curl_getinfo($curl, CURLINFO_HTTP_CODE);

	if ($info > 302) {
		return $info;
		//echo $info['http_code'] .'<a href="'. $info['url'] .'"> ' . $info['url'] .'</a><br>';
	}
	
	// Comprobar si occurió algún error
	if(curl_errno($curl)){
		//Imprimir el número del error
	    return curl_errno($curl);
	}

	//Cerrar petición
	curl_close($curl);

	if ($response) return 100;

	return 6;
}

function multi($links){

	//Para manejar llamados curl paralelos
	$curl_multiple = curl_multi_init();

	//Iterar arreglo configurando curl's individuales
	foreach ($links as $i => $url) {
		//Iniciando curl individual
		$curl_individual[$i] = curl_init($url);

		//Configurando curl individual
		//Se manda la página
	    curl_setopt($curl_individual[$i], CURLOPT_URL, $url);
	    //Tiempo para conexión
		curl_setopt($curl_individual[$i], CURLOPT_CONNECTTIMEOUT, 30);
		//Para regresar el HEAD
		curl_setopt($curl_individual[$i], CURLOPT_HEADER, true);
		//Sólo petición HEAD sin BODY
		curl_setopt($curl_individual[$i], CURLOPT_NOBODY, true);
		//Para que curl_exec() pueda ser asignado a una variable
		curl_setopt($curl_individual[$i], CURLOPT_RETURNTRANSFER, true);
		//Incluir páginas https
		curl_setopt($curl_individual[$i], CURLOPT_SSL_VERIFYPEER, false);

		curl_multi_add_handle($curl_multiple,$curl_individual[$i]);
	}

	do{
		set_time_limit(0);
		$estado = curl_multi_exec($curl_multiple,$active);
	} while($active);

	foreach ($links as $i => $url) {
			$info = curl_getinfo($curl_individual[$i]);
			$res[$i] = $info["http_code"];
			curl_multi_remove_handle($curl_multiple,$curl_individual[$i]);
			curl_close($curl_individual[$i]);
	}
	curl_multi_close($curl_multiple);

	/*
	echo "<pre>";
	var_dump($res);
	echo "</pre>";
	*/
	return $res;

}

echo "<pre>";
//var_dump($recursos);
echo "</pre>";

/*
foreach ($links as $key => $value) {
	echo "</pre>";
	echo verifica($value[0]);
}
*/

$respuesta = multi($links[0]);

foreach ($links[0] as $i => $valor) {
	echo $recursos[$i]['estado'];
	if ($recursos[$i]['estado'] > 302) {
		if ($recursos[$i]['estado'] == 1) {
			$recursos[$i]['estado'] = "Activo";
		}else{
			$recursos[$i]['estado'] = "Oculto";
		}
		echo "<tr>
					<td>".$recursos[$i]['titulo']."</td>
					<td>".$recursos[$i]['entidad']."</td>
					<td>".$recursos[$i]['estado']."</td>
					<td>".$recursos[$i]['url']."</td>
					<td>".$respuesta[$i]."</td>
				</tr>";
	}
}
	
/*
//Se ingresa directo
if ( $recursos ) {
	//Iterar por el arreglo de resultados
	for ($i = 0; $i < sizeof($recursos); $i++) {
		$url = $recursos[$i]["url"];
		//Reiniciar tiempo de espera
		set_time_limit(0);
		//Mandamos a la función
		$resultado = verifica($url);
		//Si está activo
		if ($resultado == 100 || $resultado == 60) {
			//echo "$key <a href='$value'>$value</a> <div style='color: green'>Existe</div><br>";
		//Si está desactivo
		}else{
			//Imprimimos id y página
			/*echo "<pre>";
			var_dump($recursos[$i]);
			echo "</pre>";
			echo "<tr>
					<td>".$recursos[$i]['titulo']."</td>
					<td>".$recursos[$i]['entidad']."</td>
					<td>".$recursos[$i]['estado']."</td>
					<td>".$recursos[$i]['url']."</td>
					<td>Error</td>
				</tr>";
			
			//Revisamos por el tipo de error
			// switch ($resultado) {
			// 	case 200:
			// 		echo $resultado . " Direccion no v&aacute;lida";
			// 		break;

			// 	case 6:
			// 		echo $resultado . " Fallo al resolver Host";
			// 		break;

			// 	case 28:
			// 		echo $resultado . " Tiempo de respuesta excedido";
			// 		break;

			// 	case 7:
			// 		echo $resultado . " Fallo al conectar al Host";
			// 		break;
				
			// 	default:
			// 		echo $resultado;
			// 		break;
			// }
			// echo "</div><br>";
		}
	}
}
*/
?>
		</tbody>
	</table>
</body>
</html>