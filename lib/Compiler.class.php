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
	var $compilingClasses = false;
	var $classesCompiled = array();
	var $class_in_file= array();
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
				$this->actualFile[] = $file;
				//echo 'Adding file: ' . $file . '<br />';

				$f = file_get_contents($file);
				$f = $this->compileString($f,'/__FILE__/s',
					lambda('','$x = \'\\\''.$file.'\\\'\';return $x;'));
				$f = $this->compileString($f,'/'.'#@'.//START_MACRO
									'([[:alpha:]|\_]+)[\s\t]*' .
									''.//START_PARAMS
									'([^#]+|(?R))' .
									'@#'.//END_MACRO
									'/s','processMacro'
					);
				if (Compiler::CompileOpt('recursive')) {
					$self =& $this;
					if (!$this->compilingClasses) $this->getInvolvedClasses($f,$file);
					$f = $this->compileString($f,'/compile_once[\s\t]*\([\s\t]*([^;]*)[\s\t]*\);/s',
					lambda('$matches','$y = $self->compileRecFile($file,$matches[1]); return $y;', get_defined_vars()));
				}
				$f = preg_replace('/(^\<\?php|\?\>[\s\t\n]*$|^\<\?)/','',$f);
				array_pop($this->actualFile);
		}
		return $f;
	}
	function usesClass($file, $class){
		$comp =& Compiler::Instance();
		$comp->file_uses_class[$file][] = $class;
	}
	function markAsCompiled($class, $file){
		$comp =& Compiler::Instance();
		$comp->classesCompiled[$file] = $file;
		$comp->class_in_file[strtolower($class)] = $file;
	}
	function getInvolvedClasses($str, $file){
		$int = preg_match_all('/[\s\t\n]+class[\s\t\n]+(\w+)/',$str, $matches_dec);
		//if ($int>1) {print_r($matches_dec); exit;}
		foreach($matches_dec[1] as $m){
			$this->class_in_file[strtolower($m)] = $file;
		}
		if (!is_array($this->file_uses_class[$file]))$this->file_uses_class[$file] = array();
		if (!is_array($this->file_reqs_class[$file]))$this->file_reqs_class[$file] = array();
		preg_match_all('/(\w+)::/',$str, $matches_post);
		foreach($matches_post[1] as $m){
			if ($m!='parent') {
				$this->file_uses_class[$file][] = $m;
			}
		}

		preg_match_all('/new[\s\t\n]+(\w+)/',$str, $matches_post);
		foreach($matches_post[1] as $m){
			$this->file_uses_class[$file][] = $m;
		}

		preg_match_all('/::GetWithIndex\(\'?(\w+)\'?/i',$str, $matches_post);
		foreach($matches_post[1] as $m){
			$this->file_uses_class[$file][] = $m;
		}

		preg_match_all('/::GetWithId\(\'?(\w+)\'?/i',$str, $matches_post);
		//if (count($matches_post[1])) print_r($matches_post[1]);
		foreach($matches_post[1] as $m){
			$this->file_uses_class[$file][] = $m;
		}

		preg_match_all('/\'type\'[\s\t\n]*=>[\s\t\n]*\'?(\w+)\'?/i',$str, $matches_post);
		foreach($matches_post[1] as $m){
			$this->file_uses_class[$file][] = $m;
		}

		preg_match_all('/[\s\t\n]+extends[\s\t\n]+(\w+)/',$str, $matches_pre);
		foreach($matches_pre[1] as $m){
			$this->file_reqs_class[$file][] = $m;
		}
		preg_match_all('/->defineVar\(\'\w+\'[\s\t\n]*,[\s\t\n]*\'?(\w+)\'?[\s\t\n]*\)/i',$str, $matches_post);
		//if (count($matches_post[1])) print_r($matches_post[1]);
		foreach($matches_post[1] as $m){
			$this->file_uses_class[$file][] = $m;
		}


	}
	function compileClass($class){
		/*Chequeno no compilada, Compilo las anteriores, marco esta como compilada, compilo las siguientes*/
		$file = $this->class_in_file[strtolower($class)];
		if ($file==null) {
			//echo "$class was not defined ".print_r($this->actualFile,TRUE);
			return '';
		}
		if (isset($this->classesCompiled[$file])) return '';
		$this->actualFile[] = $file;
		//echo "$class ($file)";
		$this->classesCompiled[$file] = $file;
		$comp='';
		foreach ($this->file_reqs_class[$file] as $class1) {
			$comp .= $this->compileClass($class1);
		}
		$comp .= $this->compileFile($file);
		foreach ($this->file_uses_class[$file] as $class2) {
			$comp .= $this->compileClass($class2);
		}
		array_pop($this->actualFile);
		return $comp;
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
			if (Compiler::CompileOpt('recursive')){
				Compiler::markAsCompiled('Compiler', __FILE__);
				//Compiler::markAsCompiled('Session', $this->class_in_file[strtolower('Session')]);
				$this->compiled = array();
				//print_r($this->class_in_file);
				//print_r($this->file_uses_class);
				//print_r($this->file_reqs_class);
				$this->compilingClasses = true;
				$f = '<?php '.$this->compileClass(constant('app_class')).' ?>';
				$this->compilingClasses = false;
				//echo 'Used Files: '.print_r(array_intersect($this->class_in_file, $this->classesCompiled), TRUE);
				//echo 'Unused Files: '.print_r(array_diff(array_unique($this->class_in_file), $this->classesCompiled), TRUE);
			}
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