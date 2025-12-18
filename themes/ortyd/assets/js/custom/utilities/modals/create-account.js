"use strict";
var KTCreateAccount = function() {
    var e, t, i, o, a, r, s = [];
    return {
        init: function() {
            (e = document.querySelector("#kt_modal_create_account")) && new bootstrap.Modal(e), (t = document.querySelector("#kt_create_account_stepper")) && (i = t.querySelector("#kt_create_account_form"), o = t.querySelector('[data-kt-stepper-action="submit"]'), a = t.querySelector('[data-kt-stepper-action="next"]'), (r = new KTStepper(t)).on("kt.stepper.changed", (function(e) {
                4 === r.getCurrentStepIndex() ? (o.classList.remove("d-none"), o.classList.add("d-inline-block"), a.classList.add("d-none")) : 5 === r.getCurrentStepIndex() ? (o.classList.add("d-none"), a.classList.add("d-none")) : (o.classList.remove("d-inline-block"), o.classList.remove("d-none"), a.classList.remove("d-none"))
            })), r.on("kt.stepper.next", (function(e) {
                console.log("stepper.next");
                var t = s[e.getCurrentStepIndex() - 1];
                t ? t.validate().then((function(t) {
                    console.log("validated!"), "Valid" == t ? (e.goNext(), KTUtil.scrollTop()) : Swal.fire({
                        text: "Sorry, looks like there are some errors detected, please try again.",
                        icon: "error",
                        buttonsStyling: !1,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-light"
                        }
                    }).then((function() {
                        KTUtil.scrollTop()
                    }))
                })) : (e.goNext(), KTUtil.scrollTop())
            })), r.on("kt.stepper.previous", (function(e) {
                console.log("stepper.previous"), e.goPrevious(), KTUtil.scrollTop()
            })), s.push(FormValidation.formValidation(i, {
                fields: {
					perusahaan_bentuk: {
                        validators: {
                            notEmpty: {
                                message: "Pilih Bentuk Perusahaan"
                            },
							regexp: {
								regexp: /^[a-zA-Z0-9]+$/,
								message: 'Inputan diharuskan hanya alpabet dan nomor'
							}
                        }
                    },
                    perusahaan_nama: {
                        validators: {
                            notEmpty: {
                                message: "Masukan Nama Perusahaan"
                            },
							stringLength: {
                                min: 5,
                                message: "Minimal penginputan 5 karakter"
                            },
							regexp: {
								regexp: /^[a-zA-Z0-9]+$/,
								message: 'Inputan diharuskan hanya alpabet dan nomor'
							}
                        }
                    }
					
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger,
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: ".fv-row",
                        eleInvalidClass: "",
                        eleValidClass: ""
                    })
                }
            })), s.push(FormValidation.formValidation(i, {
                fields: {
                    email: {
                        validators: {
                            notEmpty: {
                                message: "Masukan alamat email aktif"
                            },
                            emailAddress: {
                                message: "Format alamat email salah contoh : contact@pins.co.id"
                            }
                        }
                    },
                    notelp: {
                        validators: {
                            notEmpty: {
                                message: "Masukan Kontrak Person"
                            },
							digits: {
                                message: "Format kontak person salah diharuskan hanya number contoh : 02212345"
                            },
							stringLength: {
                                min: 5,
                                max: 15,
                                message: "Minimal penginputan 5 Number"
                            },
							regexp: {
								regexp: /^[a-zA-Z0-9]+$/,
								message: 'Inputan diharuskan hanya alpabet dan nomor'
							}
                        }
                    },
                    nohp: {
                        validators: {
                            notEmpty: {
                                message: "Masukan nomor HP"
                            },
							digits: {
                                message: "Format nomor HP salah diharuskan hanya number contoh : 081234567890"
                            },
							stringLength: {
                                min: 9,
                                max: 15,
                                message: "Minimal penginputan 9 Number"
                            },
							regexp: {
								regexp: /^[a-zA-Z0-9]+$/,
								message: 'Inputan diharuskan hanya alpabet dan nomor'
							}
                        }
                    }
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger,
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: ".fv-row",
                        eleInvalidClass: "",
                        eleValidClass: ""
                    })
                }
            })), s.push(FormValidation.formValidation(i, {
                fields: {
                    kbli: {
                        validators: {
                            notEmpty: {
                                message: "Pilih Minimal 1 KBLI"
                            },
							digits: {
                                message: "Format nomor HP salah diharuskan hanya number"
                            },
							regexp: {
								regexp: /^[a-zA-Z0-9]+$/,
								message: 'Inputan diharuskan hanya alpabet dan nomor'
							}
                        }
                    }
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger,
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: ".fv-row",
                        eleInvalidClass: "",
                        eleValidClass: ""
                    })
                }
            })), s.push(FormValidation.formValidation(i, {
                fields: {
                    surat_permohonan: {
                        validators: {
                            notEmpty: {
                                message: "Upload Surat Permohonan"
                            },
							file: {
								extension: 'pdf',
								type: 'application/pdf',
								message: 'Format salah diharuskan PDF'
							},
                        }
                    }
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger,
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: ".fv-row",
                        eleInvalidClass: "",
                        eleValidClass: ""
                    })
                }
            })), o.addEventListener("click", (function(e) {
                s[3].validate().then((function(t) {
					
					if(r.getCurrentStepIndex() == 4){
						console.log("validated!"), "Valid" == t ? (e.preventDefault(), o.disabled = !0, o.setAttribute("data-kt-indicator", "on"), setTimeout((function() { 
						
							var formdata = $('#kt_create_account_form');
							var actionUrl = formdata.attr('action');
							
							$.ajax({
								type: "POST",
								url: actionUrl,
								data: formdata.serialize(), // serializes the form's elements.
								success: function(data)
								{
								  alert(data); // show response from the php script.
								  o.removeAttribute("data-kt-indicator"), o.disabled = !1, r.goNext()
								},
								error: function (jqXHR, exception) {
									var msg = '';
									if (jqXHR.status === 0) {
										msg = 'Not connect.\n Verify Network.';
									} else if (jqXHR.status == 404) {
										msg = 'Requested page not found. [404]';
									} else if (jqXHR.status == 500) {
										msg = 'Internal Server Error [500].';
									} else if (exception === 'parsererror') {
										msg = 'Requested JSON parse failed.';
									} else if (exception === 'timeout') {
										msg = 'Time out error.';
									} else if (exception === 'abort') {
										msg = 'Ajax request aborted.';
									} else {
										msg = 'Uncaught Error.\n' + jqXHR.responseText;
									}
									alert(msg);
								},
							});
						
						
						}), 2e3)) : Swal.fire({
							text: "Sorry, looks like there are some errors detected, please try again.",
							icon: "error",
							buttonsStyling: !1,
							confirmButtonText: "Ok, got it!",
							customClass: {
								confirmButton: "btn btn-light"
							}
						}).then((function() {
							KTUtil.scrollTop()
						}))
					}else{
						console.log("validated!"), "Valid" == t ? (e.preventDefault(), o.disabled = !0, o.setAttribute("data-kt-indicator", "on"), setTimeout((function() { o.removeAttribute("data-kt-indicator"), o.disabled = !1, r.goNext()
						}), 2e3)) : Swal.fire({
							text: "Sorry, looks like there are some errors detected, please try again.",
							icon: "error",
							buttonsStyling: !1,
							confirmButtonText: "Ok, got it!",
							customClass: {
								confirmButton: "btn btn-light"
							}
						}).then((function() {
							KTUtil.scrollTop()
						}))
					}
					
                    
                }))
            })))
        }
    }
}();
KTUtil.onDOMContentLoaded((function() {
    KTCreateAccount.init()
}));