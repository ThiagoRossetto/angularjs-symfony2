API = {};

function load(){

    $.ajax({
        type: "GET",
        url: "resource/user/me",
        data: {},
        async: false,
        success: function(data){
            API = $.parseJSON(data);
        },
        error: function(data){

        }
    });

}

load();