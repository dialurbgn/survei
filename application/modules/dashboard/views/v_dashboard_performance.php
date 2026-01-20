<style>
/* ============= GLOBAL STYLES ============= */

* {
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/* ============= STATISTICS CARDS - ULTRA MODERN ============= */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-bottom: 35px;
}

.stat-card {
    background: white;
    border-radius: 24px;
    padding: 32px 28px;
    position: relative;
    overflow: hidden;
    border: none;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    height: 100%;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--card-color-1) 0%, var(--card-color-2) 100%);
}

.stat-card-1 {
    --card-color-1: #667eea;
    --card-color-2: #764ba2;
}

.stat-card-2 {
    --card-color-1: #f093fb;
    --card-color-2: #f5576c;
}

.stat-card-3 {
    --card-color-1: #43e97b;
    --card-color-2: #38f9d7;
}

.stat-card-4 {
    --card-color-1: #fa709a;
    --card-color-2: #fee140;
}

.stat-card-5 {
    --card-color-1: #30cfd0;
    --card-color-2: #330867;
}

.stat-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.stat-card:hover::before {
    height: 6px;
}

.stat-icon-wrapper {
    width: 64px;
    height: 64px;
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
    background: linear-gradient(135deg, var(--card-color-1), var(--card-color-2));
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    transition: all 0.3s ease;
}

.stat-card:hover .stat-icon-wrapper {
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
}

.stat-icon-wrapper i {
    font-size: 28px;
    color: white;
}

.stat-label {
    font-size: 13px;
    font-weight: 600;
    color: #7E8299;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}

.stat-value {
    font-size: 32px;
    font-weight: 800;
    background: linear-gradient(135deg, var(--card-color-1), var(--card-color-2));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 8px;
    line-height: 1.2;
}

.stat-description {
    font-size: 12px;
    color: #A1A5B7;
    font-weight: 500;
}

/* ============= PROFILE HEADER - GLASSMORPHISM PREMIUM ============= */
.profile-header {
    background: white;
    backdrop-filter: blur(20px);
    border-radius: 28px;
    padding: 40px;
    margin-bottom: 35px;
    border: 1px solid rgba(228, 230, 239, 0.6);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
    position: relative;
    overflow: hidden;
}

.profile-header::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(102, 126, 234, 0.1) 0%, transparent 70%);
    pointer-events: none;
}

.avatar-container {
    position: relative;
}

.avatar-image {
    border: 6px solid white;
    border-radius: 50%;
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
}

.avatar-container:hover .avatar-image {
    transform: scale(1.05);
    box-shadow: 0 15px 45px rgba(0, 0, 0, 0.2);
}

.status-badge {
    position: absolute;
    bottom: 10px;
    right: 10px;
    width: 24px;
    height: 24px;
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    border-radius: 50%;
    border: 4px solid white;
    box-shadow: 0 4px 12px rgba(67, 233, 123, 0.5);
    animation: pulse-badge 2s ease-in-out infinite;
}

@keyframes pulse-badge {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); box-shadow: 0 4px 20px rgba(67, 233, 123, 0.8); }
}

.profile-name {
    font-size: 28px;
    font-weight: 800;
    color: #181C32;
    margin-bottom: 8px;
}

.profile-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-top: 12px;
}

.profile-meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #7E8299;
    font-size: 14px;
    font-weight: 600;
    padding: 8px 16px;
    background: #F9F9FC;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.profile-meta-item:hover {
    background: #F1F1F4;
    color: #667eea;
    transform: translateY(-2px);
}

.profile-meta-item i {
    font-size: 18px;
    color: #667eea;
}

.btn-create-report {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 20px;
    padding: 14px 32px;
    font-weight: 700;
    font-size: 15px;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.35);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-create-report::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s;
}

.btn-create-report:hover::before {
    left: 100%;
}

.btn-create-report:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(102, 126, 234, 0.45);
}

.btn-filter {
    width: 50px;
    height: 50px;
    border-radius: 18px;
    background: white;
    border: 2px solid #E4E6EF;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.btn-filter:hover {
    background: #667eea;
    border-color: #667eea;
    transform: scale(1.1);
}

.btn-filter:hover i {
    color: white;
}

/* ============= SECTION HEADERS - MODERN DESIGN ============= */
.section-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 25px;
    padding: 18px 28px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
    border-left: 5px solid;
}

