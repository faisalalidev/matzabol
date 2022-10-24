"use strict";

// Setup basic express server
const
    express = require("express"),
    app = express(),
    server = require("http").createServer(app),
    io = require("socket.io")(server, {
        pingInterval: 1000 * 10,
        pingTimeout: 1000 * 5,
    }),
    port = process.env.PORT || 3001,
    validator = require("validator"),
    mysql = require("mysql"),
    apn = require('apn');

const apnProvider = new apn.Provider({
    // cert: "certificates/Veil_Dev_Push.pem",
    cert: "certificates/Veil_Dis_Push.pem",
    // key: "certificates/Veil_Dev_Push.pem",
    key: "certificates/Veil_Dis_Push.pem",
    passphrase: "1",
    production: true
});

// MYSQL DatabaseConnection
const db = mysql.createConnection({
    host: "localhost",
    user: "root",
    password: "",
    database: "veil_live",
    charset: 'utf8'
});

// Connect to Database
db.connect();

// Server listening to port
server.listen(port, function () {
    console.log("Server listening at port %d", port);
});

// Routing
app.use(express.static(__dirname));
let rooms = {};

io.use(function (socket, next) {
    let handshakeData = socket.request;
    let userid = handshakeData._query["user_id"];

    // Prevent Duplicate User Socket
    let userSocket = Object.values(io.sockets.sockets).find(skt => skt.user_id == userid && skt.connected);
    if (!!userSocket) {
        console.log("Disconnecting User " + userid + " Socket: " + userSocket.id);
        userSocket.disconnect();
    }

    if (rooms.hasOwnProperty(userid)) {
        delete rooms[userid];
    }

    socket.user_id = handshakeData._query["user_id"];
    socket.type = handshakeData._query["type"];

    next();
});

