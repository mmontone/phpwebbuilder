<?php

class WikiComponentHTMLHandler extends WidgetHTMLHandler{
	function &createDefaultView(){
		$v =& new XMLNodeModificationsTracker;
		$v->appendChild($c =& new XMLNodeModificationsTracker('p'));
		$c->appendChild(new HTMLContainer('',array('class'=>'Component')));
		return $v;
	}
}

/*
					<template class="WikiComponent">
						<p><container class="Component"/></p>
					</template>
 *
 * */

?>