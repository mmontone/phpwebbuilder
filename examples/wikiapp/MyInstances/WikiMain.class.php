<?php

class WikiMain extends Component{
	/**
	 * We initialize the Component
	 */
    function initialize() {
		$input=	new ValueHolder(
    				'This is a [home|niceparam=5 bookmark link],' .
    						' a [http://phpwebbuilder.sourceforge.net normal link],' .
    						' and a [mailto:no@body.com mail link]. [Nothing else matters].'
    			);
		/**
		 * We use the same ValueHolder for both components, so no programming to keep them
		 * both updated is needed
		 */
    	$this->addComponent(new TextAreaComponent($input), 'input');
    	$this->addComponent(new Text($input), 'text');
    	$this->input->addInterestIn('changed',new FunctionObject($this,'update'));
    	$this->update();
    	$this->addComponent(new Label(file_get_contents(__FILE__)), 'code');
    }
    function update(){
    	$this->addComponent(new WikiComponent($this->input->getValue()), 'wiki');
    }
}

/**
 * The wiki component expects the bookmark to be there. Else, it will break (the "home" bookmark
 * already exists).
 */

class NothingBookmark extends Bookmark{}

?>