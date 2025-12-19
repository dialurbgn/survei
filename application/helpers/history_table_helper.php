<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * History Helper for CodeIgniter 3
 * 
 * Helper untuk menyimpan history data secara dinamis
 * Otomatis membuat table history jika belum ada
 * Support MySQL dan PostgreSQL
 * 
 * @author Hanafi (UCOK)
 * @version 2.0
 */

if (!function_exists('get_db_driver')) {
    /**
     * Get database driver type
     * 
     * @return string 'mysql' or 'postgre'
     */
    function get_db_driver() {
        $CI =& get_instance();
        return $CI->db->dbdriver;
    }
}

if (!function_exists('sync_history_table_structure')) {
    /**
     * Sync history table structure with main table
     * Menambahkan kolom baru yang tidak ada di history table
     * Support MySQL dan PostgreSQL
     * 
     * @param string $main_table
     * @param string $history_table
     * @return bool
     */
    function sync_history_table_structure($main_table, $history_table) {
        
        $CI =& get_instance();
        $driver = get_db_driver();
        
        if (!table_exists($main_table) || !table_exists($history_table)) {
            return false;
        }
        
        // Get columns from main table
        if ($driver == 'postgre') {
            $main_columns_query = $CI->db->query("
                SELECT 
                    column_name as \"Field\",
                    data_type as \"Type\",
                    character_maximum_length,
                    numeric_precision,
                    numeric_scale,
                    is_nullable as \"Null\",
                    column_default as \"Default\",
                    '' as \"Comment\"
                FROM information_schema.columns
                WHERE table_name = '{$main_table}'
                ORDER BY ordinal_position
            ");
        } else {
            $main_columns_query = $CI->db->query("SHOW FULL COLUMNS FROM `{$main_table}`");
        }
        
        $main_columns = $main_columns_query->result_array();
        
        // Get columns from history table
        if ($driver == 'postgre') {
            $history_columns_query = $CI->db->query("
                SELECT 
                    column_name as \"Field\",
                    data_type as \"Type\",
                    character_maximum_length,
                    numeric_precision,
                    numeric_scale,
                    is_nullable as \"Null\",
                    column_default as \"Default\",
                    '' as \"Comment\"
                FROM information_schema.columns
                WHERE table_name = '{$history_table}'
                ORDER BY ordinal_position
            ");
        } else {
            $history_columns_query = $CI->db->query("SHOW FULL COLUMNS FROM `{$history_table}`");
        }
        
        $history_columns = $history_columns_query->result_array();
        
        // Create array of existing history columns
        $history_column_names = array_column($history_columns, 'Field');
        
        $changes_made = false;
        
        // Check each column from main table
        foreach ($main_columns as $col) {
            $field = $col['Field'];
            
            // Skip if column already exists in history table
            if (in_array($field, $history_column_names)) {
                continue;
            }
            
            // Add missing column to history table
            if ($driver == 'postgre') {
                $type = convert_postgres_type($col);
                $null = strtoupper($col['Null']) == 'YES' ? '' : 'NOT NULL';
                $default = '';
                
                // Handle default value for PostgreSQL
                if ($col['Default'] !== null) {
                    if (strpos($col['Default'], 'nextval') !== false) {
                        // Skip auto-increment defaults
                    } elseif (strtoupper($col['Default']) == 'CURRENT_TIMESTAMP' || strpos($col['Default'], 'now()') !== false) {
                        $default = "DEFAULT CURRENT_TIMESTAMP";
                    } else {
                        $cleaned_default = str_replace("::character varying", "", $col['Default']);
                        $cleaned_default = str_replace("'", "", $cleaned_default);
                        $default = "DEFAULT '" . $CI->db->escape_str($cleaned_default) . "'";
                    }
                } elseif (strtoupper($col['Null']) == 'YES') {
                    $default = 'DEFAULT NULL';
                }
                
                // Build ALTER TABLE query for PostgreSQL
                $alter_sql = "ALTER TABLE \"{$history_table}\" ADD COLUMN \"{$field}\" {$type} {$null} {$default}";
                
            } else {
                $type = $col['Type'];
                $null = $col['Null'] == 'YES' ? 'NULL' : 'NOT NULL';
                $default = '';
                $comment = '';
                
                // Handle default value for MySQL
                if ($col['Default'] !== null) {
                    if (in_array(strtoupper($col['Default']), ['CURRENT_TIMESTAMP', 'NULL'])) {
                        $default = "DEFAULT {$col['Default']}";
                    } else {
                        $default = "DEFAULT '" . $CI->db->escape_str($col['Default']) . "'";
                    }
                } elseif ($col['Null'] == 'YES') {
                    $default = 'DEFAULT NULL';
                }
                
                // Handle comment
                if (!empty($col['Comment'])) {
                    $comment = "COMMENT '" . $CI->db->escape_str($col['Comment']) . "'";
                }
                
                // Build ALTER TABLE query for MySQL
                $alter_sql = "ALTER TABLE `{$history_table}` ADD COLUMN `{$field}` {$type} {$null} {$default} {$comment}";
            }
            
            try {
                $result = $CI->db->query($alter_sql);
                
                if ($result) {
                    log_message('info', "Column '{$field}' added to {$history_table}");
                    $changes_made = true;
                } else {
                    log_message('error', "Failed to add column '{$field}' to {$history_table}");
                }
            } catch (Exception $e) {
                log_message('error', "Exception adding column '{$field}': " . $e->getMessage());
            }
        }
        
        return $changes_made;
    }
}

if (!function_exists('convert_postgres_type')) {
    /**
     * Convert PostgreSQL column info to type definition
     * 
     * @param array $col
     * @return string
     */
    function convert_postgres_type($col) {
        $type = strtoupper($col['Type']);
        
        switch ($type) {
            case 'CHARACTER VARYING':
                return 'VARCHAR(' . ($col['character_maximum_length'] ?? 255) . ')';
            case 'CHARACTER':
                return 'CHAR(' . ($col['character_maximum_length'] ?? 1) . ')';
            case 'TEXT':
                return 'TEXT';
            case 'INTEGER':
                return 'INTEGER';
            case 'BIGINT':
                return 'BIGINT';
            case 'SMALLINT':
                return 'SMALLINT';
            case 'NUMERIC':
                if ($col['numeric_precision'] && $col['numeric_scale']) {
                    return "NUMERIC({$col['numeric_precision']},{$col['numeric_scale']})";
                }
                return 'NUMERIC';
            case 'DECIMAL':
                if ($col['numeric_precision'] && $col['numeric_scale']) {
                    return "DECIMAL({$col['numeric_precision']},{$col['numeric_scale']})";
                }
                return 'DECIMAL';
            case 'REAL':
                return 'REAL';
            case 'DOUBLE PRECISION':
                return 'DOUBLE PRECISION';
            case 'BOOLEAN':
                return 'BOOLEAN';
            case 'DATE':
                return 'DATE';
            case 'TIME WITHOUT TIME ZONE':
                return 'TIME';
            case 'TIMESTAMP WITHOUT TIME ZONE':
                return 'TIMESTAMP';
            case 'TIMESTAMP WITH TIME ZONE':
                return 'TIMESTAMPTZ';
            case 'JSON':
                return 'JSON';
            case 'JSONB':
                return 'JSONB';
            default:
                return $type;
        }
    }
}

if (!function_exists('check_and_sync_history_structure')) {
    /**
     * Check and sync history table structure before saving
     * 
     * @param string $table_name
     * @return bool
     */
    function check_and_sync_history_structure($table_name) {
        
        $history_data_table = 'history_' . $table_name . '_data';
        
        if (!table_exists($history_data_table)) {
            return false;
        }
        
        return sync_history_table_structure($table_name, $history_data_table);
    }
}

if (!function_exists('save_history')) {
    /**
     * Save History Data - Dynamic Version with Auto-Sync
     * Otomatis membuat table history jika belum ada
     * Otomatis sync structure jika ada perubahan
     * Support MySQL dan PostgreSQL
     * 
     * @param string $table_name - Nama table utama (tanpa prefix)
     * @param int $record_id - ID record yang akan disimpan historynya
     * @param array $additional_data - Data tambahan untuk history (opsional)
     * @param string $keterangan - Keterangan perubahan
     * @param bool $auto_sync - Auto sync structure (default: true)
     * @return int|bool - Insert ID jika berhasil, false jika gagal
     */
    function save_history($table_name, $record_id, $additional_data = [], $keterangan = 'Tidak Ada Catatan', $auto_sync = true) {
        
        $CI =& get_instance();
        
        // Validasi input
        if (empty($table_name) || empty($record_id)) {
            log_message('error', 'save_history: table_name or record_id is empty');
            return false;
        }
        
        // Nama table history
        $history_data_table = 'history_' . $table_name . '_data';
        $history_status_table = 'history_' . $table_name . '_status';
        
        // Cek dan buat table history jika belum ada
        if (!table_exists($history_data_table)) {
            log_message('info', "save_history: Table {$history_data_table} not found, creating...");
            $create_success = create_history_data_table($table_name, $history_data_table);
            if (!$create_success) {
                log_message('error', 'save_history: Failed to create table ' . $history_data_table);
                return false;
            }
        } else {
            // Sync structure jika auto_sync enabled
            if ($auto_sync) {
                log_message('debug', 'save_history: Checking structure sync for ' . $history_data_table);
                sync_history_table_structure($table_name, $history_data_table);
            }
        }
        
        if (!table_exists($history_status_table)) {
            log_message('info', "save_history: Table {$history_status_table} not found, creating...");
            $create_success = create_history_status_table($table_name, $history_status_table);
            if (!$create_success) {
                log_message('error', 'save_history: Failed to create table ' . $history_status_table);
                return false;
            }
        }
        
        // Get current data dari table utama
        $CI->db->where('id', $record_id);
        $details = $CI->db->get($table_name);
        
        if ($details->num_rows() == 0) {
            log_message('error', 'save_history: Record not found in ' . $table_name . ' with id ' . $record_id);
            return false;
        }
        
        $CI->db->trans_begin();
        
        try {
            foreach ($details->result_array() as $row) {
                // Simpan original ID sebelum dihapus
                $original_id = $row['id'];
                
                // Hapus ID original karena akan auto increment di table history
                unset($row['id']);
                
                // Tambahkan foreign key ke record original
                $foreign_key = $table_name . '_id';
                $row[$foreign_key] = $original_id;
                
                // Tambahkan timestamp
                $row['history_created_at'] = date('Y-m-d H:i:s');
                $row['history_created_by'] = $CI->session->userdata('userid');
                
                // Insert ke history_data_table
                $insert_data = $CI->db->insert($history_data_table, $row);
                
                if (!$insert_data) {
                    $db_error = $CI->db->error();
                    throw new Exception('Failed to insert history data: ' . $db_error['message']);
                }
                
                $history_data_id = $CI->db->insert_id();
                
                // Prepare data untuk history_status_table
                $status_data = [
                    $foreign_key        => $original_id,
                    'tanggal'           => date('Y-m-d H:i:s'),
                    'status_id'         => isset($row['status_id']) ? $row['status_id'] : 0,
                    'keterangan'        => $keterangan,
                    'history_data_id'   => $history_data_id,
                    'active'            => 1,
                    'createdid'         => $CI->session->userdata('userid'),
                    'created'           => date('Y-m-d H:i:s'),
                    'modifiedid'        => $CI->session->userdata('userid'),
                    'modified'          => date('Y-m-d H:i:s')
                ];
                
                // Merge dengan additional data jika ada
                if (!empty($additional_data)) {
                    $status_data = array_merge($status_data, $additional_data);
                }
                
                // Insert ke history_status_table
                $insert_status = $CI->db->insert($history_status_table, $status_data);
                
                if (!$insert_status) {
                    $db_error = $CI->db->error();
                    throw new Exception('Failed to insert history status: ' . $db_error['message']);
                }
                
                $history_status_id = $CI->db->insert_id();
                
                $CI->db->trans_commit();
                
                log_message('info', "History saved successfully for {$table_name} ID {$record_id}");
                
                return $history_status_id;
            }
            
        } catch (Exception $e) {
            $CI->db->trans_rollback();
            log_message('error', 'save_history Error: ' . $e->getMessage());
            return false;
        }
        
        return false;
    }
}

if (!function_exists('force_sync_history_structure')) {
    /**
     * Force sync history table structure (untuk manual sync)
     * Support MySQL dan PostgreSQL
     * 
     * @param string $table_name
     * @return array - hasil sync
     */
    function force_sync_history_structure($table_name) {
        
        $CI =& get_instance();
        $driver = get_db_driver();
        $history_data_table = 'history_' . $table_name . '_data';
        
        $result = [
            'status' => 'error',
            'message' => '',
            'changes' => []
        ];
        
        if (!table_exists($table_name)) {
            $result['message'] = "Main table {$table_name} not found";
            return $result;
        }
        
        if (!table_exists($history_data_table)) {
            $result['message'] = "History table {$history_data_table} not found";
            return $result;
        }
        
        // Get columns from main table
        if ($driver == 'postgre') {
            $main_columns_query = $CI->db->query("
                SELECT 
                    column_name as \"Field\",
                    data_type as \"Type\",
                    character_maximum_length,
                    numeric_precision,
                    numeric_scale,
                    is_nullable as \"Null\",
                    column_default as \"Default\",
                    '' as \"Comment\"
                FROM information_schema.columns
                WHERE table_name = '{$table_name}'
                ORDER BY ordinal_position
            ");
        } else {
            $main_columns_query = $CI->db->query("SHOW FULL COLUMNS FROM `{$main_table}`");
        }
        
        $main_columns = $main_columns_query->result_array();
        
        // Get columns from history table
        if ($driver == 'postgre') {
            $history_columns_query = $CI->db->query("
                SELECT 
                    column_name as \"Field\",
                    data_type as \"Type\",
                    character_maximum_length,
                    numeric_precision,
                    numeric_scale,
                    is_nullable as \"Null\",
                    column_default as \"Default\",
                    '' as \"Comment\"
                FROM information_schema.columns
                WHERE table_name = '{$history_data_table}'
                ORDER BY ordinal_position
            ");
        } else {
            $history_columns_query = $CI->db->query("SHOW FULL COLUMNS FROM `{$history_data_table}`");
        }
        
        $history_columns = $history_columns_query->result_array();
        
        // Create array of existing history columns
        $history_column_names = array_column($history_columns, 'Field');
        
        $changes = [];
        
        // Check each column from main table
        foreach ($main_columns as $col) {
            $field = $col['Field'];
            
            // Skip if column already exists in history table
            if (in_array($field, $history_column_names)) {
                continue;
            }
            
            // Add missing column to history table
            if ($driver == 'postgre') {
                $type = convert_postgres_type($col);
                $null = strtoupper($col['Null']) == 'YES' ? '' : 'NOT NULL';
                $default = '';
                
                if ($col['Default'] !== null) {
                    if (strpos($col['Default'], 'nextval') !== false) {
                        // Skip auto-increment defaults
                    } elseif (strtoupper($col['Default']) == 'CURRENT_TIMESTAMP' || strpos($col['Default'], 'now()') !== false) {
                        $default = "DEFAULT CURRENT_TIMESTAMP";
                    } else {
                        $cleaned_default = str_replace("::character varying", "", $col['Default']);
                        $cleaned_default = str_replace("'", "", $cleaned_default);
                        $default = "DEFAULT '" . $CI->db->escape_str($cleaned_default) . "'";
                    }
                } elseif (strtoupper($col['Null']) == 'YES') {
                    $default = 'DEFAULT NULL';
                }
                
                $alter_sql = "ALTER TABLE \"{$history_data_table}\" ADD COLUMN \"{$field}\" {$type} {$null} {$default}";
                
            } else {
                $type = $col['Type'];
                $null = $col['Null'] == 'YES' ? 'NULL' : 'NOT NULL';
                $default = '';
                $comment = '';
                
                if ($col['Default'] !== null) {
                    if (in_array(strtoupper($col['Default']), ['CURRENT_TIMESTAMP', 'NULL'])) {
                        $default = "DEFAULT {$col['Default']}";
                    } else {
                        $default = "DEFAULT '" . $CI->db->escape_str($col['Default']) . "'";
                    }
                } elseif ($col['Null'] == 'YES') {
                    $default = 'DEFAULT NULL';
                }
                
                if (!empty($col['Comment'])) {
                    $comment = "COMMENT '" . $CI->db->escape_str($col['Comment']) . "'";
                }
                
                $alter_sql = "ALTER TABLE `{$history_data_table}` ADD COLUMN `{$field}` {$type} {$null} {$default} {$comment}";
            }
            
            try {
                $alter_result = $CI->db->query($alter_sql);
                
                if ($alter_result) {
                    $changes[] = [
                        'field' => $field,
                        'status' => 'success',
                        'message' => "Column added successfully"
                    ];
                } else {
                    $changes[] = [
                        'field' => $field,
                        'status' => 'error',
                        'message' => "Failed to add column"
                    ];
                }
            } catch (Exception $e) {
                $changes[] = [
                    'field' => $field,
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
            }
        }
        
        if (empty($changes)) {
            $result['status'] = 'success';
            $result['message'] = 'Structure already in sync';
        } else {
            $result['status'] = 'success';
            $result['message'] = count($changes) . ' columns processed';
            $result['changes'] = $changes;
        }
        
        return $result;
    }
}

if (!function_exists('get_structure_diff')) {
    /**
     * Get structure difference between main table and history table
     * Support MySQL dan PostgreSQL
     * 
     * @param string $table_name
     * @return array
     */
    function get_structure_diff($table_name) {
        
        $CI =& get_instance();
        $driver = get_db_driver();
        $history_data_table = 'history_' . $table_name . '_data';
        
        $diff = [
            'missing_in_history' => [],
            'extra_in_history' => []
        ];
        
        if (!table_exists($table_name) || !table_exists($history_data_table)) {
            return $diff;
        }
        
        // Get columns from main table
        if ($driver == 'postgre') {
            $main_columns_query = $CI->db->query("
                SELECT column_name as \"Field\"
                FROM information_schema.columns
                WHERE table_name = '{$table_name}'
                ORDER BY ordinal_position
            ");
        } else {
            $main_columns_query = $CI->db->query("SHOW COLUMNS FROM `{$table_name}`");
        }
        
        $main_columns = array_column($main_columns_query->result_array(), 'Field');
        
        // Get columns from history table
        if ($driver == 'postgre') {
            $history_columns_query = $CI->db->query("
                SELECT column_name as \"Field\"
                FROM information_schema.columns
                WHERE table_name = '{$history_data_table}'
                ORDER BY ordinal_position
            ");
        } else {
            $history_columns_query = $CI->db->query("SHOW COLUMNS FROM `{$history_data_table}`");
        }
        
        $history_columns = array_column($history_columns_query->result_array(), 'Field');
        
        // Exclude history-specific columns
        $exclude = [$table_name . '_id', 'history_created_at', 'history_created_by'];
        $history_columns = array_diff($history_columns, $exclude);
        
        // Find missing columns
        $diff['missing_in_history'] = array_diff($main_columns, $history_columns);
        
        // Find extra columns (shouldn't happen normally)
        $diff['extra_in_history'] = array_diff($history_columns, $main_columns);
        
        return $diff;
    }
}

if (!function_exists('table_exists')) {
    /**
     * Check if table exists
     * Support MySQL dan PostgreSQL
     * 
     * @param string $table_name
     * @return bool
     */
    function table_exists($table_name) {
        $CI =& get_instance();
        $driver = get_db_driver();
        
        if ($driver == 'postgre') {
            $query = $CI->db->query("
                SELECT EXISTS (
                    SELECT FROM information_schema.tables 
                    WHERE table_name = '{$table_name}'
                ) as exists
            ");
            $result = $query->row_array();
            return $result['exists'] == 't' || $result['exists'] == '1';
        } else {
            $query = $CI->db->query("SHOW TABLES LIKE '{$table_name}'");
            return $query->num_rows() > 0;
        }
    }
}

if (!function_exists('create_history_data_table')) {
    /**
     * Create history data table dynamically based on main table structure
     * Support MySQL dan PostgreSQL
     * 
     * @param string $main_table - Nama table utama
     * @param string $history_table - Nama table history yang akan dibuat
     * @return bool
     */
    function create_history_data_table($main_table, $history_table) {
        
        $CI =& get_instance();
        $driver = get_db_driver();
        
        // Check if main table exists
        if (!table_exists($main_table)) {
            log_message('error', "create_history_data_table: Table {$main_table} not found");
            return false;
        }
        
        // Get columns from main table
        if ($driver == 'postgre') {
            $columns_query = $CI->db->query("
                SELECT 
                    column_name as \"Field\",
                    data_type as \"Type\",
                    character_maximum_length,
                    numeric_precision,
                    numeric_scale,
                    is_nullable as \"Null\",
                    column_default as \"Default\",
                    '' as \"Comment\"
                FROM information_schema.columns
                WHERE table_name = '{$main_table}'
                ORDER BY ordinal_position
            ");
        } else {
            $columns_query = $CI->db->query("SHOW FULL COLUMNS FROM `{$main_table}`");
        }
        
        if ($columns_query->num_rows() == 0) {
            log_message('error', "create_history_data_table: No columns found in {$main_table}");
            return false;
        }
        
        $columns = $columns_query->result_array();
        
        // Build CREATE TABLE statement
        $foreign_key_column = $main_table . '_id';
        
        if ($driver == 'postgre') {
            // PostgreSQL CREATE TABLE
            $sql = "CREATE TABLE IF NOT EXISTS \"{$history_table}\" (\n";
            $column_definitions = [];
            
            // Add all columns from main table
            foreach ($columns as $col) {
                $field = $col['Field'];
                $type = convert_postgres_type($col);
                $null = strtoupper($col['Null']) == 'YES' ? '' : 'NOT NULL';
                $default = '';
                
                // Handle default value
                if ($col['Default'] !== null) {
                    if (strpos($col['Default'], 'nextval') !== false) {
                        // Skip auto-increment defaults for history
                        if ($field == 'id') {
                            $type = 'SERIAL';
                            $null = '';
                            $default = '';
                        }
                    } elseif (strtoupper($col['Default']) == 'CURRENT_TIMESTAMP' || strpos($col['Default'], 'now()') !== false) {
                        $default = "DEFAULT CURRENT_TIMESTAMP";
                    } else {
                        $cleaned_default = str_replace("::character varying", "", $col['Default']);
                        $cleaned_default = str_replace("'", "", $cleaned_default);
                        $default = "DEFAULT '" . $CI->db->escape_str($cleaned_default) . "'";
                    }
                } elseif (strtoupper($col['Null']) == 'YES') {
                    $default = 'DEFAULT NULL';
                }
                
                // Build column definition
                $column_def = "  \"{$field}\" {$type} {$null} {$default}";
                $column_def = preg_replace('/\s+/', ' ', trim($column_def));
                
                $column_definitions[] = $column_def;
            }
            
            // Add history-specific columns
            $column_definitions[] = "  \"{$foreign_key_column}\" INTEGER NOT NULL";
            $column_definitions[] = "  \"history_created_at\" TIMESTAMP DEFAULT NULL";
            $column_definitions[] = "  \"history_created_by\" INTEGER DEFAULT NULL";
            
            // Add PRIMARY KEY
            $sql .= implode(",\n", $column_definitions);
            $sql .= ",\n  PRIMARY KEY (\"id\")";
            $sql .= "\n);";
            
            // Create indexes separately
            $index_sqls = [];
            $index_sqls[] = "CREATE INDEX IF NOT EXISTS \"idx_{$foreign_key_column}\" ON \"{$history_table}\" (\"{$foreign_key_column}\");";
            $index_sqls[] = "CREATE INDEX IF NOT EXISTS \"idx_history_created_at\" ON \"{$history_table}\" (\"history_created_at\");";
            
        } else {
            // MySQL CREATE TABLE
            // Get indexes
            $indexes_query = $CI->db->query("SHOW INDEX FROM `{$main_table}`");
            $indexes = $indexes_query->result_array();
            
            $sql = "CREATE TABLE IF NOT EXISTS `{$history_table}` (\n";
            $column_definitions = [];
            $key_definitions = [];
            
            // Add all columns from main table
            foreach ($columns as $col) {
                $field = $col['Field'];
                $type = $col['Type'];
                $null = $col['Null'] == 'YES' ? 'NULL' : 'NOT NULL';
                $default = '';
                $extra = '';
                $comment = '';
                
                // Handle default value
                if ($col['Default'] !== null) {
                    if (in_array(strtoupper($col['Default']), ['CURRENT_TIMESTAMP', 'NULL'])) {
                        $default = "DEFAULT {$col['Default']}";
                    } else {
                        $default = "DEFAULT '" . $CI->db->escape_str($col['Default']) . "'";
                    }
                } elseif ($col['Null'] == 'YES') {
                    $default = 'DEFAULT NULL';
                }
                
                // Handle extra (but skip AUTO_INCREMENT for non-id fields)
                if ($col['Extra'] && $field == 'id') {
                    $extra = strtoupper($col['Extra']);
                }
                
                // Handle comment
                if (!empty($col['Comment'])) {
                    $comment = "COMMENT '" . $CI->db->escape_str($col['Comment']) . "'";
                }
                
                // Build column definition
                $column_def = "  `{$field}` {$type} {$null} {$default} {$extra} {$comment}";
                $column_def = preg_replace('/\s+/', ' ', trim($column_def));
                
                $column_definitions[] = $column_def;
            }
            
            // Add history-specific columns
            $column_definitions[] = "  `{$foreign_key_column}` INT(11) NOT NULL COMMENT 'Foreign key to {$main_table}.id'";
            $column_definitions[] = "  `history_created_at` DATETIME DEFAULT NULL COMMENT 'Waktu history dibuat'";
            $column_definitions[] = "  `history_created_by` INT(11) DEFAULT NULL COMMENT 'User yang membuat history'";
            
            // Add PRIMARY KEY
            $key_definitions[] = "  PRIMARY KEY (`id`)";
            
            // Add other indexes (skip PRIMARY KEY and UNIQUE KEY on slug)
            $processed_keys = [];
            foreach ($indexes as $idx) {
                $key_name = $idx['Key_name'];
                
                // Skip PRIMARY, and UNIQUE key on slug
                if ($key_name == 'PRIMARY') continue;
                if ($idx['Non_unique'] == 0 && $idx['Column_name'] == 'slug') continue;
                
                // Group by key_name
                if (!isset($processed_keys[$key_name])) {
                    $processed_keys[$key_name] = [
                        'non_unique' => $idx['Non_unique'],
                        'columns' => []
                    ];
                }
                $processed_keys[$key_name]['columns'][] = $idx['Column_name'];
            }
            
            // Add index definitions
            foreach ($processed_keys as $key_name => $key_info) {
                $columns_list = '`' . implode('`, `', $key_info['columns']) . '`';
                if ($key_info['non_unique'] == 0) {
                    $key_definitions[] = "  UNIQUE KEY `{$key_name}` ({$columns_list})";
                } else {
                    $key_definitions[] = "  KEY `{$key_name}` ({$columns_list})";
                }
            }
            
            // Add history foreign key index
            $key_definitions[] = "  KEY `idx_{$foreign_key_column}` (`{$foreign_key_column}`)";
            $key_definitions[] = "  KEY `idx_history_created_at` (`history_created_at`)";
            
            // Combine everything
            $sql .= implode(",\n", $column_definitions);
            $sql .= ",\n";
            $sql .= implode(",\n", $key_definitions);
            $sql .= "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
        }
        
        // Log the SQL
        log_message('debug', "CREATE HISTORY TABLE SQL:\n" . $sql);
        
        // Execute
        try {
            $result = $CI->db->query($sql);
            
            if ($result) {
                // Create indexes for PostgreSQL
                if ($driver == 'postgre' && isset($index_sqls)) {
                    foreach ($index_sqls as $index_sql) {
                        $CI->db->query($index_sql);
                    }
                }
                
                log_message('info', "History data table created successfully: {$history_table}");
                return true;
            } else {
                $error = $CI->db->error();
                log_message('error', "Failed to create history data table: " . print_r($error, true));
                log_message('error', "SQL: " . $sql);
                return false;
            }
            
        } catch (Exception $e) {
            log_message('error', "Exception creating history data table: " . $e->getMessage());
            log_message('error', "SQL: " . $sql);
            return false;
        }
    }
}

if (!function_exists('create_history_status_table')) {
    /**
     * Create history status table
     * Support MySQL dan PostgreSQL
     * 
     * @param string $main_table - Nama table utama
     * @param string $history_status_table - Nama table history status
     * @return bool
     */
    function create_history_status_table($main_table, $history_status_table) {
        
        $CI =& get_instance();
        $driver = get_db_driver();
        $foreign_key_column = $main_table . '_id';
        
        if ($driver == 'postgre') {
            $sql = "CREATE TABLE IF NOT EXISTS \"{$history_status_table}\" (
  \"id\" SERIAL PRIMARY KEY,
  \"{$foreign_key_column}\" INTEGER NOT NULL,
  \"tanggal\" TIMESTAMP NOT NULL,
  \"status_id\" INTEGER DEFAULT NULL,
  \"keterangan\" TEXT,
  \"history_data_id\" INTEGER DEFAULT NULL,
  \"active\" INTEGER DEFAULT 1,
  \"created\" TIMESTAMP DEFAULT NULL,
  \"modified\" TIMESTAMP DEFAULT NULL,
  \"createdid\" INTEGER DEFAULT NULL,
  \"modifiedid\" INTEGER DEFAULT NULL
);";
            
            // Create indexes separately
            $index_sqls = [
                "CREATE INDEX IF NOT EXISTS \"idx_{$foreign_key_column}\" ON \"{$history_status_table}\" (\"{$foreign_key_column}\");",
                "CREATE INDEX IF NOT EXISTS \"idx_status_id\" ON \"{$history_status_table}\" (\"status_id\");",
                "CREATE INDEX IF NOT EXISTS \"idx_tanggal\" ON \"{$history_status_table}\" (\"tanggal\");",
                "CREATE INDEX IF NOT EXISTS \"idx_history_data_id\" ON \"{$history_status_table}\" (\"history_data_id\");",
                "CREATE INDEX IF NOT EXISTS \"idx_active\" ON \"{$history_status_table}\" (\"active\");"
            ];
            
        } else {
            $sql = "CREATE TABLE IF NOT EXISTS `{$history_status_table}` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `{$foreign_key_column}` INT(11) NOT NULL COMMENT 'Foreign key to {$main_table}.id',
  `tanggal` DATETIME NOT NULL COMMENT 'Tanggal perubahan status',
  `status_id` INT(11) DEFAULT NULL COMMENT 'Status ID',
  `keterangan` TEXT COMMENT 'Keterangan perubahan',
  `history_data_id` INT(11) DEFAULT NULL COMMENT 'Foreign key to history data table',
  `active` INT(11) DEFAULT 1,
  `created` DATETIME DEFAULT NULL,
  `modified` DATETIME DEFAULT NULL,
  `createdid` INT(11) DEFAULT NULL,
  `modifiedid` INT(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_{$foreign_key_column}` (`{$foreign_key_column}`),
  KEY `idx_status_id` (`status_id`),
  KEY `idx_tanggal` (`tanggal`),
  KEY `idx_history_data_id` (`history_data_id`),
  KEY `idx_active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
        }
        
        log_message('debug', "CREATE HISTORY STATUS TABLE SQL:\n" . $sql);
        
        try {
            $result = $CI->db->query($sql);
            
            if ($result) {
                // Create indexes for PostgreSQL
                if ($driver == 'postgre' && isset($index_sqls)) {
                    foreach ($index_sqls as $index_sql) {
                        $CI->db->query($index_sql);
                    }
                }
                
                log_message('info', "History status table created successfully: {$history_status_table}");
                return true;
            } else {
                $error = $CI->db->error();
                log_message('error', "Failed to create history status table: " . print_r($error, true));
                return false;
            }
            
        } catch (Exception $e) {
            log_message('error', "Exception creating history status table: " . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('get_history')) {
    /**
     * Get history data
     * 
     * @param string $table_name - Nama table utama
     * @param int $record_id - ID record
     * @param int $limit - Limit hasil
     * @return array
     */
    function get_history($table_name, $record_id, $limit = 10) {
        
        $CI =& get_instance();
        $history_status_table = 'history_' . $table_name . '_status';
        $foreign_key = $table_name . '_id';
        
        if (!table_exists($history_status_table)) {
            return [];
        }
        
        $CI->db->select('hs.*, u.fullname as created_by_name');
        $CI->db->from($history_status_table . ' hs');
        $CI->db->join('users_data u', 'hs.createdid = u.id', 'left');
        $CI->db->where('hs.' . $foreign_key, $record_id);
        $CI->db->where('hs.active', 1);
        $CI->db->order_by('hs.tanggal', 'DESC');
        $CI->db->limit($limit);
        
        $query = $CI->db->get();
        
        return $query->result_array();
    }
}

if (!function_exists('get_history_with_data')) {
    /**
     * Get history with full data
     * 
     * @param string $table_name
     * @param int $record_id
     * @param int $limit
     * @param string $status_table - Optional: nama table status master (default: master_status_maintenance)
     * @return array
     */
    function get_history_with_data($table_name, $record_id, $limit = 10, $status_table = 'master_status_maintenance') {
        
        $CI =& get_instance();
        $history_data_table = 'history_' . $table_name . '_data';
        $history_status_table = 'history_' . $table_name . '_status';
        $foreign_key = $table_name . '_id';
        
        if (!table_exists($history_status_table) || !table_exists($history_data_table)) {
            return [];
        }
        
        $CI->db->select('hs.*, hd.*, u.fullname as created_by_name, ms.name as status_name, ms.color as status_color');
        $CI->db->from($history_status_table . ' hs');
        $CI->db->join($history_data_table . ' hd', 'hs.history_data_id = hd.id', 'left');
        $CI->db->join('users_data u', 'hs.createdid = u.id', 'left');
        $CI->db->join($status_table . ' ms', 'hs.status_id = ms.id', 'left');
        $CI->db->where('hs.' . $foreign_key, $record_id);
        $CI->db->where('hs.active', 1);
        $CI->db->order_by('hs.tanggal', 'DESC');
        $CI->db->limit($limit);
        
        $query = $CI->db->get();
        
        return $query->result_array();
    }
}

if (!function_exists('compare_history')) {
    /**
     * Compare two history records
     * 
     * @param string $table_name
     * @param int $history_id_1
     * @param int $history_id_2
     * @return array|bool
     */
    function compare_history($table_name, $history_id_1, $history_id_2) {
        
        $CI =& get_instance();
        $history_data_table = 'history_' . $table_name . '_data';
        
        if (!table_exists($history_data_table)) {
            return false;
        }
        
        $CI->db->where('id', $history_id_1);
        $data1 = $CI->db->get($history_data_table)->row_array();
        
        $CI->db->where('id', $history_id_2);
        $data2 = $CI->db->get($history_data_table)->row_array();
        
        if (!$data1 || !$data2) {
            return false;
        }
        
        $differences = [];
        
        foreach ($data1 as $key => $value) {
            if (isset($data2[$key]) && $data2[$key] != $value) {
                $differences[$key] = [
                    'old' => $value,
                    'new' => $data2[$key]
                ];
            }
        }
        
        return $differences;
    }
}

if (!function_exists('delete_old_history')) {
    /**
     * Delete old history records (soft delete)
     * 
     * @param string $table_name
     * @param int $days_old - Hapus history yang lebih lama dari X hari
     * @return bool
     */
    function delete_old_history($table_name, $days_old = 365) {
        
        $CI =& get_instance();
        $history_status_table = 'history_' . $table_name . '_status';
        
        if (!table_exists($history_status_table)) {
            return false;
        }
        
        $date_threshold = date('Y-m-d H:i:s', strtotime("-{$days_old} days"));
        
        $CI->db->where('tanggal <', $date_threshold);
        $CI->db->update($history_status_table, ['active' => 0]);
        
        return true;
    }
}

if (!function_exists('get_history_count')) {
    /**
     * Get total history count for a record
     * 
     * @param string $table_name
     * @param int $record_id
     * @return int
     */
    function get_history_count($table_name, $record_id) {
        
        $CI =& get_instance();
        $history_status_table = 'history_' . $table_name . '_status';
        $foreign_key = $table_name . '_id';
        
        if (!table_exists($history_status_table)) {
            return 0;
        }
        
        $CI->db->where($foreign_key, $record_id);
        $CI->db->where('active', 1);
        $CI->db->from($history_status_table);
        
        return $CI->db->count_all_results();
    }
}

if (!function_exists('get_latest_history')) {
    /**
     * Get latest history record
     * 
     * @param string $table_name
     * @param int $record_id
     * @return array|null
     */
    function get_latest_history($table_name, $record_id) {
        
        $CI =& get_instance();
        $history_status_table = 'history_' . $table_name . '_status';
        $foreign_key = $table_name . '_id';
        
        if (!table_exists($history_status_table)) {
            return null;
        }
        
        $CI->db->select('hs.*, u.fullname as created_by_name');
        $CI->db->from($history_status_table . ' hs');
        $CI->db->join('users_data u', 'hs.createdid = u.id', 'left');
        $CI->db->where('hs.' . $foreign_key, $record_id);
        $CI->db->where('hs.active', 1);
        $CI->db->order_by('hs.tanggal', 'DESC');
        $CI->db->limit(1);
        
        $query = $CI->db->get();
        
        return $query->num_rows() > 0 ? $query->row_array() : null;
    }
}

/* End of file history_helper.php */
/* Location: ./application/helpers/history_helper.php */