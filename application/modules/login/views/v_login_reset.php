<?php 
    $iddata = 1; 
    //$action = base_url('reset_password'); // Pastikan route ini aktif di controller Anda
   // $site_key = 'your-site-key'; // Ganti dengan reCAPTCHA site key Anda
?>

<!-- Content Box -->
<div class="d-flex justify-content-center align-items-center bg-soft-dark3 o-auto scrollbar-styled radius-lg bg-blur relative zi-3" style="min-height: 100vh;">
    <div class="card shadow-lg p-4 rounded-lg w-100" style="max-width: 450px;">

        <h3 class="text-center text-dark font-weight-bold mb-5"><?php echo 'Reset Password'; ?></h3>
        <p class="text-center text-muted mb-30">Silakan masukkan password baru Anda.</p>

        <!-- Reset Password Form -->
        <form id="form<?php echo $iddata; ?>" method="post" action="<?php echo $action; ?>" class="form fv-plugins-bootstrap5 fv-plugins-framework" data-error-message="Please fill in the missing fields.">

            <!-- New Password Field -->
            <div class="fv-row mb-10">
                <label class="d-block fw-bold text-dark fs-6 mb-2" for="password-field-new">Password Baru</label>
                <div class="position-relative">
                    <input type="password" name="password" placeholder="Password Baru" id="password-field-new" required autocomplete="off" class="form-control form-control-solid form-control-lg" style="color: #000 !important;">
                    <span class="position-absolute end-0 top-50 translate-middle-y me-3 c-pointer" onclick="togglePassword('password-field-new', 'toggle-icon-new')" style="z-index:10;">
                        <i id="toggle-icon-new" class="fa fa-eye" style="font-size: 20px;"></i>
                    </span>
                </div>
            </div>

            <!-- Confirm Password Field -->
            <div class="fv-row mb-10">
                <label class="d-block fw-bold text-dark fs-6 mb-2" for="password-field-conf">Konfirmasi Password Baru</label>
                <div class="position-relative">
                    <input type="password" name="konfirmasi" placeholder="Konfirmasi Password Baru" id="password-field-conf" required autocomplete="off" class="form-control form-control-solid form-control-lg" style="color: #000 !important;">
                    <span class="position-absolute end-0 top-50 translate-middle-y me-3 c-pointer" onclick="togglePassword('password-field-conf', 'toggle-icon-conf')" style="z-index:10;">
                        <i id="toggle-icon-conf" class="fa fa-eye" style="font-size: 20px;"></i>
                    </span>
                </div>
            </div>

            <!-- CSRF Token -->
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" class="csrf_token" required />

            <!-- reCAPTCHA -->
            <div class="fv-row mb-10">
                <div class="g-recaptcha" data-sitekey="<?php echo $site_key; ?>"></div>
                <span class="text-danger"><?php echo form_error('g-recaptcha-response'); ?></span>
            </div>

            <!-- Submit Button -->
            <div class="text-center">
                <button type="submit" id="kt_docs_formvalidation_text_submit" class="btn btn-lg btn-primary px-10 py-5 my-4">Reset Password</button>
            </div>
        </form>

        <!-- Back to Forgot Password Link -->
        <div class="text-center mt-4">
            <a href="<?php echo base_url('login'); ?>" class="text-muted fs-6">
                Lupa Password? Kembali Ke Halaman Login
            </a>
        </div>
    </div>
</div>
<!-- End Content Box -->

<!-- Script -->
<script>
    // Toggle Password Visibility
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }

    // CSRF Token Updater (optional, jika server mengirimkan hash baru)
    function updateCsrfToken(newToken) {
        $('.csrf_token').val(newToken);
    }

    // Form Validation and Submission
    $(document).ready(function () {
        var formInput = $('#form<?php echo $iddata; ?>');

        formInput.submit(function (e) {
            e.preventDefault();

            let newPassword = $('#password-field-new').val();
            let confirmPassword = $('#password-field-conf').val();
            let recaptchaResponse = grecaptcha.getResponse();
            let errors = [];

            // Validasi Password
            if (newPassword !== confirmPassword) {
                errors.push("Password Baru dan Konfirmasi Password tidak sama");
            }
            if (newPassword.length < 8) {
                errors.push("Password harus minimal 8 karakter");
            }
            if (!/[a-z]/.test(newPassword)) {
                errors.push("Password membutuhkan minimal 1 huruf kecil");
            }
            if (!/[A-Z]/.test(newPassword)) {
                errors.push("Password membutuhkan minimal 1 huruf kapital");
            }
            if (!/[0-9]/.test(newPassword)) {
                errors.push("Password membutuhkan minimal 1 angka");
            }

            // Validasi reCAPTCHA
            if (recaptchaResponse.length === 0) {
                errors.push("Harap verifikasi reCAPTCHA.");
            }

            if (errors.length === 0) {
                Swal.fire({
                    icon: "info",
                    title: "Reset Password",
                    html: 'Apakah Anda yakin ingin mereset password?',
                    showCancelButton: true,
                    confirmButtonText: "Ya, Reset",
                    cancelButtonText: "Batalkan"
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Memproses...',
                            text: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.post('<?php echo $action; ?>', formInput.serialize(), function (data) {
                            updateCsrfToken(data.csrf_hash);
                            if (data.status === "success") {
                                Swal.fire({
                                    text: "Reset Password Berhasil!",
                                    icon: "success",
                                    confirmButtonText: "OK"
                                }).then(() => {
                                    window.location.href = '<?php echo base_url('login'); ?>';
                                });
                            } else {
                                Swal.fire({
                                    text: "Reset Password Gagal",
                                    html: data.errors,
                                    icon: "error",
                                    confirmButtonText: "Tutup"
                                });
                                grecaptcha.reset();
                            }
                        }, 'json').fail(function (jqxhr, status, error) {
                            console.error("Request failed: " + error);
                            Swal.fire({
                                text: "Terjadi kesalahan saat mengirim data!",
                                icon: "error",
                                confirmButtonText: "Coba Lagi"
                            });
                        });
                    }
                });
            } else {
                Swal.fire({
                    html: "Periksa kesalahan berikut:<br>" + errors.join('<br>'),
                    icon: "error",
                    confirmButtonText: "Lanjutkan Pengisian"
                });
            }
        });
    });
</script>

