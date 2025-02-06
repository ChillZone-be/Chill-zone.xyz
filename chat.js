class TerminalChat {
    constructor() {
        this.ws = null;
        this.messages = [];
        this.init();
    }

    init() {
        this.ws = new WebSocket('wss://chat.chill-zone.xyz');
        
        this.ws.onmessage = (event) => {
            const message = JSON.parse(event.data);
            this.messages.push(message);
            this.displayMessage(message);
        };
    }

    displayMessage(message) {
        return `
            <div class="chat-message">
                <span class="timestamp">[${new Date(message.timestamp).toLocaleTimeString()}]</span>
                <span class="username">${message.username}:</span>
                <span class="content">${message.content}</span>
            </div>
        `;
    }

    sendMessage(content) {
        this.ws.send(JSON.stringify({
            type: 'message',
            content,
            username: 'visitor',
            timestamp: Date.now()
        }));
    }
} 