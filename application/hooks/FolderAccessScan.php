<?php
class FolderAccessScan {
    public function checkFolderAccess() {
        // Mendapatkan URI yang diminta
        $uri = uri_string();

        // Daftar folder yang diizinkan walaupun berada di root
        $allowed_folders = ['uploads', 'themes', 'file'];

        // Memisahkan URI berdasarkan '/'
        $uri_parts = explode('/', trim($uri, '/'));
        $folder = $uri_parts[0];

        // Kalau kosong (berarti root URL atau index.php), izinkan
        if (empty($uri) || $uri === 'index.php') {
            return;
        }

        // Path lengkap ke folder
        $folder_path = FCPATH . $folder;

        // Jika folder ada di root
        if (is_dir($folder_path)) {
            // Jika foldernya tidak ada di daftar yang diizinkan, tolak akses
            if (!in_array($folder, $allowed_folders)) {
                //show_error('Forbidden: Folder "' . $folder . '" is blocked', 403);
				redirect('404','refresh');
            } else {
                // Jika termasuk folder yang diizinkan
                //echo $uri . ' OK<br>';
                return;
            }
        } else {
            // Jika folder tidak ada di root path, loloskan
           // echo $uri . ' OK<br>';
            return;
        }
    }
}
