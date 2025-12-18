<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_DB_query_builder extends CI_DB_query_builder
{
    public function insert($table = '', $set = NULL, $escape = NULL)
    {
        log_message('debug', '✅ MY_DB_query_builder::insert terpanggil');
        $result = parent::insert($table, $set, $escape);
        if ($result) {
            $insert_id = $this->insert_id();
            $CI =& get_instance();
            $CI->load->helper('history');
            log_history($table, $insert_id, 'insert', null, $set, $CI->session->userdata('userid'));
        }
        return $result;
    }

    public function update($table = '', $set = NULL, $where = NULL, $limit = NULL)
    {
        log_message('debug', '✅ MY_DB_query_builder::update terpanggil');
        $CI =& get_instance();
        $CI->load->helper('history');

        $this->where($where);
        $query = $this->get_where($table, $where);
        $before = $query->result_array();

        $result = parent::update($table, $set, $where, $limit);

        foreach ($before as $row) {
            $primaryKey = isset($row['id']) ? $row['id'] : null;
            log_history($table, $primaryKey, 'update', $row, $set, $CI->session->userdata('userid'));
        }

        return $result;
    }

    public function delete($table = '', $where = '', $limit = NULL, $reset_data = TRUE)
    {
        log_message('debug', '✅ MY_DB_query_builder::delete terpanggil');
        $CI =& get_instance();
        $CI->load->helper('history');

        $query = $this->get_where($table, $where);
        $before = $query->result_array();

        $result = parent::delete($table, $where, $limit, $reset_data);

        foreach ($before as $row) {
            $primaryKey = isset($row['id']) ? $row['id'] : null;
            log_history($table, $primaryKey, 'delete', $row, null, $CI->session->userdata('userid'));
        }

        return $result;
    }
}
