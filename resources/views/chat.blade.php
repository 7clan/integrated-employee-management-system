<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>RAG Chat</title>

<style>
/* Floating phone-like chat */
.rag-chat {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 320px;
    height: 480px;
    background: #ffffff;
    border-radius: 18px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.2);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    font-family: system-ui, -apple-system, BlinkMacSystemFont;
}

/* Header */
.rag-header {
    background: #111827;
    color: white;
    padding: 12px;
    font-weight: bold;
    text-align: center;
}

/* Messages */
.rag-messages {
    flex: 1;
    padding: 10px;
    overflow-y: auto;
    background: #f9fafb;
}

/* Message bubbles */
.msg {
    margin-bottom: 10px;
    padding: 8px 10px;
    border-radius: 10px;
    font-size: 14px;
    max-width: 90%;
}

.msg.user {
    background: #2563eb;
    color: white;
    align-self: flex-end;
}

.msg.ai {
    background: #e5e7eb;
    color: #111827;
    align-self: flex-start;
}

/* Input area */
.rag-input {
    display: flex;
    border-top: 1px solid #e5e7eb;
}

.rag-input input {
    flex: 1;
    border: none;
    padding: 10px;
    font-size: 14px;
    outline: none;
}

.rag-input button {
    border: none;
    background: #2563eb;
    color: white;
    padding: 0 16px;
    cursor: pointer;
}
</style>
</head>

<body>

<div class="rag-chat">
    <div class="rag-header">
        AI Assistant
    </div>

    <div id="messages" class="rag-messages"></div>

    <div class="rag-input">
        <input
            type="text"
            id="question"
            placeholder="Ask something…"
            onkeydown="if(event.key==='Enter') askAI()"
        >
        <button onclick="askAI()">Send</button>
    </div>
</div>

<script>
async function askAI() {
    const input = document.getElementById('question');
    const text = input.value.trim();
    if (!text) return;

    input.value = '';

    addMessage(text, 'user');

    const res = await fetch('{{ url('/api/rag/ask') }}', {

     method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            
        },
        body: JSON.stringify({ question: text })
    });

    if (!res.ok) {
        addMessage('Error contacting AI', 'ai');
        return;
    }

    const data = await res.json();
    addMessage(data.answer || 'No answer', 'ai');
}

function addMessage(text, type) {
    const box = document.getElementById('messages');
    const div = document.createElement('div');
    div.className = 'msg ' + type;
    div.textContent = text;
    box.appendChild(div);
    box.scrollTop = box.scrollHeight;
}
</script>

</body>
</html>
