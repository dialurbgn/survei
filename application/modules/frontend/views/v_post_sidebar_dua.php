<div class="col-lg-3 mb-4 mb-lg-0">
	
	<?php
		$this->db->select('master_download_type.*, lower(master_download_type.name) as type_name');
		$this->db->where('active',1);
		$this->db->order_by('sort','ASC');
		//$this->db->limit(5);
		$query = $this->db->get('master_download_type');
		$query = $query->result_array();
		if(count($query) > 0){
	?>
			
	<div class="col-lg-12">
		<h4 class="mb-0 font-weight-bold">
			
			<?php echo 'Kategori Berkas'; ?>
								
		</h4>
								
		<div class="divider divider-primary divider-small mt-2 mb-4">
			<hr class="my-0 me-auto">
		</div>
		
		<?php 
			if(count($query) > 0){
				echo '<ul>';
				foreach($query as $rows){
					echo '<li><a href="'.base_url().'download'.'/'.$rows['slug'].'">'.$rows['name'].'</a><br></li>';
				}
				echo '<li><a href="'.base_url().'download'.'">'.'Semua Berkas'.'</a><br></li>';
				echo '</ul>';
			}
		?>
						
	</div>
	
	<?php } ?>
	
</div>