// Socket IO Connection
io.on("connection", function (socket) {

    if (socket.user_id) {
        login(socket);

        io.sockets.emit(
            "socketStatus",
            "Socket connected: " + socket.user_id + " Type: " + socket.type + " ID: " + socket.id
        );
        // send those messages that are not received by socket
    }

    // event fired when user joins the thread, it also emit the messages by thread id
    socket.on("joinRoom", function (thread) {
        if (
            rooms[socket.user_id] &&
            rooms[socket.user_id].find(th => th.thread_id == thread.thread_id)
        ) {
            socket.thread_id = thread.thread_id;

            console.log("Join: " + socket.thread_id);
            // join thread
            socket.join(socket.thread_id);

            // get messages by thread Id
            messagesByThread(socket, socket.thread_id, socket.user_id, 0, function (err, messages, pages, offset) {
                if (err) console.log("error getting thread messages", err);
                else {
                    // emit threadjoined and send last thread messages
                    if (io.sockets.sockets[socket.id])
                        io.sockets.sockets[socket.id].emit("threadJoined", {
                            user_id: socket.user_id,
                            username: socket.username,
                            messages: messages,
                            pages: pages,
                            offset: offset + 1
                        });
                }
            });
        }
    });

    // listen to sendMessage event, the client have to pass thread Id and messsage to this event
    socket.on("sendMessage", function (data, callback) {
        console.log("Message Arrived to Relay: ", socket.user_id, data.thread_id);
        data.user_id = parseInt(socket.user_id);
        data.thread_id = parseInt(data.thread_id);
        data.username = socket.username;
        data.created_at = new Date();


        // save message by thread id and sender id
        saveMessage(data.thread_id, data.user_id, data.message, data.uu_id, data.created_at, data.client_timestamp, data.sort_timestamp, function (err, result) {
            if (err) {
                console.log(err, "Error sending message");
                callback && callback(err, "Error sending message");
            } else {
                if (data.thread_id) {
                    // emit newMessage event to particular thread
                    data.message_id = result.insertId;
                    data.id = result.insertId;
                    data.message_status = "received_by_server"; //Hardcoded key for app model
                    // TODO: Create Received by Server for the Recipients not for Sender.

                    markMessageAsReceivedByServer(data.id, data.user_id, data.thread_id, function (err, result) {
                        if (!err) {
                            console.log("Sending Acknowledge to Sender: ", data.user_id, data.thread_id, data.message_id);
                            socket.emit('messageReceivedByServer', data);
                        }
                    });

                    // io.sockets.in(socket.thread_id).emit("newMessage", data);
                    socket.emit("newMessage", data);

                    // the logic behind this code is to get all the users that are linked with particular thread, and let them know
                    // about new message, this logic only send messages to connected users and also not to the sender
                    /*rooms[socket.user_id] && rooms[socket.user_id].forEach(thread => {

                     let userIds = thread.user_id.indexOf(",") !== -1 ? thread.user_id.split(",") : [thread.user_id];
                     userIds.push(socket.user_id);

                     Object.values(io.sockets.sockets)
                     .filter(connectedSocket => connectedSocket.connected &&
                     userIds.indexOf(connectedSocket.user_id) !== -1 &&
                     connectedSocket.thread_id == null
                     )
                     .forEach(connectedSocket => {
                     connectedSocket.emit("newMessage", data);
                     });
                     });*/

                    getThreadsUser(data.thread_id, socket.user_id, function (err, results) {
                        results.forEach(result => {
                            let socket = Object.values(io.sockets.sockets).find(skt => skt.user_id == result.receiver_id && skt.connected);
                            if (socket) {
                                console.log("Relay Message to Reciever: ", data.user_id, data.thread_id, result.receiver_id);
                                socket.emit("newMessage", data);
                            } else {

                                data.noti_message = `You received a new message from ${data.username}`;
                                data.noti_action_type = "chat_message";
                                data.noti_receiver_id = result.receiver_id;
                                // FIXME: We should handle notification unread cound and unread_message_count separately.
                                // saveNotification(data, function (err, notiSave) {
                                //     if (!err) {
                                //         console.log("Save Notification and User Successfully ! ");
                                console.log("Get Unread Notification");
                                getUnreadNotificationCount(result.receiver_id, function (err, notiCount) {
                                    if (!err) {
                                        data.notiCount = notiCount[0].notiCount;
                                        console.log("Unread Notification ", data.notiCount);
                                        console.log("Get Unread Messages");
                                        getUnreadMessagesCount({user_id: result.receiver_id}, result.receiver_id, function (unread_messages) {
                                            console.log("Unread Messages: ", unread_messages);
                                            data.notiCount += unread_messages;
                                            getUserDevicesByUserId(result.receiver_id, function (err, results) {
                                                if (!err) {
                                                    results.forEach(result => {
                                                        switch (result.device_type) {
                                                            case 'ios':
                                                                sendAPNNotification(data, result.device_token);
                                                                break;
                                                            case 'android':
                                                                sendFCMNotification(data, result.device_token);
                                                                break;
                                                        }
                                                    });
                                                }
                                            });

                                        });
                                    }
                                });

                                // }
                                // });

                            }
                        });
                    })
                }
            }
        });
    });

    // listen to the event leave room so that other user can listen about the user status
    // it also returns the threads so that user threads screen can be updated
    socket.on("leaveRoom", function () {
        // emit thread leaved event so that client knows user leaves the thread
        io.to(socket.thread_id).emit("threadLeaved", {
            user_id: socket.user_id,
            username: socket.username
        });
        socket.leave(socket.thread_id);
        socket.thread_id = null;

        // get threads by user id
        getThreadsByUser(socket, socket.user_id, function (error, threads) {
            rooms[socket.user_id] = threads;
            // emit getThreads event to client and return threads based on user id
            // console.log(threads.filter(thread => thread.user_images));
            socket.emit("getThreads", response(threads, 200, "Threads found!"));
        });
    });

    socket.on("messageReceived", function (data) {
        markMessageAsReceivedByUser(data.message_id, function (err, result) {
            if (!err) {
                // let sender know that, receiver gets the message
                const senderSocket = Object.values(io.sockets.sockets).find(skt => skt.connected && skt.user_id == data.sender_id);
                console.log('messageReceivedByReceiver', data);
                if (senderSocket) {
                    senderSocket.emit('messageReceivedByReceiver', data);
                    console.log(data.receiver_id, "receives the message", data.message_id);
                }
            }
        });
    });

    socket.on("messageRead", function (data) {
        markMessageAsReadByUser(data.message_id, function (err, result) {
            if (!err) {
                // let sender know that, receiver have seen the message
                const senderSocket = Object.values(io.sockets.sockets).find(skt => skt.connected && skt.user_id == data.sender_id);
                if (senderSocket) {
                    senderSocket.emit('messageReadByReceiver', data);
                    console.log(`${data.message_id} seen by user: ${data.receiver_id}`);
                }
            }
        });
    });

    socket.on("itsAMatch", function (data) {
        // console.log("ItsAMatch");
        console.log("Sender: ", socket.user_id);
        console.log("3rd Party: ", data.sender_id);
        getThreadsByUser(socket, data.sender_id, function (error, threads) {

            const senderSocket = Object.values(io.sockets.sockets).find(skt => skt.connected && skt.user_id == data.sender_id);
            if (senderSocket) {

                rooms[senderSocket.user_id] = threads;
                // emit getThreads event to client and return threads based on user id
                // console.log("Emiting to Other User" , threads);
                console.log("Sender Socket: ", senderSocket.user_id);
                console.log("Threads: ", threads[0].user_id);

                senderSocket.emit("getThreads", response(threads, 200, "Threads found!"));
            }


        });


    });

    // disconnect socket
    // on disconnect, socket must have to leave the thread and room of the socket should deleted from the server to avoid unnecessory load
    socket.on("disconnect", function () {
        console.log("Socket disconnect:", socket.user_id, "Type:", socket.type);
        io.sockets.emit(
            "socketStatus",
            "Socket disconnect: " + socket.user_id + " Type: " + socket.type
        );

        if (
            Object.values(io.sockets.sockets).filter(skt => skt.user_id == socket.user_id).length < 2) {
            socket.leave(socket.thread_id);
            delete rooms[socket.user_id];

            // on disconnect, userOffline event is broadcasted to all of the users
            socket.broadcast.emit("userOffline", {
                user_id: socket.user_id,
                username: socket.username
            });

            // thread leaved is emiited to particular thread that user has leaved the thread
            io.to(socket.thread_id).emit("threadLeaved", {
                user_id: socket.user_id,
                username: socket.username
            });
        }
    });

    // emit this event while typing and make sure to pass thread id
    socket.on("typing", function (thread_id) {
        rooms[socket.user_id] && rooms[socket.user_id]
            .filter(thread => thread.thread_id == thread_id)
            .forEach(thread => {
                let userIds = thread.user_id.indexOf(",") !== -1 ? thread.user_id.split(",") : [thread.user_id];
                Object.values(io.sockets.sockets)
                    .filter(connectedSocket => connectedSocket.connected && userIds.indexOf(connectedSocket.user_id) !== -1 &&
                    socket.user_id != connectedSocket.user_id)
                    .forEach(connectedSocket => {
                        connectedSocket.emit("typing", {
                            user_id: socket.user_id,
                            username: socket.username,
                            thread_id: thread_id
                        });
                    });
            });
    });

    // emit this event while user stop typing and make sure to pass thread id
    socket.on("stopTyping", function (thread_id) {
        rooms[socket.user_id] && rooms[socket.user_id]
            .filter(thread => thread.thread_id == thread_id)
            .forEach(thread => {
                let userIds =
                    thread.user_id.indexOf(",") !== -1
                        ? thread.user_id.split(",")
                        : [thread.user_id];

                Object.values(io.sockets.sockets)
                    .filter(connectedSocket => connectedSocket.connected &&
                        userIds.indexOf(connectedSocket.user_id) !== -1 &&
                        socket.user_id != connectedSocket.user_id
                    )
                    .forEach(connectedSocket => {
                        connectedSocket.emit("stopTyping", {
                            user_id: socket.user_id,
                            username: socket.username,
                            thread_id: thread_id
                        });
                    });
            });
    });

    // listen to get messages by page number event and return limited records based on offset
    socket.on("getMessagesByPageNumber", function (data) {
        messagesByThread(socket, data.thread_id, socket.user_id, data.offset, function (err, messages, pages, offset) {
            if (err) console.log("error getting thread messages");
            else {
                // emit threadMessages and send threadMessages by offset to current user
                io.sockets.sockets[socket.id].emit("threadMessages", {
                    user_id: socket.user_id,
                    username: socket.username,
                    messages: messages,
                    offset: offset,
                    pages: pages
                });
                // io.to(socket.thread_id).emit("threadMessages", {
                // });
            }
        });
    });

    /*============= Set Messages Read By Ids =================*/
    socket.on("messagesRead", function (data) {
        messagesReadByReceiver(data.messages_id, function (err, result) {
            if (!err) {
                // let sender know that, receiver read the messages
                const senderSocket = Object.values(io.sockets.sockets).find(skt => skt.connected && skt.user_id == data.sender_id);
                console.log('messagesReadByReceiver', data);
                if (senderSocket) {
                    senderSocket.emit('messagesReadByReceiver', data);
                    console.log(data.receiver_id, "Receiver Read the messages", data.messages_id);
                }
            }
        });
        /*messagesRead(messages_id, function (messages_status) {
         console.log(messages_status);
         socket.emit('messagesReadByReceiver', {
         messages_id: messages_id
         });
         });*/
    });
    /*============= Set Messages Read By Ids =================*/

    /*============= Set Messages Received By Ids =================*/
    socket.on("messagesReceived", function (datas) {

        datas.map(
            data => messagesReceivedByReceiver(data.message_id, function (err, result) {
                if (!err) {
                    // let sender know that, receiver receive the messages
                    const senderSocket = Object.values(io.sockets.sockets).find(skt => skt.connected && skt.user_id == data.sender_id);
                    console.log('messagesReceivedByReceiver', data);
                    if (senderSocket) {
                        senderSocket.emit('messagesReceivedByReceiver', data);
                        console.log(data.receiver_id, "Receiver Receives the messages", data.message_id);
                    }
                }
            })
        );
        /*messagesReceived(messages_id, function (messages_status) {
         console.log(messages_status);
         socket.emit('messagesReceivedByReceiver', {
         messages_id: messages_id
         });
         });*/
    });
    /*============= Set Messages Received By Ids =================*/

    /*============= Get Messages Status By Ids =================*/
    socket.on("getMessagesStatus", function (messages_id) {
        getMessagesStatus(socket, messages_id, function (messages_status) {
            socket.emit('MessagesStatus', {
                messages_status: messages_status
            });
        });
    });
    /*============= Get Messages Status By Ids =================*/

    /*============= Get Undelivered Messages =================*/
    socket.on("getUndeliveredMessages", function () {
        console.log('__Hit By Device');
        getUndeliveredMessages(socket, socket.user_id, function (undelivered_messages) {
            socket.emit('undeliveredMessages', {
                undelivered_messages: undelivered_messages
            });
            console.log('__Receive By Device');
        });
    });
    /*============= Get Undelivered Messages =================*/

    /*============= SoftDelete Message By Id =================*/
    socket.on("deleteMessageById", function (data) {
        deleteMessageById(socket, data.message_id, function (error, result) {
            if (!error) {
                console.log(result);
            }
            else {
                console.log(error);
            }
        });
    });
    /*============= SoftDelete Message By Id =================*/

    /*============= Update Message Status to Read =================*/
    socket.on("markMessageAsRead", function () {
        markMessageAsReadForAllThreads(socket.user_id, function (error, result) {
            getUnreadMessagesCount(socket, socket.user_id, function (unread_messages) {
                socket.emit('unReadMessagesCount', {
                    unread_messages: unread_messages
                });
            })
        });
    });
    /*============= Update Message Status to Read =================*/

    socket.on("getUserThreads", function () {
        // get threads by user id
        /*markMessageAsReadForAllThreads(socket.user_id, function(error, result){
         console.log('Mark All Chats as Read');
         });*/
        getThreadsByUser(socket, socket.user_id, function (error, threads) {
            rooms[socket.user_id] = threads;
            // emit getThreads event to client and return threads based on user id
            socket.emit("getThreads", response(threads, 200, "Threads found!"));
        });
    });

    socket.on("getUnReadMessagesCountByThreadId", function (data, callback) {
        getUnreadMessagesCountByThreadId(socket, data.thread_id, data.user_id, callback)
    });

    socket.on("getUnReadMessagesCount", function (data) {
        getUnreadMessagesCount(socket, data.user_id, function (unread_messages) {
            socket.emit('unReadMessagesCount', {
                unread_messages: unread_messages
            });
        })
    });

});

