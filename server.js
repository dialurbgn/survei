const WebSocket = require('ws');
const fs = require('fs');  // Modul untuk berinteraksi dengan file sistem
const wss = new WebSocket.Server({
    port: 8080,
    path: '/ws'
});

let users = {};  // Menyimpan semua user yang terhubung

// Fungsi untuk menulis daftar pengguna ke file
function saveUserListToFile() {
    const userList = Object.keys(users).join('\n');  // Menyusun daftar pengguna
    fs.writeFile('logs/user_list.txt', userList, (err) => {
        if (err) {
            console.error('Error writing to file:', err);
        } else {
            console.log('User list saved to user_list.txt');
        }
    });
}

wss.on('connection', function connection(ws) {
    let currentUser = null;

    // Menangani pesan yang masuk
    ws.on('message', function incoming(data) {
        try {
            let msg = JSON.parse(data);
            console.log('Received message:', msg);

            // Handle register request
            if (msg.type === 'register') {
                if (msg.username) {
                    users[msg.username] = ws;
                    currentUser = msg.username;
                    console.log(`${msg.username} connected`);

                    // Setelah pengguna terhubung, simpan daftar pengguna ke file
                    saveUserListToFile();
                } else {
                    console.error("Registration failed: Missing username");
                    ws.send(JSON.stringify({ type: 'error', message: 'Username required for registration' }));
                    return;
                }
            }

            // Handle message send
            else if (msg.type === 'message') {
                let to = msg.to;
                const messageId = new Date().getTime();  // Gunakan timestamp sebagai messageId
                console.log(`Forwarding message to: ${to}`);

                if (users[to]) {
                    // Mengirim pesan ke user yang dituju beserta messageId
                    users[to].send(JSON.stringify({
                        from: msg.from,
                        message: msg.message,
                        messageId: messageId  // Menambahkan messageId
                    }));
                } else {
                    console.log(`User ${to} not connected`);
                    ws.send(JSON.stringify({ type: 'error', message: `User ${to} not available` }));
                }
            }
        } catch (error) {
            console.error('⚠️ Error handling message:', error);
            ws.send(JSON.stringify({ type: 'error', message: 'Invalid message format' }));
        }
    });

    // Menangani koneksi yang ditutup
    ws.on('close', function() {
        if (currentUser) {
            console.log(`${currentUser} disconnected`);
            delete users[currentUser];  // Menghapus user yang terhubung

            // Setelah pengguna terputus, simpan daftar pengguna ke file
            saveUserListToFile();
        }
    });

    // Menangani error WebSocket
    ws.on('error', function (error) {
        console.error('⚠️ WS Client Error:', error);
    });
});

console.log('WebSocket server running at ws://localhost:8080/ws');
