<section class="page-header page-header-modern bg-color-quaternary page-header-lg border-0 m-0">
	<div class="container position-relative z-index-2">
		<div class="row text-center text-md-start py-3">
			<div class="col-md-8 order-2 order-md-1 align-self-center p-static">
				<h1 class="font-weight-bold text-color-dark text-10 mb-0">
					<?php echo 'Cek Status Pengaduan'; ?>
				</h1>
			</div>
			<div class="col-md-4 order-1 order-md-2 align-self-center">
				<ul class="breadcrumb breadcrumb-dark font-weight-bold d-block text-md-end text-4 mb-0">
					<li>
						<a href="<?php echo base_url(); ?>" class="text-decoration-none text-dark">Beranda</a>
					</li>
					<li class="text-upeercase active text-color-primary">
						<?php echo 'Cek Status Pengaduan '; ?>
					</li>
				</ul>
			</div>
		</div>
	</div>
</section>
<section class="section border-0 lazyload my-0 bg-color-light" data-bg-src="" style="background-position: 50% 100%; ">
	<div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="contact-area style-two">
                    <form name="contact_form" class="default-form contact-form" action="#" method="post">
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                              <div class="form-group">
								<label>Kategori</label>
                                <select name="subject" class="form-control" id="direktorat">
                                  <option>Pilih Kategori</option>
                                  <option>Laporan Masyarakat</option>
                                  <option>Pengaduan</option>
                                  <option>Pertanyaan</option>
                                </select>
                              </div>
							</div>
							 <div class="col-md-12">  
                              <div class="form-group">
								<label>Nomor Pengaduan/Laporan</label>
                                <input class="form-control" type="text" name="nomor_pengaduan" placeholder="Nomor Pengaduan/Laporan" required="">
                              </div>
							 </div>

                            <div class="col-sm-12">
                                <div class="form-group text-center">
                                    <button type="submit" class="btn btn-primary btn-style-one"><i class="fa fa-search"></i> Cari Pengaduan</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- <div class="col-md-6 col-sm-12 col-xs-12">
                <div class="appointment-image-holder">
                    <figure>
                        <img src="images/background/appoinment.jpg" alt="Appointment">
                    </figure>
                </div>
            </div> -->
        </div>
    </div>
</section>

<script>
$( document ).ready(function() {
    $('#direktorat').select2();
});
</script>
