<section class="page-header page-header-modern bg-color-quaternary page-header-lg border-0 m-0">
	<div class="container position-relative z-index-2">
		<div class="row text-center text-md-start py-3">
			<div class="col-md-8 order-2 order-md-1 align-self-center p-static">
				<h1 class="font-weight-bold text-color-dark text-10 mb-0">
					<?php echo $title; ?>
				</h1>
			</div>
			<div class="col-md-4 order-1 order-md-2 align-self-center">
				<ul class="breadcrumb breadcrumb-dark font-weight-bold d-block text-md-end text-4 mb-0">
					<li>
						<a href="<?php echo base_url(); ?>" class="text-decoration-none text-dark">Beranda</a>
					</li>
					<li class="text-upeercase active text-color-primary">
						<?php echo $title; ?>
					</li>
				</ul>
			</div>
		</div>
	</div>
</section>

<div class="container py-5 my-3">
					<div class="row">
					
						
										
						<div class="col-lg-9 mb-5 mb-lg-0">
						
						<div class="header-column">
											<div class="header-row justify-content-end py-2">
												<div class="header-nav-features header-nav-features-no-border ms-0 ps-0 w-100">
													<?php 
													$dataq = '';
													if(isset($_GET['q'])){
														$dataq = $this->ortyd->_clean_input_data($_GET['q']);
													}?>
													<form role="search" class="d-flex w-100" action="<?php echo base_url('search'); ?>">
														<div class="simple-search input-group w-100">
															<input style="    border: 1px solid #212529;    max-width: 90%;    border-radius: 5px 0px 0px 5px;" class="form-control text-5" id="headerSearch" name="q" type="search" value="<?php echo $dataq; ?>" placeholder="Cari...">
															<button  style="    border: 1px solid #212529;    max-width: 400px;border-radius: 0px 5px 5px 0px;" class="btn" type="submit">
																<i class="fa fa-search text-color-primary header-nav-top-icon p-relative top-1"></i>
															</button>
														</div>
														
														<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
													</form>
												</div>
											</div>
										</div>
										<br>
								

							<?php	
							if ($datarows) {
								echo '<ul>';
								$no = (int)$this->uri->segment('1') + 1;
								foreach ($datarows as $rows) {
									echo '<li>';

									$isOwner = ($rows['user_id'] == $this->session->userdata('userid') || $this->session->userdata('group_id') == 1);
									$link = '#'; // default

									if ($rows['type'] == 'Permohonan') {
										$link = 'data_pengajuan/editdata/' . $rows['slug'];
									} elseif ($rows['type'] == 'SPK') {
										$link = 'data_pengajuan_spk/view/' . $rows['slug'];
									}  elseif ($rows['type'] == 'page') {
										$link = $rows['slug'];
									} else {
										$link = $rows['type_name'].'/' . $rows['slug'];
									}

									echo $rows['type'] . '<br>';
									echo '<span style="background:' . $rows['status_color'] . ';padding: 3px;border-radius: 5px;color: #fff;font-size: 10px;">' . $rows['status_name'] . '</span><br>';

									if ($rows['type'] == 'Permohonan' && $isOwner) {
										// Tipe Permohonan dan pemilik / admin: tampilkan link edit
										echo '<a target="_blank" href="' . base_url($link) . '"><h5 style="text-transform: inherit;">' . $rows['name'] . '</h5></a>';
									}else if ($rows['type'] == 'Permohonan' && !$isOwner) {
										// Tipe Permohonan dan pemilik / admin: tampilkan link edit
										echo '<h5 style="text-transform: inherit;">' . $rows['name'] . '</h5>';
									}  else {
										// Tipe lain: link biasa
										echo '<a target="_blank" href="' . base_url($link) . '"><h5 style="text-transform: inherit;">' . $rows['name'] . '</h5></a>';
									}

									echo '</li>';
								}
								echo '</ul>';
								echo $this->pagination->create_links();
							} else {
								echo 'NO RESULT DATA';
							}

							?>

						</div>
						
						<?php include('v_post_sidebar.php'); ?>
						
					</div>
				</div>