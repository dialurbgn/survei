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

							<?php	
							if($datarows){
								$no = (int)$this->uri->segment('1') + 1;
								foreach($datarows as $rows){ 
							?>
							<article class="mb-5">
								<div class="card border-0 border-radius-0 custom-box-shadow-1">
									<div class="card-img-top">
										<a href="<?php echo base_url().strtolower($rows['type_name']).'/'.$rows['slug']; ?>">
											<?php if($rows['cover'] != ''){ ?>
											<img class="card-img-top border-radius-0 hover-effect-2" src="<?php echo base_url().$rows['path']; ?>" alt="<?php echo $rows['name']; ?>">
											<?php }else{ ?>
											<img class="card-img-top border-radius-0 hover-effect-2" src="<?php echo base_url().'themes/frontend/img/kadi/noimage.png'; ?>" alt="KADI">
											<?php } ?>
										</a>
									</div>
									<div class="card-body bg-light px-0 py-4 z-index-1">
										<p class="text-uppercase text-color-default text-1 mb-1 pt-1">
											<time pubdate datetime="2021-01-10">
												<?php 
												echo '<span>'.$this->ortyd->hari_ini($rows['date']).'</span> ditulis oleh ';
												if($rows['ditulis'] == ''){
													echo '<span>'.$this->ortyd->select2_getname($rows['createdid'],'users_data','id','fullname').'</span><p></p>';	
												}else{
													echo '<span>'.$rows['ditulis'].'</span><p></p>';	
												}
												?>
											</time> 
										</p>
										<div class="card-body p-0">
											<h4 class="card-title alternative-font-4 font-weight-semibold text-5 mb-3"><a class="text-color-dark text-color-hover-primary text-decoration-none font-weight-bold text-3" href="<?php echo base_url().strtolower($rows['type_name']).'/'.$rows['slug']; ?>">
											
												<?php echo $rows['name']; ?></a>
											
											</h4>
											
											<p class="card-text mb-2">
												<?php echo substr($this->ortyd->_clean_input_data($rows['description']),0,300).'...'; ?>
											</p>
											
											<a href="<?php echo base_url().strtolower($rows['type_name']).'/'.$rows['slug']; ?>" class="custom-view-more d-inline-flex font-weight-medium text-color-primary">
												Selengkapnya
												<img width="27" height="27" src="<?php echo base_url();?>themes/frontend/img/demos/law-firm/icons/arrow-right.svg" alt="" data-icon data-plugin-options="{'onlySVG': true, 'extraClass': 'svg-fill-color-primary ms-2'}" style="width: 27px;" />
											</a>
										</div>
									</div>
								</div>
							</article>
							<?php }
								echo $this->pagination->create_links();
							}else{
								echo 'NO CONTENT DATA';
							}
							?>

						</div>
						
						<?php include('v_post_sidebar.php'); ?>
						
					</div>
				</div>