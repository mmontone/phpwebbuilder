<?php

class CodeAnalyzer extends Component {
	var $directories;
	var $output;
	var $cases;
	var $recursive;

	function initialize() {
		$this->recursive =& new ValueHolder(true);
		$this->directories =& new ValueHolder(basedir);
		$this->cases =& new ObjectHolder(new Collection);
		$options =& new Collection;
		foreach (get_subclasses('AnalisysCase') as $analisys_case_class) {
			$options->add(new $analisys_case_class);
		}
		$this->output =& new ValueHolder('');
		$this->addComponent(new Input($this->directories), 'directories_input');
		$this->addComponent(new CheckBox($this->recursive), 'recursive_checkbox');
		$this->addComponent(new SelectMultiple($this->cases, $options), 'cases_select');
		$output_ta =& new TextAreaComponent($this->output);
		$this->addComponent($output_ta, 'output_textarea');
		$output_ta->view->addCSSClass('output');
		$this->addComponent(new CommandLink(array('text' => 'Analyze', 'proceedFunction' => new FunctionObject($this, 'analyze'))), 'analyze_button');
	}

	function analyze() {
		$c =& $this->cases->getValue();
		$cases =& $c->elements();
		//print_r($cases);

		$directories = explode(',', $this->directories->getValue());
		if ($this->recursive->getValue()) {
			$getfiles = 'getfilesrec';
		}
		else {
			$getfiles = 'getfiles';
		}

		$files = array();
		foreach($directories as $dir) {
			$files = array_merge($files, $getfiles($lam = lambda('$file','return $v=substr($file, -4)==".php";', $a=array()),$dir));
			delete_lambda($lam);
		}

		$output = '';

		foreach(array_keys($cases) as $case) {
			$analisys_case =& $cases[$case];

			foreach($files as $file) {
				$output .= $analisys_case->analyze($file);
			}
			if ($output != '') {
				$output .= "\n\n";
			}
		}

		$this->output->setValue($output);
	}
}

?>