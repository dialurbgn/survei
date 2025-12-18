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

	<div class="header-column">
											<div class="header-row justify-content-end py-2">
												<div class="header-nav-features header-nav-features-no-border ms-0 ps-0 w-100">
													<?php 
													$dataq = '';
													if(isset($_GET['q'])){
														$dataq = $_GET['q'];
													}?>
													<form role="search" class="d-flex w-100" action="<?php echo base_url('search'); ?>">
														<div class="simple-search input-group w-100">
															<input style="    border: 1px solid #212529;    max-width: 100%;    border-radius: 5px 0px 0px 5px;" class="form-control text-5" id="headerSearch" name="q" type="search" value="<?php echo $dataq; ?>" placeholder="Cari...">
															<button  style="    border: 1px solid #212529;    max-width: 400px;border-radius: 0px 5px 5px 0px;" class="btn" type="submit">
																<i class="fa fa-search text-color-primary header-nav-top-icon p-relative top-1"></i>
															</button>
														</div>
													</form>
												</div>
											</div>
										</div>
										<br>
										<br>
										
	<div class="row" id="accordion_faq">
	
		<div class="col-lg-12 mb-4 mb-lg-0">
		
		
		<?php
			$this->db->select('data_faq.*');
			$this->db->where('data_faq.active',1);
			$this->db->order_by('sort','ASC');
			$query = $this->db->get('data_faq');
			$query = $query->result_array();
			if(count($query) > 0){
				$x=0;
				foreach($query as $rows){
					$active = null;
					if($x == 0){
						$active = 'active';
					}
		?>
	
			
			<button class="accordion <?php if($active != ''){echo ' active';} ?>"><?php echo $rows['name']; ?></button>
			<div class="panel" <?php if($active != ''){echo ' style="display: block;"';} ?>>
			  <p><?php echo $rows['description']; ?></p>
			</div>
			
				<?php 
					$x++;
				} 
			}
			?>
		
		</div>
		
		

	</div>
</div>

<script>
$( document ).ready(function() {
	var acc = document.getElementsByClassName("accordion");
	var i;

	for (i = 0; i < acc.length; i++) {
	  acc[i].addEventListener("click", function() {
		/* Toggle between adding and removing the "active" class,
		to highlight the button that controls the panel */
		this.classList.toggle("active");

		/* Toggle between hiding and showing the active panel */
		var panel = this.nextElementSibling;
		if (panel.style.display === "block") {
		  panel.style.display = "none";
		} else {
		  panel.style.display = "block";
		}
	  });
	}
})

</script>