let login = function (socket) {
    if (socket.user_id) {
        let user_id = validator.escape(socket.user_id);

        // Check if user exists with particular user_id
        let checkIfUserExists = `SELECT * FROM users WHERE id = ${db.escape(user_id)}`;
        db.query(checkIfUserExists, function (error, results, fields) {
            // Error getting user data
            if (error) {
                console.error("error looking up if user exists", error);
                socket.emit("alertuserdoesntexists");
            } else {
                // User not found
                if (results.length === 0) {
                    console.log("user doesn't exist!");
                    socket.emit("alertuserdoesntexists");
                } else {
                    // User Found!
                    let user = results[0];
                    socket.user_id = user.id.toString();
                    user.user_id = socket.user_id;
                    socket.username = user.full_name;
                    console.log(
                        "Socket connected: " + socket.user_id,
                        "Type:",
                        socket.type, socket.user_id,
                        " ID: " + socket.id
                    );
                    socket.broadcast.emit("userOnline", {
                        user_id: socket.user_id,
                        username: socket.username
                    });

                    // emit to set user information to client socket
                    if (io.sockets.sockets[socket.id])
                        io.sockets.sockets[socket.id].emit("setUserInfo", user);

                    // get threads by user id
                    getThreadsByUser(socket, user.id, function (error, threads) {
                        rooms[user.id] = threads;
                        // emit getThreads event to client
                        // console.log(threads.filter(thread => thread.user_images));
                        socket.emit("getThreads", response(threads, 200, "Threads found!"));
                    });
                }
            }
        });
    }
};

