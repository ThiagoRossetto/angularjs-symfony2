$("body").ready(function(){
    $("#sidebar li").click(function(){

        if( !$(this).hasClass("dropdown") ) {
            $("#sidebar a").parents("li").removeClass("active");
            $(this).addClass("active");
        }

        if( $(this).find("ul").css("display") == "block" ) {
            $(this).find("ul").css("display","none");
        } else {
            $("#sidebar li ul").css("display","none");
            $(this).find("ul").css("display","block");
            $(this).find("ul").addClass("opened");
        }
    })
})
