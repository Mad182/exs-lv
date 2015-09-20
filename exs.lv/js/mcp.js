/**
 *  Javascript/jQuery funkcionalitāte, kas nepieciešama exs.lv
 *  administratīvajās sadaļās, kuras pieejamas tikai modiem/adminiem.
 */

$(document).ready(function($) {
    
    /*
	* Ciļņu pārslēgs profilu pārvaldības sadaļās. 
	* ------------------------------------------------- */
    $('#prof_mgmt_tabs').on('click', 'a', function(e) {    
        e.preventDefault();
        $clicked = $(this);
        if ($clicked.hasClass('active')) {
            return;
        }
        $content = $('#prof_mgmt');
        $content.fadeTo(250, 0.7);
        $.getJSON($(this).attr('href') + '?_=1&load=main', function(response) {
            if (typeof response.content !== 'undefined') {
                $clicked.addClass('active');
                $clicked.parent().siblings().children('a').removeClass('active');
                $content.html(response.content);
            }
            $content.fadeTo(150, 1);
        });
    });
    
    /*
	* Profilu pārvaldība - piesaistīto profilu saraksta atvēršana.
	* ------------------------------------------------------------- */
	$('#profile-list').on('click', '.show-children', function(e) {  
		$(this).parent().parent().next().toggle();    
		e.preventDefault();
	});

    /*
	* Profilu pārvaldība - dažādas profilu grupas iespējas.
	* ------------------------------------------------------------- */
	$('#profile-list').on(
        'click',
        '.connect-profile, .delete-group, .edit-description',
        function(e) {
            e.preventDefault();
            var addr = $(this).attr('href');        
            $.get(addr, function(data) {
                $.fancybox(data);
            });
        }
    );
});
