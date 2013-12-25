/* ik pēc noteikta laika atjauno runescape faktu */
function refresh_fact(elem) {    
    $.get('/rsfacts?_=1', function(response) {
      if (response.length < 300) {
        elem.html('<span>RS fakts:</span> ' + response);
      }
    });
}

$(document).ready(function () {
    
    /* atjauno runescape random faktu */
    var $rsfact = $('#random-fact');
    refresh_fact($rsfact);
    setInterval(function() {
        refresh_fact($rsfact);
    }, 7000);
    
    /* podziņa ātrai scrollošanai uz augšu */
    var $elem = $('#scroll-up');
    $(window).scroll(function() {
        if( $(this).scrollTop() > 100)
            $elem.stop().animate({bottom: '40px', opacity: 0.6}, 500);
        else
            $elem.stop().animate({bottom: '200px', opacity: 0}, 200, function() {
                $(this).css({bottom:'-100px'});
            });
    });
    $elem.click(function() {
        $('html, body').stop().animate({scrollTop: 0}, 500, function() {
           $elem.stop().animate({bottom: '500px', opacity: 0}, 200, function() {
                $(this).css({bottom:'-100px'});
           });
        });
    });
    
    /* runescape augšējās navigācijas pielīmēšana */
    jQuery(function($) {
    
        var $topmenu    = $('#top-menu');
        var $header     = $('#header');
        var height      = $topmenu.offset().top - 32;
        
        function fixDiv() {                  
          if ($(window).scrollTop() >= height ) {
            $topmenu.css({'position': 'fixed', 'top': '32px'});
            $header.css({'margin-bottom': '35px'});
          }
          else {
            $topmenu.css({'position': 'relative', 'top': 'auto'});
            $header.css({'margin-bottom': 'auto'});
          }
        }
        $(window).scroll(fixDiv);
        fixDiv();
    });
    
});
