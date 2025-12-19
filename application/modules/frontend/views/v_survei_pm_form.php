<?php
    $exclude = $exclude;
    $exclude_detail = $exclude_detail;
    
    $query_column = $this->ortyd->getviewlistform($module, $exclude, 2);
    $query_column_detail = $this->ortyd->getviewlistform($module_detail, $exclude_detail, 2);
    
    if($query_column && $query_column_detail){
        // Initialize master variables
        foreach($query_column as $rows_column){
            ${$rows_column['name']} = null;
        }
        
        // Initialize detail variables
        foreach($query_column_detail as $rows_column){
            ${$rows_column['name']} = 0;
        }
        
        $is_edit_mode = false;
        $disable_identity_fields = false; // Default: field bisa diisi
        
        // Auto-fill dari data user yang login (jika sudah login)
        if(isset($is_logged_in) && $is_logged_in === true && isset($user_data) && $user_data != null){
            $survei_pm_nama = $user_data->fullname ?? '';
            $survei_pm_email = $user_data->email ?? '';
            $survei_pm_tlp = $user_data->notelp ?? '';
            
            // Coba ambil NIP dari database jika ada
            if(property_exists($user_data, 'nip')){
                $survei_pm_nip = $user_data->nip ?? '';
            }
        }
        
        if(isset($id) && $id != null && $id != '0'){
            $iddata = $id;
            $typedata = 'Edit';
            $is_edit_mode = true;
            $disable_identity_fields = true; // DISABLE field identitas di mode edit
            
            // Load master data
            if(isset($datarow) && $datarow != null){
                foreach($query_column as $rows_column){
                    foreach ($datarow as $rows) {
                        ${$rows_column['name']} = $rows->{$rows_column['name']};
                    }
                }
            }
            
            // Load detail data
            if(isset($datarow_detail) && $datarow_detail != null){
                foreach($query_column_detail as $rows_column){
                    foreach ($datarow_detail as $rows) {
                        ${$rows_column['name']} = $rows->{$rows_column['name']};
                    }
                }
            }
        }else{
            $iddata = 0;
            $typedata = 'Buat';
            $disable_identity_fields = false; // Field identitas bisa diisi di mode buat baru
        }
    }else{
        $newURL = base_url('frontend');
        header('Location: '.$newURL);
    }
?>

