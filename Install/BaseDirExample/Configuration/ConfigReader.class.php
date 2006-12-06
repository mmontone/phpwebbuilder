<?php

/* Improve!! */
/**
 * Reads an ini file, and defines each constant.
 */

class ConfigReader
{
    function read($file_name, $section="") {
    	$configuration = $this->readAll($file_name);
    	if ($section==""){
    		return $configuration[$this->sectionName($file_name)];
    	} else {
    		return $configuration[$section];
    	}
    }
    function readAll($file_name) {
    	if (file_exists($file_name)){
    		return parse_ini_file($file_name, TRUE);
    	} else {
    		return array();
    	}
    }
    function readAct($file_name){
    	return array_merge($this->read($file_name,"global"),$this->read($file_name));
    }
    function loadDir($value,$file_name){
    	if ((substr($value,0,1)=='/') or ereg('^[[:alpha:]]:', $value)){
		} else {
			$value= dirname($file_name).'/'.$value;
		}
		if (substr($value,-1)!=="/") $value.='/';
		return $value;
    }
    function load($file_name){
    	$conf = $this->readAct($file_name);
        foreach ($conf as $key => $value) {
        	switch (substr($key,-3)){
        		case 'dir':
	        		$v = $this->loadDir($value,$file_name);
	        		break;
        		default:
        			$v = $value;
        	}
        	if (!defined($key)) define($key, $v);
        }
        return $conf;
    }
    function write($file_name, $configuration) {
    	$str = "<?/*\n";
    	$present = $this->readAct($file_name) ;
    	$changes ="";
		$diff = array_diff_assoc($present, $configuration);
		$nochanges = count($diff)==0;
		$changes .= print_r($diff, TRUE);
		$conf = $this->readAll($file_name);
		$sect = $this->sectionName($file_name);
		$global_keys=array_keys($conf["global"]);
		$global_conf=array();
		foreach($global_keys as $key){
			$global_conf[$key] = $configuration[$key];
			unset($configuration[$key]);
		}
		$conf[$sect] = $configuration;
		$conf["global"] = $global_conf;
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
				//El archivo existe, y la configuraci�n est� bien
				echo("El Archivo de configuraci�n no pudo ser creado,\n".
								"usando el actual que es correcto.");
			} else {
				echo"El Archivo de configuraci�n no pudo ser creado,\n".
								"deber� crear $file_name a mano.";
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