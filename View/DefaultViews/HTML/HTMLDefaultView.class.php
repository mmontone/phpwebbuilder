<?php

class HTMLDefaultView extends PWBFactory{
}

class ComponentHTMLDefaultView extends HTMLDefaultView{
	function &createInstanceFor(&$component){
		return $component->createDefaultView();
	}
}
?>