<section class="section border-0 my-0 py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                
                <!-- Header Title -->
                <div class="appear-animation" data-appear-animation="fadeInUp" data-appear-animation-delay="0">
                    <h2 class="mb-0 font-weight-bold text-center"><?php echo $title; ?></h2>
                    <div class="divider divider-primary divider-small mt-3 mb-4 mx-auto" style="max-width: 200px;">
                        <hr class="my-0">
                    </div>
                </div>
                
                <!-- Info Box -->
                <?php if (!$is_logged_in): ?>
                <!-- BELUM LOGIN - Tampilkan peringatan -->
                <div class="appear-animation" data-appear-animation="fadeInUp" data-appear-animation-delay="200">
                    <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
                        <h5 class="alert-heading mb-2">
                            <i class="fas fa-exclamation-triangle"></i> 
                            <strong>Login Diperlukan</strong>
                        </h5>
                        <p class="mb-3">Untuk mengisi form survei ini, Anda harus login terlebih dahulu menggunakan akun Google.</p>
                        <ul class="mb-3 ps-4">
                            <li>Login dengan Google untuk <strong>kemudahan dan keamanan</strong></li>
                            <li>Data Anda akan <strong>otomatis terisi</strong> dari profil Google</li>
                            <li>Anda dapat <strong>mengedit data</strong> Anda kapan saja</li>
                            <li><strong>Email tidak dapat diubah</strong> karena digunakan sebagai identitas</li>
                        </ul>
                        
                        <a href="<?php echo $googlelink; ?>" 
                           class="btn btn-lg btn-light btn-flex btn-color-gray-700 btn-active-color-primary bg-state-light flex-center text-nowrap w-100">
                            <img alt="Google Logo" 
                                 src="<?php echo base_url(); ?>themes/ortyd/assets/media/svg/brand-logos/google-icon.svg" 
                                 class="h-20px me-3" />
                            <span class="fw-bold">Login dengan Google untuk Melanjutkan</span>
                        </a>
                        
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
                
                <?php elseif ($is_logged_in && !$is_edit_mode): ?>
                <!-- SUDAH LOGIN - Form baru -->
                <div class="appear-animation" data-appear-animation="fadeInUp" data-appear-animation-delay="200">
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        <h5 class="alert-heading mb-2">
                            <i class="fas fa-check-circle"></i> 
                            <strong>Selamat Datang, <?php echo $user_data->fullname; ?>!</strong>
                        </h5>
                        <ul class="mb-0 ps-4">
                            <li>Anda sudah login dengan email: <strong><?php echo $user_data->email; ?></strong></li>
                            <li>Beberapa data telah <strong>terisi otomatis</strong> dari profil Anda</li>
                            <li>Silakan <strong>lengkapi data yang masih kosong</strong></li>
                            <li>Email <strong>tidak dapat diubah</strong> karena digunakan sebagai identitas</li>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
                
                <?php else: ?>
                <!-- SUDAH LOGIN - Mode Edit -->
                <div class="appear-animation" data-appear-animation="fadeInUp" data-appear-animation-delay="200">
                    <div class="alert alert-warning mb-4" role="alert">
                        <h5 class="alert-heading mb-2">
                            <i class="fas fa-edit"></i> 
                            <strong>Mode Edit Data</strong>
                        </h5>
                        <p class="mb-2">Anda sedang mengedit data survei yang sudah tersimpan.</p>
                        <div class="alert alert-info mt-3 mb-0">
                            <i class="fas fa-lock"></i> 
                            <strong>PENTING:</strong> Data identitas (Nama, NIP, Email, No. Telp, Kecamatan) <strong>tidak dapat diubah</strong>. 
                            Anda hanya dapat mengubah <strong>jumlah penerima manfaat</strong>.
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Form -->
                <form class="contact-form" id="formSurvei<?php echo $iddata; ?>" action="<?php echo $action; ?>" method="POST">
                    
                    <?php if (!$is_logged_in): ?>
                    <!-- OVERLAY untuk disable form jika belum login -->
                    <div style="position: relative;">
                        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; 
                                    background: rgba(255,255,255,0.8); z-index: 999; 
                                    display: flex; align-items: center; justify-content: center;
                                    backdrop-filter: blur(3px);">
                            <div class="text-center p-5">
                                <i class="fas fa-lock fa-4x text-warning mb-3"></i>
                                <h4 class="text-dark mb-3">Form Terkunci</h4>
                                <p class="text-muted mb-4">Silakan login dengan Google terlebih dahulu</p>
                                <a href="<?php echo $googlelink; ?>" 
                                   class="btn btn-primary btn-lg">
                                    <img alt="Google" 
                                         src="<?php echo base_url(); ?>themes/ortyd/assets/media/svg/brand-logos/google-icon.svg" 
                                         class="h-20px me-2" />
                                    Login dengan Google
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Section 1: Data Identitas -->
                    <div class="appear-animation" data-appear-animation="fadeInUp" data-appear-animation-delay="400">
                        <div class="card border-0 shadow-sm mb-4 <?php echo !$is_logged_in ? 'opacity-50' : ''; ?>">
                            <div class="card-header <?php echo $disable_identity_fields ? 'bg-secondary' : 'bg-primary'; ?> text-white">
                                <h4 class="mb-0 font-weight-bold text-white">
                                    <i class="fas fa-user"></i> Data Identitas Petugas Survei
                                </h4>
                                <?php if (!$is_logged_in): ?>
                                <p class="mb-0 mt-2 small text-white">
                                    <i class="fas fa-info-circle"></i> Data akan terisi otomatis setelah login
                                </p>
                                <?php elseif ($disable_identity_fields): ?>
                                <p class="mb-0 mt-2 small text-white">
                                    <i class="fas fa-lock"></i> Data identitas tidak dapat diubah (sudah terdaftar)
                                </p>
                                <?php else: ?>
                                <p class="mb-0 mt-2 small text-white">
                                    <i class="fas fa-check-circle"></i> Data terisi dari profil Google Anda
                                </p>
                                <?php endif; ?>
                            </div>
                            <div class="card-body p-4">
                                
                                <?php if ($is_logged_in): ?>
                                <div class="alert <?php echo $disable_identity_fields ? 'alert-warning' : 'alert-info'; ?> mb-4">
                                    <i class="fas fa-info-circle"></i> 
                                    <strong>Catatan:</strong> 
                                    <?php if ($disable_identity_fields): ?>
                                        Data identitas <strong>TERKUNCI</strong> dan tidak dapat diubah. Hanya jumlah penerima manfaat yang dapat diupdate.
                                    <?php else: ?>
                                        Data dengan latar abu-abu tidak dapat diubah karena digunakan sebagai identitas.
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                                
                                <div class="row pb-2 mb-1">
                                    <?php
                                    if($query_column){
                                        foreach($query_column as $rows_column){ 
                                            $viewtable = $module;
                                            $width_column = $this->ortyd->width_column($viewtable, $rows_column['name']);
                                            $tipe_data = $this->ortyd->getTipeData($viewtable, $rows_column['name']);
                                            $label_name = $this->ortyd->translate_column($viewtable, $rows_column['name']);
                                            $label_name_text = $label_name;
                                            
                                            $is_required = ($rows_column['is_nullable'] == 'NO') ? 'required' : '';
                                            $required_mark = ($rows_column['is_nullable'] == 'NO') ? '<span class="text-danger">*</span>' : '';
                                            
                                            // Disable field jika:
                                            // 1. User belum login -> disabled (tidak kirim data)
                                            // 2. Mode edit -> readonly (tetap kirim data tapi tidak bisa diubah)
                                            $disabled_attr = !$is_logged_in ? 'disabled' : '';
                                            $readonly_attr = $disable_identity_fields ? 'readonly' : '';
                                            $readonly_style = $disable_identity_fields ? 'style="background-color: #e9ecef; cursor: not-allowed;"' : '';
                                    ?>
                                    
                                    <?php if($tipe_data == 'TEXTAREA'){ ?>
                                        <div class="form-group col-lg-<?php echo $width_column; ?>">
                                            <label><?php echo $label_name; ?> <?php echo $required_mark; ?></label>
                                            <textarea rows="3" 
                                                      name="<?php echo $rows_column['name']; ?>" 
                                                      id="<?php echo $rows_column['name']; ?>" 
                                                      class="form-control text-3 h-auto py-2" 
                                                      placeholder="<?php echo 'Masukkan '.$label_name_text; ?>" 
                                                      data-msg-required="<?php echo $label_name_text; ?> wajib diisi."
                                                      <?php echo $disabled_attr; ?>
                                                      <?php echo $readonly_attr; ?>
                                                      <?php echo $readonly_style; ?>
                                                      <?php echo $is_required; ?>><?php echo ${$rows_column['name']}; ?></textarea>
                                        </div>
                                    
                                    <?php }elseif($tipe_data == 'DATE' || $rows_column['name'] == 'date' || $rows_column['name'] == 'tanggal'){ ?>
                                        <div class="form-group col-lg-<?php echo $width_column; ?>">
                                            <label><?php echo $label_name; ?> <?php echo $required_mark; ?></label>
                                            <input type="text" 
                                                   name="<?php echo $rows_column['name']; ?>" 
                                                   id="<?php echo $rows_column['name']; ?>" 
                                                   class="form-control text-3 h-auto py-2 datetime-picker" 
                                                   placeholder="<?php echo 'Pilih '.$label_name_text; ?>" 
                                                   value="<?php echo ${$rows_column['name']}; ?>"
                                                   data-msg-required="<?php echo $label_name_text; ?> wajib diisi."
                                                   readonly="true"
                                                   autocomplete="off"
                                                   <?php echo $disabled_attr; ?>
                                                   <?php echo $is_required; ?> />
                                        </div>
                                    
                                    <?php }elseif($rows_column['name'] == 'survei_pm_email'){ ?>
                                        <div class="form-group col-lg-<?php echo $width_column; ?>">
                                            <label><?php echo $label_name; ?> <?php echo $required_mark; ?></label>
                                            <input type="email" 
                                                   name="<?php echo $rows_column['name']; ?>" 
                                                   id="<?php echo $rows_column['name']; ?>" 
                                                   class="form-control text-3 h-auto py-2" 
                                                   placeholder="contoh@email.com" 
                                                   value="<?php echo ${$rows_column['name']}; ?>"
                                                   data-msg-required="Email wajib diisi."
                                                   data-msg-email="Masukkan alamat email yang valid."
                                                   maxlength="255"
                                                   autocomplete="email"
                                                   readonly 
                                                   style="background-color: #e9ecef; cursor: not-allowed;"
                                                   required />
                                            
                                            <small class="form-text text-muted">
                                                <i class="fas fa-lock"></i> Email tidak dapat diubah (digunakan sebagai identitas)
                                            </small>
                                        </div>
                                    
                                    <?php }elseif($tipe_data == 'NUMBER'){ ?>
                                        <div class="form-group col-lg-<?php echo $width_column; ?>">
                                            <label><?php echo $label_name; ?> <?php echo $required_mark; ?></label>
                                            <input type="number" 
                                                   name="<?php echo $rows_column['name']; ?>" 
                                                   id="<?php echo $rows_column['name']; ?>" 
                                                   class="form-control text-3 h-auto py-2" 
                                                   placeholder="<?php echo 'Masukkan '.$label_name_text; ?>" 
                                                   value="<?php echo ${$rows_column['name']}; ?>"
                                                   data-msg-required="<?php echo $label_name_text; ?> wajib diisi."
                                                   <?php echo $disabled_attr; ?>
                                                   <?php echo $readonly_attr; ?>
                                                   <?php echo $readonly_style; ?>
                                                   <?php echo $is_required; ?> />
                                        </div>
                                    
                                    <?php }elseif($tipe_data == 'SELECT'){ ?>
                                        <div class="form-group col-lg-<?php echo $width_column; ?>">
                                            <label><?php echo $label_name; ?> <?php echo $required_mark; ?></label>
                                            <?php 
                                                // Untuk select, gunakan disabled karena readonly tidak work di select
                                                // Tapi tambahkan hidden input untuk kirim value
                                                $readonlyselect = '';  // Inisialisasi variable
                                                $disable = '';
                                                
                                                if ($disable_identity_fields) {
                                                    $disable = 'disabled';
                                                } else if (!$is_logged_in) {
                                                    $disable = 'disabled';
                                                }
                                                
                                                $linkcustom = 'select2';
                                                if($rows_column['name'] == 'survei_pm_wil_id'){
                                                    $linkcustom = 'select2_kecamatan';
                                                }
                                                
                                                include(APPPATH."views/common/select2formsidefront.php"); 
                                            ?>
                                            <?php if ($disable_identity_fields): ?>
                                            <!-- Hidden input untuk kirim value select yang disabled -->
                                            <input type="hidden" 
                                                   name="<?php echo $rows_column['name']; ?>" 
                                                   value="<?php echo ${$rows_column['name']}; ?>" />
                                            <small class="form-text text-muted">
                                                <i class="fas fa-lock"></i> Field ini tidak dapat diubah
                                            </small>
                                            <?php endif; ?>
                                        </div>
                                    
                                    <?php }else{ ?>
                                        <div class="form-group col-lg-<?php echo $width_column; ?>">
                                            <label><?php echo $label_name; ?> <?php echo $required_mark; ?></label>
                                            <input type="text" 
                                                   name="<?php echo $rows_column['name']; ?>" 
                                                   id="<?php echo $rows_column['name']; ?>" 
                                                   class="form-control text-3 h-auto py-2" 
                                                   placeholder="<?php echo 'Masukkan '.$label_name_text; ?>" 
                                                   value="<?php echo ${$rows_column['name']}; ?>"
                                                   data-msg-required="<?php echo $label_name_text; ?> wajib diisi."
                                                   maxlength="255"
                                                   <?php echo $disabled_attr; ?>
                                                   <?php echo $readonly_attr; ?>
                                                   <?php echo $readonly_style; ?>
                                                   <?php echo $is_required; ?> />
                                            <?php if ($disable_identity_fields): ?>
                                            <small class="form-text text-muted">
                                                <i class="fas fa-lock"></i> Field ini tidak dapat diubah
                                            </small>
                                            <?php endif; ?>
                                        </div>
                                    <?php } ?>
                                    
                                    <?php 
                                        }
                                    } 
                                    ?>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    
                   <!-- Section 2: Data Detail POK -->
