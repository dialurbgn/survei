				<input type="hidden" id="cookies_sso" name="cookies_sso" value="<?=$this->input->cookie('sso_perizinan_token', TRUE)?>">
				
				<?php
				
				$content = $this->ortyd->getPageFrontend();
					if($content != null){
						$content_web = $this->shortcode->parse($content[0]['content']);
						$title = title;
						$og_tipe = title;
						$og_title = title;
						$og_url = base_url();
						$meta_description = $content[0]['name'].' - '.substr($this->ortyd->_clean_input_data($content[0]['description']),0,300);
					}
					
				?>
				<div class="row">
							
					<?= $content_web; ?>
									
				</div>
			<!-- STYLE -->
				
				<div class="modal fade" id="bannerModal" tabindex="-1" role="dialog" aria-labelledby="bannerModalLabel" aria-hidden="true">
				  <div class="modal-dialog" role="document">
					<div class="modal-content ">
					  <!-- <div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						  <span aria-hidden="true">&times;</span>
						</button>
					  </div> -->
					  <div class="modal-body">
							<div class="owl-carousel owl-theme" id="owl-slider-home">
							
							<?php
							$this->db->select('data_popup.*,data_gallery.path');
							$this->db->where('data_popup.active',1);
							$this->db->join('data_gallery','data_gallery.id = data_popup.cover','left');
							$this->db->order_by('data_popup.sort','ASC');
							//$this->db->limit(3);
							$query = $this->db->get('data_popup');
							$query = $query->result_array();
							if(count($query) > 0){
								foreach($query as $rows){
							?>
								<div class="item" >
									<img data-lazy="<?php echo base_url().$rows['path']; ?>" alt="<?php echo $rows['title']; ?>" style="width:100%;max-width: 600px;">
								</div>
							
								<?php }
							}
							?>
								
								
							</div>

					  </div>
					  <!-- <div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-primary">Save changes</button>
					  </div> -->
					</div>
				  </div>
				</div>

				<script>
				$( document ).ready(function() {
					$("#bannerModal").modal('show');
					$('#owl-slider-home').owlCarousel({
						items:1,
						lazyLoad:true,
						loop:true,
						//autoWidth:true,
						 responsiveClass:true,
						margin:10,
						autoplay:true,
						autoplayTimeout:2000,
						autoplayHoverPause:false,
						 responsive:{
								0:{
									items:1,
									nav:true
								}
							}
					});
					
				});
				

				</script>
