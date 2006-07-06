<?php

class WikiMain extends Component{

    function initialize() {
    	$this->addComponent(new TextAreaComponent(
    		new ValueHolder(
    				$v = 'This is a [link|niceparam=5 bookmark link],' .
    						' a [http://phpwebbuilder.sourceforge.net normal link],' .
    						' and a [mailto:no@body.com mail link]. [Nothing else matters].'
    			)
    		), 'input');
    	$this->input->addEventListener(array('change'=>'update'),$this);
    	$this->update();
    	$this->addComponent(new Label(file_get_contents(__FILE__)), 'code');
    }
    function update(){
    	$this->addComponent(new WikiComponent($this->input->getValue()), 'wiki');
    	$this->addComponent(new Label($this->input->getValue()), 'text');
    }
}
?>