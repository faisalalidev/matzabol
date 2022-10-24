let messageInput = $('input#messageInput'),
    typingSpan = $('span#receiverTypeMessage'),
    FADE_TIME = 150, // milliseconds
    TYPING_TIMER_LENGTH = 400, // milliseconds
    typing = false,
    lastTypingTime,
    threadJoined = false,
    pages,
    offset,
    offlineMessages = [],
    id = null;

while (!id) {
    id = prompt("Enter User Id", '1');
}

/**
 * connect socket when user is authenticated, currently socket is created using userId
 **/
let socket = io('http://' + window.location.hostname, {query: `user_id=${id}&type=web`, path:'/node/socket.io'});

socket.on('connect', function () {
    console.log('connected', socket.thread_id);

    // check if socket has thread_id, if yes emit joinRoom by threadId
    if (socket.thread_id) {
        // removing offline messages
        $('div[message-type="offline"]').remove();
        socket.emit("joinRoom", {
            thread_id: socket.thread_id
        });
    }
    // send all those messages that are send by the user when socket is disconnected
    offlineMessages.forEach(offlineMessage => {
        console.log(offlineMessage);
        socket.emit("sendMessage", offlineMessage, function (err, errorMessage) {
            // called when error occurred while sending message
            console.log(err, errorMessage);
        });
    });
    offlineMessages = [];
});
/**
 * Event triggered after socket created, to set user data
 */
socket.on('setUserInfo', function (data) {
    socket.user_id = data.user_id;
    console.log(JSON.stringify(data));
});
/**
 * Event to populate threads of user
 */
socket.on("getThreads", function (response) {
    console.log(response);
    let str = "";
    response.Result.forEach(thread => {
        str += '<li class="item-recent-profile dis-block" ' +
            'thread_id="' + thread.thread_id + '" ' +
            'user_id="' + thread.user_id + '">' +
            '<a href="#" class="dis-flex flex-w flex-r-m trans-0-3 bg1-hov p-l-30 p-r-30 p-t-10 p-b-10">' +
            '<div class="dis-flex flex-col-r w-100per-55">' +
            '<div class="dis-flex flex-sb w-full">' +
            '<span class="txt7 t-right"></span>' +
            '<span class="txt8 t-right" id="receiverName">' + thread.full_name + '</span><span id="unread_messages">(' + thread.unread_messages + ')</span>' +
            '</div>' +
            '<span class="txt7" id="lastMessage">' + thread.last_message + '</span>' +
            '<span class="txt7" style="display: none; color: green;" itemprop="typing">is typing..</span>' +
            '</div>' +
            '<div id="status" class="' + (thread.isOnline ? 'state1' : 'state3') + ' m-l-15">' +
            '<div class="wrap-cir-pic size3">' +
            '<img src="images/recent-01.jpg" alt="' + thread.full_name + '">' +
            '</div>' +
            '</div>' +
            '</a>' +
            '</li>';
    });
    $('ul#threads').html(str);
    $('.list-member-chat ul a').on('click', function (e) {
        e.preventDefault();
        let receiverName = $(this).find('span#receiverName').text().trim();
        let thread_id = $(this).parent().attr('thread_id');
        // let user_id = $(this).parent().attr('user_id');
        socket.thread_id = thread_id;
        socket.emit("joinRoom", {
            thread_id: thread_id
        });
        $('.message-chat span#receiverName').text(receiverName);
        $('.wrap-chat').addClass('show-message-chat');
    });
});
/**
 * Event triggered when user joined the room, it gives the messages according to thread_id
 */
socket.on('threadMessages', function (data) {
    pages = data.pages;
    data.messages.forEach(msg => {
        changeMessageState(msg);
        if (msg.user_id == socket.user_id) {
            $('div#messages').append(setMessageStatus(msg));
        } else {
            $('div#messages').append(`<div class="recv-mess" message_id="${msg.id}">${msg.message}</div>`);
        }
    });
});
socket.on("threadJoined", function (data) {
    threadJoined = true;
    pages = data.pages;
    offset = data.offset;
    console.log(pages, offset, data.messages.length);
    $('div#messages').html('');
    data.messages.reverse().forEach(msg => {
        if (msg.user_id != socket.user_id) {
            changeMessageState(msg);
        }
        if (msg.user_id == socket.user_id) {
            $('div#messages').append(setMessageStatus(msg));
        } else {
            $('div#messages').append(`<div class="recv-mess" message_id="${msg.id}">${msg.message}</div>`);
        }
    });
    scrollToBottom();
});

