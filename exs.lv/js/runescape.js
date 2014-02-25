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

    /* parāda/paslēpj sērijai piesaistītos kvestus */
    $('.show-related-quests').live('click', function(e) {    
        $(this).parent().parent().next().toggle('slow');    
        e.preventDefault();
    });
    
    /* parāda izvēlni ar visiem piesaistāmajiem kvestiem */
    $('.open-quest-list').live('click', function(e) {
    
        var return_state = false;
        
        $.getJSON($(this).attr('href') + '?_=1', function(response) {
            if (response.state == 'success') {
                $.fancybox(response.content);
            } else {
                alert(response.state);
            }
        });
        e.preventDefault();
    });
    
    /* piesaista/atsaista sērijai kvestu */
    $('.related-quest').live('click', function(e) {
    
        e.preventDefault();
        
        $a_clicked = $(this);
        $li_parent = $(this).parent();
        
        if ( ! confirm('Vai tiešām vēlies veikt šo darbību?') )            
            return false;
        
        $.ajax({
            url: $(this).attr('href') + '?_=1',
            dataType: 'json',
            async: false,
            success: function(response) {
                if (response.state != 'success') {
                    alert('Error: wrong parameters given!');
                }
                    
                if (response.type == 'added') {
                    $li_parent.addClass('mark-added');
                    $li_parent.removeClass('mark-removed');
                    
                    if (response.series_id && response.content) {
                        $('#series-' + response.series_id).children('td', 0).children('form', 0).children('.related-quests').append(response.content);
                    }
                }
                else {
                    $li_parent.addClass('mark-removed');
                    $li_parent.removeClass('mark-added');
                    
                    if (response.quest_id) {
                        $('#quest-' + response.quest_id).parent().remove();
                    }
                }
                
                if (response.url && response.url_inner) {
                    $a_clicked.attr('href', response.url).html(response.url_inner);
                }
            }
        }); 
    });
    
});