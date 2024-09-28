<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <!-- Include CSS links here -->
</head>
<style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
            justify-content: space-between;
            margin: 20px;
        }
        .messages_list {
            width: 30%;
            border-right: 1px solid #ccc;
            padding-right: 20px;
        }
        .message_item {
            padding: 10px;
            border-bottom: 1px solid #ccc;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .message_item:hover {
            background-color: #f0f0f0;
        }
        .message_item > div {
            margin-bottom: 5px;
        }
        .message_box {
            width: 65%;
            padding-left: 20px;
        }
        .message {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .left {
            background-color: lightblue;
            text-align: left;
        }
        .right {
            background-color: lightgreen;
            text-align: right;
        }
    </style>
<body>
    <?php include 'sidebar.php'; ?>

    <?php include 'navbar.php'; ?>

    <div class="container">
        
        <div class="messages_list">
            <!-- Messages list will be populated dynamically using JavaScript -->
        </div>
        <div class="message_box">
            <!-- Message box content will be populated dynamically using JavaScript -->


        </div>
        <form id="chatForm" method="POST" action="">
      <input type="text" id="messageInput" class="chat-input" name="message" placeholder="Type your message...">
      <button type="submit" class="chat-send">Send</button>
    </form>
    </div>

    <!-- Include JavaScript code here -->
</body>
</html>
<!-- Inside the <body> tag, after the container divs -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const messagesList = document.querySelector('.messages_list');
        const messageBox = document.querySelector('.message_box');

        // Fetch messages list when the page loads
        fetchMessagesList();

        function fetchMessagesList() {
            // Fetch the messages list using AJAX or fetch API
            fetch('get_messages_list.php') // Update URL as needed
                .then(response => response.json())
                .then(data => {
                    // Populate messages list
                    messagesList.innerHTML = generateMessagesList(data);
                })
                .catch(error => {
                    console.error('Error fetching messages list:', error);
                });
        }

        function generateMessagesList(messages) {
            let html = '';
            messages.forEach(message => {
                html += `<div class="message_item" data-customer-id="${message.customer_id}">
                            <div>${message.Name}</div>
                            <div>${message.last_message}</div>
                            <div>${message.last_timestamp}</div>
                        </div>`;
            });
            return html;
        }

        // Add event listener for message item click
        messagesList.addEventListener('click', function(event) {
            const clickedItem = event.target.closest('.message_item');
            if (clickedItem) {
                const customerId = clickedItem.dataset.customerId;
                fetchMessagesForCustomer(customerId);
            }
        });

        function fetchMessagesForCustomer(customerId) {
            // Fetch messages for the selected customer
            fetch(`get_messages.php?customer_id=${customerId}`) // Update URL as needed
                .then(response => response.json())
                .then(data => {
                    // Populate message box
                    messageBox.innerHTML = generateMessageBox(data);
                })
                .catch(error => {
                    console.error('Error fetching messages for customer:', error);
                });
        }

        function generateMessageBox(messages) {
    let html = '';
    let prevCustomerName = '';
    let prevProfilePic = '';

    messages.forEach(message => {
        const messageClass = message.status === '1' ? 'left' : 'right';
        const messageContent = message.message_text;
        const customerName = message.Name;
        const profilePic = message.ProfilePicFilename;

        // Check if the current message belongs to the same customer as the previous one
        const isSameCustomer = prevCustomerName === customerName && prevProfilePic === profilePic;

        // Include customer name and profile picture only if it's a new customer's message
        if (!isSameCustomer) {
            html += `
                <div class="message-header">
                    <img src="../img/profile/${profilePic}" alt="Profile Pic" class="profile-pic">
                    <span class="customer-name">${customerName}</span>
                </div>`;
        }

        // Include the message content
        html += `
            <div class="message ${messageClass}">
                <div class="message-content">${messageContent}</div>
            </div>`;

        // Update previous customer's details
        prevCustomerName = customerName;
        prevProfilePic = profilePic;
    });

    return html;
}

    });
</script>