const changeMessageState = function (msg) {
    switch (msg.message_status) {
        case 'received_by_server':
            socket.emit('messageReceived', {
                message_id: msg.id,
                receiver_id: socket.user_id,
                sender_id: msg.user_id
            });
            socket.emit('messageRead', {
                message_id: msg.id,
                receiver_id: socket.user_id,
                sender_id: msg.user_id
            });
            break;
        case 'received_by_user':
            socket.emit('messageRead', {
                message_id: msg.id,
                receiver_id: socket.user_id,
                sender_id: msg.user_id
            });
            break;
    }
};

const setMessageStatus = function (msg) {
    let messageStr = '';
    let checkboxes = '';
    switch (msg.message_status) {
        case 'received_by_server':
            checkboxes = '<span class="ti-check"></span>';
            messageStr = `<div class="send-mess" message_id="${msg.id}">${checkboxes}${msg.message}</div>`;
            break;
        case 'received_by_user':
            checkboxes = '<span class="ti-check"></span><span class="ti-check"></span>';
            messageStr = `<div class="send-mess" message_id="${msg.id}">${checkboxes}${msg.message}</div>`;
            break;
        case 'read_by_user':
            checkboxes = '<span style="color: blue" class="ti-check"></span><span style="color: blue" class="ti-check"></span>';
            messageStr = `<div class="send-mess" message_id="${msg.id}">${checkboxes}${msg.message}</div>`;
            break;
    }
    return messageStr;
};

/**
 *  this message is called both for sender and receiver so they can update their chat screen
 *  you can check receiver and sender using user_id
 *  data.user_id != socket.user_id ? 'Receiver' : 'Sender'
 * **/
socket.on("newMessage", function (data) {
    // Track this anywhere to check whether user joined the thread or not, if yes, populate the screen
    // if not, update the thread screen
    if (threadJoined) {
        let messagesDiv = $('div#messages');
        if (data.user_id == socket.user_id) {
            // Sender
            messagesDiv.append('<div class="send-mess" message_id="' + data.id + '">' + data.message + '</div>');
        } else {
            // Receiver
            messagesDiv.append('<div class="recv-mess" message_id="' + data.id + '">' + data.message + '</div>');
        }
        scrollToBottom();
        if (data.user_id != socket.user_id) {
            socket.emit('messageReceived', {
                message_id: data.message_id,
                receiver_id: socket.user_id,
                sender_id: data.user_id
            });
            socket.emit('messageRead', {
                message_id: data.message_id,
                receiver_id: socket.user_id,
                sender_id: data.user_id
            });
        }
    } else {
        $('#threads li[thread_id="' + data.thread_id + '"]').find('#lastMessage').text(data.message);
        if (data.user_id != socket.user_id) {
            socket.emit('getUnReadMessagesCount', {
                thread_id: data.thread_id,
                user_id: socket.user_id
            }, function (err, result) {
                $('#threads li[thread_id="' + data.thread_id + '"]').find('#unread_messages').text(`(${result[0].unread_messages})`);
            });

            socket.emit('messageReceived', {
                message_id: data.message_id,
                receiver_id: socket.user_id,
                sender_id: data.user_id
            });
        }
    }

    // fire this when receiver receives the message
    // when sender and current user id didn't match then we know its receiver
    // so we have to fire acknowledgement event i.e messageReceived
});
// event get fired when user offline
socket.on("userOffline", function (data) {
    let statusDiv = $('#threads li[user_id="' + data.user_id + '"] div#status');
    statusDiv.removeClass("state1");
    statusDiv.addClass("state3");
});
// event get fired when user online
socket.on("userOnline", function (data) {
    let statusDiv = $('#threads li[user_id="' + data.user_id + '"] div#status');
    statusDiv.removeClass("state3");
    statusDiv.addClass("state1");
});
socket.on("typing", function (data) {
    if (threadJoined && socket.thread_id == data.thread_id) {
        typingSpan.fadeIn(FADE_TIME);
    } else {
        let threadLi = $('#threads li[thread_id="' + data.thread_id + '"]');
        threadLi.find('#lastMessage').hide();
        threadLi.find('span[itemprop="typing"]').fadeIn(FADE_TIME);
    }
});
socket.on("stopTyping", function (data) {
    if (threadJoined && socket.thread_id == data.thread_id) {
        typingSpan.fadeOut(FADE_TIME);
    }
    else {
        let threadLi = $('#threads li[thread_id="' + data.thread_id + '"]');
        threadLi.find('span[itemprop="typing"]').hide();
        threadLi.find('#lastMessage').show();
    }
});
socket.on("messageReceivedByServer", function (data) {
    const messageDiv = $('div[message_id="' + data.message_id + '"]');
    messageDiv.find('span').remove();
    messageDiv.prepend('<span class="ti-check"></span>');
});
socket.on("messageReceivedByReceiver", function (data) {
    const messageDiv = $('div[message_id="' + data.message_id + '"]');
    messageDiv.find('span').remove();
    messageDiv.prepend('<span class="ti-check"></span><span class="ti-check"></span>');
});
socket.on("messageReadByReceiver", function (data) {
    const messageDiv = $('div[message_id="' + data.message_id + '"]');
    messageDiv.find('span').remove();
    messageDiv.prepend('<span class="ti-check"></span><span class="ti-check"></span>');
    messageDiv.find('span').css('color', 'blue');
});

