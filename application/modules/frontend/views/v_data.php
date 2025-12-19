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