<div class="appear-animation" data-appear-animation="fadeInUp" data-appear-animation-delay="600">
    <div class="card border-0 shadow-sm mb-4 <?php echo !$is_logged_in ? 'opacity-50' : ''; ?>">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0 font-weight-bold text-white">
                <i class="fas fa-list-ol"></i> Detail Jumlah Penerima Manfaat MBG per Jenis
            </h4>
            <?php if ($is_edit_mode): ?>
            <p class="mb-0 mt-2 small text-white">
                <i class="fas fa-edit"></i> Anda dapat mengubah jumlah penerima manfaat di bawah ini
            </p>
            <?php endif; ?>
        </div>
        <div class="card-body p-4">
            
            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle"></i> Masukkan jumlah penerima manfaat untuk setiap kategori. Isi dengan <strong>0</strong> jika tidak ada.
            </div>
            
            <div class="row">
                <?php
                if($query_column_detail){
                    foreach($query_column_detail as $rows_column){ 
                        $viewtable = $module_detail;
                        $width_column = 4;
                        $tipe_data = $this->ortyd->getTipeData($viewtable, $rows_column['name']);
                        $label_name = $this->ortyd->translate_column($viewtable, $rows_column['name']);
                        $label_name_text = $label_name;
                        
                        // Detail POK TIDAK DI-DISABLE - tetap bisa diubah di mode edit
                        $disabled_attr = !$is_logged_in ? 'disabled' : '';
                ?>
                
                <div class="form-group col-lg-<?php echo $width_column; ?> ps-3 pe-3">
                    <label><?php echo $label_name; ?></label>
                    <input type="text" 
                           name="<?php echo $rows_column['name']; ?>" 
                           id="<?php echo $rows_column['name']; ?>" 
                           class="form-control text-3 h-auto py-2 pok-input" 
                           placeholder="0" 
                           value="<?php echo ${$rows_column['name']}; ?>"
                           data-label="<?php echo $label_name_text; ?>"
                           inputmode="numeric"
                           pattern="[0-9]*"
                           autocomplete="off"
                           <?php echo $disabled_attr; ?> />
                    <small class="form-text text-muted">Jumlah: <span class="pok-value font-weight-semibold text-primary"><?php echo ${$rows_column['name']}; ?></span> orang</small>
                </div>
                
                <?php 
                    }
                } 
                ?>
            </div>
            
            <!-- Total Penerima -->
            <div class="row mt-3">
                <div class="col-lg-12">
                    <div class="alert alert-success mb-0">
                        <h5 class="mb-0 font-weight-bold">
                            <i class="fas fa-calculator"></i> Total Penerima Manfaat: 
                            <span id="totalPenerima" class="text-dark">0</span> Orang
                        </h5>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<!-- CSRF Token -->