$('button#sendMessageButton').on('click', function (e) {
    sendMessage();
});
$('button#leaveThreadButton').on('click', function (e) {
    threadJoined = false;
    socket.thread_id = null;
    socket.emit("leaveRoom", {});
});
$(window).keydown(function (event) {
    // Auto-focus the current input when a key is typed
    if (!(event.ctrlKey || event.metaKey || event.altKey)) {
        messageInput.focus();
    }
    // When the client hits ENTER on their keyboard
    if (event.which === 13) {
        // send message here
        sendMessage();
    }
});

// fire event on scroll top
$('#messages').scroll(function () {
    if ($('#messages').scrollTop() <= 0) {
        if (offset <= pages) {
            offset++;
            socket.emit('getMessagesByPageNumber', {thread_id: socket.thread_id, offset: offset});
        }
    }
});
messageInput.on("input", function () {
    updateTyping();
});

let updateTyping = function () {
    if (!typing) {
        typing = true;
        socket.emit("typing", socket.thread_id);
    }
    lastTypingTime = new Date().getTime();
    setTimeout(function () {
        let typingTimer = new Date().getTime();
        let timeDiff = typingTimer - lastTypingTime;
        if (timeDiff >= TYPING_TIMER_LENGTH && typing) {
            socket.emit("stopTyping", socket.thread_id);
            typing = false;
        }
    }, TYPING_TIMER_LENGTH);
};
let scrollToBottom = function () {
    $('div#messages').animate({scrollTop: $('div#messages').prop("scrollHeight")}, 50);
};
let sendMessage = function () {
    let message = messageInput.val();
    if (message) {
        if (socket.connected) {
            socket.emit("sendMessage", {
                message: message,
                thread_id: socket.thread_id,
                uu_id: guid(),
//                receiver_id: socket.user_id
            }, function (err, errorMessage) {
                // called when error occurred while sending message
                console.log(err, errorMessage);
            });
        } else {
            $('div#messages').append('<div class="send-mess" message-type="offline">' + message + '</div>');
            scrollToBottom();
            offlineMessages.push({
                message: message,
                thread_id: socket.thread_id,
//                receiver_id: socket.user_id
            });
        }
    }
    messageInput.val('');
};

function guid() {
    function s4() {
        return Math.floor((1 + Math.random()) * 0x10000)
            .toString(16)
            .substring(1);
    }
    return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
}

