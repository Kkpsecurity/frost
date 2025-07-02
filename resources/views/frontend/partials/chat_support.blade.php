<style>
    .chat-box {
        position: fixed;
        bottom: 10px;
        right: 30px;
        width: 400px;
        height: 450px;
        background: #fff;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        z-index: 9999;
        display: none;
    }

    .chat-box .chat-header {
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
        font-size: 16px;
        font-weight: 600;
        color: #555;
    }

    .chat-box .chat-header .chat-close {
        position: absolute;
        top: 15px;
        right: 20px;
        font-size: 20px;
        cursor: pointer;
    }

    .chat-box .chat-message {
        height: 300px;
        overflow-y: auto;
    }

    .chat-box .chat-message .chat-message-content {
        padding: 15px 20px;
    }

    .chat-box .chat-message .chat-message-content .chat-message-item {
        margin-bottom: 20px;
    }

    .chat-box .chat-message .chat-message-content .chat-message-item .chat-message-item-content {
        display: flex;
        flex-direction: row;
    }

    .chat-box .chat-message .chat-message-content .chat-message-item .chat-message-item-content .chat-message-item-content-text {
        padding: 10px 15px;
        background: #eee;
        border-radius: 5px;
        font-size: 14px;
        color: #555;
        min-height: 300px;
        word-wrap: break-word;
        border: 1px solid #eee;
        background:rgba(0, 0, 0, 0.2)
    }

    .chat-box .chat-footer {
        padding: 15px 20px;
        border-top: 1px solid #eee;
    }

    .chat-box .chat-footer .chat-footer-content {
        display: flex;
        flex-direction: row;
    }

    .chat-box .chat-footer .chat-footer-content .chat-footer-content-input {
        flex: 1;
    }

    .chat-box .chat-footer .chat-footer-content .chat-footer-content-input input {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #eee;
        border-radius: 5px;
        font-size: 14px;
        color: #555;
    }

    .chat-trigger {
        position: fixed;
        bottom: 10px;
        right: 30px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #a7a1ff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
    }

    .chat-trigger i {
        font-size: 20px;
        color: #555;
    }
</style>


<div class="chat-trigger" onclick="toggleChat()">
    <i class="fa fa-comments"></i>
</div>

<div class="chat-box">
    <div class="chat-header">
        Chat Support
        <span class="chat-close" onclick="toggleChat()">
            <i class="fa fa-times"></i>
        </span>
    </div>
    <div class="chat-message">
        <div class="chat-message-content">
            <div class="chat-message-item">
                <div class="chat-message-item-content">
                    <div class="chat-message-item-content-text">
                        <p>Hi, I'm here to help you. What's your question?</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="chat-footer">
        <div class="chat-footer-content">
            <div class="chat-footer-content-input">
                <input type="text" placeholder="Type your message here...">
            </div>
            <div class="chat-footer-content-send">
                <button class="btn btn-primary btn-block">Send</button>
            </div>
        </div>
    </div>
</div>


<script>
    function toggleChat() {
        const chatBox = document.getElementsByClassName('chat-box')[0]; // Access the first element
        if (chatBox.style.display == 'none' || !chatBox.style.display) {
            chatBox.style.display = 'block';
        } else {
            chatBox.style.display = 'none';
        }
    }
</script>