<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" class="csrf_token" />

<!-- Section 3: Captcha & Submit -->
<div class="appear-animation" data-appear-animation="fadeInUp" data-appear-animation-delay="800">
    
    <!-- Captcha -->
    <div class="row pb-2 mb-1">
        <div class="form-group col">
            <label>Verifikasi Keamanan <span class="text-danger">*</span></label>
            <!-- Cloudflare Turnstile - Will be rendered explicitly by JavaScript -->
            <div class="cf-turnstile"></div>
        </div>
    </div>
    
    <!-- Submit Button -->
    <div class="row">
        <div class="form-group col">
            <?php if ($is_logged_in): ?>
            <input type="submit" 
                   id="btnSubmitSurvei" 
                   value="<?php echo $is_edit_mode ? 'Update Jumlah Penerima Manfaat' : 'Simpan Data Survei'; ?>" 
                   class="btn btn-primary btn-modern text-uppercase font-weight-bold text-3 py-3 btn-px-5 w-100" 
                   data-loading-text="Memproses..." />
            <?php else: ?>
            <a href="<?php echo $googlelink; ?>" 
               class="btn btn-warning btn-modern text-uppercase font-weight-bold text-3 py-3 btn-px-5 w-100">
                <img alt="Google" 
                     src="<?php echo base_url(); ?>themes/ortyd/assets/media/svg/brand-logos/google-icon.svg" 
                     class="h-20px me-2" />
                Login dengan Google untuk Melanjutkan
            </a>
            <?php endif; ?>
        </div>
    </div>
    
</div>

<?php if (!$is_logged_in): ?>
    </div> <!-- Close overlay div -->
<?php endif; ?>

</form>
                
            </div>
        </div>
    </div>
</section>

<!-- Load Cloudflare Turnstile -->
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

<script>
// ==========================================
// TURNSTILE CONFIGURATION
// ==========================================
var turnstileWidgetId = null;
var turnstileToken = null;
var turnstileReady = false;

// Callback functions
function onTurnstileSuccess(token) {
    console.log('✓ Turnstile verified');
    turnstileToken = token;
    turnstileReady = true;
}

function onTurnstileExpired() {
    console.warn('⚠ Turnstile expired');
    turnstileReady = false;
    turnstileToken = null;
    if (turnstileWidgetId !== null && typeof turnstile !== 'undefined') {
        turnstile.reset(turnstileWidgetId);
    }
}

function onTurnstileError() {
    console.error('✗ Turnstile error');
    turnstileReady = false;
    turnstileToken = null;
}

// Initialize Turnstile with explicit render
function initializeTurnstile() {
    var attempts = 0;
    var maxAttempts = 50;
    
    var checkTurnstile = setInterval(function() {
        attempts++;
        
        if (typeof turnstile !== 'undefined') {
            clearInterval(checkTurnstile);
            
            try {
                var existingWidget = document.querySelector('.cf-turnstile');
                if (existingWidget && turnstileWidgetId !== null) {
                    turnstile.remove(turnstileWidgetId);
                }
                
                turnstileWidgetId = turnstile.render('.cf-turnstile', {
                    sitekey: '<?php echo $site_key; ?>',
                    theme: 'light',
                    size: 'normal',
                    callback: onTurnstileSuccess,
                    'expired-callback': onTurnstileExpired,
                    'error-callback': onTurnstileError
                });
                
                console.log('✓ Turnstile initialized:', turnstileWidgetId);
                
            } catch (error) {
                console.error('✗ Turnstile init error:', error);
            }
        } else if (attempts >= maxAttempts) {
            clearInterval(checkTurnstile);
            console.error('✗ Turnstile failed to load');
        }
    }, 100);
}

