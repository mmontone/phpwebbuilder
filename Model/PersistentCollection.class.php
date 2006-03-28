<?

$DBObject = DBObject;

class PersistentCollection {
      var $order;
      var $conditions;
      var $dataType;
      var $limit=10;
      var $offset=0;
      var $formphp = "Action.php";
	function PersistentCollection($dataType=""){
		$this->dataType = $dataType;
                $this->conditions = array();
	}
      function findById($id){
        $obj = new $this->dataType;
        $obj->setID($id);
        return $obj;
      }
      function tableName () {
        $obj = new $this->dataType;
        return $obj->tableName();
      }
      function conditions () {
      	$cond="";
        if ($this->conditions == null) return " WHERE 1=1 ";
      	foreach ($this->conditions as $f => $c) {
      		$cond = $cond . $f . $c[0] . $c[1] . " AND ";
      	}
      	$cond = " WHERE " .$cond. " 1=1 ";
        return $cond;
      }
      function visit($obj) {
      	return $obj->visitedPersistentCollection($this);
      }
      function limit () {
      		if ($this->limit!=0) return " LIMIT " .$this->limit . $this->offset();
      }
      function offset() {
        return " OFFSET ". $this->offset;
      }
      function elements () {
        $obj =& new $this->dataType;
        $sql = "SELECT ".$obj->fieldNames("SELECT")." FROM ". $this->tableName() . $this->conditions(). $this->order . $this->limit() ;
        $db = new mysqldb;
        $reg = $db->SQLExec($sql, FALSE, $this);
        $col=array();
        while ($data = $db->fetchrecord($reg)) {
           $obj =& PersistentObject::getWithId($this->dataType, $data["id"]);
           $col[] =& $obj;
        }
        return $col;
      }

      /* Deprecated */
      function objects() {
        return $this->elements();
      }

      function size() {
        $obj =& new $this->dataType;
        $sql = "SELECT COUNT(id) as 'collection_size' FROM " . $this->tableName();
        $db =& new mysqldb;
        $reg = $db->SQLExec($sql, FALSE, $this);
        $data = $db->fetchrecord($reg);
        return $data['collection_size'];
      }

      function &getObj($id){
        $obj =& new $this->dataType;
        $ret =& $obj->getWithId($this->dataType,$id);
        return $ret;
      }
	function toArray () {
		$objs = $this->objects();
		$arr=array();
		foreach($objs as $obj) {
			$arr[]=$obj->toArray();
		}
		return $arr;
	}
	 function toPlainArray () {
	 	$arr = array("offset"=> $this->offset,
                               "limit"=> $this->limit,
                               "dataType"=> $this->dataType,
                               "order"=> $this->order,
                               "conditions"=> ereg_replace("\"", "%22",
                               					ereg_replace("\'", "%27",
                               						ereg_replace("%", "%25",
                               							serialize($this->conditions)))),
                               "ObjType"=> get_class($this)
                       );
                       return $arr;
       }

}

?>
