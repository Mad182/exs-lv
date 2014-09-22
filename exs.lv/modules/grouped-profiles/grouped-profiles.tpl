<!-- START BLOCK : content-info -->
<p class="note">Šī sadaļa paredzēta, lai kopā saistītu tos profilus, kurus izmanto viens lapas lietotājs. Sasaistot profilus kopā, pie kāda profila bloķēšanas varēs norādīt, lai uzreiz bloķēti tiktu visi šī lietotāja profili.<br><br>Arī pašiem ērtāk redzēt, kurš cik daudz profilus izveidojis, kā arī tos meklēt sarakstā.<br><br><strong>Nebūtu vēlams, ka informācija par šādas sistēmas esamību nonāktu ārpus moderatoru diskusijām.</strong></p>
<!-- END BLOCK : content-info -->

<!-- START BLOCK : new-profile-form -->
<div class="new-main">
    <div style="float:left;width:45%">
        <form method="post" action="/{category-url}/search">
            <p class="small-text">Profila meklēšana pēc ID:</p>
            <p>
                <input type="text" name="user_id">
                <input type="submit" name="submit" class="danger button" value="Meklēt">
            </p>
        </form>
    </div>
    <div style="float:right;width:45%">
        <form method="post" action="/{category-url}/add-main">
            <p class="small-text">Pievieno galveno profilu, norādot profila ID:</p>
            <p>
                <input type="text" name="userid" value="{userid}">
                <input type="submit" name="submit" class="danger button" value="Pievienot">
            </p>
        </form>
    </div>
    <div class="clearfix"></div>
</div>
<!-- END BLOCK : new-profile-form -->

<!-- START BLOCK : new-child-form -->
<div class="fancy-container">
    <p class="fancy-title">Cita profila piesaiste</p>
    <p class="fancy-info" style="width:50%">
        Lai iegūtu profila ID, atver šī lietotāja profilu un nokopē to no adreses.<br><br>
        Piemēram, ja adrese ir <i>exs.lv/user/115</i>, tad ID ir <i>115</i>.
    </p>
    <p><strong>Galvenais profils:</strong>&nbsp;{main-profile}</p>
    <form method="post" action="/{category-url}/add-child/{main-id}">
        <label for="child_id" style="font-weight:bold">Piesaistāmā profila ID:</label><br>
        <input id="child_id" style="position:relative;top:3px" type="text" name="child_id" value=""><br>
        <input class="button primary" style="position:relative;top:6px" type="submit" name="submit" value="Pievienot">
    </form>
</div>
<!-- END BLOCK : new-child-form -->

<!-- START BLOCK : edit-description -->
<div class="fancy-container">
    <p class="fancy-title">Saistīto profilu apraksta rediģēšana</p>
    <p><strong>Galvenais profils:</strong>&nbsp;{main-profile}</p>
    <form method="post" action="/{category-url}/edit/{main-id}">
        <label for="description" style="font-weight:bold">Apraksts:</label><br>
        <textarea id="description" class="profiles-description" name="description">{description}</textarea>
        <br>
        <input class="button primary" type="submit" name="submit" value="Atjaunot">
        <input class="button danger" type="submit" onClick="javascript:$.fancybox.close();return false;" name="submit" value="Atcelt">
    </form>
</div>
<!-- END BLOCK : edit-description -->

<!-- START BLOCK : delete-confirmation -->
<div class="fancy-container">
    <p class="fancy-title">Profilu grupas dzēšana</p>
    <p class="fancy-info" style="width:50%">
        Šai grupai ir piesaistīti <span style="color:red"><strong>{profile-count}</strong></span> profili.
    </p>
    <p><strong>Galvenais profils:</strong>&nbsp;{main-profile}</p>
    <p><strong>Vai tiešām vēlies šo grupu dzēst?</strong></p>
    <form method="post" action="/{category-url}/delete-group/{main-id}">
        <input class="button primary" type="submit" name="submit" value="Dzēst">
        <input class="button danger" type="submit" onClick="javascript:$.fancybox.close();return false;" name="submit" value="Atcelt">
    </form>
</div>
<!-- END BLOCK : delete-confirmation -->

<!-- START BLOCK : no-profiles -->
<p class="note">Nav neviena pievienota profila.</p>
<!-- END BLOCK : no-profiles -->

