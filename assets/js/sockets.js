window.onload = function () {
    var socket = new WebSocket('ws://127.0.0.1:8080');
    var status = document.querySelector("#status");
    var users_status = document.querySelector("#users_status");
    console.log(socket);

    socket.onopen = function () {
        console.info("Connection established succesfully");
    };

    socket.onclose = function (event) {
        if (event.wasClean) {
            status.innerHTML += 'Соединение закрыто';
        } else {
            status.innerHTML += 'Соединение закрыто';
        }
        status.innerHTML += '<br>Код: ' + event.code + ' Причина: ' + event.reason;
    };

    socket.onmessage = function (event) {
        var message = JSON.parse(event.data);
        switch (message.topic) {
            case 'users_online':
                users_status.innerHTML = "";
                for (var i = 0; i < message.users.length; i++) {
                    users_status.innerHTML += `
                        <div class="panel individual_user_online">
                                <i class="glyphicon glyphicon-ok-sign"></i> ${message.users[i]}
                            </div>
                    `;
                }

                return;
                break;
            case 'message':
                status.innerHTML += `<div class="panel panel-1">
                                <div class="container-fluid">
                                    <div class="col-lg-12">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="user"><span class="name">${message.username}</span><span class="date">2018-05-23 23:37:43</span>
                                                </div>
                                                <div class="message">${message.message}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                break;
        }

        var block = document.getElementById("my_scroll_block");
        block.scrollTop = block.scrollHeight;
    };

    socket.onerror = function (event) {
        alert("Error: something went wrong with the socket.");
        console.error(e);
    };


    document.forms["messages"].onsubmit = function () {
        alert('');
        var message = this.msg.value;
        socket.send(message);

        return false;
    }

    $('#myButton').on('click', function (event) {
        var message = $('#text-for-sending').val();

        if (!socket) {
            alert('asdasd');
            console.log('VSE Ne och!');
            socket=new WebSocket('ws://127.0.0.1:8080');
        }
        socket.send(message);

        return false;
    });
}