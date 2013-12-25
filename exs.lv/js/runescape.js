/* ik pēc noteikta laika atjauno runescape faktu */
function refresh_fact() {    
    $.get('/rsfacts?_=1', function(response) {
      if (response.length < 300) {
        $('#random-fact').html('<span>RS fakts:</span> ' + response);
      }
    });
}

$(document).ready(function () {
    
    refresh_fact();
    setInterval("refresh_fact()", 7000);

    /* scrollošanas uz augšu podziņa */
    $(window).scroll(function() {
        if( $(this).scrollTop() > 100)
            $('#scroll-up').stop().animate({bottom: '40px'}, 500);
        else
            $('#scroll-up').stop().animate({bottom: '-100px'}, 500);
    });
    $('#scroll-up').click(function() {
        $('html, body').stop().animate({scrollTop: 0}, 500, function() {
           $('#scroll-up').stop().animate({bottom: '-100px'}, 500);
        });
    });
    
    /* runescape augšējās navigācijas pielīmēšana */
    jQuery(function($) {
    
        var $topmenu    = $('#top-menu');  
        var $height     = $topmenu.offset().top - 32;
        
        function fixDiv() {                  
          if ($(window).scrollTop() >= $height ) {
            $topmenu.css({'position': 'fixed', 'top': '32px'});
            $('#header').css({'margin-bottom': '35px'});
          }
          else {
            $topmenu.css({'position': 'relative', 'top': 'auto'});
            $('#header').css({'margin-bottom': 'auto'});
          }
        }
        $(window).scroll(fixDiv);
        fixDiv();
    });
    
});