.section-header-1 { border-left-color: #667eea; }
.section-header-2 { border-left-color: #f093fb; }

.section-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
}

.section-header-2 .section-icon {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    box-shadow: 0 6px 20px rgba(240, 147, 251, 0.3);
}

.section-icon i {
    font-size: 24px;
    color: white;
}

.section-title {
    flex: 1;
}

.section-title h4 {
    margin: 0;
    font-size: 18px;
    font-weight: 800;
    color: #181C32;
    letter-spacing: 0.3px;
}

.section-title p {
    margin: 0;
    font-size: 13px;
    color: #7E8299;
    font-weight: 500;
}

/* ============= CHART CONTAINERS - PREMIUM CARDS ============= */
.chart-card {
    background: white;
    border-radius: 24px;
    padding: 32px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(228, 230, 239, 0.6);
    transition: all 0.3s ease;
    height: 100%;
    position: relative;
    overflow: hidden;
}

.chart-card::after {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(135deg, #667eea, #764ba2, #f093fb, #f5576c);
    border-radius: 24px;
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: -1;
}

.chart-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
}

.chart-card:hover::after {
    opacity: 0.1;
}

/* ============= GRID LAYOUTS - RESPONSIVE & MODERN ============= */
.dashboard-grid {
    display: grid;
    gap: 24px;
    margin-bottom: 30px;
}

.grid-layout-1 {
    grid-template-columns: 1fr;
}

.grid-layout-2 {
    grid-template-columns: repeat(2, 1fr);
}

.grid-layout-3 {
    grid-template-columns: repeat(3, 1fr);
}

@media (max-width: 991px) {
    .grid-layout-2, .grid-layout-3 {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 767px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
}

/* ============= FILTER DROPDOWN ============= */
.filter-menu {
    border-radius: 20px !important;
    border: none !important;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15) !important;
    padding: 20px !important;
    z-index: 9999 !important;
    position: relative !important;
	transform: none !important;
}

.filter-menu .form-label {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #7E8299;
    margin-bottom: 8px;
}

.filter-menu .form-select,
.filter-menu .form-control {
    border-radius: 12px;
    border: 2px solid #E4E6EF;
    padding: 10px 16px;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.filter-menu .form-select:focus,
.filter-menu .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.select2-container {
    z-index: 99999 !important;
}

.select2-dropdown {
    z-index: 99999 !important;
    border-radius: 12px;
    border: 2px solid #E4E6EF;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

/* ============= ANIMATIONS ============= */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.stat-card,
.chart-card,
.section-header {
    animation: fadeInUp 0.6s ease-out backwards;
}

.stat-card:nth-child(1) { animation-delay: 0.1s; }
.stat-card:nth-child(2) { animation-delay: 0.2s; }
.stat-card:nth-child(3) { animation-delay: 0.3s; }
.stat-card:nth-child(4) { animation-delay: 0.4s; }
.stat-card:nth-child(5) { animation-delay: 0.5s; }

/* ============= SCROLLBAR STYLING ============= */
::-webkit-scrollbar {
    width: 10px;
    height: 10px;
}

::-webkit-scrollbar-track {
    background: #F1F1F4;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
}

/* ============= RESPONSIVE UTILITIES ============= */
@media (max-width: 991px) {
    .profile-header {
        padding: 28px;
    }
    
    .chart-card {
        padding: 24px;
    }
}
</style>

<!--begin::Container-->
<div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-xxl">
    <!--begin::Post-->
    <div class="content flex-row-fluid" id="kt_content">
        
        <!--begin::Profile Header-->
        <div class="profile-header">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="avatar-container">
                        <?php
                            $fullname = $this->session->userdata('fullname');
                            $this->db->select('data_gallery.path, users_data.fullname');
                            $this->db->where('users_data.id',$this->session->userdata('userid'));
                            $this->db->join('data_gallery', 'data_gallery.id = users_data.cover', 'left');
                            $queryimage = $this->db->get('users_data');
                            $queryimage = $queryimage->result_object();
                            if ($queryimage) {
                                $relative_path = $queryimage[0]->path;
                                $full_path = FCPATH . $relative_path;
                                if($relative_path){
                                    if (file_exists($full_path)) {
                                        $imagecover = base_url() . $relative_path;
                                    } else {
                                        $imagecover = base_url() . 'themes/ortyd/assets/media/avatars/blank.png';
                                    }
                                }else{
                                    $imagecover = base_url() . 'themes/ortyd/assets/media/avatars/blank.png';
                                }
                            } else {
                                $imagecover = base_url().'themes/ortyd/assets/media/avatars/blank.png';
                            }
                        ?>
                        <img src="<?php echo $imagecover; ?>" alt="Avatar" width="120" height="120" class="avatar-image" />
                        <div class="status-badge"></div>
                    </div>
                </div>
                
                <div class="col">
                    <div class="profile-name">
                        <?php echo $this->session->userdata('fullname'); ?>
                        <i class="ki-duotone ki-verify fs-1 text-primary ms-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                    
                    <div class="profile-meta">
                        <div class="profile-meta-item">
                            <i class="ki-duotone ki-profile-circle">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <?php echo $this->session->userdata('username'); ?>
                        </div>
                        <div class="profile-meta-item">
                            <i class="ki-duotone ki-profile-circle">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <?php echo $this->ortyd->select2_getname($this->session->userdata("group_id"),"users_groups","id","name"); ?>
                        </div>
                        <div class="profile-meta-item">
                            <i class="ki-duotone ki-sms">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <?php echo $this->session->userdata('email'); ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-auto">
                    <div class="d-flex gap-3 align-items-center">
                        <a href="<?php echo base_url('survei'); ?>" class="btn-create-report">
                            <i class="ki-duotone ki-plus fs-2 me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Isi Survei
                        </a>
                        
                        <button class="btn-filter" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            <i class="ki-duotone ki-setting-3 fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                            </i>
                        </button>
                        
                        <!--begin::Filter Menu-->
                        <div class="menu menu-sub menu-sub-dropdown filter-menu w-300px" data-kt-menu="true">
                            <div class="mb-4">
                                <label class="form-label">Tahun</label>
                                <select class="form-select form-select-sm" id="filter_tahun">
                                    <?php 	
                                    if(isset($_GET['tahun'])){
                                        $i = $_GET['tahun'];
                                    }else{
                                        $i = date('Y');
                                    }
                                    for($y=date('Y')+1;$y>=2023;$y--) { 
                                    ?>
                                    <option value="<?php echo $y; ?>" <?php if($y == $i){echo 'selected';}?>><?php echo $y; ?></option>
                                    <?php } ?>	
                                </select>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Provinsi</label>
                                <select class="form-select form-select-sm" id="filter_provinsi">
                                    <option value="ALL" selected>Semua Provinsi</option>
                                </select>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Kabupaten/Kota</label>
                                <select class="form-select form-select-sm" id="filter_kabkota">
                                    <option value="ALL" selected>Semua Kab/Kota</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="form-label">Kelompok</label>
                                <select class="form-select form-select-sm" id="filter_kelompok">
                                    <option value="ALL" selected>Semua Kelompok</option>
                                </select>
                            </div>
                        </div>
                        <!--end::Filter Menu-->
                    </div>
                </div>
            </div>
        </div>
        <!--end::Profile Header-->

        <!--begin::Statistics Grid-->
        <div class="stats-grid">
            <div class="stat-card stat-card-1">
                <div class="stat-icon-wrapper">
                    <i class="ki-duotone ki-chart-simple">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                    </i>
                </div>
                <div class="stat-label">Total</div>
                <div class="stat-value" id="total_survei">0</div>
                <div class="stat-description">Penerima Manfaat</div>
            </div>
            
            <div class="stat-card stat-card-2">
                <div class="stat-icon-wrapper">
                    <i class="ki-duotone ki-geolocation">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
                <div class="stat-label">Total Provinsi</div>
                <div class="stat-value" id="total_provinsi">0</div>
                <div class="stat-description">Cakupan Wilayah</div>
            </div>
            
            <div class="stat-card stat-card-3">
                <div class="stat-icon-wrapper">
                    <i class="ki-duotone ki-map">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                </div>
                <div class="stat-label">Total Kab/Kota</div>
                <div class="stat-value" id="total_kabkota">0</div>
                <div class="stat-description">Kab/Kota Terdata</div>
            </div>
            
            <div class="stat-card stat-card-4">
                <div class="stat-icon-wrapper">
                    <i class="ki-duotone ki-category">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                    </i>
                </div>
                <div class="stat-label">Total Kelompok</div>
                <div class="stat-value" id="total_kelompok">0</div>
                <div class="stat-description">Jenis Kelompok</div>
            </div>
            
            <div class="stat-card stat-card-5">
                <div class="stat-icon-wrapper">
                    <i class="ki-duotone ki-profile-user">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                    </i>
                </div>
                <div class="stat-label">Total Surveyor</div>
                <div class="stat-value" id="total_surveyor">0</div>
                <div class="stat-description">Petugas Survei</div>
            </div>
        </div>
        <!--end::Statistics Grid-->

        <!--begin::Section: Survei Timeline-->
        <div class="section-header section-header-1">
            <div class="section-icon">
                <i class="ki-duotone ki-calendar">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
            </div>
            <div class="section-title">
                <h4>Timeline Survei per Bulan</h4>
                <p>Grafik perkembangan survei berdasarkan bulan</p>
            </div>
        </div>

        <div class="dashboard-grid grid-layout-1">
            <div class="chart-card">
                <div id="chart-timeline" style="height:450px"></div>
            </div>
        </div>

        <!--begin::Section: Distribusi Wilayah-->
        <div class="section-header section-header-2">
            <div class="section-icon">
                <i class="ki-duotone ki-geolocation">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
            </div>
            <div class="section-title">
                <h4>Distribusi Wilayah</h4>
                <p>Sebaran survei berdasarkan provinsi dan kabupaten/kota</p>
            </div>
        </div>

        <div class="dashboard-grid grid-layout-2">
            <div class="chart-card">
                <div id="chart-provinsi" style="height:400px"></div>
            </div>
            
            <div class="chart-card">
                <div id="chart-kabkota" style="height:400px"></div>
            </div>
        </div>

        <!--begin::Section: Kelompok-->
        <div class="section-header section-header-1">
            <div class="section-icon">
                <i class="ki-duotone ki-abstract-26">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
            </div>
            <div class="section-title">
                <h4>Distribusi Kelompok</h4>
                <p>Sebaran survei berdasarkan kelompok sasaran</p>
            </div>
        </div>

        <div class="dashboard-grid grid-layout-1">
            <div class="chart-card">
                <div id="chart-kelompok" style="height:450px"></div>
            </div>
        </div>

        <!--begin::Google Connect Section-->
        <?php $logged_in = $this->session->userdata('google_id');
        if ( $logged_in != null && $logged_in != '') { ?>
            <div class="row g-3 my-4">
                <div class="col-12">
                    <a href="<?php echo $googlelink; ?>" class="btn btn-flex btn-outline btn-text-gray-700 btn-active-color-primary bg-state-light flex-center text-nowrap w-100">
                        <img alt="Logo" src="<?php echo base_url(); ?>themes/ortyd/assets/media/svg/brand-logos/google-icon.svg" class="h-15px me-3" />
                        Remove Google Connect
                    </a>
                </div>
            </div>
        <?php }else{ ?>
            <div class="row g-3 my-4">
                <div class="col-12">
                    <a href="<?php echo $googlelink; ?>" class="btn btn-flex btn-outline btn-text-gray-700 btn-active-color-primary bg-state-light flex-center text-nowrap w-100">
                        <img alt="Logo" src="<?php echo base_url(); ?>themes/ortyd/assets/media/svg/brand-logos/google-icon.svg" class="h-15px me-3" />
                        Connect Google Account
                    </a>
                </div>
            </div>
        <?php } ?>
        <!--end::Google Connect Section-->

    </div>
    <!--end::Post-->
</div>
<!--end::Container-->

<script>
function get_dashboard_stats(tahun, provinsi, kabkota, kelompok){
    $.post('<?php echo base_url($headurl.'/getcount'); ?>',{
        tahun : tahun, 
        provinsi : provinsi,
        kabkota : kabkota,
        kelompok : kelompok,
        <?php echo $this->security->get_csrf_token_name(); ?> : csrfHash
    }, function (data) {
        obj = JSON.parse(data);
        updateCsrfToken(obj.csrf_hash)
        if(obj.message == "success"){ 
            $('#total_survei').text(obj.data.total_semua);
            $('#total_provinsi').text(obj.data.total_provinsi);
            $('#total_kabkota').text(obj.data.total_kabkota);
            $('#total_kelompok').text(obj.data.total_kelompok);
            $('#total_surveyor').text(obj.data.total_surveyor);
        }
    })
}

async function get_dashboard_charts(tahun, provinsi, kabkota, kelompok){
    FusionCharts.ready(function() {
        
       // Chart: Timeline Survei (SYNC)
$.ajax({
    url: '<?php echo base_url($headurl.'/survei_timeline'); ?>',
    type: 'POST',
    dataType: 'json',
    async: false, // <-- PENTING: tunggu selesai dulu
    data: {
        tahun : tahun, 
        provinsi : provinsi,
        kabkota : kabkota,
        kelompok : kelompok,
        <?php echo $this->security->get_csrf_token_name(); ?> : csrfHash
    },
    success: function (obj) {

        updateCsrfToken(obj.csrf_hash);

        if(obj.message == "success"){ 
            var caption = 'Timeline Survei per Bulan';
            var subCaption = "Tahun " + tahun;
            
            var myChart = new FusionCharts({
                type: "msspline",
                renderAt: "chart-timeline",
                width: "100%",
                height: "100%",
                dataFormat: "json",
                containerBackgroundOpacity: '0',
                dataSource: {
                    chart: {
                        "chartLeftMargin": "20",
                        "chartTopMargin": "20",
                        "chartRightMargin": "0",
                        "chartBottomMargin": "5",
                        xAxisValueBgColor: '#ffffff',
                        "baseFontColor": "#52575D",
                        "baseFontSize": "11px",
                        xAxisValueBgAlpha: 0,
                        "bgColor": "#ffffff",
                        "bgAlpha": "0",
                        "toolTipBgColor": "#FFFFFF",
                        "toolTipBorderColor": "#CCCCCC",
                        "toolTipColor": "#000000",
                        "toolTipBgAlpha": "100",
                        "showToolTipShadow": "1",
                        valueFontColor : "#52575D",
                        caption: caption,
                        "captionFont": "Arial",
                        "captionFontColor": "#52575D",
                        "captionFontSize": "14",
                        "subCaption": subCaption,
                        "subCaptionFontColor": "#52575D",
                        "subCaptionFontSize": "12",
                        "alignCaptionWithCanvas": "0",
                        "captionHorizontalPadding": "10",
                        "captionOnTop": "1",
                        "captionAlignment": "left",
                        xaxisname: "",
                        yaxisname: "",
                        theme: "fusion",
                        "showValues": "1",
                        showlegend: "1",
                        legendItemFontSize:"10",
                        showpercentvalues: "0",
                        legendposition: "bottom",
                        usedataplotcolorforlabels: "1",
                        showLabels: "1",
                        animateClockwise: "1",
                        "placeValuesInside": "0",
                        "numberScaleValue": "1000,1000,1000",
                        "numberScaleUnit": " rb, jt, M",
                        "decimalSeparator": ",",
                        "thousandSeparator": ".",
                        "rotateValues": "0",
                        valueFontSize : "11",
                        showYAxisValues : 1,
                        showXAxisValues : 1
                    },
                    "categories": [{
                        "category": obj.data
                    }],
                    "dataset": obj.data5
                },
                events: {
                    dataPlotClick: function (eventObj, dataObj) {
                        drillDownAll(tahun);
                    }
                }
            }).render();
        }
    },
    error: function(xhr){
        console.log("Error load survei_timeline:", xhr.responseText);
    }
});

        
     // Chart: Survei Per Provinsi (SYNC)
$.ajax({
    url: '<?php echo base_url($headurl.'/survei_by_provinsi'); ?>',
    type: 'POST',
    dataType: 'json',
    async: false, // <-- PENTING: biar nunggu selesai dulu
    data: {
        tahun : tahun, 
        provinsi : provinsi,
        kabkota : kabkota,
        kelompok : kelompok,
        <?php echo $this->security->get_csrf_token_name(); ?> : csrfHash
    },
    success: function (obj) {

        updateCsrfToken(obj.csrf_hash);

        if(obj.message == "success"){ 
            var caption = "Survei per Provinsi";
            var subCaption = "Jumlah Survei";
            
            var myChart = new FusionCharts({
                type: "bar2d",
                renderAt: "chart-provinsi",
                width: "100%",
                height: "100%",
                dataFormat: "json",
                containerBackgroundOpacity: '0',
                dataSource: {
                    chart: {
                        numDivLines :0,
                        divLineColor:'#fff',
                        "chartLeftMargin": "20",
                        "chartTopMargin": "10",
                        "chartRightMargin": "0",
                        "chartBottomMargin": "15",
                        captionPosition: "left",
                        plottooltext: "<b>$displayValue</b> ",
                        xAxisValueBgColor: '#ffffff',
                        "baseFontColor": "#52575D",
                        "baseFontSize": "10px",
                        xAxisValueBgAlpha: 0,
                        "bgColor": "#ffffff",
                        "bgAlpha": "0",
                        "toolTipBgColor": "#FFFFFF",
                        "toolTipBorderColor": "#CCCCCC",
                        "toolTipColor": "#000000",
                        "toolTipBgAlpha": "100",
                        "showToolTipShadow": "1",
                        valueFontColor : "#FFF",
                        caption: caption,
                        "captionFont": "Arial",
                        "captionFontColor": "#52575D",
                        "captionFontSize": "14",
                        "subCaption": subCaption,
                        "subCaptionFontColor": "#52575D",
                        "subCaptionFontSize": "12",
                        "alignCaptionWithCanvas": "0",
                        "captionHorizontalPadding": "10",
                        "captionOnTop": "1",
                        "captionAlignment": "left",
                        "outCnvBaseFontSize": "10",
                        xaxisname: "",
                        yaxisname: "",
                        theme: "fusion",
                        "showValues": "1",
                        showlegend: "0",
                        "showZeroPlane": "1",
                        "showZeroPlaneValue": "1",
                        showpercentvalues: "0",
                        valueFontSize : "10",
                        legendposition: "right",
                        legenditemfontsize : "10",
                        usedataplotcolorforlabels: "1",
                        showLabels: "1",
                        animateClockwise: "1",
                        showYAxisValues : 0,
                        showXAxisValues : 1,
                        "rotateValues": "1"
                    },
                    data: obj.data
                },
                events: {
                    dataPlotClick: function (eventObj, dataObj) {
                        var linkStr = dataObj.link;
                        if(linkStr && linkStr.indexOf('drillDownProvinsi') > -1){
                            var matches = linkStr.match(/drillDownProvinsi\('([^']+)','([^']+)'\)/);
                            if(matches){
                                drillDownProvinsi(matches[1], matches[2], tahun);
                            }
                        }
                    }
                }
            }).render();
        }
    },
    error: function(xhr){
        console.log("Error load survei_by_provinsi:", xhr.responseText);
    }
});


       // Chart: Survei Per Kab/Kota (SYNC)
$.ajax({
    url: '<?php echo base_url($headurl.'/survei_by_kabkota'); ?>',
    type: 'POST',
    dataType: 'json',
    async: false, // <-- SYNC MODE
    data: {
        tahun : tahun, 
        provinsi : provinsi,
        kabkota : kabkota,
        kelompok : kelompok,
        <?php echo $this->security->get_csrf_token_name(); ?> : csrfHash
    },
    success: function (obj) {

        updateCsrfToken(obj.csrf_hash);

        if(obj.message == "success"){ 
            var caption = "Top 10 Survei per Kab/Kota";
            var subCaption = "Jumlah Survei";
            
            var myChart = new FusionCharts({
                type: "bar2d",
                renderAt: "chart-kabkota",
                width: "100%",
                height: "100%",
                dataFormat: "json",
                containerBackgroundOpacity: '0',
                dataSource: {
                    chart: {
                        numDivLines :0,
                        divLineColor:'#fff',
                        "chartLeftMargin": "20",
                        "chartTopMargin": "10",
                        "chartRightMargin": "0",
                        "chartBottomMargin": "15",
                        captionPosition: "left",
                        plottooltext: "<b>$displayValue</b> ",
                        xAxisValueBgColor: '#ffffff',
                        "baseFontColor": "#52575D",
                        "baseFontSize": "10px",
                        xAxisValueBgAlpha: 0,
                        "bgColor": "#ffffff",
                        "bgAlpha": "0",
                        "toolTipBgColor": "#FFFFFF",
                        "toolTipBorderColor": "#CCCCCC",
                        "toolTipColor": "#000000",
                        "toolTipBgAlpha": "100",
                        "showToolTipShadow": "1",
                        valueFontColor : "#000000",
                        "valueInsideColor": "#FFFFFF",
                        caption: caption,
                        "captionFont": "Arial",
                        "captionFontColor": "#52575D",
                        "captionFontSize": "14",
                        "subCaption": subCaption,
                        "subCaptionFontColor": "#52575D",
                        "subCaptionFontSize": "12",
                        "alignCaptionWithCanvas": "0",
                        "captionHorizontalPadding": "10",
                        "captionOnTop": "1",
                        "captionAlignment": "left",
                        "outCnvBaseFontSize": "10",
                        xaxisname: "",
                        yaxisname: "",
                        theme: "fusion",
                        "showValues": "1",
                        showlegend: "0",
                        "showZeroPlane": "1",
                        "showZeroPlaneValue": "1",
                        showpercentvalues: "0",
                        valueFontSize : "10",
                        legendposition: "right",
                        legenditemfontsize : "10",
                        usedataplotcolorforlabels: "1",
                        showLabels: "1",
                        animateClockwise: "1",
                        showYAxisValues : 0,
                        showXAxisValues : 1,
                        "rotateValues": "1"
                    },
                    data: obj.data
                },
                events: {
                    dataPlotClick: function (eventObj, dataObj) {
                        var linkStr = dataObj.link;
                        if(linkStr && linkStr.indexOf('drillDownKabkota') > -1){
                            var matches = linkStr.match(/drillDownKabkota\('([^']+)','([^']+)'\)/);
                            if(matches){
                                drillDownKabkota(matches[1], matches[2], tahun);
                            }
                        }
                    }
                }
            }).render();
        }
    },
    error: function(xhr){
        console.log("Error load survei_by_kabkota:", xhr.responseText);
    }
});


       // Chart: Survei Per Kelompok (COLUMN CHART)
$.ajax({
    url: '<?php echo base_url($headurl.'/survei_by_kelompok'); ?>',
    type: 'POST',
    dataType: 'json',
    async: false, // <-- INI YANG BIKIN SYNC
    data: {
        tahun : tahun, 
        provinsi : provinsi,
        kabkota : kabkota,
        kelompok : kelompok,
        <?php echo $this->security->get_csrf_token_name(); ?> : csrfHash
    },
    success: function (obj) {

        updateCsrfToken(obj.csrf_hash);

        if(obj.message == "success"){ 
            var caption = "Distribusi Survei per Kelompok";
            var subCaption = "Jumlah Survei";
            
            var myChart = new FusionCharts({
                type: "column2d",
                renderAt: "chart-kelompok",
                width: "100%",
                height: "100%",
                dataFormat: "json",
                containerBackgroundOpacity: '0',
                dataSource: {
                    chart: {
                        "chartLeftMargin": "20",
                        "chartTopMargin": "10",
                        "chartRightMargin": "0",
                        "chartBottomMargin": "5",
                        captionPosition: "left",
                        xAxisValueBgColor: '#ffffff',
                        "baseFontColor": "#52575D",
                        xAxisValueBgAlpha: 0,
                        "bgColor": "#ffffff",
                        "bgAlpha": "0",
                        "toolTipBgColor": "#FFFFFF",
                        "toolTipBorderColor": "#CCCCCC",
                        "toolTipColor": "#000000",
                        "toolTipBgAlpha": "100",
                        "showToolTipShadow": "1",
                        valueFontColor : "#52575D",
                        caption: caption,
                        "captionFont": "Arial",
                        "captionFontColor": "#52575D",
                        "captionFontSize": "14",
                        "subCaption": subCaption,
                        "subCaptionFontColor": "#52575D",
                        "subCaptionFontSize": "12",
                        labelFontColor:"#52575D",
                        smartLineColor:"#52575D",
                        "rotateValues": "1",
                        "alignCaptionWithCanvas": "0",
                        "captionHorizontalPadding": "10",
                        "captionOnTop": "1",
                        "captionAlignment": "left",
                        plottooltext: "<b>$displayValue</b> ",
                        "showValues": "1",
                        showlegend: "0",
                        showpercentvalues: "0",
                        legendposition: "bottom",
                        usedataplotcolorforlabels: "1",
                        theme: "fusion",
                        showLabels: "1",
                        animateClockwise: "1",
                        "placeValuesInside": "0",
                        "numberScaleValue": "1000,1000,1000",
                        "numberScaleUnit": " rb, jt, M",
                        "decimalSeparator": ",",
                        "thousandSeparator": ".",
                        valueFontSize : "10",
                        "labelDisplay": "rotate",
                        "slantLabels": "1"
                    },
                    data: obj.data
                },
                events: {
                    dataPlotClick: function (eventObj, dataObj) {
                        var linkStr = dataObj.link;
                        if(linkStr && linkStr.indexOf('drillDownKelompok') > -1){
                            var matches = linkStr.match(/drillDownKelompok\('([^']+)','([^']+)'\)/);
                            if(matches){
                                drillDownKelompok(matches[1], matches[2], tahun);
                            }
                        }
                    }
                }
            }).render();
        }
    },
    error: function(xhr){
        console.log("Error load survei_by_kelompok:", xhr.responseText);
    }
});

	
	})
}

var popupOpened = false;
// FUNGSI DRILL DOWN
// FUNGSI DRILL DOWN - POPUP DATATABLES

function drillDownAll(tahun) {
	
	if(popupOpened) return;   // ⛔ cegah dobel
    popupOpened = true;
	
    if(!tahun) tahun = <?php echo isset($_GET['tahun']) ? $_GET['tahun'] : date('Y'); ?>;
    
    // Container untuk DataTables
    var container = $('<div/>');
    container.html('<table class="table table-striped table-bordered" id="table-detail-survei" style="width:100%">' +
        '<thead>' +
        '<tr>' +
        '<th>No</th>' +
        '<th>Nama</th>' +
        '<th>Email</th>' +
        '<th>Telepon</th>' +
        '<th>Wilayah</th>' +
        '<th>Kelompok</th>' +
		'<th>Nama Unit</th>' +
        '<th>Tanggal</th>' +
		'<th>Pria</th>' +
		'<th>Wanita</th>' +
		'<th>Semua</th>' +
        '<th>Status</th>' +
        '</tr>' +
        '</thead>' +
        '</table>');
    
    var box = bootbox.dialog({
        size: "xl",
        title: '<i class="ki-duotone ki-map fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i> Detail Survei - ' + tahun,
        message: container,
        buttons: {
            close: {
                label: '<i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i> Tutup',
                className: 'btn-secondary',
                callback: function() {
					 popupOpened = false;
                    if($.fn.DataTable.isDataTable('#table-detail-survei')) {
                        $('#table-detail-survei').DataTable().destroy();
                    }
                }
            }
        }
    });
	
	box.on('hidden.bs.modal', function () {
		popupOpened = false;
		if($.fn.DataTable.isDataTable('#table-detail-survei')) {
			$('#table-detail-survei').DataTable().destroy();
		}
	});
    
    box.on('shown.bs.modal', function() {
        $('#table-detail-survei').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?php echo base_url('dashboard/getColumnDetail'); ?>',
                type: 'POST',
                data: function(d) {
                    d.tahun = tahun;
                    d.<?php echo $this->security->get_csrf_token_name(); ?> = csrfHash;
                },
                dataSrc: function(json) {
                    if(json.csrf_hash) {
                        updateCsrfToken(json.csrf_hash);
                    }
                    return json.data;
                }
            },
            columns: [
                { data: 'no', orderable: false },
                { data: 'survei_pm_nama' },
                { data: 'survei_pm_email' },
                { data: 'survei_pm_tlp' },
                { data: 'wilayah' },
                { data: 'kelompok' },
				{ data: 'nama_unit' },
                { data: 'tanggal' },
				{ data: 'total_pria' },
				{ data: 'total_wanita' },
				{ data: 'total_semua' },
                { data: 'status', orderable: false }
            ],
            language: {
                processing: 'Memuat data...',
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                infoFiltered: '(filter dari _MAX_ total data)',
                zeroRecords: 'Tidak ada data yang cocok',
                emptyTable: 'Tidak ada data tersedia',
                paginate: {
                    first: '<<',
                    last: '>>',
                    next: '>',
                    previous: '<'
                }
            },
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [[6, 'desc']]
        });
    });
}


