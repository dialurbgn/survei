<?php

class My_form_validation extends CI_Form_validation {
        
    public function get_errors() {
        return $this->_error_array;
    }
    
}

?>