// this function saves the user message in the db using thread id and sender id and returns the callback with err and resultss
// if err is null then result is success otherwise check what the error is
let saveMessage = function (threadId, senderId, message, uu_id, created_at, client_timestamp, sort_timestamp, callback) {
    let insertQuery = `INSERT INTO chats (thread_id, sender_id, message, uu_id, status, created_at, updated_at, client_timestamp, sort_timestamp) 
                        VALUES (
                            ${db.escape(parseInt(threadId))}, 
                            ${db.escape(parseInt(senderId))}, 
                            ${db.escape(message)}, 
                            ${db.escape(uu_id)}, 
                            ${db.escape(1)}, 
                            ${db.escape(created_at)}, 
                            ${db.escape(created_at)},
                            ${db.escape(client_timestamp)},
                            ${db.escape(sort_timestamp)}
                        );`;
    db.query(insertQuery, function (err, result, fields) {
        callback(err, result);
    });
};

// this method returns the messages by thread id, currenttly it is returning all the messages in the thread
let messagesByThread = function (socket, threadId, user_id, offset, callback) {
    getMessagesCountByThread(socket, threadId, function (err, countResult) {
        let limit = 20;
        let tempOffset = ((offset <= 0 || isNaN(offset) ? 1 : offset) - 1) * limit;
        let pages = Math.ceil(countResult[0].count / limit);

        // let updateQuery = `UPDATE chat_reads SET
        //                 type = ${db.escape('read_by_user')},
        //                 received_by_user = ${db.escape(new Date())},
        //                 read_by_user = ${db.escape(new Date())},
        //                 updated_at = ${db.escape(new Date())}
        //                 WHERE chat_id IN (SELECT c.id FROM chats c
        //                                     INNER JOIN (select * from chat_reads) cr
        //                                     ON c.id = cr.chat_id
        //                                     WHERE c.thread_id =  ${db.escape(parseInt(threadId))}
        //                                     AND cr.user_id != ${user_id}
        //                                     AND (cr.received_by_user IS NULL OR cr.read_by_user IS NULL))`;

        // db.query(updateQuery, function (err, results) {
        //     if (!err) {
        let query = `SELECT 
                        c.id,
                        c.uu_id,
                        c.thread_id, 
                        c.sender_id AS user_id, 
                        cr.user_id AS receiver_id,
                        u.full_name AS username, 
                        c.message, c.created_at, 
                        DATE_FORMAT(c.created_at, '%Y-%m-%d %H:%i:%s') AS created_at,
                        DATE_FORMAT(c.client_timestamp, '%Y-%m-%d %H:%i:%s') AS client_timestamp,
                        c.sort_timestamp, 
                        cr.type AS message_status
                    FROM chats c
                        INNER JOIN users u ON c.sender_id = u.id
                        LEFT JOIN chat_reads cr ON c.id = cr.chat_id
                    WHERE c.thread_id = ${db.escape(parseInt(threadId))} AND c.deleted_at IS NULL AND (c.deleted_by <> ${db.escape(parseInt(socket.user_id))} OR c.deleted_by IS NULL)
                        ORDER BY c.id DESC
                        LIMIT ${db.escape(limit)}
                        OFFSET ${db.escape(tempOffset)};`;
        db.query(query, function (err, results, fields) {
            callback(err, results, pages, offset);
        });
        // }
        // });
    });
};