function drillDownProvinsi(prov_code, prov_name, tahun) {
	
	if(popupOpened) return;   // ⛔ cegah dobel
    popupOpened = true;
	
    if(!tahun) tahun = <?php echo isset($_GET['tahun']) ? $_GET['tahun'] : date('Y'); ?>;
    
    // Container untuk DataTables
    var container = $('<div/>');
    container.html('<table class="table table-striped table-bordered" id="table-detail-survei" style="width:100%">' +
        '<thead>' +
        '<tr>' +
        '<th>No</th>' +
        '<th>Nama</th>' +
        '<th>Email</th>' +
        '<th>Telepon</th>' +
        '<th>Wilayah</th>' +
        '<th>Kelompok</th>' +
		'<th>Nama Unit</th>' +
        '<th>Tanggal</th>' +
		'<th>Pria</th>' +
		'<th>Wanita</th>' +
		'<th>Semua</th>' +
        '<th>Status</th>' +
        '</tr>' +
        '</thead>' +
        '</table>');
    
    var box = bootbox.dialog({
        size: "xl",
        title: '<i class="ki-duotone ki-map fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i> Detail Survei - ' + prov_name,
        message: container,
        buttons: {
            close: {
                label: '<i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i> Tutup',
                className: 'btn-secondary',
                callback: function() {
					 popupOpened = false;
                    if($.fn.DataTable.isDataTable('#table-detail-survei')) {
                        $('#table-detail-survei').DataTable().destroy();
                    }
                }
            }
        }
    });
	
	box.on('hidden.bs.modal', function () {
		popupOpened = false;
		if($.fn.DataTable.isDataTable('#table-detail-survei')) {
			$('#table-detail-survei').DataTable().destroy();
		}
	});
    
    box.on('shown.bs.modal', function() {
        $('#table-detail-survei').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?php echo base_url('dashboard/getColumnDetail'); ?>',
                type: 'POST',
                data: function(d) {
                    d.provinsi_code = prov_code;
                    d.tahun = tahun;
                    d.<?php echo $this->security->get_csrf_token_name(); ?> = csrfHash;
                },
                dataSrc: function(json) {
                    if(json.csrf_hash) {
                        updateCsrfToken(json.csrf_hash);
                    }
                    return json.data;
                }
            },
            columns: [
                { data: 'no', orderable: false },
                { data: 'survei_pm_nama' },
                { data: 'survei_pm_email' },
                { data: 'survei_pm_tlp' },
                { data: 'wilayah' },
                { data: 'kelompok' },
				{ data: 'nama_unit' },
                { data: 'tanggal' },
				{ data: 'total_pria' },
				{ data: 'total_wanita' },
				{ data: 'total_semua' },
                { data: 'status', orderable: false }
            ],
            language: {
                processing: 'Memuat data...',
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                infoFiltered: '(filter dari _MAX_ total data)',
                zeroRecords: 'Tidak ada data yang cocok',
                emptyTable: 'Tidak ada data tersedia',
                paginate: {
                    first: '<<',
                    last: '>>',
                    next: '>',
                    previous: '<'
                }
            },
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [[6, 'desc']]
        });
    });
}

