<?php

defined('BASEPATH') OR exit('No direct script access allowed');
// panggil autoload dompdf nya
require_once 'dompdf-master/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

#[\AllowDynamicProperties]
class Pdfgenerator {
    
    private $fontFamily = 'roboto';
    private $fontDir;
    
    public function __construct()
    {
        $this->fontDir = FCPATH . 'application/libraries/dompdf-master/fonts/';
    }

    private function getOptions()
    {
        $options = new Options();
        $options->set('isRemoteEnabled', TRUE);
        $options->set('defaultFont', $this->fontFamily);
        $options->set('fontDir', $this->fontDir);
        $options->set('fontCache', $this->fontDir);
        $options->set('chroot', FCPATH);

        return $options;
    }


    public function generate($html, $filename='', $paper = '', $orientation = '', $stream=TRUE, $output = 'uploads/')
    {   
		//echo $html;die();
        $options = new Options();
        $options->set('isRemoteEnabled', TRUE);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper($paper, $orientation);
        $dompdf->render();
        if ($stream) {
            
            $dir = '.'.$output;
					
            if(!file_exists($dir)){
                mkdir($dir,0755,true);
            }
                
            $pdfContent = $dompdf->output();
            file_put_contents(FCPATH.$output.$filename.".pdf", $pdfContent);
            $dompdf->stream($filename.".pdf", array("Attachment" => 0));
        } else {
            return $dompdf->output();
        }
    }
    
    public function savePDF($html, $filename='', $paper = '', $orientation = '', $stream=TRUE, $output = 'uploads/')
    {   
		//echo $html;die();
       // $options = new Options();
        $options = $this->getOptions();
        $options->set('isRemoteEnabled', TRUE);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper($paper, $orientation);
        $dompdf->render();
        if ($stream) {
            
            $dir = './'.$output;
					
            if(!file_exists($dir)){
                mkdir($dir,0755,true);
            }
                
            $pdfContent = $dompdf->output();
            $result = file_put_contents(FCPATH.$output.$filename.".pdf", $pdfContent);
            if ($result === false) {
               return false;
            } else {
               return base_url().$output.$filename.".pdf";
            }
        } else {
            return $dompdf->output();
        }
    }
    
    public function generatecertificate($html, $filename='', $paper = '', $orientation = '', $stream=TRUE, $output = '', $certificate_no = '')
    {   
    
        // echo $certificate_no;
        //die();
        // Get CI instance to access other libraries, models, etc.
        $this->CI =& get_instance();
        
        // Load the 'session' library from within the custom library
       //$this->CI->load->library('session');

		//echo $html;die();
        //$options = new Options();
        $options->set('isRemoteEnabled', TRUE);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper($paper, $orientation);
        $dompdf->render();
        if ($stream) {
            
            //$output = 'uploads/certificate/page1/';
            
            $pdfContent = $dompdf->output();
            
            if(PHP_OS == 'WINNT'){
				$file = str_replace('/', '\\',FCPATH.$output.$certificate_no.".pdf");
			}else{
                $file = FCPATH.$output.$certificate_no.".pdf";
            }

            file_put_contents($file, $pdfContent);
            
            $page1 = $file;
            $certificate_file = $this->CI->ortyd->getCert($page1,$certificate_no);
            
            if($certificate_file != null){
                //file_put_contents($certificate_file, $pdfContent);
                //$dompdf->stream(FCPATH.$certificate_file, array("Attachment" => 0));
                return $certificate_file;
            }else{
                return null;
            }
            
        } else {
            return null;
        }
    }
    
}