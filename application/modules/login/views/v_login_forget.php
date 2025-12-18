<?php 
    $iddata = 1;
?>

<!-- Content Box -->
<div class="d-flex justify-content-center align-items-center bg-soft-dark3 o-auto scrollbar-styled radius-lg bg-blur relative zi-3" style="min-height: 100vh;">
    <div class="card shadow-lg p-4 rounded-lg w-100" style="max-width: 450px;">
        <!-- Title -->
        <h2 class="text-center text-dark fs-28 fs-24-sm lh-normal">
            <?php echo title; ?>
        </h2>
        <p class="text-center text-dark fs-16 mt-4">
            Reset Password
        </p>
        
        <!-- Contact Form -->
        <form id="form<?php echo $iddata; ?>" class="validate-me mt-4" name="login_form" method="post" action="<?php echo $action; ?>" data-error-message="Please fill in the missing fields.">
            <!-- Email -->
            <div class="form-group">
                <label for="email" class="fs-16 text-dark">Email</label>
                <input type="text" name="email" id="email" class="form-control" placeholder="Email" required autocomplete="off">
            </div>

            <!-- CSRF Token -->
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" class="csrf_token" required />

            <!-- Captcha -->
            <div class="form-group mt-4">
                <div class="g-recaptcha" data-sitekey="<?php echo $site_key; ?>"></div>
                <span class="text-danger"><?php echo form_error('g-recaptcha-response'); ?></span>
            </div>

            <!-- Submit Button -->
            <div class="d-flex justify-content-center mt-4">
                <button type="submit" id="kt_docs_formvalidation_text_submit" class="btn btn-primary w-100">
                    Kirim Email
                </button>
            </div>
        </form>
        <!-- End Form -->

        <!-- Links for forgot and sign-up -->
        <div class="mt-4 text-center">
            <a href="<?php echo base_url('login'); ?>" class="text-dark fs-15 text-decoration-none">Kembali Ke Halaman Login</a>
        </div>
    </div>
</div>
<!-- End Content Box -->

<script>
    $(document).ready(function () {
        var forminput = document.getElementById('form<?php echo $iddata; ?>');
        const submitButton = document.getElementById('kt_docs_formvalidation_text_submit');

        submitButton.addEventListener('click', function (e) {
            e.preventDefault(); // Prevent the default form submission

            // Validasi format email menggunakan regex
            var email = document.getElementById('email').value;
            var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
            
            if (!emailPattern.test(email)) {
                Swal.fire({
                    text: 'Format email tidak valid. Silahkan masukkan email yang valid.',
                    icon: 'error',
                    confirmButtonText: 'Tutup',
                    customClass: {
                        confirmButton: 'btn btn-danger'
                    }
                });
                return; // Stop further execution if email is invalid
            }

            // Show loading indicator
            Swal.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Check if captcha is validated
            if (!grecaptcha.getResponse()) {
                Swal.fire({
                    text: 'ERROR: Please verify you are human by filling out the captcha',
                    icon: 'error',
                    confirmButtonText: 'Tutup',
                    customClass: {
                        confirmButton: 'btn btn-danger'
                    }
                });
                return;
            }

            $.ajax({
                url: '<?php echo $action; ?>',
                type: 'POST',
                data: $('#form<?php echo $iddata; ?>').serialize(),
                success: function (data) {
                   var obj = JSON.parse(data);
                    updateCsrfToken(obj.csrf_hash);
                    if (obj.status == "success") {
                        Swal.fire({
                            title: "Berhasil",
                            text: obj.message,
                            icon: "info",
                            confirmButtonText: "Tutup",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "<?php echo base_url('login'); ?>";
                            }
                        });
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: obj.errors,
                            icon: "error",
                            confirmButtonText: "Tutup",
                            customClass: {
                                confirmButton: "btn btn-danger"
                            }
                        });
                        grecaptcha.reset(); // Reset captcha if there's an error
                    }
                },
                error: function (jqxhr, status, error) {
                    console.error("Request failed: " + error);
                    if (jqxhr.status === 403) {
                        $.get('<?php echo base_url('request_csrf_token'); ?>', function (data) {
                            updateCsrfToken(data.csrf_hash);
                        });
                    }

                    Swal.fire({
                        text: "Terjadi kesalahan saat mengirim data!",
                        icon: "error",
                        confirmButtonText: "Coba Lagi",
                        customClass: {
                            confirmButton: "btn btn-danger"
                        }
                    });
                }
            });
        });
    });
</script>
