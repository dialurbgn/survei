<!-- Chat Form Page -->
<div class="container mt-5">
    <div class="card">
        <div class="card-header" style="background-color: white; color: #005ccb; padding: 20px; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);">
            <strong>Private Chat</strong><br>
            <small>Welcome, <?= $this->session->userdata('username') ?></small>
        </div>
        <div class="card-body">
            <!-- Select User -->
            <label for="to">Kirim ke:</label>
            <select id="to" class="form-control mb-2" style="width: 100%;"></select>

            <!-- Chat Box -->
            <div id="chat-box-container" style="height:300px; overflow:auto; border:1px solid #ccc; padding:5px; background:#f9f9f9; margin-bottom:10px; font-size:12px; border-radius:5px;"></div>

            <!-- Message Input -->
            <div class="input-group">
                <input type="text" id="message" class="form-control form-control-sm" placeholder="Ketik pesan..." />
                <div class="input-group-append">
                    <button class="btn btn-primary btn-sm" onclick="sendMessage()" style="background: #005ccb;">Kirim</button>
                </div>
            </div>

            <!-- Notification Button -->
            <button id="enable-notifications" class="btn btn-sm btn-warning mt-3" style="width: 100%; display:none;">
                🔔 Aktifkan Notifikasi
            </button>
        </div>
    </div>
</div>

