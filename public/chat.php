<?php
include '../includes/back_button.php';

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
    import { getDatabase, ref, onChildAdded, push, remove,onChildRemoved } from "https://www.gstatic.com/firebasejs/10.13.1/firebase-database.js";
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
    
    let lastDate = '';
    let lastMessageDate = ''; 

    // Fetch existing messages and listen for new ones
    onChildAdded(messagesRef, (snapshot) => {
        const message = snapshot.val();
        const messageId = snapshot.key;
        const timestamp = message.timestamp ? message.timestamp : new Date().toISOString(); 
        displayMessage(messageId, message.userName, message.text, timestamp);
    });

    // Send message to Firebase
    document.getElementById('send-message').addEventListener('click', () => {
        const messageInput = document.getElementById('message-input').value;
        const timestamp = new Date().toISOString(); 

        if (messageInput.trim()) {
            push(messagesRef, {
                userName: userName,
                text: messageInput,
                timestamp: timestamp
            });
            document.getElementById('message-input').value = ''; // Clear input field
        }
    });

     // Function to delete a message from Firebase
     function deleteMessage(messageId) {
        const messageRef = ref(database, 'classrooms/' + classroomId + '/messages/' + messageId);
        remove(messageRef);
    }
    
    // Display message in chat
    function displayMessage(messageId, user, text, timestamp) {
        const messagesDiv = document.getElementById('messages');

        const messageDate = timestamp ? new Date(timestamp).toLocaleDateString() : new Date().toLocaleDateString();

        // If the message date is different from the last message date, show the date separator
        if (messageDate !== lastMessageDate) {
            const dateSeparator = document.createElement('div');
            dateSeparator.classList.add('date-separator');
            dateSeparator.innerText = messageDate;
            messagesDiv.appendChild(dateSeparator);
            lastMessageDate = messageDate;
        }

        const messageElement = document.createElement('div');
        messageElement.classList.add('message');
        messageElement.setAttribute('data-id', messageId ||'');

        if (user === '<?php echo htmlspecialchars($userName); ?>') {
            messageElement.classList.add('my-message');
            messageElement.innerHTML = `<div class="message-sender">${user}</div>
            <div class="message-content">${text}</div>
                <div class="message-footer">
                    <span class="message-timestamp">${new Date(timestamp).toLocaleTimeString()}</span>
                    <button class="delete-message"><i class="fas fa-trash"></i></button>
            </div>`;
            messageElement.querySelector('.delete-message').addEventListener('click', () => deleteMessage(messageId));
        } else {
            messageElement.classList.add('other-message');
            messageElement.innerHTML = `<div class="message-sender">${user}</div><div class="message-content">${text}</div>
             <div class="message-footer">
                    <span class="message-timestamp">${new Date(timestamp).toLocaleTimeString()}</span>
                </div>`;
        }
        messagesDiv.appendChild(messageElement);
        messagesDiv.scrollTop = messagesDiv.scrollHeight; // Auto-scroll to the bottom
    }

    onChildRemoved(messagesRef, (snapshot) => {
    const messageId = snapshot.key;

    // Remove the message from the DOM if it is deleted by any user
    const messageElement = document.querySelector(`.message[data-id="${messageId}"]`);
    if (messageElement) {
        messageElement.remove(); // Remove the message element from the DOM
    }
});


</script>

</body>
</html>
