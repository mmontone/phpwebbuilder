<?

require_once dirname(__FILE__) . '/FlowView.class.php';

class FieldView extends FlowView
{
	var $field;
    var $config;

    function FieldView(&$field, &$config) {
	   $this->field =& $field;
       $this->config =& $config;
    }
}

?>