function drillDownKabkota(kabkota_code, kabkota_name, tahun) {
	
	if(popupOpened) return;   // ⛔ cegah dobel
    popupOpened = true;
	
    if(!tahun) tahun = <?php echo isset($_GET['tahun']) ? $_GET['tahun'] : date('Y'); ?>;
    
    var container = $('<div/>');
    container.html('<table class="table table-striped table-bordered" id="table-detail-survei" style="width:100%">' +
        '<thead>' +
        '<tr>' +
        '<th>No</th>' +
        '<th>Nama</th>' +
        '<th>Email</th>' +
        '<th>Telepon</th>' +
        '<th>Wilayah</th>' +
        '<th>Kelompok</th>' +
		'<th>Nama Unit</th>' +
        '<th>Tanggal</th>' +
		'<th>Pria</th>' +
		'<th>Wanita</th>' +
		'<th>Semua</th>' +
        '<th>Status</th>' +
        '</tr>' +
        '</thead>' +
        '</table>');
    
    var box = bootbox.dialog({
        size: "xl",
        title: '<i class="ki-duotone ki-geolocation fs-2"><span class="path1"></span><span class="path2"></span></i> Detail Survei - ' + kabkota_name,
        message: container,
        buttons: {
            close: {
                label: '<i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i> Tutup',
                className: 'btn-secondary',
                callback: function() {
					 popupOpened = false;
					 
                    if($.fn.DataTable.isDataTable('#table-detail-survei')) {
                        $('#table-detail-survei').DataTable().destroy();
                    }
                }
            }
        }
    });
	
	box.on('hidden.bs.modal', function () {
		popupOpened = false;
		if($.fn.DataTable.isDataTable('#table-detail-survei')) {
			$('#table-detail-survei').DataTable().destroy();
		}
	});
    
    box.on('shown.bs.modal', function() {
        $('#table-detail-survei').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?php echo base_url('dashboard/getColumnDetail'); ?>',
                type: 'POST',
                data: function(d) {
                    d.kabkota_code = kabkota_code;
                    d.tahun = tahun;
                    d.<?php echo $this->security->get_csrf_token_name(); ?> = csrfHash;
                },
                dataSrc: function(json) {
                    if(json.csrf_hash) {
                        updateCsrfToken(json.csrf_hash);
                    }
                    return json.data;
                }
            },
            columns: [
                { data: 'no', orderable: false },
                { data: 'survei_pm_nama' },
                { data: 'survei_pm_email' },
                { data: 'survei_pm_tlp' },
                { data: 'wilayah' },
                { data: 'kelompok' },
				{ data: 'nama_unit' },
                { data: 'tanggal' },
				{ data: 'total_pria' },
				{ data: 'total_wanita' },
				{ data: 'total_semua' },
                { data: 'status', orderable: false }
            ],
            language: {
                processing: 'Memuat data...',
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                infoFiltered: '(filter dari _MAX_ total data)',
                zeroRecords: 'Tidak ada data yang cocok',
                emptyTable: 'Tidak ada data tersedia',
                paginate: {
                    first: '<<',
                    last: '>>',
                    next: '>',
                    previous: '<'
                }
            },
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [[6, 'desc']]
        });
    });
}

