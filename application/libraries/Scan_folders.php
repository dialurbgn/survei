<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Scan_folders {

    // Fungsi untuk memindai folder dan file
    public function scan_forbidden_folders($dir, $allowed_folders) {
        // Memindai folder
        $files = scandir($dir);

        $result = [];

        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $file_path = $dir . DIRECTORY_SEPARATOR . $file;
                if (is_dir($file_path)) {
                    // Jika folder tidak diizinkan, tampilkan peringatan
                    $folder_name = basename($file_path);
                    if (!in_array($folder_name, $allowed_folders)) {
                        $result[] = "Akses folder dilarang: $file_path";
                    } else {
                        // Rekursif ke sub-folder
                        $result = array_merge($result, $this->scan_forbidden_folders($file_path, $allowed_folders));
                    }
                }
            }
        }

        return $result;
    }
}
