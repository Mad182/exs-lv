$(document).ready(function () {
    
    /* atjauno runescape random faktu */    
    var $fact_box = $('.facts-box');
    $('.fetch-new-fact').live('click', function(e) {
        
        $fact_box.children('p').fadeTo(150, 0.5);
        
        $.getJSON('/rsfacts?_=1', function(response) {
            if (response.state) {
                $fact_box.html('<p>' + response.content + '</p>');
            }
        });        
        e.preventDefault();
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