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
        
        if(isset($id) && $id != null && $id != '0'){
            $iddata = $id;
            $typedata = 'Edit';
            $is_edit_mode = true;
            
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
                <?php if (!$is_edit_mode): ?>
                <div class="appear-animation" data-appear-animation="fadeInUp" data-appear-animation-delay="200">
                    <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
                        <h5 class="alert-heading mb-2"><i class="fas fa-info-circle"></i> <strong>Informasi Penting</strong></h5>
                        <ul class="mb-0 ps-4">
                            <li>Anda <strong>tidak perlu mendaftar</strong> terlebih dahulu</li>
                            <li>Cukup isi <strong>5 data pertama</strong> (Nama, NIP, Email, No Telp, Kecamatan)</li>
                            <li>Sistem akan otomatis mendaftarkan Anda, Pastikan email yang di daftarkan adalah email yang aktif</li>
                            <li>Untuk mengubah data di kemudian hari, isi kembali <strong>5 data yang sama</strong></li>
                            <li>Email digunakan sebagai identitas untuk akses data Anda</li>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        
                        <a href="<?php echo $googlelink; ?>" class="btn btn-flex  btn-color-black btn-outline btn-text-black-700 btn-active-color-primary bg-state-light flex-center text-nowrap w-100">
										<img alt="Logo" src="<?php echo base_url(); ?>themes/ortyd/assets/media/svg/brand-logos/google-icon.svg" class="h-15px me-3" />Lanjutkan Isian dengan Google</a>
                                        
                    </div>
                   
                </div>
                <?php else: ?>
                <div class="appear-animation" data-appear-animation="fadeInUp" data-appear-animation-delay="200">
                    <div class="alert alert-success mb-4" role="alert">
                        <h5 class="alert-heading mb-2"><i class="fas fa-check-circle"></i> <strong>Mode Edit Data</strong></h5>
                        <p class="mb-0">Anda sedang mengedit data survei yang sudah tersimpan. Ubah data yang diperlukan lalu klik Simpan.</p>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Form -->
                <form class="contact-form" id="formSurvei<?php echo $iddata; ?>" action="<?php echo $action; ?>" method="POST">
                    
                    <!-- Section 1: Data Identitas -->
                    <div class="appear-animation" data-appear-animation="fadeInUp" data-appear-animation-delay="400">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-primary text-white">
                                <h4 class="mb-0 font-weight-bold text-white">
                                    <i class="fas fa-user"></i> Data Identitas Petugas Survei
                                </h4>
                                <p class="mb-0 mt-2 small text-white"><i class="fas fa-exclamation-triangle"></i> 5 Field Wajib untuk Identifikasi</p>
                            </div>
                            <div class="card-body p-4">
                                
                                <div class="alert alert-warning mb-4">
                                    <strong>Penting:</strong> Pastikan data berikut diisi dengan benar. Data ini digunakan untuk mengidentifikasi Anda saat ingin mengubah data di kemudian hari.
                                </div>
                                
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
                                                   required />
                                            <small class="form-text text-muted">Email untuk identifikasi saat edit data</small>
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
                                                   <?php echo $is_required; ?> />
                                        </div>
                                    
                                    <?php }elseif($tipe_data == 'SELECT'){ ?>
                                        <div class="form-group col-lg-<?php echo $width_column; ?>">
                                            <label><?php echo $label_name; ?> <?php echo $required_mark; ?></label>
                                            <?php 
                                                $readonlyselect = '';
                                                $disable = '';
                                                $linkcustom = 'select2';
                                                if($rows_column['name'] == 'survei_pm_wil_id'){
                                                    $linkcustom = 'select2_kecamatan';
                                                }
                                                
                                                include(APPPATH."views/common/select2formsidefront.php"); 
                                            ?>
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
                                                   <?php echo $is_required; ?> />
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
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-success text-white">
                                <h4 class="mb-0 font-weight-bold text-white">
                                    <i class="fas fa-list-ol"></i> Detail Jumlah Penerima Manfaat MBG per Jenis
                                </h4>
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
                                    ?>
                                    
                                    <div class="form-group col-lg-<?php echo $width_column; ?> ps-3 pe-3">
                                        <label><?php echo $label_name; ?></label>
                                        <input type="number" 
                                               name="<?php echo $rows_column['name']; ?>" 
                                               id="<?php echo $rows_column['name']; ?>" 
                                               class="form-control text-3 h-auto py-2 pok-input" 
                                               placeholder="0" 
                                               value="<?php echo ${$rows_column['name']}; ?>"
                                               min="0"
                                               step="1" />
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
                                <div class="g-recaptcha" data-sitekey="<?php echo $site_key; ?>"></div>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="row">
                            <div class="form-group col">
                                <input type="submit" 
                                       id="btnSubmitSurvei" 
                                       value="<?php echo $is_edit_mode ? 'Perbarui Data Survei' : 'Simpan Data Survei'; ?>" 
                                       class="btn btn-primary btn-modern text-uppercase font-weight-bold text-3 py-3 btn-px-5 w-100" 
                                       data-loading-text="Memproses..." />
                            </div>
                        </div>
                        
                    </div>
                    
                </form>
                
            </div>
        </div>
    </div>
</section>

<!-- Load Required Libraries -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<!-- Load Moment.js jika belum ada -->
<script>
if (typeof moment === 'undefined') {
    document.write('<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"><\/script>');
}
</script>

<!-- Load Daterangepicker jika belum ada -->
<script>
if (typeof $.fn.daterangepicker === 'undefined') {
    document.write('<script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.14.1/daterangepicker.min.js"><\/script>');
    document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.14.1/daterangepicker.css" />');
}
</script>

<script>
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
        initializeSurveiForm();
    });
    
    function initializeSurveiForm() {
        var isEditMode = <?php echo $is_edit_mode ? 'true' : 'false'; ?>;
        
        // Initialize Select2 if available
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2-popup').select2({
                placeholder: 'Pilih...',
                allowClear: true,
                width: '100%'
            });
        }
        
        // Initialize Datetime Picker with fallback
        initializeDatePicker();
        
        // Calculate total penerima
        calculateTotal();
        
        // Update total on input change
        $('.pok-input').on('input change', function() {
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
    
    function handleFormSubmission() {
        // Validasi email
        var email = $("input[name='survei_pm_email']").val();
        var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        
        if (!emailPattern.test(email)) {
            Swal.fire({
                title: '<strong>Oops...</strong>',
                icon: 'error',
                html: 'Silahkan masukkan alamat email yang valid.'
            });
            return false;
        }
        
        // Cek reCAPTCHA
        if (typeof grecaptcha !== 'undefined') {
            var recaptchaResponse = grecaptcha.getResponse();
            if (recaptchaResponse.length === 0) {
                Swal.fire({
                    title: '<strong>Oops...</strong>',
                    icon: 'error',
                    html: 'Silahkan centang kotak reCAPTCHA untuk melanjutkan.'
                });
                return false;
            }
        }
        
        // Validasi required fields
        var forminput = document.getElementById('formSurvei<?php echo $iddata; ?>');
        var requiredattr = 0;
        var requiredattrdata = [];
        
        for(var i=0; i < forminput.elements.length; i++){
            if(forminput.elements[i].value === '' && forminput.elements[i].hasAttribute('required')){
                var fieldName = forminput.elements[i].getAttribute('data-msg-required') || 
                               forminput.elements[i].getAttribute('placeholder') || 
                               forminput.elements[i].name;
                requiredattrdata.push(fieldName + '<br>');
                requiredattr = 1;
            }
        }
        
        if(requiredattr == 0){
            var isEditMode = <?php echo $is_edit_mode ? 'true' : 'false'; ?>;
            var confirmText = isEditMode ? 
                'Apakah Anda yakin akan mengupdate data survei ini?' :
                'Apakah Anda yakin akan menyimpan data survei ini?<br><br><span class="text-info"><i class="fas fa-info-circle"></i> Anda akan otomatis terdaftar di sistem</span>';
            
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
        
        return false;
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
                    Swal.fire({
                        title: '<strong>Oops...</strong>',
                        icon: 'error',
                        html: 'Ada yang bermasalah!<br>' + (data && data.error ? data.error : 'Terjadi kesalahan')
                    });
                    
                    // Reset captcha
                    if (typeof grecaptcha !== 'undefined') {
                        grecaptcha.reset();
                    }
                }
            },
            error: function(jqxhr, status, error) {
                console.error("Request failed:", error);
                console.error("Status:", status);
                console.error("Response:", jqxhr.responseText);
                
                Swal.fire({
                    title: '<strong>Oops...</strong>',
                    icon: 'error',
                    html: 'Terjadi kesalahan saat mengirim data. Silakan coba lagi.'
                });
                
                // Reset captcha
                if (typeof grecaptcha !== 'undefined') {
                    grecaptcha.reset();
                }
            },
            complete: function() {
                // Enable button kembali
                submitButton.prop('disabled', false).val(originalValue);
            }
        });
    }
    
})();
</script>

