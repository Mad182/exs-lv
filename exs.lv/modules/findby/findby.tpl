<!-- START BLOCK : mcp-profiles-tabs -->
<ul id="prof_mgmt_tabs" class="tabs">
	<li><a href="/findby" class="active">Profilu meklētājs</a></li>
    <!-- START BLOCK : grouped-enabled -->
	<li><a href="/grouped-profiles">Profilu sasaiste</a></li>
    <!-- END BLOCK : grouped-enabled -->
</ul>
<!-- END BLOCK : mcp-profiles-tabs -->

<!-- START BLOCK : mcp-find-outer-start -->
<div id="prof_mgmt">
<!-- END BLOCK : mcp-find-outer-start -->

<!-- START BLOCK : mcp-find-profiles -->
<p class="note" style="margin-top:20px">
    <span style="color:orangered">*</span> Šajos laukos daļu no kritērija var aizstāt ar %, piemēram, <em>192.%.1.%</em> vai <em>Mo%lla</em>.
</p>
<div id="findby">
    <form id="search-nick" method="post" action="/{category-url}">
        <p>Lietotājvārds:</p>
        <p class="form-input-box">
            <input type="text" name="nick" value="{nick}">
            <input type="submit" name="submit" class="button primary" value="Meklēt">
        </p>
    </form>
    <form id="search-mail" method="post" action="/{category-url}">
        <p>E-pasts:</p>
        <p class="form-input-box">
            <input type="text" name="mail" value="{mail}">
            <input type="submit" name="submit" class="button primary" value="Meklēt">
        </p>
    </form>
    <form id="search-ip" method="post" action="/{category-url}">
        <p>Pēdējā lietotā IP: <span style="color:orangered">*</span></p>
        <p class="form-input-box">
            <input type="text" name="ip" value="{ip}">
            <input type="submit" name="submit" class="button primary" value="Meklēt">
        </p>
    </form>
    <form id="search-vip" method="post" action="/{category-url}">
        <p>Vispār lietota IP: <span style="color:orangered">*</span></p>
        <p class="form-input-box">
            <input type="text" name="vip" value="{vip}">
            <input type="submit" name="submit" class="button primary" value="Meklēt">
        </p>
    </form>
    <form id="search-agent" method="post" action="/{category-url}">
        <p>User-agent: <span style="color:orangered">*</span></p>
        <p class="form-input-box">
            <input type="text" name="useragent" value="{useragent}">
            <input type="submit" name="submit" class="button primary" value="Meklēt">
        </p>
    </form>

    <!-- START BLOCK : search-results -->
    <p class="note">Nospiežot uz atrasta lietotājvārda, aplūkojama plašāka informācija!</p>
    <table id="user-results">
        <tr class="th">
            <td>Profils</td>
            <td>{ip-type}</td>
            <td>E-pasts</td>
            <td>Karma</td>
            <td>Dienas</td>
        </tr>
        <!-- START BLOCK : search-result -->
        <tr>
            <td class="get-user-info" data-id="{id}">
                <a href="/user/{id}">{nick}</a>
            </td>
            <td>{lastip}</td>
            <td>{mail}</td>
            <td>{karma}</td>
            <td>{date}</td>
        </tr>
        <tr class="is-hidden">
            <td id="data-{id}" class="wider-row" colspan="5"></td>
        </tr>
        <!-- END BLOCK : search-result -->
    </table>
    <!-- END BLOCK : search-results -->
</div>
<!-- END BLOCK : mcp-find-profiles -->

<!-- START BLOCK : mcp-find-outer-end -->
</div>
<!-- END BLOCK : mcp-find-outer-end -->