<style>
    .card-header {
        background-color: white;
        color: #005ccb;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }

    .chat-user {
        font-weight: bold;
        margin-top: 5px;
        cursor: pointer;
        padding: 5px;
        border-radius: 5px;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    .chat-user:hover {
        background-color: #f0f0f0;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    }

    .chat-user:focus {
        outline: none;
        box-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
    }
</style>

<script>

$(window).on('load', function() {
    // Panggil WebSocket setelah halaman selesai dimuat
    connectWebSocket();
});

    $(document).ready(function() {
        
        $('#toggle-chat').prop('disabled',true);
        
         if ("Notification" in window && Notification.permission !== "granted") {
            $('#enable-notifications').show();
        }

        $('#enable-notifications').click(function () {
            Notification.requestPermission().then(function (permission) {
                if (permission === "granted") {
                    $('#enable-notifications').hide();
                    alert("Notifikasi telah diaktifkan!");
                } else {
                    alert("Notifikasi diblokir.");
                }
            });
        });

        // Pastikan select2 disetel setelah halaman dimuat ulang
        const select2Element = $('#to');
        const savedUser = localStorage.getItem('selectedUser');

        if (savedUser) {
            const existingOption = select2Element.find(`option[value="${savedUser}"]`);

            // Jika pengguna belum ada dalam select2, tambahkan
            if (existingOption.length === 0) {
                const newOption = new Option(savedUser, savedUser, true, true);
                select2Element.append(newOption).trigger('change');
            } else {
                // Jika pengguna sudah ada, set nilai dan trigger change
                select2Element.val(savedUser).trigger('change');
            }

            // Ubah placeholder setelah memilih pengguna
            $('#message').attr('placeholder', 'Ketik pesan untuk ' + savedUser + '...');
        } else {
            // Jika savedUser null, kosongkan select2 dan placeholder
            select2Element.val(null).trigger('change');
            $('#message').attr('placeholder', 'Ketik pesan...');
        }

        // WebSocket connection
        

        // Inisialisasi select2 untuk memilih pengguna
        $("#to").select2({
            placeholder: 'Pilih User',
            escapeMarkup: function (markup) {
                return markup;
            },
            width: '100%',
            multiple: false,
            ajax: {
                type: "POST",
                url: "<?php echo base_url('data_inbox/select2useronline'); ?>",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term,
                        table: 'users_data',
                        id: 'username',
                        name: 'username',
                        page: params.page,
                        <?php echo $this->security->get_csrf_token_name(); ?> : csrfHash 
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
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

        // Mengubah placeholder dan menyimpan pilihan pengguna ke localStorage
        $(document).on('change', '#to', function() {
            const selectedUser = $(this).val();
            $('#message').attr('placeholder', 'Ketik pesan untuk ' + selectedUser + '...');
            localStorage.setItem('selectedUser', selectedUser);
        });

        $('#message').keydown(function(e) {
            if (e.which === 13 && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    });

    // Fungsi untuk menghubungkan WebSocket
    function connectWebSocket() {
        ws = new WebSocket('<?php echo ws_url; ?>');

        ws.onopen = function() {
            console.log('Connected to WebSocket');
            ws.send(JSON.stringify({ type: 'register', username: username }));
            connectedUsers[username] = true; // Menandai pengguna yang terhubung
            loadChat();
        };

        ws.onmessage = function(e) {
            const data = JSON.parse(e.data);
            connectedUsers[data.from] = true; // Menandai pengguna yang terhubung

            const container = document.getElementById('chat-box-container');
            let userChatBox = document.getElementById('chat-box-' + data.from);

            if (!userChatBox) {
                userChatBox = document.createElement('div');
                userChatBox.id = 'chat-box-' + data.from;
                userChatBox.style.borderTop = '1px solid #ddd';
                userChatBox.style.marginTop = '5px';
                userChatBox.innerHTML = `
                    <div style="font-weight:bold; margin-top:5px;">
                        Chat dengan ${data.from}
                    </div>`;
                container.appendChild(userChatBox);
            }

            userChatBox.innerHTML += `<div id="message-${data.from}-${data.messageId}"><b>${data.from}:</b> ${data.message} <span id="read-status-${data.from}" style="color:green; display:none;">✅</span></div>`;

            container.scrollTop = container.scrollHeight;

            // Putar suara notifikasi
            notificationSound.play();

            // Cek apakah pengguna sudah ada di dalam select2
            const select2Element = $('#to');
            const selectedUser = data.from;

            // Cek apakah pengguna sudah ada di dalam select2
            const existingOption = select2Element.find(`option[value="${selectedUser}"]`);

            // Jika pengguna belum ada di dalam select2, tambahkan pengguna tersebut
            if (existingOption.length === 0) {
                const newOption = new Option(selectedUser, selectedUser, true, true);
                select2Element.append(newOption).trigger('change');
            }

            // Pilih pengguna yang baru ditambahkan
            select2Element.val(selectedUser).trigger('change');

            // Simpan pengguna yang dipilih ke localStorage
            localStorage.setItem('selectedUser', selectedUser);

            // Update placeholder untuk pesan
            $('#message').attr('placeholder', 'Ketik pesan untuk ' + selectedUser + '...');

            // Jika chat popup tersembunyi, buka chat popup
            if (!$('#chat-body').is(':visible')) {
                $('#chat-body').slideDown(200);
            }

            // Tampilkan notifikasi jika tab tidak aktif
            if (document.hidden || !$('#chat-body').is(':visible')) {
                if (Notification.permission === "granted") {
                    new Notification('Pesan Baru dari ' + data.from, {
                        body: data.message,
                        icon: '<?php echo base_url("themes/ortyd/assets/images/chat-icon.png"); ?>'
                    });
                }
            }

            markAllAsRead(data.from);
            saveChat();
        };

        ws.onclose = function() {
            console.log('WebSocket closed. Reconnecting in 3 seconds...');
            setTimeout(connectWebSocket, 3000);
            connectedUsers[username] = false; // Menghapus status koneksi pengguna
        };

        ws.onerror = function(e) {
            console.error('WebSocket error', e);
        };
    }

    // Fungsi untuk mengirim pesan
    function sendMessage() {
        const to = document.getElementById('to').value.trim();
        const message = document.getElementById('message').value.trim();
        const container = document.getElementById('chat-box-container');

        if (to !== '' && message !== '') {
            if (ws.readyState === WebSocket.OPEN) {
                ws.send(JSON.stringify({
                    type: 'message',
                    from: username,
                    to: to,
                    message: message
                }));

                const chatBox = document.getElementById('chat-box-' + to);
                chatBox.innerHTML += `<div style="text-align:right;"><b>You:</b> ${message}</div>`;

                document.getElementById('message').value = '';
                container.scrollTop = container.scrollHeight;
                notificationSound.play();
                saveChat();
            } else {
                 Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan!',
                    text: 'WebSocket belum terhubung. Tunggu beberapa detik atau refresh halaman.'
                });
            }
        }
    }

    function markAllAsRead(from) {
        const messages = document.querySelectorAll(`#chat-box-${from} div span[id^="read-status-${from}"]`);
        messages.forEach((message) => {
            message.style.display = 'inline';
        });
    }

    function saveChat() {
        const content = document.getElementById('chat-box-container').innerHTML;
           $.post('<?php echo base_url("data_inbox/savechat"); ?>', {
            content: content,
            <?php echo $this->security->get_csrf_token_name(); ?> : csrfHash
        }, function(response) {
            // Update CSRF token baru dari respons
            if (response.csrf_hash) {
                 updateCsrfToken(response.csrf_hash)
            }

            // Lakukan hal lain dengan respons, misalnya tampilkan pesan
        });
    }

    function loadChat() {
       $.get('<?php echo base_url("data_inbox/loadchat"); ?>', function(response) {
            $('#chat-box-container').html(response.chat_html);
            document.getElementById('chat-box-container').scrollTop = document.getElementById('chat-box-container').scrollHeight;

            // Update CSRF token
            if (response.csrf_hash) {
                updateCsrfToken(response.csrf_hash)
            }
        }, 'json')
        .fail(function(jqxhr, status, error) {
									console.error("Request failed: " + error);
									
									// Menangani jika statusnya 403 dan mengambil token CSRF baru
									if (jqxhr.status === 403) {
										$.get('<?php echo base_url('request_csrf_token'); ?>', function(data) {
											csrfHash = data.csrf_hash;
											updateCsrfToken(csrfHash); // Perbarui token CSRF
											// Lakukan retry atau aksi lainnya
										});
									}

									Swal.fire({
										text: "Terjadi kesalahan saat mengirim data!",
										icon: "error",
										buttonsStyling: false,
										confirmButtonText: "Coba Lagi",
										customClass: {
											confirmButton: "btn btn-danger"
										},
										didOpen: () => {
											$('.swal2-container').css('z-index', 99999); // Ensures the alert is in front
										}
									});

									//loadingclose();
								});        // Penting: minta respons dalam format JSON
    }
</script>
