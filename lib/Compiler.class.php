<?php


function processMacro($matches) {
	$macro = $matches[1];
	$body = $matches[2];
	//var_dump($matches);
	//echo '<br/>Processing macro: ' . $macro . '<br />';
	//echo 'Macro: ' . $macro . '<br />';
	//echo 'Body: ' . $body . '<br />';

	$code = $macro . '(\'' . ereg_replace('([\\])*\'', '\1\1\\\'', $body) . '\');';
	//$body = addslashes($body);
	//$code = $macro . '(\'' . $body . '\');';
	//echo 'Evaluating: ' . $code . '<br />';
	$result = null;
	eval ('$result = ' . $code);
	//echo 'Result: ' . $result . '<br />';
	return $result;
}

class Compiler {
	var $compiled = array ();
	var $toCompile;
	var $toCompileSuffix;
    var $tempdir;

	function Compiler() {
		$this->toCompile = explode(',', @constant('compile'));
		$this->toCompileSuffix = implode('-', $this->toCompile);
		$this->compile_path = array(dirname(dirname(__FILE__)).'/',constant('pwbdir'),constant('basedir'));
	}
	function CompileOpt($opt) {
		global $compilerInstance;
		return in_array($opt, $compilerInstance->toCompile);
	}
	function compileFile($file){
		$f='';
		$file = $this->getRealPath($file);
		if (!in_array($file, $this->compiled)) {
				$this->compiled[] = $file;

				//echo 'Adding file: ' . $file . '<br />';

				$f = file_get_contents($file);
				$f = $this->compileString($f,'/'.'#@'.//START_MACRO
									'([[:alpha:]|\_]+)[\s\t]*' .
									''.//START_PARAMS
									'([^#]+|(?R))' .
									'@#'.//END_MACRO
									'/s','processMacro'
					);
				$f = $this->compileString($f,'/__FILE__/s',
					lambda('','return \'\\\''.$file.'\\\'\';'));
				if (Compiler::CompileOpt('recursive')) {
					$self =& $this;
					$f = $this->compileString($f,'/compile_once[\s\t]*\([\s\t]*([^;]*)[\s\t]*\);/s',
					lambda('$matches','return $self->compileRecFile($file,$matches[1]);', get_defined_vars()));
				}
				$f = preg_replace('/(^\<\?php|\?\>[\s\t\n]*$|^\<\?)/','',$f);
		}
		return $f;
	}
	function compileRecFile($outfile, $infile){
		$x = eval('return '.$infile.';');
		return $this->compileFile($x);
	}
	function getRealPath($file){
	   $address = split('(/|\\\\)', $file);
	   $keys = array_keys($address, '..');

	   foreach($keys as $keypos => $key)
	   {
	       array_splice($address, $key - ($keypos * 2 + 1), 2);
	   }

	   $address = implode('/', $address);
	   $address = str_replace('./', '', $address);
	   //echo 'from '.$file.' to '.$address;
	   return $address;
	}
	function compile($file) {
		if (in_array($file, $this->compiled)) return;
		$tmpname = $this->getTempFile($file, $this->toCompileSuffix);
		//print_backtrace('compiling '.$file .' to '.$tmpname);
		if (isset($_REQUEST['recompile']) or ((@constant('recompile')!='NEVER') && (@ filemtime($tmpname) < @ filemtime($file)))) {
			$fo = fopen($tmpname, 'w');
			$f = '<?php '.$this->compileFile($file).' ?>';
			//if ($fo==null) print_backtrace($file." temp: ".$tmpname);
			fwrite($fo, $f);
			fclose($fo);
		}
		require_once $tmpname;
	}
	function compileString($str, $pat, $func) {
		if (preg_match($pat, $str, $matches) > 0) {
			//echo 'Compiling string: ' . $str;
			// Notes: 's' makes '.' match 'newline'
			//        '?' after '*' means no-greedy matching
			//var_dump($matches);
			$str = preg_replace_callback($pat, $func, $str);
			//ereg('\/\*@[[:alpha:]]\s*(.*)\*\/', $f, $matches);
			//echo($str);
			return $this->compileString($str,$pat, $func); // Recursive call (macros generating code with macros)
		} else {
			//echo 'Compiled string: ' . $str . '<br />';
			return $str;
		}

	}
	function &Instance() {
		global $compilerInstance;
		return $compilerInstance;
	}
	function getTempFile($file, $extra) {
		return $this->getRealPath(implode(array($this->getTempDir($file),basename($file, '.php') , $extra , '.php')));
	}
	function getTempDir($file) {
		if ($this->tempdir === null) {
			if (defined('compile_dir')) {
				$this->tempdir = constant('compile_dir');
			} else {
				$this->tempdir = sys_get_temp_dir();
			}
			if (substr($this->tempdir,-1)!=="/") $this->tempdir.='/';
		}
		foreach($this->compile_path as $p) {
			if ($p!=''&&substr($file, 0, strlen($p))==$p) {
				$fd = substr(dirname($file),strlen($p));
				break;
			}
		}
		if ($fd==null) {
			$fd = substr(dirname($file),strlen(constant('basedir')));
		}
		$dir = $this->tempdir . $fd;
		if (substr($dir,-1)!=="/") $dir.='/';
		$res = @ mkdir_r($dir, 0777);
		if (!$res) {
			print_backtrace_and_exit('Cannot make directory: ' . $dir);
		}
		return $dir;
	}
}
$compilerInstance =& new Compiler;
if (defined('compile')){
	function compile_once($file) {
		$compilerInstance =& Compiler::instance();
		$compilerInstance->compile($file);
	}
} else {
	function compile_once($file) {
		require_once($file);
	}
}
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
			$temp_file = tempnam('', '');
			if ($temp_file) {
				$temp_dir = dirname($temp_file);
				return $temp_dir;
			} else {
				return FALSE;
			}
		}
	}
}

function mkdir_r($dirName, $rights=0777){
   if (file_exists($dirName)) return true;
   return mkdir_r(dirname($dirName), $rights) &&
   		  @mkdir($dirName, $rights);
}
?>