// Improved validation
function validateTurnstile() {
    console.log('Validating Turnstile...');
    
    if (typeof turnstile === 'undefined') {
        Swal.fire({
            title: '<strong>Error</strong>',
            icon: 'error',
            html: 'Sistem verifikasi keamanan gagal dimuat. Silakan refresh halaman.'
        }).then(() => {
            var submitButton = $('#btnSubmitSurvei');
            submitButton.prop('disabled', false).val(originalValue);
            //resetTurnstile();
        });
        return false;
    }
    
    const turnstileResponse = document.querySelector('[name="cf-turnstile-response"]');
    
    if (!turnstileResponse || !turnstileResponse.value) {
        Swal.fire({
            title: '<strong>Verifikasi Keamanan Diperlukan</strong>',
            icon: 'warning',
            html: 'Silakan centang kotak verifikasi keamanan untuk melanjutkan.',
            confirmButtonText: 'OK',
            confirmButtonColor: '#0088cc'
        }).then(() => {
            var submitButton = $('#btnSubmitSurvei');
            submitButton.prop('disabled', false).val(originalValue);
            //resetTurnstile();
        });
        return false;
    }
    
    console.log('✓ Turnstile valid');
    return true;
}

// Reset function
function resetTurnstile() {
    console.log('Reloading page to reset Turnstile...');
    location.reload();
}

// Auto-refresh every 4 minutes
setInterval(function() {
    if (turnstileReady && turnstileWidgetId !== null && typeof turnstile !== 'undefined') {
        console.log('Reloading page to reset Turnstile...');
        location.reload();
    }
}, 4 * 60 * 1000);

// Cek status login dan mode edit
var isLoggedIn = <?php echo $is_logged_in ? 'true' : 'false'; ?>;
var isEditMode = <?php echo $is_edit_mode ? 'true' : 'false'; ?>;
var disableIdentityFields = <?php echo $disable_identity_fields ? 'true' : 'false'; ?>;