function drillDownKelompok(kelompok_id, kelompok_name, tahun) {
	
	if(popupOpened) return;   // ⛔ cegah dobel
    popupOpened = true;
	
    if(!tahun) tahun = <?php echo isset($_GET['tahun']) ? $_GET['tahun'] : date('Y'); ?>;
    
    var container = $('<div/>');
    container.html('<table class="table table-striped table-bordered" id="table-detail-survei" style="width:100%">' +
        '<thead>' +
        '<tr>' +
        '<th>No</th>' +
        '<th>Nama</th>' +
        '<th>Email</th>' +
        '<th>Telepon</th>' +
        '<th>Wilayah</th>' +
        '<th>Kelompok</th>' +
		'<th>Nama Unit</th>' +
        '<th>Tanggal</th>' +
		'<th>Pria</th>' +
		'<th>Wanita</th>' +
		'<th>Semua</th>' +
        '<th>Status</th>' +
        '</tr>' +
        '</thead>' +
        '</table>');
    
    var box = bootbox.dialog({
        size: "xl",
        title: '<i class="ki-duotone ki-people fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i> Detail Survei - ' + kelompok_name,
        message: container,
        buttons: {
            close: {
                label: '<i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i> Tutup',
                className: 'btn-secondary',
                callback: function() {
					popupOpened = false;
                    if($.fn.DataTable.isDataTable('#table-detail-survei')) {
                        $('#table-detail-survei').DataTable().destroy();
                    }
                }
            }
        }
    });
	
	box.on('hidden.bs.modal', function () {
		popupOpened = false;
		if($.fn.DataTable.isDataTable('#table-detail-survei')) {
			$('#table-detail-survei').DataTable().destroy();
		}
	});
    
    box.on('shown.bs.modal', function() {
        $('#table-detail-survei').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?php echo base_url('dashboard/getColumnDetail'); ?>',
                type: 'POST',
                data: function(d) {
                    d.kelompok_id = kelompok_id;
                    d.tahun = tahun;
                    d.<?php echo $this->security->get_csrf_token_name(); ?> = csrfHash;
                },
                dataSrc: function(json) {
                    if(json.csrf_hash) {
                        updateCsrfToken(json.csrf_hash);
                    }
                    return json.data;
                }
            },
            columns: [
                { data: 'no', orderable: false },
                { data: 'survei_pm_nama' },
                { data: 'survei_pm_email' },
                { data: 'survei_pm_tlp' },
                { data: 'wilayah' },
                { data: 'kelompok' },
				{ data: 'nama_unit' },
                { data: 'tanggal' },
				{ data: 'total_pria' },
				{ data: 'total_wanita' },
				{ data: 'total_semua' },
                { data: 'status', orderable: false }
            ],
            language: {
                processing: 'Memuat data...',
                search: 'Cari:',
                lengthMenu: 'Tampilkan _MENU_ data',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                infoFiltered: '(filter dari _MAX_ total data)',
                zeroRecords: 'Tidak ada data yang cocok',
                emptyTable: 'Tidak ada data tersedia',
                paginate: {
                    first: '<<',
                    last: '>>',
                    next: '>',
                    previous: '<'
                }
            },
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [[6, 'desc']]
        });
    });

}