<script>
$(document).ready(function () {
    let typingTimer;
    const doneTypingInterval = 500; // ms
    
    // Field input text biasa
    const textFields = [
        '#survei_pm_nama',
        '#survei_pm_nip',
        '#survei_pm_email',
        '#survei_pm_tlp'
    ];
    
    // Field Select2
    const select2Fields = [
        '#survei_pm_kec_code'
    ];
    
    // Event untuk input text biasa
    $(textFields.join(',')).on('keydown', function (e) {
        // Jika ENTER ditekan → langsung cek
        if (e.key === 'Enter') {
            e.preventDefault();
            getDataExist();
            return;
        }
        // Debounce untuk key lain
        clearTimeout(typingTimer);
        typingTimer = setTimeout(function () {
            getDataExist();
        }, doneTypingInterval);
    });
    
    // Event untuk Select2 - trigger saat value berubah
    $(select2Fields.join(',')).on('change', function () {
        getDataExist();
    });
    
    function getDataExist() {
        $.ajax({
            url: '<?= site_url("frontend/getDataExist"); ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                survei_pm_nama: $('#survei_pm_nama').val(),
                survei_pm_nip: $('#survei_pm_nip').val(),
                survei_pm_email: $('#survei_pm_email').val(),
                survei_pm_tlp: $('#survei_pm_tlp').val(),
                survei_pm_kec_code: $('#survei_pm_kec_code').val(),
                <?= $this->security->get_csrf_token_name(); ?>:
                    '<?= $this->security->get_csrf_hash(); ?>'
            },
            success: function (res) {
                if (res.status === 'success') {
                    location.reload(); // 🔥 AUTO RELOAD
                } else if (res.status === 'error') {
                    console.warn(res.error);
                }
            },
            error: function(xhr, status, error) {
                console.error('Ajax error:', error);
            }
        });
    }
});
</script>


<style>
/* Custom Styling */
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
</style>