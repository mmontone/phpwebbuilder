<?php

function compile_once($file) {
	global $compilerInstance;
	$compilerInstance->compile($file);
}



class Compiler {
	var $compiled = array ();
	var $toCompile;
	var $toCompileSuffix;
	function Compiler(){
		$this->toCompile = explode(',',constant('compile'));
		$this->toCompileSuffix = implode('-',$this->toCompile);
	}
	function CompileOpt($opt){
		global $compilerInstance;
		return in_array($opt,$compilerInstance->toCompile);
	}
	function compile($file) {
		if (defined('compile')) {
			if (!in_array($file, $this->compiled)) {
				$this->compiled[] = $file;
				$tmpname = $this->getTempFile($file, $this->toCompileSuffix);
				if (@filemtime($tmpname)< @filemtime($file)) {
					$f = file_get_contents($file);
					$this->compileString($f);
					$fo = fopen($tmpname, 'w');
					fwrite($fo, $f);
					fclose($fo);
				}
				require_once $tmpname;
			}
		} else {
			require_once ($file);
		}
	}
	function compileString($str){
		if (preg_match('/\/\*@/', $str) > 0) {
			//echo 'Processing file: ' . $file . '<br />';
			// Notes: 's' makes '.' match 'newline'
			//        '?' after '*' means no-greedy matching
			$f = preg_replace_callback('/\/\*@([[:alpha:]|\_]+)[\s\t]*(.*?)\*\//s', 'processMacro', $f);
			//ereg('\/\*@[[:alpha:]]\s*(.*)\*\/', $f, $matches);
			//print_r($matches);
			//echo($f);
			return $this->compileString($f); // Recursive call (macros generating code with macros)
		} else {
			return $str;
		}

	}
	function getTempFile($file, $extra) {
		return $this->getTempDir($file).'/'. basename($file, '.php') . '-'.$extra.'.php';
	}
	function getTempDir($file){
		if ($this->tempdir===null) {
			if (defined('compile_dir')){
				$this->tempdir = constant('compile_dir');
			}else {
				$this->tempdir=sys_get_temp_dir();
			}
		}
		@mkdir($this->tempdir.dirname($file), 0700, true);
		return $this->tempdir.dirname($file);
	}
}

$compilerInstance = new Compiler;

if (!function_exists('sys_get_temp_dir')) {
	// Based on http://www.phpit.net/
	// article/creating-zip-tar-archives-dynamically-php/2/
	function sys_get_temp_dir() {
		// Try to get from environment variable
		if (!empty ($_ENV['TMP'])) {
			return realpath($_ENV['TMP']);
		} else
			if (!empty ($_ENV['TMPDIR'])) {
				return realpath($_ENV['TMPDIR']);
			} else
				if (!empty ($_ENV['TEMP'])) {
					return realpath($_ENV['TEMP']);
				}

		// Detect by creating a temporary file
		else {
			// Try to use system's temporary directory
			// as random name shouldn't exist
			$temp_file = tempnam(md5(uniqid(rand(), TRUE)), '');
			if ($temp_file) {
				$temp_dir = realpath(dirname($temp_file));
				unlink($temp_file);
				return $temp_dir;
			} else {
				return FALSE;
			}
		}
	}
}
?>