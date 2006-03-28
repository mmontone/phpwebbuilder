<?php

/* Improve!! */
/**
 * Reads an ini file, and defines each constant.
 */

class ConfigReader
{
    function read($file_name) {
    	$configuration = $this->readAll($file_name);
    	return $configuration[$this->sectionName($file_name)];
    }
    function readAll($file_name) {
    	if (file_exists($file_name)){
    		return parse_ini_file($file_name, TRUE);
    	} else {
    		return array();
    	}
    }
    function load($file_name){
    	$conf = $this->read($file_name);
        foreach ($conf as $key => $value) {
        	define($key, $value);
        }
    }
    function write($file_name, $configuration) {
    	$str = "<?/*\n";
    	$this->load($file_name);
    	$nochanges = true;
    	$changes ="";
		foreach($configuration as $data=>$name){
			if ($name != constant($data)){
				$nochanges = false;
				$changes .= $data.":".constant($data)."=>".$name.",<br/>\n"; 
			}
		}		
		$conf = $this->readAll($file_name);
		$conf[$this->sectionName($file_name)] = $configuration;
    	foreach ($conf as $sectName=>$sect){
    		$str .= "[".$sectName."]\n";
    		foreach ($sect as $key => $value) {
    			$str .= $key . "=" . $value . "\n";
    		}	
    	}
    	$str .= "*/?>";
    	$c = fopen($file_name, "w+");
		if (!$c) { //No puedo guardar el archivo
			if (file_exists($file_name) && $nochanges) {
				//El archivo existe, y la configuración está bien
				echo("El Archivo de configuración no pudo ser creado,\n".
								"usando el actual que es correcto.");
			} else {
				echo"El Archivo de configuración no pudo ser creado,\n".
								"deberá crear $file_name a mano.";
				echo "las diferencias son ".$changes;
				echo "dump del archivo:<br />\n";
				echo ereg_replace("\n", "<br/>\n", $str);
			}
		} else {
			fwrite($c,$str);
			fclose($c);
		} 
    }
    function sectionName($file_name){
   		$conf = dirname($file_name)."/serverconfig";
		if (file_exists($conf)){
			$s = file_get_contents($conf);
			return $s;
		}
		else {
	    	return "server";
		}
    }
}
?>