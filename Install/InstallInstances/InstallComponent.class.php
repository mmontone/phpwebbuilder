<?php
require_once dirname(__FILE__) . "/../BaseDirExample/Configuration/ConfigReader.class.php";

function handle_dberror(&$db) {
	$db->rollback();
	echo $db->lastError();
	exit;
}



class InstallComponent extends Component {
	function initialize() {
		$this->addComponent(new Text(new ValueHolder($s='status')),'status');
		$this->datas = array (
			"serverhost" => "Database Server",
			"basename" => "Database Name",
			"baseuser" => "Database Username",
			"basepass" => "Database Password",
			"basedir" => "Application directory",
			"appdir" => "Classes Directory",
			"pwbdir" => "PHPWebBuilder's Directory",
			"app_class" => "Application Class",
			"site_url" => "Application's Url",
			"pwb_url" => "PHPWebBuilder's Url",
			"db_driver" => "Database Driver",
			"baseprefix" => "Database table prefix",
			"sitename" => "Application Name",
			"page_renderer" => "Page Renderer(Ajax, or Standard)",
			"translator" => "Translator(Spanish or English available)",
			"peardir" => "PEAR Directory (leave blank if in path)"

		);
		$c =& new CompositeWidget();
		$c->addComponent(new Label("Config.php file's location"),'name');
		$c->addComponent(new Input(new ValueHolder($configfile = dirname(dirname(dirname($_SERVER["DOCUMENT_ROOT"] . $_SERVER["PHP_SELF"]))) . "/config.php")), 'value');
		$c->value->addEventListener(array('changed'=>'readConfigFile'), $this);
		$this->addComponent($c,'configfile');
		$default["nivelcount"] = "0";
		$default["serverhost"] = "localhost";
		$default["AdminPass"] = "lihuen";
		$default["TipoGrafico"] = "barras";
		$default["basedir"] = dirname(dirname($_SERVER["DOCUMENT_ROOT"] . dirname($_SERVER["PHP_SELF"])));
		$default["appdir"] = $default["basedir"] . "/MyInstances";
		$default["site_url"] = "http://" . dirname(dirname($_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"])));
		$default["db_driver"] = "MySQLDriver";
		$default["baseprefix"] = "";
		$default["pwbdir"] = dirname($_SERVER["DOCUMENT_ROOT"] . dirname($_SERVER["PHP_SELF"]));
		$default["peardir"] = "";
		$default["page_renderer"] = "AjaxPageRenderer";
		$default["translator"] = "EnglishTranslator";

		foreach ($this->datas as $data => $name) {
			$this->addPrompt("$name:",$data, $default[$data]);
		}
		$this->addComp("Erase data from previous installation:", 'execEliminar',new CheckBox(new ValueHolder($vh='')));
		$this->addComponent(new ActionLink($this, 'do_install_config', 'Create config file', $n=null), 'install');
		$this->addComponent(new ActionLink($this, 'do_install_database', 'Update Database', $n=null), 'installdatabase');
		$this->addComponent(new ActionLink($this, 'do_install_application', 'Install Application', $n=null), 'installapplication');
		$this->readConfigFile();
	}
	function addPrompt($label,$name, $defvalue){
		$this->addComp($label, $name, new Input(new ValueHolder($defvalue)));
	}
	function addComp($label, $name, &$comp){
		$c =& new CompositeWidget();
		$c->addComponent(new Label($label),'name');
		$c->addComponent($comp,'value');
		$this->addComponent($c,$name);

	}
	function readConfigFile(){
		$configfile = $this->configfile->value->getValue();
		$this->status->setValue($t = "Loading Existing Configuration from ".$configfile);
		if (file_exists($configfile)) {
			$conf = new ConfigReader;
			$act = array_merge($conf->read($configfile, "global"),$conf->read($configfile));
			foreach ($act as $data => $name) {
				$this->addPrompt($data,$data,$act[$data]);
			}
		} else {
			$this->status->setValue($t = "Creating new configuration, $configfile doesn't exist");
		}
	}
	function do_install_config() {
		$this->status->setValue($t="installing...");
		$configfile = $this->configfile->value->getValue();
		foreach ($this->datas as $data => $name) {
			$form[$data] = $this->$data->value->getValue();
		}
		$_SESSION[$form["sitename"]] = $_SESSION[sitename];
		$conf = new ConfigReader;
		$conf->write($configfile, $form);
		$conf->load($configfile);
		includemodule($form["pwbdir"]."/database");
		includemodule($form["pwbdir"]."/Model");
		includemodule($form["pwbdir"]."/DefaultCMS");
		includemodule($form["pwbdir"]."/Instances");
		$app = explode(",", $form["app"]);
		includemodule($form["appdir"]);
		foreach ($app as $dir) {
			$d = trim($dir);
			if ($d!=""){
				includemodule($form["basedir"]."/".$d);
			}
		}
	}
	function load_application(){
		$conf =& new ConfigReader;
		$configfile = $this->configfile->value->getValue();
		$c = $conf->load($configfile);
		$m = $c['modules']!=''?$c['modules']:"Core,Application,Model,Instances,View,database,DefaultCMS,QuicKlick,CodeAnalyzer";
		$a = $c['app']!=''?$c['app']:"MyInstances,MyComponents";
		includeAllModules(pwbdir, $m);
		$bdir = $conf->loadDir($c['basedir'],$configfile);
		includeAllModules($bdir, $a);
	}
	function do_install_database(){
		$this->load_application();
		$db =& DBSession::instance();
		$db->beginTransaction();
		if ($this->execEliminar->value->getValue()) {
			$sql = "";
			foreach (get_subclasses('PersistentObject') as $c) {
				if (strcasecmp($c, "ObjSQL") == 0)
					continue;
				$c = new $c;
				$sql .= "DROP TABLE IF EXISTS " . $c->tableName() . ";";
			}
			$sqls = explode(";", $sql);
			print_r($db->batchExec($sqls));
		}
		$sql= TablesChecker::checkTables(false);
		$sqls = explode(";", $sql);
		print_r($db->batchExec($sqls));

		// crear las tablas
		echo 'Requiring ' . dirname(__FILE__) . "/../SQLs/minimal-data.php";
		require_once dirname(__FILE__) . "/../SQLs/minimal-data.php";

		$db->commit();
		$this->status->setValue($t="Installation Successful");
	}

	function do_install_application() {
		$this->load_application();
		$app_class = $this->app_class->value->getValue();
		if (($app_class == null) or ($app_class == '')) {
			echo 'The application is is not defined';
			return;
		}
		eval('$res =& ' . $app_class . '::Install(\'' . $this->basedir->value->getValue() . '\');');

		if (is_object($res)) {
			// Exception raised
			echo 'Error installing application';

			/*
			$dialog =& ErrorDialog::create($res->getMessage());
			$dialog->onAccept(new FunctionObject($this, 'installErrorAccepted'));
			$this->call($dialog);*/
		}
		else {
			echo 'The application has been installed successfully';

			/*
			$dialog =& NotificationDialog::create('The application has been installed successfully');
			$dialog->onAccept(new FunctionObject($this, 'installSuccessAccepted'));
			$this->call($dialog);*/
		}
	}

	function installSuccessAccepted() {

	}

	function installErrorAccepted() {

	}
}
?>