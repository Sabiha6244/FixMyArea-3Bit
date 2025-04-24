<?php
session_start();
$conn = new mysqli("localhost", "root", "", "3bit");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$senderId = isset($_GET['sender_id']) ? (int)$_GET['sender_id'] : 0;
$receiverId = isset($_GET['receiver_id']) ? (int)$_GET['receiver_id'] : 0;
$issueId = isset($_GET['issue_id']) ? (int)$_GET['issue_id'] : 0;

if (!$senderId || !$receiverId || !$issueId) {
    die("Missing parameters.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chat - FixMyArea</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #1f1f1f;
            color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }

        .chat-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background-color: #2c2c2c;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
        }

        .chat-box {
            height: 420px;
            overflow-y: auto;
            padding: 20px;
            background-color: #3b3b3b;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .message-row {
            display: flex;
            align-items: flex-end;
            margin-bottom: 15px;
        }

        .message {
            padding: 10px 15px;
            border-radius: 20px;
            max-width: 70%;
            word-wrap: break-word;
            font-size: 15px;
            position: relative;
        }

        .sent {
            background-color: #28a745;
            color: white;
            margin-left: auto;
            border-bottom-right-radius: 0;
        }

        .received {
            background-color: #6c757d;
            color: white;
            margin-right: auto;
            border-bottom-left-radius: 0;
        }

        .profile-pic {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            background-color: #999;
            margin: 0 10px;
        }

        .timestamp {
            font-size: 0.75em;
            color: #d1d1d1;
            margin-top: 5px;
        }

        .input-group input {
            background-color: #2e2e2e;
            color: white;
            border: 1px solid #444;
        }

        .input-group input:focus {
            background-color: #2e2e2e;
            color: white;
            border-color: #666;
        }

        .input-group .btn {
            background-color: #28a745;
            border: none;
        }

        .input-group .btn:hover {
            background-color: #218838;
        }

    </style>
</head>
<body>

<div class="chat-container">
    <h4 class="text-center mb-4">Real-Time Chat</h4>
    <div class="chat-box" id="chatBox"></div>

    <form id="messageForm">
        <div class="input-group">
            <input type="text" name="message" id="message" class="form-control" placeholder="Type a message..." required autocomplete="off">
            <div class="input-group-append">
                <button class="btn btn-success" type="submit">Send</button>
            </div>
        </div>
    </form>
</div>

<script>
    const senderId = <?= $senderId ?>;
    const receiverId = <?= $receiverId ?>;
    const issueId = <?= $issueId ?>;
    const chatBox = document.getElementById('chatBox');
    const messageForm = document.getElementById('messageForm');
    const messageInput = document.getElementById('message');

    function loadMessages() {
        fetch(`fetch_messages.php?sender_id=${senderId}&receiver_id=${receiverId}&issue_id=${issueId}`)
            .then(res => res.json())
            .then(messages => {
                chatBox.innerHTML = '';
                messages.forEach(msg => {
                    const row = document.createElement('div');
                    row.classList.add('message-row');

                    const isSent = msg.sender_id == senderId;
                    const msgDiv = document.createElement('div');
                    msgDiv.classList.add('message', isSent ? 'sent' : 'received');

                    const timestamp = new Date(msg.timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                    msgDiv.innerHTML = `<div>${msg.message}</div><div class="timestamp">${timestamp}</div>`;

                    const img = document.createElement('img');
                    img.classList.add('profile-pic');
                    img.src = msg.profile_picture || 'default-profile.png';

                    if (isSent) {
                        row.appendChild(msgDiv);
                        row.appendChild(img);
                    } else {
                        row.appendChild(img);
                        row.appendChild(msgDiv);
                    }

                    chatBox.appendChild(row);
                });

                chatBox.scrollTop = chatBox.scrollHeight;
            });
    }

    loadMessages();
    setInterval(loadMessages, 2000);

    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const msg = messageInput.value.trim();
        if (!msg) return;

        const formData = new FormData();
        formData.append('sender_id', senderId);
        formData.append('receiver_id', receiverId);
        formData.append('issue_id', issueId);
        formData.append('message', msg);

        fetch('send_message.php', {
            method: 'POST',
            body: formData
        }).then(() => {
            messageInput.value = '';
            messageInput.focus();
            loadMessages();
        });
    });
</script>

</body>
</html>
