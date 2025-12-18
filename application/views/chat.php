<!-- Chat Popup -->
<div id="chat-popup" style="position:fixed; bottom:5px; right:20px; z-index:9999;">
    <div class="card card-custom gutter-b example example-compact" style="border:1px solid #ccc; border-radius:8px; overflow:hidden; transition: width 0.3s ease-in-out, right 0.3s ease-in-out;">
       <div id="chatboxheader" class="card-header d-flex justify-content-between align-items-center p-2" style="background:#005ccb; color:white; transition: height 0.3s ease-in-out, width 0.3s ease-in-out;">
    <div id="chat-header-text" style="display:none;">
        <strong>Private Chat</strong><br>
        <small>Welcome, <?= $this->session->userdata('username') ?></small>
    </div>
    <div class="btn-group" style="display: flex; gap: 5px;">
    
        <button id="go-to-chat" class="btn btn-sm btn-light" style="font-size:16px; width:30px; height:30px; padding:0; display:none; align-items:center; justify-content:center;" title="Klik untuk menuju Chat Penuh">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 4h16v16H4z"/>
    </svg>
</button>

        <button id="toggle-chat" class="btn btn-sm btn-light" style="font-size:16px; width:30px; height:30px; padding:0; display:flex; align-items:center; justify-content:center;">
            <svg id="toggle-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="2" />
                <circle cx="12" cy="18" r="2" />
                <circle cx="12" cy="6" r="2" />
            </svg>
        </button>
        <!-- Add Go to Full Chat Icon (hidden initially) -->
    </div>
</div>


        <div class="card-body p-3" id="chat-body" style="background:white; display:none;">
            <button id="enable-notifications" class="btn btn-sm btn-warning mb-2" style="width: 100%; display:none;">
                🔔 Aktifkan Notifikasi
            </button>

            <label for="to">Kirim ke:</label>
            <select id="to" class="form-control mb-2" style="width: 100%;"></select>

            <div id="chat-box-container" style="height:200px; overflow:auto; border:1px solid #ccc; padding:5px; background:#f9f9f9; margin-bottom:10px; font-size:12px; border-radius:5px;"></div>

            <div class="input-group">
                <input type="text" id="message" class="form-control form-control-sm" placeholder="Ketik pesan..." />
                <div class="input-group-append">
                    <button class="btn btn-primary btn-sm" onclick="sendMessage()" style="background: #005ccb;">Kirim</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.chat-user { font-weight: bold; margin-top: 5px; cursor: pointer; padding: 5px; border-radius: 5px; transition: background-color 0.3s ease, box-shadow 0.3s ease; }
.chat-user:hover { background-color: #f0f0f0; box-shadow: 0 0 10px rgba(0, 0, 0, 0.2); }
.chat-user:focus { outline: none; box-shadow: 0 0 10px rgba(0, 123, 255, 0.5); }

.card-header .btn-group {
    display: flex;
    gap: 8px; /* Memberikan jarak antar tombol yang lebih proporsional */
    align-items: center;
}

#go-to-chat {
    font-size: 16px;
    width: 30px;
    height: 30px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.3s ease-in-out;
    position: relative;
}

#go-to-chat:hover {
    background-color: #e0e0e0;
    transform: scale(1.1);
}

#go-to-chat:after {
    content: "→";  /* Menambahkan teks panah ke kanan sebagai petunjuk */
    position: absolute;
    top: 40px;
    font-size: 14px;
    color: #005ccb;
    display: none;
}

#go-to-chat:hover:after {
    display: block;
}

#toggle-chat, #go-to-chat {
    font-size: 16px;
    width: 30px;
    height: 30px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%; /* Untuk membuat tombol lebih bulat */
    transition: all 0.3s ease-in-out;
}

#toggle-chat:hover, #go-to-chat:hover {
    background-color: #e0e0e0; /* Menambahkan efek hover untuk tombol */
    transform: scale(1.1); /* Efek sedikit membesar saat hover */
}

</style>

<script>
const username = '<?= $this->session->userdata('username') ?>';
let connectedUsers = {}; // Menyimpan status koneksi pengguna
let ws = null;
let notificationSound = new Audio('<?php echo base_url("themes/ortyd/assets/sounds/chat.mp3"); ?>');


