<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_DB_mysqli_driver extends CI_DB_mysqli_driver
{
    public function insert($table = '', $set = NULL, $escape = NULL)
    {
        log_message('debug', 'MY_DB_mysqli_driver::insert - Table: ' . $table . ', Data size: ' . strlen(json_encode($set)));
        $result = parent::insert($table, $set, $escape);

        if ($result) {
            $CI =& get_instance();
            $CI->load->helper('history');
            log_history($table, $this->insert_id(), 'insert', null, $set, $CI->session->userdata('userid'));
        }

        return $result;
    }

    public function update($table = '', $set = NULL, $where = NULL, $limit = NULL)
    {
        $CI =& get_instance();
        $CI->load->helper('history');

        if (empty($where) || !is_array($where)) {
            log_message('error', '❌ MY_DB_mysqli_driver::update - WHERE clause is empty or invalid. Update aborted to prevent full table overwrite. Table: ' . $table);
            return false;
        }

        $before = $this->get_where($table, $where)->result_array();
        $result = parent::update($table, $set, $where, $limit);

        foreach ($before as $row) {
            $primaryKey = $row['id'] ?? null;
            log_history($table, $primaryKey, 'update', $row, $set, $CI->session->userdata('userid'));
        }

        return $result;
    }

    public function delete($table = '', $where = '', $limit = NULL, $reset_data = TRUE)
    {
        $CI =& get_instance();
        $CI->load->helper('history');

        if (empty($where) || !is_array($where)) {
            log_message('error', '❌ MY_DB_mysqli_driver::delete - WHERE clause is empty or invalid. Delete aborted to prevent full table wipe. Table: ' . $table);
            return false;
        }

        $before = $this->get_where($table, $where)->result_array();
        $result = parent::delete($table, $where, $limit, $reset_data);

        foreach ($before as $row) {
            $primaryKey = $row['id'] ?? null;
            log_history($table, $primaryKey, 'delete', $row, null, $CI->session->userdata('userid'));
        }

        return $result;
    }
}
