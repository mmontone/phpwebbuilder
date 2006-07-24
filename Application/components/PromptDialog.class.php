<?php

require_once dirname(__FILE__) . '/../Component.class.php';

class PromptDialog extends Component
{
	var $message;

	function PromptDialog($message, $callback_actions=array('on_accept' => 'text_filled_in')) {
		parent::Component();
		$this->registerCallbacks($callback_actions);
		$this->message = $message;
	}

	function render_on(&$out) {
		$html =& $this->html_renderer;
		$out .= "<h1>" . $this->message . "</h1>\n";
		$html->begin_form_for_action('done', $out);
		$out .= "    <input type=text name=" . $html->form_param('text') . " /></br>";
		$out .= $html->submit_button(array('label' => 'Accept', 'action' => 'accept'));
		$out .= "</form>";
	}

	function declare_actions() {
		return array('done');
	}

	function done($params) {
		$this->callback('on_accept',$params);
	}
}
?>