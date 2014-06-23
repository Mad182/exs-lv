/**
 *  Parādīs adreses saturu fancybox logā
 */
function fancyContent(url) {
    $.getJSON(url, function(response) {
        if (response.state == 'success') {
            $.fancybox(response.content);
        } else {
            if (response.message) {
                alert(response.message);
            } else {
                alert('Kļūda, ielādējot saturu.');
            }
        }
    });
}

$(document).ready(function () {
    
    /**
     *  RuneScape random fakta atjaunotājs
     */
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
    
    /**
     *  Pielīmēs RuneScape projekta augšējo navigāciju
     */
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
    
    /**
     *  Prasmju sadaļas pārvietošanās pa lapām
     */
    $('a.skill-pager').live('click', function() {
        var elem = $(this).parent().parent();
        elem.fadeTo(250, 0.5);
        elem.load($(this).attr('href'), function() {
            elem.fadeTo(250, 1);
        });
        return false;
    });    
    
    /**
     *  Parāda sērijai piesaistītos kvestus
     */
    $('.series-list').on('click', '.related-quests', function(e) {
        e.preventDefault();
        fancyContent($(this).attr('href') + '?_=1');
    });
    $('body').on('click', '.show-series-quests', function(e) {
        e.preventDefault();
        parent.$.fancybox.close();
        fancyContent($(this).attr('href') + '?_=1');
    });
    
    /**
     *  Parāda izvēlni ar visiem piesaistāmajiem kvestiem
     */
    $('body').on('click', '.change-list', function(e) {     
        e.preventDefault();
        parent.$.fancybox.close();
        fancyContent($(this).attr('href') + '?_=1');
    });
    
    /**
     *  Atjauno sērijas kvestu secību
     */
    $('body').on('submit', '#quest-order', function(e) {
        e.preventDefault();

        $form = $(this);
        
        $.ajax({
			type: "POST",
			dataType: "json",
			url: $form.attr('action') + '?_=1',
			data: $form.serialize(),
			success: function(response) {
                if (response.state == 'error') {
                    alert(response.message);
                } else {
                    $form.parent().parent().replaceWith(response.content);
                    $('.response').html('Secība atjaunota');
                    setTimeout(function() {
                        $('.response').html('');
                    }, 4000);
                }
			}
		});
    });
    
    /**
     *  Pievieno/dzēš sērijai piesaistītu kvestu
     */
    $('body').on('click', '.set-quest', function(e) {    
        e.preventDefault();
        
        $a_clicked = $(this);
        $li_parent = $(this).parent();
        
        $.ajax({
            url: $(this).attr('href') + '?_=1',
            dataType: 'json',
            async: false,
            success: function(response) {
                if (response.state == 'error') {
                    alert(response.message);
                    return;
                }                    
                if (response.type == 'del') {
                    $li_parent.removeClass('mark-removed');
                    $li_parent.removeClass('mark-neutral');
                    $li_parent.addClass('mark-added');
                    $('#series-' + response.series_id + ' .no-quests').remove();
                } else {
                    $li_parent.addClass('mark-removed');
                    $li_parent.removeClass('mark-added');
                }
                $a_clicked.attr('href', response.url);
            }
        });
    });
    
    /**
     * Dzēš ierakstu par RuneScape pamācību
     */
    $('.del-page').live('click', function(e) {

        e.preventDefault();
        
        if (confirm('Vai tiešām vēlies šo ierakstu dzēst?')) {

            $elem = $(this);
            
            $.getJSON($elem.attr('href') + '?_=1', function(response) {
                if (response.state == 'success') {
                    $elem.parent().parent().hide('slow');
                } else {
                    alert(response.content);
                }
            });
        }
    });
    
    /**
     * Slēpj/parāda ierakstu par RuneScape pamācību
     */
    $('.hide-page').live('click', function(e) {

        $elem = $(this);
        
        $.getJSON($elem.attr('href') + '?_=1', function(response) {
            if (response.state == 'success') {
                if (response.content == 'hidden') {
                    $elem.parent().parent().attr('style', 'opacity:0.5');
                } else {
                    $elem.parent().parent().attr('style', 'opacity:1.0');
                }
            } else {
                alert(response.content);
            }
        });
        
        e.preventDefault();
    });    
});
