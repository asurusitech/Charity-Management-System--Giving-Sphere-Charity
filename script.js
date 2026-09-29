document.getElementById('send-button').addEventListener('click', sendMessage);
document.getElementById('chat-input').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        sendMessage();
    }
});

function sendMessage() {
    const input = document.getElementById('chat-input');
    const message = input.value.trim();

    if (message === '') return;

    const chatBox = document.getElementById('chat-box');
    const messageElement = document.createElement('div');
    messageElement.classList.add('message', 'sent');
    messageElement.textContent = message;
    chatBox.appendChild(messageElement);

    input.value = '';
    chatBox.scrollTop = chatBox.scrollHeight;

    // Simulate admin reply
    setTimeout(() => {
        const adminMessageElement = document.createElement('div');
        adminMessageElement.classList.add('message', 'received');
        adminMessageElement.textContent = 'This is an auto-reply from admin.';
        chatBox.appendChild(adminMessageElement);
        chatBox.scrollTop = chatBox.scrollHeight;
    }, 1000);
}

