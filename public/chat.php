<?php
session_start();
$classroomId = $_SESSION['selected_classroom'] ?? '';
$userName = $_SESSION['username'] ?? ''; // Assumes user name is stored in session
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classroom Chat</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/chat.css">
</head>
<body>

<div id="chat-container">
    <h2>Classroom Chat - Room: <?php echo htmlspecialchars($classroomId); ?></h2>
    <div id="messages"></div>
    <div id="message-form">
        <input type="text" id="message-input" placeholder="Type your message here...">
        <button id="send-message"><i class="fas fa-paper-plane"></i></button>
    </div>
</div>

<script type="module">
    // Import the functions you need from the SDKs you need
    import { initializeApp } from "https://www.gstatic.com/firebasejs/10.13.1/firebase-app.js";
    import { getDatabase, ref, onChildAdded, push } from "https://www.gstatic.com/firebasejs/10.13.1/firebase-database.js";

    // Your web app's Firebase configuration
    const firebaseConfig = {
        apiKey: "AIzaSyB2ppawQvZgcCyqsFZIveFMMhRRi6CIRiE",
        authDomain: "virtualstudyspace-a3086.firebaseapp.com",
        projectId: "virtualstudyspace-a3086",
        storageBucket: "virtualstudyspace-a3086.appspot.com",
        messagingSenderId: "49998314625",
        appId: "1:49998314625:web:f45863520246bf97866180",
        measurementId: "G-3TS1FG8P68",
        databaseURL: "https://virtualstudyspace-a3086-default-rtdb.asia-southeast1.firebasedatabase.app/"
    };

    // Initialize Firebase
    const app = initializeApp(firebaseConfig);
    const database = getDatabase(app);

    const classroomId = '<?php echo htmlspecialchars($classroomId); ?>';
    const userName = '<?php echo htmlspecialchars($userName); ?>';

    const messagesRef = ref(database, 'classrooms/' + classroomId + '/messages');

    // Fetch existing messages and listen for new ones
    onChildAdded(messagesRef, (snapshot) => {
        const message = snapshot.val();
        displayMessage(message.userName, message.text);
    });

    // Send message to Firebase
    document.getElementById('send-message').addEventListener('click', () => {
        const messageInput = document.getElementById('message-input').value;
        if (messageInput.trim()) {
            push(messagesRef, {
                userName: userName,
                text: messageInput
            });
            document.getElementById('message-input').value = ''; // Clear input field
        }
    });

    // Display message in chat
    function displayMessage(user, text) {
        const messagesDiv = document.getElementById('messages');
        const messageElement = document.createElement('div');
        messageElement.classList.add('message');
        if (user === '<?php echo htmlspecialchars($userName); ?>') {
            messageElement.classList.add('my-message');
            messageElement.innerHTML = `<div class="message-content">${text}</div><div class="message-sender">${user}</div>`;
        } else {
            messageElement.classList.add('other-message');
            messageElement.innerHTML = `<div class="message-sender">${user}</div><div class="message-content">${text}</div>`;
        }
        messagesDiv.appendChild(messageElement);
        messagesDiv.scrollTop = messagesDiv.scrollHeight; // Auto-scroll to the bottom
    }
</script>

</body>
</html>