let getMessagesCountByThread = function (socket, threadId, callback) {
    let query = `SELECT COUNT(c.id) as count FROM chats c WHERE c.thread_id = ${db.escape(parseInt(threadId))} AND c.deleted_at IS NULL AND (c.deleted_by <> ${db.escape(parseInt(socket.user_id))} OR c.deleted_by IS NULL)`;
    db.query(query, function (err, results, fields) {
        callback(err, results);
    });
};

// this returns all the threads that are referenced to the user, you have to pass the user id
let getThreadsByUser = function (socket, userId, callback) {
    let query = `
       SELECT
            u.*,
            th.id AS thread_id, 
            th.type as threadType,
            (SELECT IF (COUNT(id) = 2, "1", "0") AS is_matched FROM profile_like_dislike_boost pldb WHERE (pldb.is_like = "1" OR pldb.is_boost = "1") AND ((pldb.reciever_id = u.id AND pldb.sender_id = ${db.escape(userId)}) OR (pldb.reciever_id = ${db.escape(userId)} AND pldb.sender_id = u.id ))) AS is_match,
            (SELECT COUNT(c.id) FROM chats c INNER JOIN chat_reads cr ON c.id = cr.chat_id 
                WHERE c.thread_id = th.id AND cr.type <> 'read_by_user' AND c.sender_id <> ${db.escape(userId)} AND c.deleted_at IS NULL AND (c.deleted_by <> ${db.escape(parseInt(socket.user_id))} OR c.deleted_by IS NULL)) AS unread_messages,
            (SELECT c.message FROM chats c WHERE c.thread_id = th.id AND (c.deleted_by <> ${db.escape(parseInt(socket.user_id))} OR c.deleted_by IS NULL) ORDER BY c.id DESC LIMIT 1) AS last_message,
            (SELECT c.id FROM chats c WHERE c.thread_id = th.id ORDER BY c.updated_at DESC LIMIT 1) AS last_message_id,
            (SELECT c.updated_at FROM chats c WHERE c.thread_id = th.id ORDER BY c.updated_at DESC LIMIT 1) AS last_message_time
       FROM users u
            INNER JOIN thread_users thus2 ON u.id = thus2.user_id
            INNER JOIN threads th ON th.id = thus2.thread_id
       WHERE thus2.thread_id IN (SELECT th.id FROM thread_users thus INNER JOIN threads th ON thus.thread_id = th.id WHERE thus.user_id = ${db.escape(userId)})
       AND u.id <> ${db.escape(userId)}
       AND u.deleted_at IS NULL
       GROUP BY th.id
       ORDER BY last_message_id DESC;`;

    db.query(query, function (error, results, fields) {
        if (error) callback(error);
        else {

            let arr = [];
            results.map(result => arr.push(result.id));

            db.query(`SELECT reciever_id, sender_id FROM user_reports WHERE (sender_id=${db.escape(userId)} and reciever_id IN (${db.escape(arr)})) OR (reciever_id=${db.escape(userId)} and sender_id IN (${db.escape(arr)}))`,
                function (err, reportedUsers, fields) {
                    // All Recievers who are reported by the user;

                    if (reportedUsers) {
                        results = results.filter((result) => {
                            let reported = false;
                            reportedUsers.map(reportedUser => {
                                if (reported) {
                                    return;
                                }
                                reported = (result.id === reportedUser.reciever_id || result.id == reportedUser.sender_id);
                                //reported = (result.id === reportedUser.reciever_id || userId == reportedUser.reciever_id);
                            });
                            // Do not include in Final Array if reported = true;
                            return !reported;
                        });
                    }

                    //let qqq = `SELECT * FROM user_images WHERE user_id IN (${db.escape(results.map(result => result.id).join(","))})`;
                    let qqq = `SELECT * FROM user_images WHERE user_id IN (${db.escape(arr)})`;
                    db.query(qqq,
                        function (err, userImages, fields) {
                            callback(
                                null,
                                results.map(result => {
                                    result.user_id = result.id.toString();
                                    result.user_image = userImages
                                        .filter(userImage => userImage.user_id == result.id)
                                        .map(userImage => {
                                            userImage.sort_order = (userImage.sort_order).toString();
                                            delete userImage.deleted_at;
                                            return userImage;
                                        });
                                    result.isOnline = result.user_id in rooms;
                                    delete result.deleted_at;
                                    delete result.password;
                                    return result;
                                })
                            ); // results == threads
                        }
                    );
                });
        }
    });
};

