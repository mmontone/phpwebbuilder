<?php
/**
 * Renders in HTML the specified tags.
 */
class HtmlRenderer
{
    function HtmlRenderer() {
        $this->body = "";
        $this->scripts = "";
        $this->head = "";
    }

    function script($script_text) {
        $this->scripts .= $script_text . "\n";
    }

    function stylesheetLink($href) {
    	$this->addLink(array('href' => $href,
                             'rel' => 'stylesheet',
                             'type' => 'text/css'));
    }

    function scriptLink($params) {
        $type = $params['type'];
        $src = $params['src'];

        $this->head .= "<script type=\"$type\" src=\"$src\">";
    }

    function link($params) {
        $this->head .= "<link href=\"". $params['href'] . "\" rel=\"" . $params['rel'] . "\" type=\"" . $params['type']. "\">\n";
    }

    function text($text) {
      $this->body .= $text . "\n";
    }

    function render() {
        $ret = "<html>\n";
        $ret .= "<head>\n";
        $ret .= $this->head;
        $ret .= "</head>\n";
        $ret .= "<body>\n";
        $ret .= "<script>\n";
        $ret .= $this->scripts;
        $ret .= "</script>\n";
        $ret .= $this->body;
        $ret .= "</body>\n";
        $ret .= "</html>";
        return $ret;
    }


    function begin_form_for_action($action_selector) {
        $this->body .= "<form action=dispatch_action.php>\n";
        $this->body .= "    <input type=hidden name=app_path value=" . $_REQUEST['app_path'] . " />\n";
        $this->body .= "    <input type=hidden name=action value=" . $action_selector . " />\n";
        $this->append_component_information();
        $this->append_backbutton_information();
    }

    function append_component_information() {
        $index_nesting = 1;
        $component_renderer = ComponentRenderer::getInstance();
        foreach ($component_renderer->rendering_chain  as $component) {
            $this->body .= "    <input type=hidden name=comp_" . $index_nesting++ . " value=" . $component->holder->owner_index() . " />\n";
        }
    }

    function submit_button($params) {
        $this->body .= '<input type="submit" name="action_' . $params['action'] . '" value="' . $params['label'] . '" />';
    }

    function append_backbutton_information() {
        $app =& Application::instance();
        $app->backbutton_manager->append_form_fields($out);
    }

    function begin_form() {
        $this->body .= "<form action=dispatch_action.php>\n";
        $this->body .= "    <input type=hidden name=app_path value=" . $_REQUEST['app_path'] . " />\n";
        $this->append_component_information();
        $this->append_backbutton_information();
    }

    function form_param($param) {
        return 'p_' . $param;
    }
}

?>