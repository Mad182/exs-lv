<!-- START BLOCK : steam-->
<h1>Šobrīd spēlē spēles Steam</h1>

    <!-- START BLOCK : steam-login-->
    <p>
        <a href="/steam-login">
            Vai vēlies, lai arī citi redz tavu aktivitāti?
        </a>
    </p>
    <!-- END BLOCK : steam-login-->

    <!-- START BLOCK : steam-game-row-->
    <div id="steam-online">

        <!-- START BLOCK : steam-game-->
        <div class="game">
            <div class="hero-image">
                <img src="http://cdn.akamai.steamstatic.com/steam/apps/{game-id}/header.jpg">
            </div>
            <div class="player-list">
                <ul>

                    <!-- START BLOCK : steam-player-->
                    <li>
                        <a href="{profile-url}" class="steam-link">
                            <img src="/bildes/steam-ico.png" alt="steam profils">
                        </a>
                        <a href="/user/{id}">
                            {nick}
                        </a>
                    </li>
                    <!-- END BLOCK : steam-player-->

                </ul>
            </div>
        </div>
        <!-- END BLOCK : steam-game-->

    </div>
    <!-- END BLOCK : steam-game-row-->

<!-- END BLOCK : steam-->