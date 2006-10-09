<?php
//TODO Remove
class PermissionChecker {
    function &addComponent(&$parent, &$comp, $cond, $position=null) {
		/*if ($cond){
			$parent->addComponent($comp, $position);
		}*/
		return PermissionChecker::exec($parent, 'addComponent', $cond, $a=array(&$comp, $position));
    }
    function &exec(&$obj, $mess, $func, &$params){
    	if (is_object($func)){
    		$ok = $func->call();
    	} else {
    		$ok = $func(User::logged());
    	}
    	if ($ok){
			$rec= array();
			$rec[0]=&$obj;
			$rec[1]=$mess;
			$ret = call_user_func_array($rec,$params);
    		return $ret;
    	} else {
    		$ret = null;
    		return $ret;
    	}
    }
}
?>