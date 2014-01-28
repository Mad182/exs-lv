<!-- START BLOCK : main-runescape-header -->
<h1 style="margin:-20px auto 20px">RuneScape jaunākie raksti</h1>
<!-- END BLOCK : main-runescape-header -->

{runescape-news}

<h2><strong>Jaunākie / pēdējie komentētie raksti</strong></h2>
<!-- START BLOCK : recent-page-list-default -->
{content}
<!-- END BLOCK : recent-page-list-default -->


<!-- START BLOCK : recent-page-list -->
<div class="article-column"{column-style}>
    <!-- START BLOCK : pagelist-runtime-bind -->
    <script type="text/javascript">
        $(document).ready(function() {
            $('.news-col-newest li').on('click', 'a', function(e) {
                $pagelist = $(this).parent().parent().parent();
                $.getJSON('/?type=newest&page=57&_=1', function(response) {                
                    if (response.status == 'success') {
                        $pagelist.wrap('<span>').parent().html(response.content);
                    }
                });
                e.preventDefault();
            });
        });
    </script>
    <!-- END BLOCK : pagelist-runtime-bind -->
    <div style="height:7px;width:70%;margin:5px auto;background:rgb(195, 208, 228)"></div>
    <ul class="official-news">
    <!-- START BLOCK : list-page -->
        <li style="padding-left:12px">{page-avatar}
            <p class="news-p">
                <a class="news-title" href="/read/{strid}">{title}</a>
                <span class="news-date">{additional-info}</span>
            </p>
        </li>
    <!-- END BLOCK : list-page -->
    </ul>    
    <!-- START BLOCK : pagelist-hidden -->
    <p class="pagelist">{pages}</p>
    <!-- END BLOCK : pagelist-hidden -->
    <div style="height:7px;width:70%;margin:5px auto;background:rgb(195, 208, 228)"></div>
</div>
<!-- END BLOCK : recent-page-list -->