$(document).ready(function() { 
    var tahun = <?php echo isset($_GET['tahun']) ? $_GET['tahun'] : date('Y'); ?>;
    var provinsi = 'ALL';
    var kabkota = 'ALL';
    var kelompok = 'ALL';
    
    // Initialize Select2 for Provinsi
    $("#filter_provinsi").select2({
        width: '100%',
        placeholder: 'Pilih Provinsi',
        ajax: {
            type: "POST",
            url: "<?php echo base_url(); ?>dashboard/select2",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term,
                    table: 'm_set_wil_administratif',
                    id:'SUBSTR(wil_prov_kode, 1, 2)',
                    name:'wil_prov_nama',
                    reference: '1',
                    reference_id: 'wil_level',
                    page: params.page, 
                    <?php echo $this->security->get_csrf_token_name(); ?> : csrfHash
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                updateCsrfToken(data.csrf_hash)
                return {
                    results: $.map(data.items, function (item) {
                        return {
                            id: item.id,
                            text: item.name
                        }
                    }),
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true
        }
    });
    
    // Initialize Select2 for Kabupaten/Kota
    $("#filter_kabkota").select2({
        width: '100%',
        placeholder: 'Pilih Kab/Kota',
        ajax: {
            type: "POST",
            url: "<?php echo base_url(); ?>dashboard/select2",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term,
                    table: 'm_set_wil_administratif',
                    id:'wil_kab_kode',
                    name:'wil_kab_nama',
                    reference: '2',
                    reference_id: 'wil_level',
                    page: params.page, 
                    <?php echo $this->security->get_csrf_token_name(); ?> : csrfHash
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                updateCsrfToken(data.csrf_hash)
                return {
                    results: $.map(data.items, function (item) {
                        return {
                            id: item.id,
                            text: item.name
                        }
                    }),
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true
        }
    });
    
    // Initialize Select2 for Kelompok
    $("#filter_kelompok").select2({
        width: '100%',
        placeholder: 'Pilih Kelompok',
        ajax: {
            type: "POST",
            url: "<?php echo base_url(); ?>dashboard/select2",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term,
                    table: 'master_kelompok',
                    id:'id',
                    name:'nama_kelompok',
                    page: params.page, 
                    <?php echo $this->security->get_csrf_token_name(); ?> : csrfHash
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                updateCsrfToken(data.csrf_hash)
                return {
                    results: $.map(data.items, function (item) {
                        return {
                            id: item.id,
                            text: item.name
                        }
                    }),
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true
        }
    });
    
    // Event listeners for filters
    $("#filter_tahun").on("change", function() { 
        tahun = $("#filter_tahun").val();
        provinsi = $("#filter_provinsi").val() || 'ALL';
        kabkota = $("#filter_kabkota").val() || 'ALL';
        kelompok = $("#filter_kelompok").val() || 'ALL';
        get_dashboard_stats(tahun, provinsi, kabkota, kelompok);
        get_dashboard_charts(tahun, provinsi, kabkota, kelompok);
    });
    
    $("#filter_provinsi").on("select2:select select2:unselect", function(e) { 
        tahun = $("#filter_tahun").val();
        provinsi = $("#filter_provinsi").val() || 'ALL';
        kabkota = $("#filter_kabkota").val() || 'ALL';
        kelompok = $("#filter_kelompok").val() || 'ALL';
        get_dashboard_stats(tahun, provinsi, kabkota, kelompok);
        get_dashboard_charts(tahun, provinsi, kabkota, kelompok);
    });
    
    $("#filter_kabkota").on("select2:select select2:unselect", function(e) { 
        tahun = $("#filter_tahun").val();
        provinsi = $("#filter_provinsi").val() || 'ALL';
        kabkota = $("#filter_kabkota").val() || 'ALL';
        kelompok = $("#filter_kelompok").val() || 'ALL';
        get_dashboard_stats(tahun, provinsi, kabkota, kelompok);
        get_dashboard_charts(tahun, provinsi, kabkota, kelompok);
    });
    
    $("#filter_kelompok").on("select2:select select2:unselect", function(e) { 
        tahun = $("#filter_tahun").val();
        provinsi = $("#filter_provinsi").val() || 'ALL';
        kabkota = $("#filter_kabkota").val() || 'ALL';
        kelompok = $("#filter_kelompok").val() || 'ALL';
        get_dashboard_stats(tahun, provinsi, kabkota, kelompok);
        get_dashboard_charts(tahun, provinsi, kabkota, kelompok);
    });
    
    // Initialize dashboard
    get_dashboard_stats(tahun, 'ALL', 'ALL', 'ALL');
    get_dashboard_charts(tahun, 'ALL', 'ALL', 'ALL');
});
</script>