/* ik pēc noteikta laika atjauno runescape faktu */
function refresh_fact(elem, status) {   
    if (status == false) {
        $.getJSON('/rsfacts?_=1', function(response) {
          if (response.state == 'success') {
            elem.html('<span>RS fakts:</span> ' + response.content);
          }
        });
    }
}

$(document).ready(function () {
    
    /* atjauno runescape random faktu */
    var facts_stopped = false;
    var $rsfact = $('#random-fact');
    var $fact_container = $('.facts-box');
    
    refresh_fact($rsfact, facts_stopped);
    setInterval(function() {
        refresh_fact($rsfact, facts_stopped);
    }, 7000);
    
    $fact_container.live('mouseover', function() {
        facts_stopped = true;
    }).live('mouseout', function() {
        facts_stopped = false;
    });
    
    /* podziņa ātrai scrollošanai uz augšu */
    var $elem = $('#scroll-up');
    $(window).scroll(function() {
        if( $(this).scrollTop() > 100) {
            $elem.stop().animate({bottom: '40px', opacity: 0.6}, 500);
        }
        else {
            $elem.stop().animate({bottom: '200px', opacity: 0}, 200, function() {
                $(this).css({bottom:'-100px'});
            });
        }
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
    
    /* prasmju sadaļas pārvietošanās pa lapām */
    $('a.skill-pager').live('click', function() {
        var elem = $(this).parent().parent();
        elem.fadeTo(250, 0.5);
        elem.load($(this).attr('href'), function() {
            elem.fadeTo(250, 1);
        });
        return false;
    });    
});