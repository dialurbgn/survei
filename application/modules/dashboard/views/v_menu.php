
<style>
.menu-container {
    padding: 1rem;
}
.menu-card {
    background: #fff;
    border-radius: 12px;
    padding: 1.5rem 1rem;
    margin-bottom: 1.5rem;
    text-align: center;
    transition: 0.3s ease;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    position: relative;
    cursor: pointer;
    min-height: 180px;
}
.menu-card:hover {
    background: #f8f9fa;
    transform: translateY(-4px);
    z-index: 10;
}
.menu-icon-main {
    font-size: 2.5rem;
    color: #4e73df;
    background: #eef1f8;
    padding: 20px;
    border-radius: 16px;
    width: 70px;
    height: 70px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.4s ease, background 0.3s, color 0.3s;
    box-shadow: 0 4px 10px rgba(0,0,0,0.06);
}
.menu-card:hover .menu-icon-main {
    transform: scale(1.15);
    background: #dce9ff;
    color: #1cc88a;
}
.menu-title-text {
    font-size: 14px;
    font-weight: 600;
    color: #333;
    margin-top: 12px;
    min-height: 40px;
}
.dropdown-child {
    display: none;
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    background: #fff;
    border-radius: 8px;
    padding: 0.5rem 0;
    min-width: 200px;
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    z-index: 999;
}
.menu-card.open .dropdown-child {
    display: block;
}
.dropdown-child a {
    display: flex;
    align-items: center;
    padding: 10px 14px;
    font-size: 13px;
    color: #333;
    text-decoration: none;
    transition: 0.2s;
}
.dropdown-child a:hover {
    background: #f1f1f1;
}
.dropdown-child i {
    margin-right: 8px;
    font-size: 15px;
    width: 20px;
    text-align: center;
}
</style>

<script>
function toggleDropdown(el) {
    const allCards = document.querySelectorAll('.menu-card');
    allCards.forEach(card => {
        if (card !== el.closest('.menu-card')) {
            card.classList.remove('open');
        }
    });
    el.closest('.menu-card').classList.toggle('open');
}
document.addEventListener('click', function(e) {
    if (!e.target.closest('.menu-card')) {
        document.querySelectorAll('.menu-card').forEach(card => card.classList.remove('open'));
    }
});
</script>

<div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-xxl">
	<div class="content flex-row-fluid" id="kt_content">
		<div class="">
			<div class="col-sm-12" style="padding: 0px;">
				<div class="card-body" style="padding: 5px;">

					<div class="row menu-container">
						<?php 
						$this->db->select('master_menu.*');
						$this->db->where('parent_id', null);
						$this->db->where('master_menu.show', 1);
						$this->db->where('users_groups_access.view', 1);
						$this->db->where('gid', $this->session->userdata('group_id'));
						$this->db->join('users_groups_access', 'master_menu.id = users_groups_access.menu_id');
						$this->db->order_by('sort', 'asc');
						$query = $this->db->get('master_menu')->result_object();

						if ($query) {
							foreach ($query as $rows) {
								$this->db->select('master_menu.*');
								$this->db->where('parent_id', $rows->id);
								$this->db->where('master_menu.show', 1);
								$this->db->where('users_groups_access.view', 1);
								$this->db->where('gid', $this->session->userdata('group_id'));
								$this->db->join('users_groups_access', 'master_menu.id = users_groups_access.menu_id');
								$this->db->order_by('sort', 'asc');
								$querychild = $this->db->get('master_menu');
								$count = $querychild->num_rows();
								$querychild = $querychild->result_object();
						?>
							<div class="col-md-2 col-6">
								<div class="menu-card">
									<?php if ($count == 0) { ?>
										<a href="<?= base_url($rows->url); ?>">
											<i class="menu-icon-main <?= $rows->icon; ?>"></i>
											<div class="menu-title-text"><?= $rows->name; ?></div>
										</a>
									<?php } else { ?>
										<div onclick="toggleDropdown(this)">
											<i class="menu-icon-main <?= $rows->icon; ?>"></i>
											<div class="menu-title-text"><?= $rows->name; ?></div>
										</div>
										<div class="dropdown-child">
											<?php foreach ($querychild as $rowschild) { ?>
												<a href="<?= base_url($rowschild->url); ?>">
													<i class="<?= $rowschild->icon; ?>"></i><?= $rowschild->name; ?>
												</a>
											<?php } ?>
										</div>
									<?php } ?>
								</div>
							</div>
						<?php }} ?>

						<!-- Tambahan User Manual -->
						<div class="col-md-2 col-6">
							<div class="menu-card">
								<a href="<?= base_url('uploads/user_manual.pdf'); ?>" target="_blank">
									<i class="menu-icon-main fa fa-book"></i>
									<div class="menu-title-text">User Manual</div>
								</a>
							</div>
						</div>
					</div>
										
				</div>
			</div>
		</div>
	</div>
</div>