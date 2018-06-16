$(document).ready(function () {

    ajaxSearch();

    function ajaxSearch() {
        jQuery("#load").click(function () {
            var outResult = "";
            console.log('asdasd');
            searchRequest = jQuery.ajax({
                type: "GET",
                url: "http://127.0.0.1:8000/ajax/front-controller",
                data: {
                    'route': 'ajax.message-search'
                },
                dataType: "text",
                beforeSend: function () {
                    var buttonWidth = $(".btn-load").css("width");
                    $(".load-wrapp").css({
                        'display': 'block'
                    });
                    $(".load-wrapp+span").css({
                        'display': 'none'
                    });
                    $(".btn-load").css({
                        'min-width': parseInt(buttonWidth)
                    })
                },
                success: function (msg) {
                    var result = JSON.parse(msg);
                    $.each(result, function (index, value) {
                        outResult += "<div class=\"panel panel-1\">\n" +
                            "           <div class=\"row\">\n" +
                            "             <div class=\"col-lg-12\">\n" +
                            "                <div class=\"user\"><span class=\"name\">" + value.username + "</span><span class=\"date\">"+ value.date+"</span>\n" +
                            "                </div>\n" +
                            "             <div class=\"message\">" + value.message + "</div>\n" +
                            "           </div>\n" +
                            "         </div>\n" +
                            "       </div>";
                    });
                    $("#status").prepend(outResult);
                    $(".load-wrapp").css({
                        'display': 'none'
                    })
                    $(".load-wrapp+span").css({
                        'display': 'block'
                    })
                }
            })
        });
    }

})