// Wait for all scripts to load
(function() {
    'use strict';
    
    // Check if jQuery is loaded
    if (typeof jQuery === 'undefined') {
        console.error('jQuery is not loaded!');
        return;
    }
    
    // Check if Swal is loaded
    if (typeof Swal === 'undefined') {
        console.warn('SweetAlert2 is not loaded! Form submission will use default alerts.');
        window.Swal = {
            fire: function(config) {
                if (typeof config === 'object') {
                    alert(config.title + '\n' + config.html || config.text || '');
                }
                return {
                    then: function(callback) {
                        callback({ isConfirmed: true });
                        return this;
                    }
                };
            }
        };
    }
    
    // Wait for document ready
    $(document).ready(function() {
        // Initialize Turnstile
        setTimeout(function() {
            initializeTurnstile();
        }, 500);
        
        if (!isLoggedIn) {
            // User belum login, tampilkan pesan
            console.log('User belum login. Form di-disable.');
            
            // Prevent form submission
            $('#formSurvei<?php echo $iddata; ?>').on('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: '<strong>Login Diperlukan</strong>',
                    icon: 'warning',
                    html: 'Silakan login dengan Google terlebih dahulu untuk mengisi form survei.',
                    showCancelButton: false,
                    confirmButtonText: 'Login dengan Google',
                    confirmButtonColor: '#0088cc'
                }).then(function() {
                    window.location.href = '<?php echo $googlelink; ?>';
                });
                return false;
            });
        } else {
            // User sudah login, initialize form
            initializeSurveiForm();
            addInputValidation();
        }
    });
    
    function initializeSurveiForm() {
    
    // Proteksi field identitas di mode edit
    if (disableIdentityFields) {
        // Set readonly pada text input (bukan disabled, agar data tetap terkirim)
        $('#survei_pm_nama, #survei_pm_nip, #survei_pm_email, #survei_pm_tlp')
            .attr('readonly', true)
            .removeAttr('required') // Hapus required
            .css({
                'background-color': '#e9ecef',
                'cursor': 'not-allowed',
                'pointer-events': 'none'
            })
            .on('keydown keypress keyup paste cut', function(e) {
                e.preventDefault();
                return false;
            });
        
        // Untuk select, disable tapi tambahkan hidden input (sudah ditambah di PHP)
        if ($('#survei_pm_wil_id').length > 0) {
            $('#survei_pm_wil_id').prop('disabled', true).removeAttr('required');
            if (typeof $.fn.select2 !== 'undefined') {
                $('#survei_pm_wil_id').select2('destroy');
                $('#survei_pm_wil_id').prop('disabled', true);
            }
            // Hidden input sudah dibuat di PHP untuk mengirim value
        }
        
        // Tampilkan pesan info
        Swal.fire({
            title: '<strong>Mode Edit</strong>',
            icon: 'info',
            html: 'Data identitas Anda sudah terdaftar dan tidak dapat diubah.<br>Anda hanya dapat mengubah <strong>jumlah penerima manfaat</strong>.',
            confirmButtonText: 'OK, Mengerti',
            confirmButtonColor: '#0088cc'
        });
    }
    
    // Initialize Select2 untuk field yang masih aktif (non-disabled)
    if (typeof $.fn.select2 !== 'undefined' && !disableIdentityFields) {
        $('.select2-popup:not([disabled])').select2({
            placeholder: 'Pilih...',
            allowClear: true,
            width: '100%'
        });
    }
    
    // Initialize Datetime Picker dengan fallback
    initializeDatePicker();
    
    // Calculate total penerima
    calculateTotal();
    
    // Proteksi input POK - hanya angka (SELALU AKTIF - tidak tergantung mode)
    $('.pok-input').on('input', function() {
        var value = $(this).val();
        // Hapus semua karakter kecuali angka
        var cleaned = value.replace(/[^0-9]/g, '');
        
        if (value !== cleaned) {
            $(this).val(cleaned);
            var label = $(this).attr('data-label') || 'Field ini';
            showValidationMessage($(this), label + ' hanya boleh diisi dengan angka');
            setTimeout(() => {
                clearValidationMessage($(this));
            }, 2000);
        }
        
        // Update display value
        var val = parseInt(cleaned || 0);
        if (val < 0) val = 0;
        $(this).val(val);
        $(this).next('.form-text').find('.pok-value').text(val);
        calculateTotal();
    });
    
    // Proteksi paste - hanya angka
    $('.pok-input').on('paste', function(e) {
        e.preventDefault();
        var pastedData = (e.originalEvent || e).clipboardData.getData('text/plain');
        var cleaned = pastedData.replace(/[^0-9]/g, '');
        $(this).val(cleaned).trigger('input');
    });
    
    // Proteksi keypress - hanya angka
    $('.pok-input').on('keypress', function(e) {
        // Allow: backspace, delete, tab, escape, enter
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13]) !== -1 ||
            // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
            (e.keyCode === 65 && e.ctrlKey === true) ||
            (e.keyCode === 67 && e.ctrlKey === true) ||
            (e.keyCode === 86 && e.ctrlKey === true) ||
            (e.keyCode === 88 && e.ctrlKey === true) ||
            // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
            return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && 
            (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
    
    // Prevent negative values
    $('.pok-input').on('change blur', function() {
        var val = parseInt($(this).val() || 0);
        if (val < 0) val = 0;
        $(this).val(val);
        $(this).next('.form-text').find('.pok-value').text(val);
        calculateTotal();
    });
    
    // Form submission handler
    $('#formSurvei<?php echo $iddata; ?>').on('submit', function(e) {
        e.preventDefault();
        handleFormSubmission();
    });
}

function handleFormSubmission() {
    // Hapus semua pesan error sebelumnya
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    
    var hasError = false;
    var errorMessages = [];
    
    // SKIP VALIDASI FIELD IDENTITAS jika mode edit (disabled)
    if (!disableIdentityFields) {
        // Validasi Nama
        var nama = $('#survei_pm_nama').val().trim();
        if (nama) {
            if (!/^[a-zA-Z\s]+$/.test(nama)) {
                showValidationMessage($('#survei_pm_nama'), 'Nama hanya boleh mengandung huruf dan spasi');
                errorMessages.push('Nama tidak valid (hanya boleh huruf dan spasi)');
                hasError = true;
            } else if (nama.length < 3) {
                showValidationMessage($('#survei_pm_nama'), 'Nama minimal 3 karakter');
                errorMessages.push('Nama minimal 3 karakter');
                hasError = true;
            }
        }
        
        // Validasi NIP
        var nip = $('#survei_pm_nip').val().trim();
        if (nip) {
            if (!/^[0-9]+$/.test(nip)) {
                showValidationMessage($('#survei_pm_nip'), 'NIP hanya boleh mengandung angka');
                errorMessages.push('NIP tidak valid (hanya boleh angka)');
                hasError = true;
            } else if (nip.length < 8) {
                showValidationMessage($('#survei_pm_nip'), 'NIP minimal 8 digit');
                errorMessages.push('NIP minimal 8 digit');
                hasError = true;
            }
        }
        
        // Validasi Email
        var email = $('#survei_pm_email').val().trim();
        if (email) {
            if (!isValidEmail(email)) {
                showValidationMessage($('#survei_pm_email'), 'Format email tidak valid (contoh: nama@email.com)');
                errorMessages.push('Format email tidak valid');
                hasError = true;
            }
        } else {
            showValidationMessage($('#survei_pm_email'), 'Email wajib diisi');
            errorMessages.push('Email wajib diisi');
            hasError = true;
        }
        
        // Validasi Telepon
        var tlp = $('#survei_pm_tlp').val().trim();
        if (tlp) {
            var tlpDigits = tlp.replace(/[^0-9]/g, '');
            if (tlpDigits.length < 10) {
                showValidationMessage($('#survei_pm_tlp'), 'Nomor telepon minimal 10 digit');
                errorMessages.push('Nomor telepon minimal 10 digit');
                hasError = true;
            } else if (tlpDigits.length > 15) {
                showValidationMessage($('#survei_pm_tlp'), 'Nomor telepon maksimal 15 digit');
                errorMessages.push('Nomor telepon maksimal 15 digit');
                hasError = true;
            }
        }
    }
    
    // Validasi POK Input - harus angka (SELALU DIVALIDASI)
    var pokError = false;
    $('.pok-input').each(function() {
        var value = $(this).val();
        var label = $(this).attr('data-label') || 'Jumlah penerima';
        
        // Cek apakah ada karakter selain angka
        if (value && !/^[0-9]+$/.test(value)) {
            showValidationMessage($(this), label + ' hanya boleh diisi dengan angka');
            errorMessages.push(label + ' tidak valid (hanya boleh angka)');
            pokError = true;
            hasError = true;
        }
        
        // Cek nilai negatif
        var numValue = parseInt(value || 0);
        if (numValue < 0) {
            $(this).val(0);
            showValidationMessage($(this), label + ' tidak boleh negatif');
            errorMessages.push(label + ' tidak boleh negatif');
            pokError = true;
            hasError = true;
        }
    });
    
    // Validasi total penerima tidak 0 (opsional)
    var totalPenerima = 0;
    $('.pok-input').each(function() {
        totalPenerima += parseInt($(this).val() || 0);
    });
    
    if (totalPenerima === 0) {
        Swal.fire({
            title: '<strong>Peringatan</strong>',
            icon: 'warning',
            html: 'Total penerima manfaat adalah 0. Apakah Anda yakin ingin melanjutkan?',
            showCancelButton: true,
            confirmButtonText: 'Ya, Lanjutkan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#6c757d'
        }).then(function(result) {
            if (result.isConfirmed) {
                proceedWithSubmission();
            }
        });
        return false;
    }
    
    // Jika ada error validasi
    if (hasError) {
        var submitButton = $('#btnSubmitSurvei');
        Swal.fire({
            title: '<strong>Validasi Gagal</strong>',
            icon: 'error',
            html: 'Silakan perbaiki data berikut:<br><br>' + errorMessages.join('<br>')
        }).then(() => {
            submitButton.prop('disabled', false).val(isEditMode ? 'Update Jumlah Penerima Manfaat' : 'Simpan Data Survei');
        });
        return false;
    }
    
    proceedWithSubmission();
    return false;
}

function proceedWithSubmission() {
    // Cek Cloudflare Turnstile
    if (!validateTurnstile()) {
        return false;
    }
    
    // Validasi required fields (skip field yang disabled atau readonly)
    var forminput = document.getElementById('formSurvei<?php echo $iddata; ?>');
    var requiredattr = 0;
    var requiredattrdata = [];
    
    for(var i=0; i < forminput.elements.length; i++){
        var element = forminput.elements[i];
        
        // Skip jika element disabled, readonly, atau hidden
        if (element.disabled || element.readOnly || element.type === 'hidden') {
            continue;
        }
        
        // Skip jika element memiliki style pointer-events: none (disabled via CSS)
        var style = window.getComputedStyle(element);
        if (style.pointerEvents === 'none') {
            continue;
        }
        
        if(element.value === '' && element.hasAttribute('required')){
            var fieldName = element.getAttribute('data-msg-required') || 
                           element.getAttribute('placeholder') || 
                           element.name;
            requiredattrdata.push(fieldName + '<br>');
            requiredattr = 1;
        }
    }
    
    if(requiredattr == 0){
        var confirmText = isEditMode ? 
            'Apakah Anda yakin akan mengupdate jumlah penerima manfaat?' :
            'Apakah Anda yakin akan menyimpan data survei ini?';
        
        Swal.fire({
            title: '<strong>' + (isEditMode ? 'Update Data' : 'Simpan Data') + '</strong>',
            icon: 'question',
            html: confirmText + '<br><br><strong>Total Penerima: ' + $('#totalPenerima').text() + ' Orang</strong>',
            showCancelButton: true,
            confirmButtonText: isEditMode ? 'Ya, Update' : 'Ya, Simpan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#0088cc',
            cancelButtonColor: '#dc3545'
        }).then(function(result) {
            if (result.isConfirmed) {
                submitForm();
            }
        });
    } else {
        var datanya = requiredattrdata.join('');
        Swal.fire({
            title: '<strong>Oops...</strong>',
            icon: 'error',
            html: 'Masih ada data wajib yang belum terisi:<br><br>' + datanya
        });
    }
}

    function addInputValidation() {
        // SKIP VALIDASI INPUT jika field disabled (mode edit)
        if (disableIdentityFields) {
            return;
        }
        
        // Validasi Nama - hanya huruf dan spasi
        $('#survei_pm_nama').on('input', function() {
            var value = $(this).val();
            // Hapus karakter yang bukan huruf atau spasi
            var cleaned = value.replace(/[^a-zA-Z\s]/g, '');
            if (value !== cleaned) {
                $(this).val(cleaned);
                showValidationMessage($(this), 'Nama hanya boleh mengandung huruf dan spasi');
            } else {
                clearValidationMessage($(this));
            }
        });
        
        // Validasi NIP - hanya angka
        $('#survei_pm_nip').on('input', function() {
            var value = $(this).val();
            // Hapus karakter yang bukan angka
            var cleaned = value.replace(/[^0-9]/g, '');
            if (value !== cleaned) {
                $(this).val(cleaned);
                showValidationMessage($(this), 'NIP hanya boleh mengandung angka');
            } else {
                clearValidationMessage($(this));
            }
        });
        
        // Validasi Telepon - hanya angka, +, -, (, ), dan spasi
        $('#survei_pm_tlp').on('input', function() {
            var value = $(this).val();
            // Hapus karakter selain angka dan karakter telepon yang valid
            var cleaned = value.replace(/[^0-9+\-() ]/g, '');
            if (value !== cleaned) {
                $(this).val(cleaned);
                showValidationMessage($(this), 'Nomor telepon hanya boleh mengandung angka dan karakter +, -, (, )');
            } else {
                clearValidationMessage($(this));
            }
        });
        
        // Validasi Email - real-time format check
        $('#survei_pm_email').on('blur', function() {
            var email = $(this).val();
            if (email && !isValidEmail(email)) {
                showValidationMessage($(this), 'Format email tidak valid');
            } else {
                clearValidationMessage($(this));
            }
        });
    }
    
    function isValidEmail(email) {
        var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        return emailPattern.test(email);
    }
    
    function showValidationMessage(element, message) {
        // Hapus pesan error yang ada
        element.removeClass('is-valid').addClass('is-invalid');
        element.next('.invalid-feedback').remove();
        
        // Tambah pesan error baru
        element.after('<div class="invalid-feedback d-block">' + message + '</div>');
    }
    
    function clearValidationMessage(element) {
        element.removeClass('is-invalid').addClass('is-valid');
        element.next('.invalid-feedback').remove();
        
        // Hapus is-valid jika field kosong
        if (!element.val()) {
            element.removeClass('is-valid');
        }
    }
    
    function initializeDatePicker() {
        var dateFields = $('.datetime-picker');
        
        if (dateFields.length === 0) return;
        
        // Check if daterangepicker is available
        if (typeof $.fn.daterangepicker !== 'undefined' && typeof moment !== 'undefined') {
            dateFields.each(function() {
                try {
                    $(this).daterangepicker({
                        singleDatePicker: true,
                        showDropdowns: true,
                        minYear: 1901,
                        maxYear: parseInt(moment().format('YYYY'), 10),
                        locale: {
                            format: 'YYYY-MM-DD',
                            cancelLabel: 'Batal',
                            applyLabel: 'Pilih'
                        }
                    });
                } catch (e) {
                    console.warn('Daterangepicker initialization failed:', e);
                    // Fallback to HTML5 date input
                    $(this).attr('type', 'date').removeAttr('readonly');
                }
            });
        } else {
            // Fallback to HTML5 date input
            console.warn('Daterangepicker or Moment.js not available, using HTML5 date input');
            dateFields.attr('type', 'date').removeAttr('readonly');
        }
    }
    
    function calculateTotal() {
        var total = 0;
        $('.pok-input').each(function() {
            total += parseInt($(this).val() || 0);
        });
        $('#totalPenerima').text(total.toLocaleString('id-ID'));
    }

    function submitForm() {
        var submitButton = $('#btnSubmitSurvei');
        var originalValue = submitButton.val();
        
        // Disable button dan ubah teks
        submitButton.prop('disabled', true).val('Memproses...');
        
        // Kirim data
        $.ajax({
            url: '<?php echo $action; ?>',
            type: 'POST',
            data: $('#formSurvei<?php echo $iddata; ?>').serialize(),
            dataType: 'json',
            timeout: 30000,
            success: function(data) {
                if (data && data.csrf_hash) {
                    if (typeof updateCsrfToken === 'function') {
                        updateCsrfToken(data.csrf_hash);
                    } else {
                        $('.csrf_token').val(data.csrf_hash);
                    }
                }
                
                if(data && data.status == "success"){
                    var successMessage = data.message || 'Data berhasil disimpan';
                    
                    if (data.is_new_user) {
                        successMessage += '<br><br><div class="alert alert-info mt-3 text-start">✓ Anda telah terdaftar di sistem<br>✓ Gunakan 5 data yang sama untuk edit data di kemudian hari</div>';
                    }
                    
                    Swal.fire({
                        title: '<strong>Berhasil!</strong>',
                        icon: 'success',
                        html: successMessage,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#0088cc'
                    }).then(function() {
                        window.location.reload();
                    });
                    
                } else {
                    var errorMsg = data && data.error ? data.error : 'Terjadi kesalahan';
                    var isTurnstileError = errorMsg.toLowerCase().includes('verifikasi') || 
                                          errorMsg.toLowerCase().includes('captcha') ||
                                          errorMsg.toLowerCase().includes('turnstile');
                    
                    if (isTurnstileError) {
                        Swal.fire({
                            title: '<strong>Verifikasi Gagal</strong>',
                            icon: 'error',
                            html: errorMsg + '<br><br><small>Verifikasi akan dimuat ulang otomatis.</small>',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#0088cc'
                        }).then(() => {
                            resetTurnstile();
                            submitButton.prop('disabled', false).val(originalValue);
                        });
                    } else {
                        Swal.fire({
                            title: '<strong>Oops...</strong>',
                            icon: 'error',
                            html: 'Ada yang bermasalah!<br>' + errorMsg
                        }).then(() => {
                            submitButton.prop('disabled', false).val(originalValue);
                        });
                    }
                }
            },
            error: function(jqxhr, status, error) {
                console.error("Request failed:", error);
                console.error("Status:", status);
                console.error("Response:", jqxhr.responseText);
                
                var errorMessage = 'Terjadi kesalahan saat mengirim data.';
                
                if (status === 'timeout') {
                    errorMessage = 'Koneksi timeout. Silakan coba lagi.';
                } else if (jqxhr.status === 0) {
                    errorMessage = 'Tidak ada koneksi internet.';
                } else if (jqxhr.status === 403) {
                    errorMessage = 'Verifikasi keamanan gagal. Halaman akan dimuat ulang.';
                    setTimeout(function() { location.reload(); }, 2000);
                }
                
                Swal.fire({
                    title: '<strong>Oops...</strong>',
                    icon: 'error',
                    html: errorMessage
                }).then(() => {
                    submitButton.prop('disabled', false).val(originalValue);
                    resetTurnstile();
                });

            },
            complete: function() {
                // Enable button kembali
                submitButton.prop('disabled', false).val(originalValue);
            }
        });
    }
    
})();
</script>


