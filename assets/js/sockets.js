$(document).ready(function () {
    var socket = new WebSocket('ws://127.0.0.1:8080');
    var status = document.querySelector("#status");
    console.log(socket);

    socket.onopen = function () {
        console.info("Connection established succesfully");
    };

    socket.onclose = function (event) {
        alert("Connection closed!");
    };

    socket.onmessage = function (event) {
        var message = JSON.parse(event.data);
        switch (message.topic) {
            case 'users_online':
                $("#users_status").empty();
                var users = "";
                for (var i = 0; i < message.users.length; i++) {
                    users += "\n<div class=\"panel individual_user_online\">\n " +
                        "           <i class=\"glyphicon glyphicon-ok-sign\"></i> " + message.users[i] + "\n" +
                        "        </div>\n";
                }
                $("#users_status").append(users);
                $(".count").empty().append(message.users.length+" users online");
                break;
            case 'message':
                var message_block = "<div class=\"panel panel-1\">\n" +
                    "           <div class=\"row\">\n" +
                    "             <div class=\"col-lg-12\">\n" +
                    "                <div class=\"user\"><span class=\"name\">" + message.username + "</span><span class=\"date\">"+ message.date+"</span>\n" +
                    "                </div>\n" +
                    "             <div class=\"message\">" + message.message + "</div>\n" +
                    "           </div>\n" +
                    "         </div>\n" +
                    "       </div>";

                $("#status").append(message_block);
                break;
        }

        var block = document.getElementById("my_scroll_block");
        block.scrollTop = block.scrollHeight;
    };

    socket.onerror = function (event) {
        alert("Error: something went wrong with the socket.");
        console.error(e);
    };


    $('#myButton').click(function (event) {
        var message = $('#text-for-sending').val();
        $.trim(message);
        if (!(message.length == 0)) {
            $('#text-for-sending').val('');
            socket.send(message);
            return false;
        }
    });

    $('#text-for-sending').keydown(function (event) {
        var message = $('#text-for-sending').val();
        $.trim(message);
        if (!(message.length == 0)) {
            if (event.ctrlKey && event.keyCode == 13) {
                $('#text-for-sending').val('');
                socket.send(message);
                return false;
            }
        }
    });
})