<?php

class WikiApp extends Application{
	function &setRootComponent(){
		return new WikiMain();
	}
}
?>