const deleteMessageById = function (socket, message_id, callback) {
    let query = `SELECT deleted_by FROM chats WHERE uu_id = ${db.escape(message_id)};`;
    console.log(query);
    db.query(query, function (err, result) {
        if (!err) {
            console.log(result);
            if (result && result.deleted_by != null) {
                let query2 = `UPDATE chats SET deleted_at = ${db.escape(new Date())} WHERE uu_id = ${db.escape(message_id)};`;
                db.query(query2, function (err, result) {
                    callback(err, result);
                });
            }
            else {
                let query3 = `UPDATE chats SET deleted_by = ${db.escape(parseInt(socket.user_id))} WHERE uu_id = ${db.escape(message_id)};`;
                db.query(query3, function (err, result) {
                    callback(err, result);
                });
            }
        }
        else {
            console.log(err);
        }
    });
};

const markMessageAsReceivedByServer = function (chat_id, user_id, thread_id, callback) {
    let insertQuery = `INSERT INTO chat_reads (chat_id, user_id, created_at, updated_at) 
                       SELECT ${db.escape(chat_id)}, user_id, ${db.escape(new Date())}, ${db.escape(new Date())} 
                       FROM thread_users 
                       WHERE thread_id = ${db.escape(parseInt(thread_id))} AND user_id <> ${db.escape(parseInt(user_id))};`;
    db.query(insertQuery, function (err, result) {
        callback(err, result);
    });
};

const markMessageAsReceivedByUser = function (chat_id, callback) {
    let query = `UPDATE chat_reads crs SET 
                        crs.type = ${db.escape('received_by_user')}, 
                        crs.received_by_user = ${db.escape(new Date())},
                        crs.updated_at = ${db.escape(new Date())}
                 WHERE crs.chat_id = ( SELECT id FROM chats WHERE uu_id = ${db.escape(chat_id)} );`;
    db.query(query, function (err, result, fields) {
        callback(err, result);
    });
};

const markMessageAsReadByUser = function (chat_id, callback) {
    let query = `UPDATE chat_reads crs SET 
                        crs.type = ${db.escape('read_by_user')}, 
                        crs.read_by_user = ${db.escape(new Date())},
                        crs.updated_at = ${db.escape(new Date())}
                 WHERE crs.chat_id = ( SELECT id FROM chats WHERE uu_id = ${db.escape(chat_id)} );`;
    db.query(query, function (err, result, fields) {
        callback(err, result);
    });
};

const markMessageAsReadForAllThreads = function (user_id, callback) {
    let query = `UPDATE chat_reads crs SET 
                        crs.type = ${db.escape('read_by_user')}, 
                        crs.read_by_user = ${db.escape(new Date())},
                        crs.updated_at = ${db.escape(new Date())}
                 WHERE crs.chat_id in (SELECT c.id FROM chats c INNER JOIN (SELECT * FROM chat_reads) AS cr ON c.id = cr.chat_id INNER JOIN thread_users tu ON  c.thread_id = tu.thread_id WHERE cr.type <> 'read_by_user' AND c.sender_id <> ${db.escape(user_id)} AND tu.user_id = ${db.escape(user_id)});`;
    db.query(query, function (err, result, fields) {
        callback(err, result);
    });
};

