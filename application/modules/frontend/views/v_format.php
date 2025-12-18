<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Kalibrasi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .table, .th, .td {
            border: 1px solid black;
        }
        .th, .td {
            padding: 8px;
            text-align: left;
        }
        .header {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .footer {
            margin-top: 20px;
        }

         .text-center {
        	text-align: center;
        }
        .text-right {
        	text-align: right;
        }

        .text-left {
        	text-align: left;
        }

        .signature {
            width: 70%;
            margin-left: 15%;
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        .signature div {
            text-align: center;
        }
    </style>
	
	<script src="<?php echo base_url(); ?>themes/ortyd/assets/vendors/qr-code/dist/qr-code.js" type="text/javascript"></script> 
	
</head>
<body style="background-size: cover;background-image:url('<?php echo base_url(); ?>themes/ortyd/assets/media/format/sertifikat1.jpg')">

<div class="header">
    <h1> SERTIFIKAT KALIBRASI </h1>
    <h3><i> CALIBRATION CERTIFICATE </i></h3>

    <div class="signature" style="padding-top:30px">
    	<div></div>
    	<div class="text-right">
    		<B>Nomor Seri : <?php echo isset($datasertifikat['certificate_no'])?$datasertifikat['certificate_no']:'-'?></B><br>
    		<B>Hal 1 dari <?php echo isset($datasertifikat['alat_jumlah_halaman'])?$datasertifikat['alat_jumlah_halaman']:'-'?></B><br>
    		<B><label style="font-size: 12px;"><i>Page 1 of <?php echo isset($datasertifikat['alat_jumlah_halaman'])?$datasertifikat['alat_jumlah_halaman']:'-'?></i></label></B>
    	</div>
    </div>
</div>

<center>
	<table border="0" style="width: 70%;">
	    <tbody>
	    	<tr>
	    		<td><b><u>Identitas Alat</u></b></td>
	    	</tr>
	    	<tr>
	    		<td style="font-size: 12px;"><i>Instrument Identity</i></td>
	    	</tr>
	    	<tr>
	    		<td>Nama<br><label style="font-size: 12px;"><i>Name</i></label></td>
	    		<td>:</td>
	    		<td><?php echo isset($datasertifikat['alat_nama'])?$datasertifikat['alat_nama']:'-'?></td>
	    	</tr>
	    	<tr>
	    		<td>Alamat<br><label style="font-size: 12px;"><i>Address</i></label></td>
	    		<td>:</td>
	    		<td width="100%"><?php echo isset($datasertifikat['permohonan_alamat'])?$datasertifikat['permohonan_alamat']:'-'?></td>
	    	</tr>
	    	<tr>
	    		<td width="40%"><b><u>Identitas Pemilik</u></b></td>
	    	</tr>
	    	<tr>
	    		<td style="font-size: 12px;"><i>Owner Identity</i></td>
	    	</tr>
	    	<tr>
	    		<td>Nama <br><label style="font-size: 12px;"><i>Name</i></label></td>
	    		<td>:</td>
	    		<td><?php echo isset($datasertifikat['permohonan_pic'])?$datasertifikat['permohonan_pic']:'-'?></td>
	    	</tr>
	    	<tr>
	    		<td>Alamat <br><label style="font-size: 12px;"><i>Address</i></td>
	    		<td>:</td>
	    		<td><?php echo isset($datasertifikat['permohonan_alamat'])?$datasertifikat['permohonan_alamat']:'-'?></td>
	    	</tr>
	    	<tr>
	    		<td><b><u>Identitas Standar</u></b></td>
	    	</tr>
	    	<tr>
	    		<td style="font-size: 12px;"><i>Reference Identity</i></td>
	    	</tr>
	    	<tr>
	    		<td>Nama <br><label style="font-size: 12px;"><i>Name</i></td>
	    		<td>:</td>
	    		<td><?php echo isset($datasertifikat['nama_ketertelusuran'])?$datasertifikat['nama_ketertelusuran']:'-'?></td>
	    	</tr>
	    	<tr>
	    		<td>Ketertelusuran<br><label style="font-size: 12px;"><i>Traceability</i></td>
	    		<td>:</td>
	    		<td><?php echo isset($datasertifikat['nama_ketertelusuran'])?$datasertifikat['nama_ketertelusuran']:'-'?></td>
	    	</tr>
	    	<tr>
	    		<td><b><u>Pengesahan</u></b></td>
	    	</tr>
	    	<tr>
	    		<td style="font-size: 12px;"><i>Authorization</i></td>
	    	</tr>
	    	<tr>
	    		<td>Pejabat <br><label style="font-size: 12px;"><i>Author Officer</i></label></td>
	    		<td>:</td>
	    		<td><?php echo isset($datasertifikat['position_name'])?$datasertifikat['position_name']:'-'?></td>
	    	</tr>
	    	<tr>
	    		<td>Nama <br><label style="font-size: 12px;"><i>Name</i></label></td>
	    		<td>:</td>
	    		<td><?php echo isset($datasertifikat['fullname'])?$datasertifikat['fullname']:'-'?></td>
	    	</tr>
	    	<tr>
	    		<td>Tanggal Pengesahan <br><label style="font-size: 12px;"><i>Date Of Issue</i></label></td>
	    		<td>:</td>
	    		<td><?php echo isset($datasertifikat['created'])?$datasertifikat['created']:'-'?></td>
	    	</tr>
	    </tbody>
	</table>
</center>

<div class="footer">
    <table>
    	<tr>
    		<td width="10%">
				<qr-code
				  id="qr1"
				  contents="<?php echo base_url('certificate/').base64_encode($slug); ?>"
				  module-color="#1c5b99"
				  position-ring-color="#1c5b99"
				  position-center-color="#1c5b99"
				  mask-x-to-y-ratio="1.2"
				  style="
					width: 200px;
					height: 200px;
					margin: 2em auto;
					background-color: #fff;
				  "
				>
				  <img width="40" style="border-radius: 50px;padding: 2px;box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;border-color: #009694;" src="<?php echo base_url('logo.svg'); ?>" slot="icon" />
				  
				</qr-code>

			</td>
    		<td style="font-size: 12px;">
    			<b>
    				<i>
    					Sertifikat ini diterbitkan melalui sistem pelayanan secara elektronik pada Direktorat Jenderal Perlindungan Konsumen dan Tertib Niaga Kementerian Perdagangan yang tidak membutuhkan cap dan tanda tangan basah.
    				</i>
    			</b>
    		</td>
    	</tr>
    	<tr>
    		<td width="10%"></td>
    		<td style="font-size: 12px;">
				<i>
					This sertificate issued by Directorate General of Consumer Protection and Trade Compliance’s electronic services system. No Signature or seal is required
				</i>
    		</td>
    	</tr>
    </table>
</div>
</body>
</html>