<style>
/* Custom Styling */

/* Validation Styling */
.form-control.is-invalid {
    border-color: #dc3545;
    padding-right: calc(1.5em + 0.75rem);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.form-control.is-valid {
    border-color: #28a745;
    padding-right: calc(1.5em + 0.75rem);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}

.form-control:focus.is-invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.form-control:focus.is-valid {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.section {
    background-color: #f8f9fa;
}

.card {
    border-radius: 10px;
    overflow: hidden;
}

.card-header {
    border-bottom: 0;
    padding: 1.5rem;
}

.card-body {
    background-color: #ffffff;
}

.form-control {
    border: 1px solid #ced4da;
    border-radius: 5px;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #0088cc;
    box-shadow: 0 0 0 0.2rem rgba(0, 136, 204, 0.25);
}

.text-3 {
    font-size: 1rem !important;
}

.pok-value {
    font-size: 1.1em;
}

#totalPenerima {
    font-size: 1.8em;
    font-weight: 700;
}

.btn-modern {
    border-radius: 50px;
    transition: all 0.3s ease;
}

.btn-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 136, 204, 0.3);
}

.shadow-sm {
    box-shadow: 0 0.125rem 0.625rem rgba(0, 0, 0, 0.1) !important;
}

.divider-primary hr {
    border-top: 2px solid #0088cc;
    width: 100px;
}

/* Alert Styling */
.alert {
    border-radius: 8px;
}

.alert-info {
    background-color: #e7f3ff;
    border-color: #b3d9ff;
    color: #004085;
}

.alert-warning {
    background-color: #fff3cd;
    border-color: #ffeaa7;
    color: #856404;
}

.alert-success {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}

/* Animation */
.appear-animation {
    animation-duration: 0.8s;
    animation-fill-mode: both;
}

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

.appear-animation[data-appear-animation="fadeInUp"] {
    animation-name: fadeInUp;
}

/* Responsive */
@media (max-width: 768px) {
    .card-body {
        padding: 1.5rem !important;
    }
    
    .btn-px-5 {
        padding-left: 2rem !important;
        padding-right: 2rem !important;
    }
}

.select2-container--default .select2-selection--single {
   padding-top: 5px !important;
    padding-bottom: 10px !important;
    height: 40px !important;
}
</style>