const getThreadsUser = function (thread_id, user_id, callback) {
    db.query(`SELECT tu.user_id AS receiver_id FROM thread_users tu
              WHERE tu.thread_id = ${thread_id} AND tu.user_id <> ${user_id}`, (err, reuslt, fields) => callback(err, reuslt, fields))
};

//const getUserDevicesByUserId = function (user_id, callback) {
//    db.query(`SELECT * FROM user_devices WHERE user_id = ${user_id}`, callback);
//};
const getUserDevicesByUserId = function (user_id, callback) {
    db.query(`SELECT * FROM user_devices ud 
              INNER JOIN users u ON ud.user_id = u.id
              WHERE u.notify_message = '1'
              AND ud.user_id = ${user_id}`, callback);
};

const getUnreadMessagesCountByThreadId = function (socket, thread_id, user_id, callback) {
    db.query(`SELECT COUNT(c.id) as unread_messages FROM chats c INNER JOIN chat_reads cr ON c.id = cr.chat_id 
              WHERE c.thread_id = ${thread_id} AND cr.type <> 'read_by_user' AND c.sender_id <> ${db.escape(user_id)} AND c.deleted_at IS NULL AND (c.deleted_by <> ${db.escape(parseInt(socket.user_id))} OR c.deleted_by IS NULL)`,
        (err, result, fields) => {
            callback(result[0].unread_messages);
        });
};

const getUnreadMessagesCount = function (socket, user_id, callback) {
    /*db.query(`SELECT COUNT(c.id) as unread_messages FROM chats c INNER JOIN chat_reads cr ON c.id = cr.chat_id
     WHERE cr.type <> 'read_by_user' AND c.sender_id <> ${db.escape(user_id)}`,
     (err, result, fields) => {
     callback(result[0].unread_messages);
     });*/

    // FIXME: What is this joke? Why are we using socket.user_id when we already have user_id
    db.query(`SELECT COUNT(c.id) as unread_messages FROM chats c INNER JOIN chat_reads cr ON c.id = cr.chat_id INNER JOIN thread_users tu ON c.thread_id = tu.thread_id LEFT JOIN user_reports ur ON (reciever_id=c.sender_id AND ur.sender_id=${db.escape(user_id)}) OR (ur.sender_id=c.sender_id AND reciever_id=${db.escape(user_id)}) WHERE cr.type <> 'read_by_user' AND c.sender_id <> ${db.escape(user_id)} AND tu.user_id = ${db.escape(user_id)} AND c.deleted_at IS NULL AND (c.deleted_by <> ${db.escape(parseInt(socket.user_id))} OR c.deleted_by IS NULL) AND ((reciever_id IS NULL AND ur.sender_id IS NULL) OR (reciever_id<>c.sender_id AND ur.sender_id<>c.sender_id))`,
        (err, result, fields) => {
            callback(result[0].unread_messages);
        });
};

const getUndeliveredMessages = function (socket, user_id, callback) {
    let query = `SELECT c.*, cr.user_id AS receiver_id, DATE_FORMAT(c.created_at, '%Y-%m-%d %H:%i:%s') AS created_at, DATE_FORMAT(c.client_timestamp, '%Y-%m-%d %H:%i:%s') AS client_timestamp, c.sort_timestamp, cr.type AS message_status, u.full_name AS username, u.id AS user_id FROM chats c 
                 INNER JOIN chat_reads cr ON c.id = cr.chat_id 
                 INNER JOIN users u ON u.id = c.sender_id
                 WHERE cr.type = 'received_by_server' AND c.sender_id <> ${db.escape(user_id)} AND cr.user_id = ${db.escape(user_id)}
                 AND c.deleted_at IS NULL AND (c.deleted_by <> ${db.escape(parseInt(socket.user_id))} OR c.deleted_by IS NULL);`;
    db.query(query, (err, result, fields) => {
        if (!err) {
            callback(result);
        }
        else {
            console.log(err);
        }
    });
};

const getMessagesStatus = function (socket, messages_id, callback) {
    let query = `SELECT c.id, cr.type AS message_status 
                 FROM chats c INNER JOIN chat_reads cr ON c.id = cr.chat_id 
                 WHERE c.uu_id IN (${db.escape(messages_id)}) 
                 AND c.deleted_at IS NULL AND (c.deleted_by <> ${db.escape(parseInt(socket.user_id))} OR c.deleted_by IS NULL)`;
    db.query(query, (err, result, fields) => {
        if (!err) {
            callback(result);
        }
        else {
            console.log(err);
        }
    });
};

const messagesReceivedByReceiver = function (message_id, callback) {
    let query = `UPDATE chat_reads cr SET 
                        cr.type = ${db.escape('received_by_user')}, 
                        cr.received_by_user = ${db.escape(new Date())},
                        cr.updated_at = ${db.escape(new Date())}
                 WHERE cr.chat_id IN ( SELECT id FROM chats WHERE uu_id IN (${db.escape(message_id)}) ) AND cr.type <> 'read_by_user';`;
    db.query(query, function (err, result, fields) {
        if (!err) {
            callback(err, result);
        }
        else {
            console.log(err);
        }
    });
};

