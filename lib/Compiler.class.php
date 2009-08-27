<?php

function processMacro($matches) {
	$macro = $matches[1];
	$body = $matches[2];
	//var_dump($matches);
	//echo '<br/>Processing macro: ' . $macro . '<br />';
	//echo 'Macro: ' . $macro . '<br />';
	//echo 'Body: ' . $body . '<br />';

	$code = $macro . '(\'' . preg_replace('/([\\\\])*\'/', '\1\1\\\'', $body) . '\');';
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
	var $file_uses_class= array();
	var $file_reqs_class= array();
	var $usageRules = array();
	var $compiling=false;
	function Compiler($options) {
		$this->toCompile = $options;
		$this->toCompileSuffix = implode('-', $this->toCompile);
		$this->compile_path = array(Compiler::getRealPath($this->tempDir()),Compiler::getRealPath(dirname(dirname(__FILE__)).'/'),Compiler::getRealPath(constant('pwbdir')),Compiler::getRealPath(constant('basedir')));
	}
	//Find the option in the options array
	function CompileOpt($opt) {
		global $compilerInstance;
		return in_array($opt, $compilerInstance->toCompile);
	}
	/** Compiling the file */
	function compileFile($file){
		$f='';
		$file = $this->getRealPath($file);

		if (!in_array($file, $this->compiled)) {
				$this->compiled[$file] = $file;
				$this->actualFile[] = $file;
		  //echo 'Adding file: ' . $file . '<br />';

				$f = file_get_contents($file);
				/** Replace the __FILE__ constant with the actual path of the file */
				$f = $this->compileString($f,'/__FILE__/s',
					lambda('','$x = \'\\\''.$file.'\\\'\';return $x;'));
				/** Process the macros */
				$f = $this->compileString($f,'/'.'#@'.//START_MACRO
									'([[:alpha:]|\_]+[0-9]*)[\s\t]*' .
									''.//START_PARAMS
									'([^#]+|(?R))' .
									'@#'.//END_MACRO
									'/s','processMacro'
					);
				if (Compiler::CompileOpt('minimal')||Compiler::CompileOpt('recursive') || Compiler::CompileOpt('optimal')) {
					if (!$this->compilingClasses || Compiler::CompileOpt('minimal')) {
						if (Compiler::CompileOpt('minimal')||Compiler::CompileOpt('optimal')) {
							$this->getInvolvedClasses($f,$file);
						}
						//Remove the PHP tags of the beggining
						$f = preg_replace('/(^\<\?php|\?\>[\s\t\n]*$|^\<\?)/','',$f);
						//Remove the compile_once tags, they've already been used
						eval(str_replace('compile_once','',$f));
					}
					$self =& $this;
					/** */
					$f = $this->compileString($f,'/compile_once[\s\t]*\([\s\t]*([^;]*)[\s\t]*\);/s',
					$lam = lambda('$matches','$y = $self->compileRecFile($file,$matches[1]); return $y;', get_defined_vars()));
					delete_lambda($lam);
				}
				$f = preg_replace('/(^\<\?php|\?\>[\s\t\n]*$|^\<\?)/','',$f);
				/** Remove from the files queue */
				array_pop($this->actualFile);
		}
		/** Returns the contents of the file */
		return $f;
	}
	function forCompilation($class){
		$comp =& Compiler::Instance();
		$file = $comp->class_in_file[strtolower($class)];
		unset($comp->compiled[$file]);
		$f = $comp->compileFile($file);
		eval($f);

	}
	/** Add a class to the ones used by a file */
	function usesClass($file, $class){
		$comp =& Compiler::Instance();
		$comp->file_uses_class[$file][] = $class;
	}
	/** Add a file as compiled, and register the class's file*/
	function markAsCompiled($class, $file){
		$comp =& Compiler::Instance();
		$comp->classesCompiled[$file] = $file;
		$comp->class_in_file[strtolower($class)] = $file;
	}
	/** Returns if a class has already been compiled */
	function compiledClass($class){
		return isset($this->classesCompiled[$this->class_in_file[strtolower($class)]]);
	}
	/** Returns if a class has already been declared */
	function declaredClass($class){
		return isset($this->class_in_file[strtolower($class)]);
	}
	/** Returns if a class has already been required. In optimal compilation, adds the class to the compilation file */
	function requiredClass($class){
		if(Compiler::CompileOpt('optimal')){
			$inst =& Compiler::Instance();
			$c = $inst->compileClass($class);
			if ($c!=null){
				$old = file_get_contents($inst->compiledOutput);
				$f = fopen($inst->compiledOutput, 'w');
				fwrite($f,substr($old, 0,-2).$c.'?>');
				fclose($f);
				$cf = $inst->getCompFile();
				$cfo = fopen($cf, 'w');
				fwrite($cfo, serialize($inst));
				fclose($cfo);
				if (!class_exists($class)) {
					eval($c);
				}
			}
		}
		return class_exists($class);
	}
	/** Adds a rule that tells whether a file uses a class */
	function addClassUsageRule($ereg, $pos){
		$this->usageRules[]=array('rule'=>$ereg,'pos'=>$pos);
	}
	/** Finds all the classes involved by another class */
	function getInvolvedClasses($str, $file){
		$int = preg_match_all('/[\s\t\n]+class[\s\t\n]+(\w+)/',$str, $matches_dec);
		//if ($int>1) {print_r($matches_dec); exit;}
		foreach($matches_dec[1] as $m){
			@$this->class_in_file[strtolower($m)] = $file;
		}
		if (!is_array(@$this->file_uses_class[$file]))$this->file_uses_class[$file] = array();
		if (!is_array(@$this->file_reqs_class[$file]))$this->file_reqs_class[$file] = array();

		preg_match_all('/[\s\t\n]+extends[\s\t\n]+(\w+)/',$str, $matches_pre);
		foreach($matches_pre[1] as $m){
			$this->file_reqs_class[$file][] = $m;
		}


		foreach($this->usageRules as $ur){
			preg_match_all($ur['rule'],$str, $matches_post);
			foreach($matches_post[$ur['pos']] as $m){
				$this->file_uses_class[$file][] = $m;
			}
		}
	}
	/** Checks if a class was compiled */
	function classCompiled($class){
		$inst =& Compiler::Instance();
		return (isset($inst->classesCompiled[@$inst->class_in_file[strtolower($class)]]))
			|| (!$inst->compiling && class_exists($class));
	}
	/** Compiles a class */
	function compileClass($class){
		/*Chequeno no compilada, Compilo las anteriores, marco esta como compilada, compilo las siguientes*/
		$file = @$this->class_in_file[strtolower($class)];
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
	   $file = str_replace('\\', '/', $file);
	   if (strpos($file, './')==FALSE) return $file;
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
		$file = Compiler::getRealPath($file);
		if (in_array($file, $this->compiled)) return;
		$tmpname = $this->getTempFile($file, $this->toCompileSuffix);
		//echo ('compiling '.$file .' to '.$tmpname . '<br/>');
		if (isset($_REQUEST['recompile']) or ((@constant('recompile')!='NEVER') && (@ filemtime($tmpname) < @ filemtime($file)))) {
			$fo = fopen($tmpname, 'w');
			$this->addClassUsageRule('/(\w+)[\s\t\n]*::/',1);
			$this->addClassUsageRule('/new[\s\t\n]+(\w+)/',1);
			$this->addClassUsageRule('/::[\s\t\n]*GetWithIndex\(\'?(\w+)\'?/i',1);
			$this->addClassUsageRule('/PersistentCollection\(\'?(\w+)\'?/i',1);
			$this->addClassUsageRule('/::[\s\t\n]*GetWithId\(\'?(\w+)\'?/i',1);
			$this->addClassUsageRule('/\'type\'[\s\t\n]*=>[\s\t\n]*\'?(\w+)\'?/i',1);
			$this->addClassUsageRule('/->defineVar\(\'\w+\'[\s\t\n]*,[\s\t\n]*\'?(\w+)\'?[\s\t\n]*\)/i',1);
			$f = $this->compileFile($file);
			//$meta=find_metadata();
			$f = '<?php '.$f.' ?>';
			if (Compiler::CompileOpt('optimal')){
				$this->compiledOutput = $tmpname;
				$this->compiling = true;
				Compiler::markAsCompiled('Compiler', __FILE__);
				Compiler::markAsCompiled('OQLCompiler', __FILE__);
				Compiler::markAsCompiled('parent', __FILE__);
				$this->compiled = array();
				$this->compilingClasses = true;
				$f = '<?php '.$this->compileClass(constant('app_class'));
				$f .=
					find_metadata().
					' ?>';

				$this->createCompilationFile();
				fwrite($fo, $f);
				fclose($fo);
				return; //DO NOT INCLUDE ANYTHING ELSE.
			}
			fwrite($fo, $f);
			fclose($fo);
			//Do not include, will be included later
			if (Compiler::CompileOpt('recursive') || Compiler::CompileOpt('minimal')){
				return;
			}
		}
		require_once $tmpname;
	}
	function createCompilationFile(){
		$cf = $this->getCompFile();
		$cfo = fopen($cf, 'w');
		$this->compiling = false;
		fwrite($cfo, serialize($this));
		$this->compiling = true;

		fclose($cfo);

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
	function actualFile(){
		$comp =& Compiler::Instance();
		return $comp->actualFile[count($comp->actualFile)-1];
	}
	function &Instance() {
		global $compilerInstance;
		return $compilerInstance;
	}
	function getTempFile($file, $extra) {
		return $this->getRealPath(implode(array($this->getTempDir($this->getRealPath($file)),basename($file, '.php') , $extra , '.php')));
	}
	function getCompFile(){
		return $this->getTempDir('').strtolower(constant('app_class')).'compiled.php';
	}
	function tempDir(){
		if ($this->tempdir === null) {
			if (defined('compile_dir')) {
				$this->tempdir = constant('compile_dir');
			} else {
				$this->tempdir = sys_get_temp_dir();
			}

			$this->tempdir = str_replace('\\','/',$this->getRealPath($this->tempdir));
			if (substr($this->tempdir,-1)!=="/") $this->tempdir.='/';
            $this->tempdir.=constant('app_class')."/";
		}
		return $this->tempdir;
	}
	function getTempDir($file) {
		$file=str_replace('\\','/',$file);
		$file=str_replace('//','/',$file);
		$fd = dirname($file);

		foreach($this->compile_path as $p) {
			//The beggining of the path matches
			if ($p!=''&&substr($file, 0, strlen($p))==$p) {
				//Save the relative dir of the file
				$fd = substr(dirname($file),strlen($p));
				break;
			}
		}

		//print_r($this->compile_path);
		$dir = $this->tempdir . $fd;
		if (substr($dir,-1)!=="/") $dir.='/';
		$res = @ mkdir_r($dir, 0777);
		if (!$res) {
			print_backtrace_and_exit('Cannot make directory: ' . $dir);
		}
		return $dir;
	}
	function Create(){
		$options = explode(',', @constant('compile'));
		foreach(array('minimal', 'optimal','recursive')as $opt){
			if (in_array($opt, $options)){
				$compilerclass = $opt.'Compiler';
				return new $compilerclass($options);
			}
		}
		return new StandardCompiler($options);
	}
	function initPrecompilingRequest(){

		$this->compile(dirname(__FILE__).'/Compiler/PHPCC.class.php');
		$file = $this->getTempDir('') . strtolower(constant('app_class')) . '.php';
		if (!file_exists($file)) {
			$_REQUEST['recompile'] = 'yes';
		}
		if (isset ($_REQUEST['recompile'])) {
			$fo = fopen($file, 'w');
			$f = '<?php ' . getIncludes() . ' ?>';
			fwrite($fo, $f);
			fclose($fo);

		}
		$this->compile($file);
		//var_dump($this->class_in_file);
		//$comp->compiled = array();
	}
	function initRequest(){
		$this->compile(dirname(__FILE__).'/Compiler/PHPCC.class.php');
		eval (getIncludes());
	}
	function finishRequest(){}
	function requireCompiled($class){
		$file = @$this->class_in_file[strtolower($class)];
		if ($file!==null) {
			$tmpname = $this->getTempFile($file, $this->toCompileSuffix);
			require_once($tmpname);
		}
	}
}

class StandardCompiler extends Compiler{}

class OptimalCompiler extends Compiler{
	function initRequest(){
		$this->initPrecompilingRequest();
	}
}
class RecursiveCompiler extends Compiler{
	function initRequest(){
		$this->initPrecompilingRequest();
	}
}

/**
 * En el recompile, compila cada uno de los archivos como en la compilaci�n com�n.
 * Adem�s, guarda el nombre de cada clase en un array.
 * En la carga normal, carga una funci�n que busca el archivo de la clase en el
 * array armado anteriormente.
*/
class MinimalCompiler extends Compiler{
	function initRequest(){
		$this->compilingClasses= true;
		$f = '
					function __autoload($class) {
					  $inst =& Compiler::Instance();
				      //echo "autoloading $class<br/>";
				       $inst->requireCompiled($class);
			        }';
		eval($f);
		$this->initPrecompilingRequest();
	}
	function finishRequest(){

	}
	function initPrecompilingRequest(){
		$file = $this->getTempDir('') . strtolower(constant('app_class')) . '.php';
		if (!file_exists($file)) {
			$_REQUEST['recompile'] = 'yes';
		}
		if (isset ($_REQUEST['recompile'])) {
			$fo = fopen($file, 'w');
			$f = '<?php ' . getIncludes() . ' ?>';
			fwrite($fo, $f);
			fclose($fo);
			$pre_declared=get_declared_classes();
			$this->compile(dirname(__FILE__).'/Compiler/PHPCC.class.php');
			$this->compile($file);
			//var_dump(array_diff(get_declared_classes(),$pre_declared));
			$this->createCompilationFile();
		}
		//var_dump($this->class_in_file);
		//$comp->compiled = array();
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
			$temp_file = tempnam(basedir.'/tmp/', '');
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
   if (file_exists($dirName) || $dirName=='/') return true;
   return mkdir_r(dirname($dirName), $rights) &&
   		  @mkdir($dirName, $rights);
}

$compilerInstance =& Compiler::create();
$compfile = $compilerInstance->getCompFile();
if ((Compiler::CompileOpt('optimal') || Compiler::CompileOpt('minimal') ) && file_exists($compfile) && !@$_REQUEST['recompile']){
	$compilerInstance = unserialize(file_get_contents($compfile));
	var_dump($compilerInstance);
}

function compile_once($file) {
	$compilerInstance =& Compiler::instance();
	$compilerInstance->compile($file);
}

?>