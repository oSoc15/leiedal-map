$(document).ready(function(){
        
            var toggleNav = false;
        $('#invButton').click(function(){
            if (toggleNav === false) {
                $("").removeClass("slideright");

                $("#nav_overlay").addClass("slideleft");
                $("#invButton").css('display', 'none');
                
                $("body").css('overflow', 'visible');
                toggleNav = true;
            } else {
                $("").addClass("slideright");
                $("").removeClass("slideleft");

                
                $("").css('overflow', 'hidden');
                toggleNav = false;
            }
        });
    
    $('#invButton').hover(function(){
        $("nav").css('width', '55%');
        //setTimeout(function(){$("nav").css('width', '56%');}, 2000);
    });
    
    $('#invButton').mouseleave(function(){
        $("nav").css('width', '56%');
    });
    
    $('#imgButton').hover(function(){
    $("nav").css('width', '55%');
        //setTimeout(function(){$("nav").css('width', '56%');}, 2000);
    });
    
    $('#imgButton').mouseleave(function(){
        $("nav").css('width', '56%');
    });
});