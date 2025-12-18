<?php

function log_history($table, $primaryKey, $action, $dataBefore = null, $dataAfter = null, $userId = null)
{
	if($table == 'history_log'){
		return false;
	}
	
    $CI =& get_instance();
    $CI->load->database();

	if ($userId === null) {
        $userId = $CI->session->userdata('userid');
    }
	
    $maxLength = 1024 * 1024; // 1MB limit, sesuaikan dengan kebutuhan

    $dataBeforeJson = $dataBefore ? json_encode($dataBefore) : null;
    $dataAfterJson = $dataAfter ? json_encode($dataAfter) : null;

    if ($dataBeforeJson && strlen($dataBeforeJson) > $maxLength) {
        $dataBeforeJson = substr($dataBeforeJson, 0, $maxLength) . '... [truncated]';
    }

    if ($dataAfterJson && strlen($dataAfterJson) > $maxLength) {
        $dataAfterJson = substr($dataAfterJson, 0, $maxLength) . '... [truncated]';
    }

    $logData = [
        'table_name'   => $table,
        'primary_key'  => $primaryKey,
        'action'       => $action,
        'data_before'  => $dataBeforeJson,
        'data_after'   => $dataAfterJson,
        'created_at'   => date('Y-m-d H:i:s'),
        'user_id'      => $userId,
    ];

    $CI->db->insert('history_log', $logData);
}


