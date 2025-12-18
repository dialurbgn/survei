<?php
//require 'vendor/autoload.php';

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ValidationException;

class QrCodeGenerator
{
    public function generateQRCodeWithLogo($link,$label,$output)
    {
       $writer = new PngWriter();

        // Create QR code
        $qrCode = new QrCode(
            data: $link,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Low,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(29, 84, 169, 1),
            backgroundColor: new Color(255, 255, 255, 0)
        );

        // Create generic logo
        $logo = new Logo(
            path: base_url().'logo-report.png',
            resizeToWidth: 50,
            punchoutBackground: false
        );

        // Create generic label
        $label = new Label(
            text: $label,
            textColor: new Color(29, 84, 169, 1)
        );

        $result = $writer->write($qrCode, $logo, $label);

        // Validate the result
        //$writer->validateResult($result, 'Life is too short to be generating QR codes');
        
        $result->saveToFile($output);
        
        return true;
    }
}