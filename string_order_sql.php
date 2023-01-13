<?php
/*
SCRIPT PARA AJUSTAR CADENA DE CARACTERES CUANDO SE RECIBE UN LISTADO CON NOMBRES

<?php
$cadena = "11001030600020120000100/D11001030600020120000100solicitud201312265223.doc
11001030600020120000100/D11001030600020120000100Solicitud201312274652.pdf
11001030600020130002400/D11001030600020130002400SCCONTENIDOCONFLICTO2013130111056.PDF
11001030600020130004100/D11001030600020130004100CONFLICTO2013131162349.PDF
11001030600020130004200/D11001030600020130004200CONFLICTO201313117720.PDF
11001030600020150010200/D110010306000201500102001AUTORIZAPUBLICACION202098104244.pdf
11001030600020170010500/D11001030600020170010500Notificacion_T133034011806752342.pdf
11001030600020170010500/D11001030600020170010500Notificacion_T133034014123963845.pdf
11001030600020170010500/D11001030600020170010500Notificacion_T133057470548352465.pdf
11001030600020170010500/D11001030600020170010500Notificacion_T133083296264631996.pdf
11001030600020170010500/D11001030600020170010500Notificacion_T133119695609775824.pdf
11001030600020190002700/D110010306000201900027000AUTOADMITEREVOCATORIA2020249105.doc.doc
11001030600020190012600/D110010306000201900126001AUTORIZAPUBLICACION20209810575";
?>

*/

require 'nuevosql1.php';

$array = explode("\n", $cadena);

/*
print"<pre>";
print_r($array);
print"<pre>";
*/

/*Recorrer todo el archivo con cadenas para reemplazar*/
$count=0;

foreach($array as $dt){
	
	/*Separar primero el numero de radicado*/
	$radicadoynombre = explode("/", $dt);
	
	/*Separar por el punto para saber cual es la extension*/
	$nombreyextension = explode(".", $radicadoynombre[1]);
	
	/*Sacar la extension del documento revisando que el archivo si traiga extension 
	y que sea extension menor de tres digitos de lo contrario es basura 
	o es que el nombre tiene un punto pero esta en el intermedio del texto ej: documento.acuerdo.2020.pdf pero sin el .pdf */
	if(sizeof($nombreyextension)>1){
		$extension = $nombreyextension[(sizeof($nombreyextension)-1)];
	}else{
		$extension = "";
	}
	
	/*Organizar el nombre solo del documento
	hay que tener en cuenta los posibles nombres incorrectos
	como sin extension entonces no habria ultimo registro
	como si tiene nombres con puntos
	*/
	if($extension == ""){
		/*Indica que no tiene extension entonces el nombre del archivo sera igual al completo*/
		$nombresolo = $radicadoynombre[1];
		$tipo = "1";
	}elseif(sizeof($nombreyextension)==2){
		/*Si el nombre es estandar y tiene solo un punto en la extension entonces se sabra por la cantidad de campos en el explode*/
		$nombresolo = $nombreyextension[0];
		$tipo = "2";
	}else{
		/*Si la cadena tiene extension pero contiene mas de un . dentro como or ejemplo: documento.acuerdo.2020.pdf o documento.acuerdo.pdf.pdf */
		$pos = strrpos($radicadoynombre[1], ".".$extension);
		if($pos !== false){
			$nombresolo = substr_replace($radicadoynombre[1], "", $pos, strlen(".".$extension));
		}
		
		$tipo = "3";
	}
	

	//echo $count." - ".$tipo." - INSERT INTO TAL ".$radicadoynombre[0]." NMBRE TAL ".$nombresolo."  extension ".$extension;
	
	echo "<br>";
	echo "<br>";
	echo "UPDATE T121DRDOCSPROC SET A121NOMBDOCU = '".$nombresolo."', A121TIPOARCH = '.".$extension."' WHERE (A121LLAVPROC = '".$radicadoynombre[0]."' AND UPPER(A121NOMBDOCU) = UPPER('".$nombresolo."')  	AND UPPER(ISNULL(A121TIPOARCH,'.doc')) = UPPER(ISNULL(A121TIPOARCH,'.".$extension."')));";
	
	$count++;
	
}
?>
