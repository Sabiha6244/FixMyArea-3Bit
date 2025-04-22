<?php
session_start();
$conn = new mysqli("localhost", "root", "", "3bit");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate and get URL parameters
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
        body { background-color: #2e2e2e; color: white; }
        .chat-container { max-width: 800px; margin: 50px auto; }
        .chat-box {
            height: 400px;
            overflow-y: auto;
            border: 1px solid #ccc;
            padding: 15px;
            background-color: #3a3a3a;
        }
        .message {
            padding: 8px 12px;
            border-radius: 15px;
            margin-bottom: 10px;
            max-width: 70%;
        }
        .sent { background-color: #28a745; color: white; align-self: flex-end; margin-left: auto; }
        .received { background-color: #6c757d; color: white; align-self: flex-start; margin-right: auto; }
        .input-group input { background-color: #2e2e2e; color: white; border: 1px solid #555; }
        .input-group button { background-color: #28a745; border: none; }
    </style>
</head>
<body>

<div class="chat-container">
    <h3 class="text-center">Chat with Service Provider</h3>
    <div class="chat-box d-flex flex-column mb-3" id="chatBox"></div>

    <form id="messageForm">
        <div class="input-group">
            <input type="text" name="message" id="message" class="form-control" placeholder="Type your message..." required>
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

    // Load chat messages
    function loadMessages() {
        fetch(`fetch_messages.php?sender_id=${senderId}&receiver_id=${receiverId}&issue_id=${issueId}`)
            .then(res => res.json())
            .then(messages => {
                chatBox.innerHTML = '';
                messages.forEach(msg => {
                    const div = document.createElement('div');
                    div.classList.add('message');
                    div.classList.add(msg.sender_id == senderId ? 'sent' : 'received');
                    div.innerHTML = msg.message;
                    chatBox.appendChild(div);
                });
                chatBox.scrollTop = chatBox.scrollHeight;
            });
    }

    // Initial load + poll every 2s
    loadMessages();
    setInterval(loadMessages, 2000);

    // Handle message send
    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const msg = messageInput.value.trim();
        if (msg === '') return;

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
            loadMessages();
        });
    });
</script>

</body>
</html>