$(document).ready(function() {
    
    loadChat()
    let bg = $('body').css('background-image');
    let match = bg.match(/url\(["']?(.*?)["']?\)/);

    if (match && match[1]) {
        let imageUrl = match[1];

        let img = new Image();
        img.crossOrigin = "anonymous";
        img.src = imageUrl;

        img.onload = function () {
            const { darkest, brightest } = getDarkestAndBrightestColor(img);
            console.log("Warna tergelap (dibatasi):", darkest);
            console.log("Warna terang:", brightest);

            const gradient = `linear-gradient(to bottom right, rgb(${darkest}) 50%, rgb(${brightest}) 100%)`;

            $('#chatboxheader').css('background', gradient);
            $('#installBtn').css('background', gradient);
        };
    }
    
    if (window.self !== window.top) {
        $('#chat-popup').remove();
        return;
    }
    
    if (window.location.pathname.includes("data_inbox/chat")) {
        // Sembunyikan chat popup jika berada di halaman chat penuh
        $('#chat-body').hide();  // Menyembunyikan bagian chat body
        $('#chat-header-text').hide();  // Menyembunyikan header
        $('#chat-popup').animate({ width: '70px', right: '0px' }, 300); // Menyempitkan ukuran popup
        $('#go-to-chat').hide(); // Menyembunyikan ikon "Go to Full Chat"
    } else {
        // Jika tidak di halaman chat, tetap tampilkan popup
        $('#toggle-chat').show(); // Menampilkan tombol toggle jika tidak di halaman chat
    }


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

     // Handle click event on the "Go to Full Chat" button
    $('#go-to-chat').click(function() {
        // Redirect to the full chat page (URL might be different depending on your application)
        window.location.href = '<?php echo base_url("data_inbox/chat"); ?>';
    });

    // Menampilkan atau menyembunyikan chat popup
    $('#toggle-chat').click(function() {
        const isVisible = $('#chat-body').is(':visible');
        if (isVisible) {
            $('#chat-body').slideUp(200);
            $('#chat-header-text').hide();
            $('#toggle-icon').html(`<line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" />`);
            $('#chat-popup').animate({ width: '70px', right: '0px' }, 300);
            $('#go-to-chat').hide(); // Hide "Go to Full Chat" icon
       } else {
            $('#chat-body').slideDown(200);
            $('#chat-header-text').show();
            $('#toggle-icon').html(`<line x1="5" y1="12" x2="19" y2="12" />`);
            $('#chat-popup').animate({ width: '400px', right: '20px' }, 300);
             $('#go-to-chat').show(); // Show "Go to Full Chat" icon when chat is open
           
        }
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
    connectWebSocket();

    // Inisialisasi select2 untuk memilih pengguna
    $("#to").select2({
        dropdownParent: $('#chat-popup'),
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
                updateCsrfToken(data.csrf_hash)
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

    $('#message').keypress(function(e) {
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

        if (connectedUsers[data.from]) {
            userChatBox.innerHTML += `<div id="message-${data.from}-${data.messageId}"><b>${data.from}:</b> ${data.message} <span id="read-status-${data.from}" style="color:green; display:none;">✅</span></div>`;
        } else {
            userChatBox.innerHTML += `<div style="color:red;">Pengguna ${data.from} tidak tersedia saat ini.</div>`;
        }

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
            
             if (window.location.pathname.includes("data_inbox/chat")) {
                // Sembunyikan chat popup jika berada di halaman chat penuh
                $('#chat-body').hide();  // Menyembunyikan bagian chat body
                $('#chat-header-text').hide();  // Menyembunyikan header
                $('#chat-popup').animate({ width: '70px', right: '0px' }, 300); // Menyempitkan ukuran popup
                $('#go-to-chat').hide(); // Menyembunyikan ikon "Go to Full Chat"
                $('#toggle-chat').prop('disabled',true);
            } else {
                // Jika tidak di halaman chat, tetap tampilkan popup
                $('#chat-body').slideDown(200);
                $('#chat-header-text').show();
                $('#go-to-chat').show(); // Menyembunyikan ikon "Go to Full Chat"
                $('#toggle-icon').html(`<line x1="5" y1="12" x2="19" y2="12" />`);
                $('#chat-popup').animate({ width: '400px', right: '20px' }, 300);
                $('#toggle-chat').show(); // Menampilkan tombol toggle jika tidak di halaman chat
                
            }
    
           
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

            if (!document.getElementById('chat-box-' + to)) {
                const newChatBox = document.createElement('div');
                newChatBox.id = 'chat-box-' + to;
                newChatBox.style.borderTop = '1px solid #ddd';
                newChatBox.style.marginTop = '5px';
                newChatBox.innerHTML = `<div style="font-weight:bold; margin-top:5px;">Chat dengan ${to}</div>`;
                container.appendChild(newChatBox);
            }

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
    }else{
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian!',
            text: 'Pilih Kirim ke siapa dan pesan tidak boleh kosong.'
        });
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
        content: content
    }, function(response) {
        // Update CSRF token baru dari respons
       // if (response.csrf_hash) {
            // updateCsrfToken(response.csrf_hash)
       // }

        // Lakukan hal lain dengan respons, misalnya tampilkan pesan
    });
}

function loadChat() {
    var formData = new FormData();
    $.ajax({
        url: '<?php echo base_url("data_inbox/loadchat"); ?>',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            $('#chat-box-container').html(response.chat_html);
            document.getElementById('chat-box-container').scrollTop = document.getElementById('chat-box-container').scrollHeight;

           // if (response.csrf_hash) {
               // updateCsrfToken(response.csrf_hash);
            //}
        },
        error: function(xhr, status, error) {
            console.error('Gagal memuat chat:', error);
        }
    });
}


$(document).on('click', '#chat-popup', function() {
    $('#chat-popup').css('border', '1px solid #ccc');
});
</script>