const messagesReadByReceiver = function (messages_id, callback) {
    let query = `UPDATE chat_reads cr SET 
                        cr.type = ${db.escape('read_by_user')}, 
                        cr.read_by_user = ${db.escape(new Date())},
                        cr.updated_at = ${db.escape(new Date())}
                 WHERE cr.chat_id IN ( SELECT id FROM chats WHERE uu_id IN (${db.escape(messages_id)}) ) AND cr.type <> 'read_by_user';`;
    db.query(query, function (err, result, fields) {
        if (!err) {
            callback(err, result);
        }
        else {
            console.log(err);
        }
    });
};

const saveNotification = function (data, callback) {

    /*let insertQuery = `INSERT INTO notifications (message, sender_id, ref_id, action_type, created_at, updated_at)
     VALUES (
     ${db.escape(data.noti_message)},
     ${db.escape(parseInt(data.user_id))},
     ${db.escape(parseInt(data.message_id))},
     ${db.escape(data.noti_action_type)},
     ${db.escape(data.created_at)},
     ${db.escape(data.created_at)}
     );
     SET @noti_id := (SELECT MAX(id) AS noti_id FROM notifications LIMIT 1);
     INSERT INTO notification_user (notification_id, user_id) VALUES (@noti_id,${db.escape(parseInt(data.noti_receiver_id))}); `;

     db.query(insertQuery, callback);
     */
    let insertQuery = `INSERT INTO notifications (message, sender_id, ref_id, action_type, created_at, updated_at) 
                        VALUES (
                            ${db.escape(data.noti_message)}, 
                            ${db.escape(parseInt(data.user_id))}, 
                            ${db.escape(parseInt(data.message_id))}, 
                            ${db.escape(data.noti_action_type)}, 
                            ${db.escape(data.created_at)}, 
                            ${db.escape(data.created_at)} 
                        )`;
    db.query(insertQuery, function (err, notiSave) {
        if (!err) {
            console.log("Save Notification Successfully! ");
            db.query(`INSERT INTO notification_user (notification_id, user_id) VALUES (${db.escape(parseInt(notiSave.insertId))},${db.escape(parseInt(data.noti_receiver_id))})`, function (err, notiUser) {
                if (!err) {
                    console.log("Save Notification User Successfully ! ");
                    callback(err, notiSave);
                }
            })
        }
    });
};

const getUnreadNotificationCount = function (user_id, callback) {
    db.query(`SELECT COUNT(id) AS notiCount FROM notification_user WHERE is_read = 0 AND user_id = ${user_id}`, callback);
};

const sendAPNNotification = (data, device_token) => {
    const note = new apn.Notification();
    note.expiry = Math.floor(Date.now() / 1000) + 3600; // Expires 1 hour from now.
    note.badge = data.notiCount;
    note.sound = "default";
    note.alert = `You received a new message from ${data.username}`;
    note.payload = data;
    note.topic = 'com.pl.Veil';
    console.log(device_token);
    apnProvider.send(note, device_token).then((result) => {// see documentation for an explanation of result
        console.log(JSON.stringify(result, null, 4));
    });
};

const sendFCMNotification = (data, device_token) => {
    // const note = new apn.Notification();
    // note.expiry = Math.floor(Date.now() / 1000) + 3600; // Expires 1 hour from now.
    // note.alert = data.message;
    // note.payload = data;
    // apnProvider.send(note, device_token).then((result) => {// see documentation for an explanation of result
    //     console.log(result);
    // });
};

const response = function (output = {}, code = 404, message = "Error", isBlocked = 0, authToken = "", pages = 0, format = "json") {
    return {
        Message: message,
        Code: code,
        Result: output || {},
        UserBlocked: isBlocked,
        token: authToken,
        pages: pages
    };
};

const getNotReceivedMessages = function (socket) {
    const query = `SELECT c.id, c.thread_id, c.uu_id, c.sender_id AS user_id
                   FROM chats c
                   INNER JOIN users u ON c.sender_id = u.id
                   LEFT JOIN chat_reads cr ON c.id = cr.chat_id
                   WHERE cr.type = 'received_by_server'
                   AND c.sender_id = ${socket.user_id} AND c.deleted_at IS NULL AND (c.deleted_by <> ${db.escape(parseInt(socket.user_id))} OR c.deleted_by IS NULL)`;
};

const getUUIDFromMessageId = function (message_id, callback) {
    let query = `SELECT uu_id FROM chats WHERE c.id = ${db.escape(messages_id)}`;
    db.query(query, (err, result, fields) => {
        if (!err) {
            callback(result);
        }
        else {
            console.log(err);
        }
    });
};

