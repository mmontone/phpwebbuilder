<?

require_once dirname(__FILE__) . '/DataField.class.php';

class enumField extends TextField {
      var $vals;
      function enumField ($name, $isIndex, $vals) {
         parent::TextField ($name, $isIndex);
         $this->vals =& $vals;
      }
	function &visit(&$obj) {
		return $obj->visitedEnumField($this);
	}
}