<!-- START BLOCK : scroll-to -->
<script type="text/javascript">
    $(document).ready(function() {
        var aTag = $({main-id});
        aTag.next().removeClass('is-hidden'); 
        $('html, body').animate({scrollTop: aTag.offset().top}, 'slow');
    });
</script>
<!-- END BLOCK : scroll-to -->

<!-- START BLOCK : profile-list -->
<table id="profile-list" class="mod-list-table">
    <tr style="font-weight:bold">
        <td style="width:30px">Nr.</td>
        <td style="width:250px">Main profils</td>
        <td class="centered" style="width:100px">Profilu skaits</td>
        <td class="centered" style="width:20px"></td>
        <td class="centered" style="width:100px">Iespējas</td>
    </tr>
    <!-- START BLOCK : a-profile -->
    <tr id="profile-{user_id}" class="main-profile">
        <td>{counter}</td>
        <td>
            <a href="/user/{user_id}">{user_nick}</a>
            <a class="show-children" href="javascript:void(0);return false">
                <img src="/bildes/fugue-icons/arrow-down.png" title="Skatīt piesaistītos profilus" alt="">
            </a>
        </td>
        <td class="centered">{profile_count}</td>
        <td class="centered"></td>
        <td class="centered" style="position:relative">
            <a class="connect-profile" href="/{category-url}/add-child/{ug_id}">
                <img src="/bildes/fugue-icons/sql-join-left.png" title="Piesaistīt profilu" alt="">
            </a>
            <a href="/user/{user_id}/block">
                <img src="/bildes/fugue-icons/auction-hammer.png" title="Skatīt bloķēšanas sadaļu" alt="">
            </a>
            <a class="edit-description" href="/{category-url}/edit/{ug_id}">
                <img src="/bildes/fugue-icons/script--plus.png" title="Labot grupas komentāru" alt="">
            </a>
            <a class="delete-group" href="/{category-url}/delete-group/{ug_id}">
                <img src="/bildes/fugue-icons/bin-full.png" title="Dzēst grupu" alt="">
            </a>
        </td>
    </tr>
    <tr class="is-hidden">
        <td colspan="5">
            <!-- START BLOCK : all-children -->
            <div class="child-block">
                <p style="float:right"><strong>Redzēts:</strong> {user_seen}, <strong>pēdējā IP:</strong> {user_lastip}</p>
                {description}
                
                <!-- START BLOCK : no-children -->
                <p style="font-weight:bold">Šim profilam citu piesaistītu profilu nav.</p>
                <!-- END BLOCK : no-children -->

                <table class="child-table clearfix">
                    <!-- START BLOCK : child-table-header -->
                    <tr style="font-weight:bold">
                        <td style="width:20px">ID</td>
                        <td style="width:120px">Lietotājvārds</td>
                        <td style="width:120px">Redzēts</td>
                        <td style="width:120px">Pēdējā IP</td>
                        <td style="width:80px">&nbsp;</td>
                    </tr>
                    <!-- END BLOCK : child-table-header -->
                    <!-- START BLOCK : a-child -->
                    <tr>
                        <td>{child_id}</td>
                        <td><a href="/user/{child_id}">{child_nick}</a></td>
                        <td>{child_seen}</td>
                        <td>{child_lastip}</td>
                        <td>
                            <a class="confirm" href="/{category-url}/change-main/{child_parent}">
                                <img src="/bildes/fugue-icons/arrow-135-medium.png" title="Mainīt vietām ar galveno profilu" alt="">
                            </a>
                            <a href="/user/{child_id}/block">
                                <img src="/bildes/fugue-icons/auction-hammer.png" title="Skatīt bloķēšanas sadaļu" alt="">
                            </a>
                            <a class="confirm" href="/{category-url}/delete-child/{child_parent}/{user_id}">
                                <img src="/bildes/fugue-icons/bin-full.png" title="Atsaistīt profilu" alt="">
                            </a>
                        </td>
                    </tr>
                    <!-- END BLOCK : a-child -->
                </table>
            </div>
            <!-- END BLOCK : all-children -->
        </td>
    </tr>
    <!-- END BLOCK : a-profile -->
</table>
<!-- END BLOCK : profile-list -->
