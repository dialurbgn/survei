<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// Pastikan MPDF di-load dari autoloader Composer
require_once FCPATH . 'vendor/autoload.php';
// Load MPDF autoload (jika menggunakan Composer)
use Mpdf\Mpdf;

#[\AllowDynamicProperties]
class Mpdfgenerator {
    
    // Fungsi untuk menghasilkan PDF
    public function generate($html, $filename='', $paper = 'A4', $orientation = 'P', $stream=TRUE, $output = 'uploads/')
    {   
    
    
        $tempDir = FCPATH . 'file/cache/tmp';
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        // Membuat objek MPDF dengan pengaturan kertas dan orientasi
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => $paper,
            'orientation' => $orientation,
            'tempDir' => $tempDir // Menentukan direktori sementara
        ]);

        // Menulis HTML ke dalam PDF
        $mpdf->WriteHTML($html);

        // Jika stream = TRUE, tampilkan PDF di browser, jika FALSE simpan file
        if ($stream) {
            // Pastikan folder output ada
            $dir = './' . $output;
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }

            // Simpan PDF di folder yang ditentukan
            $pdfContent = $mpdf->Output('', 'S'); // 'S' untuk menyimpan output sebagai string
            file_put_contents(FCPATH.$output.$filename.".pdf", $pdfContent);

            // Streaming PDF ke browser (tanpa mendownload, '0' berarti tampilkan di browser)
            $mpdf->Output($filename.".pdf", 'I');
        } else {
            // Mengembalikan output PDF sebagai string
            return $mpdf->Output('', 'S');
        }
    }
    
    // Fungsi untuk menyimpan PDF (tanpa streaming)
    public function savePDF($html, $filename='', $paper = 'A4', $orientation = 'P', $stream=TRUE, $output = 'uploads/')
    {   
    
        $tempDir = FCPATH . 'file/cache/tmp';
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        // Membuat objek MPDF
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => $paper,
            'orientation' => 'P',
            'isHtml5ParserEnabled' => true, // Mengaktifkan HTML5 parsing
            'isRemoteEnabled' => true, // Mengizinkan pengambilan gambar dari URL eksternal
            'tempDir' => $tempDir // Menentukan direktori sementara
        ]);

        // Menulis HTML ke dalam PDF
        $mpdf->WriteHTML($html);

        // Jika stream = TRUE, simpan dan stream file
        if ($stream) {
            // Pastikan folder output ada
            $dir = './' . $output;
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }

            // Menyimpan file PDF
            $pdfContent = $mpdf->Output('', 'S'); // 'S' untuk menyimpan output sebagai string
            $result = file_put_contents(FCPATH . $output . $filename . ".pdf", $pdfContent);

            if ($result === false) {
                return false;
            } else {
                // Mengembalikan URL dari file yang disimpan
                return base_url() . $output . $filename . ".pdf";
            }
        } else {
            return $mpdf->Output('', 'S'); // Mengembalikan PDF sebagai string
        }
    }

    // Fungsi untuk menghasilkan sertifikat PDF
    public function generatecertificate($html, $filename='', $paper = 'A4', $orientation = 'P', $stream=TRUE, $output = '', $certificate_no = '')
    {   
        // Dapatkan instance CI untuk akses ke library dan model lainnya
        $this->CI =& get_instance();

        $tempDir = FCPATH . 'file/cache/tmp';
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        
        // Membuat objek MPDF
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => $paper,
            'orientation' => $orientation,
            'tempDir' => $tempDir // Menentukan direktori sementara
        ]);

        // Menulis HTML ke dalam PDF
        $mpdf->WriteHTML($html);

        if ($stream) {
            // Tentukan path untuk menyimpan file PDF
            if(PHP_OS == 'WINNT'){
                $file = str_replace('/', '\\', FCPATH . $output . $certificate_no . ".pdf");
            } else {
                $file = FCPATH . $output . $certificate_no . ".pdf";
            }

            // Simpan PDF ke dalam file
            $pdfContent = $mpdf->Output('', 'S'); // 'S' untuk menyimpan output sebagai string
            file_put_contents($file, $pdfContent);

            // Panggil model atau fungsi lain untuk mendapatkan sertifikat
            $certificate_file = $this->CI->ortyd->getCert($file, $certificate_no);

            if ($certificate_file != null) {
                return $certificate_file; // Mengembalikan path file sertifikat yang sudah dihasilkan
            } else {
                return null; // Sertifikat tidak ditemukan
            }
        } else {
            return null; // Jika tidak di-streaming, kembalikan null
        }
    }
}
