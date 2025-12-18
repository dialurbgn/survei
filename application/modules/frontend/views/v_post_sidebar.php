<div class="col-lg-2">

	<?php
		$this->db->select('data_article.*, lower(master_article_jenis.name) as type_name');
		$this->db->where('master_article_jenis.name','Berita');
		$this->db->where('data_article.active',1);
		$this->db->where('data_article.is_publish',1);
		$this->db->join('master_article_jenis','master_article_jenis.id = data_article.jenis_id','left');
		$this->db->order_by('date DESC, created DESC');
		$this->db->limit(5);
		$query = $this->db->get('data_article');
		$query = $query->result_array();
		if(count($query) > 0){
	?>
	
	<div class="col-lg-12">
		<h4 class="mb-0 font-weight-bold">
								
					<?php echo 'Berita Terbaru'; ?>
								
		</h4>
								
		<div class="divider divider-primary divider-small mt-2 mb-4">
			<hr class="my-0 me-auto">
		</div>
		
		<?php 
			if(count($query) > 0){
				echo '<ul>';
				foreach($query as $rows){
					echo '<li><a href="'.base_url().$rows['type_name'].'/'.$rows['slug'].'">'.$rows['name'].'</a><br></li>';
				}
				echo '</ul>';
			}
		?>
						
	</div>
	
	<?php } ?>
	
	<?php
		$this->db->select('data_article.*, lower(master_article_jenis.name) as type_name');
		$this->db->where('master_article_jenis.name','Informasi');
		$this->db->where('data_article.active',1);
		$this->db->where('data_article.is_publish',1);
		$this->db->join('master_article_jenis','master_article_jenis.id = data_article.jenis_id','left');
		$this->db->order_by('date DESC, created DESC');
		$this->db->limit(5);
		$query = $this->db->get('data_article');
		$query = $query->result_array();
		if(count($query) > 0){
	?>
			
	<div class="col-lg-12">
		<h4 class="mb-0 font-weight-bold">
			
			<?php echo 'Informasi Terbaru'; ?>
								
		</h4>
								
		<div class="divider divider-primary divider-small mt-2 mb-4">
			<hr class="my-0 me-auto">
		</div>
		
		<?php 
			if(count($query) > 0){
				echo '<ul>';
				foreach($query as $rows){
					echo '<li><a href="'.base_url().$rows['type_name'].'/'.$rows['slug'].'">'.$rows['name'].'</a><br></li>';
				}
				echo '</ul>';
			}
		?>
						
	</div>
	
	<?php } ?>
	
	<?php
		$this->db->select('data_article.*, lower(master_article_jenis.name) as type_name');
		$this->db->where('master_article_jenis.name','Artikel');
		$this->db->where('data_article.active',1);
		$this->db->where('data_article.is_publish',1);
		$this->db->join('master_article_jenis','master_article_jenis.id = data_article.jenis_id','left');
		$this->db->order_by('date DESC, created DESC');
		$this->db->limit(5);
		$query = $this->db->get('data_article');
		$query = $query->result_array();
		if(count($query) > 0){
	?>
	
	<div class="col-lg-12">
		<h4 class="mb-0 font-weight-bold">
								
					<?php echo 'Artikel Terbaru'; ?>
								
		</h4>
								
		<div class="divider divider-primary divider-small mt-2 mb-4">
			<hr class="my-0 me-auto">
		</div>
		
		<?php 
			if(count($query) > 0){
				echo '<ul>';
				foreach($query as $rows){
					echo '<li><a href="'.base_url().$rows['type_name'].'/'.$rows['slug'].'">'.$rows['name'].'</a><br></li>';
				}
				echo '</ul>';
			}
		?>
						
	</div>
	
	<?php } ?>
</div>