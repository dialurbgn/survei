<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

	#[\AllowDynamicProperties]
    class Template {

        function load($template = '', $view = '' , $vars = array(), $return = FALSE)
        {
            //$vars['template_contents'] = APPPATH . 'views/' . $view;
            $vars['template_contents'] = APPPATH . '/modules/' . $view;
            $this->CI =& get_instance();
            return $this->CI->load->view($template, $vars, $return);
        }
        
    }

?>
