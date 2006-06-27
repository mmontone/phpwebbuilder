<?php

class WikiMain extends Component{

    function initialize() {
    	$this->addComponent(new TextAreaComponent(new ValueHolder($v = "This is a [link|lala=5 bookmark link]\n, a [http://phpwebbuilder.sourceforge.net normal link], and a [mailto:no@body.com mail link] ")), 'input');
    	$this->input->addEventListener(array('change'=>'update'),$this);
    	$this->update();
    }
    function update(){
    	$this->addComponent(new WikiComponent($this->input->getValue()), 'wiki');
    	$this->addComponent(new Label($this->input->getValue()), 'text');
    }
}
?>