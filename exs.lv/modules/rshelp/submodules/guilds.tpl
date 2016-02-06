<!-- START BLOCK : guilds-intro-text -->
<div class="rs-intro" style="margin-bottom:20px">
    <span class="vc-ghost-item"></span>
    <img id="wise-old-man" class="vc-item" src="{img-server}/bildes/runescape/intro/wise-old-man.png" title="Wise Old Man" alt="">
    <p class="vc-item" style="max-width:90%">Ģildes ir īpaši iekārtotas ēkas, kurās var iekļūt tikai attiecīgajā prasmē sevišķi iepratušies spēlētāji, 
    ja tie sasnieguši noteiktu prasmes līmeni, izpildījuši grūtu kvestu vai sakrājuši noteiktu Quest Points skaitu.<br><br>Katrai ģildei ir savas unikālās iespējas, ieskaitot vieglāku piekļuvi rīkiem un resursiem, kas veltīti attiecīgajai prasmei, 
    un veikaliem, kuros pārdod citur nenopērkamus priekšmetus.</p>
</div>
<div class="simple-note" style="margin-bottom:20px">
  <img style="margin-right:7px;position:relative;left:3px;top:1px" src="{img-server}/bildes/runescape/star-p2p-small.png" alt="">
    Ģilde pieejama tikai maksājošajiem spēlētājiem
</div>
<!-- END BLOCK : guilds-intro-text -->


<!-- START BLOCK : no-guilds-found -->
<p class="simple-note">
    Sadaļai nav pievienota neviena ģilde.
</p>
<!-- END BLOCK : no-guilds-found -->


<!-- START BLOCK : guilds-block -->
<div style="margin-left:20px">
    <!-- START BLOCK : guild -->
    <div class="guide-block{newline}">
        <p class="guide-title">{title}{members_only}</p>
        <a href="{strid}"><img{cluetip} src="{img-server}/bildes/runescape/guilds/{image}" title="{cluetip_title}" alt=""></a>
        <p><span>Vieta:</span> {starting_point}</p>
        <p style="padding-bottom:5px"><span>Prasības:</span>&nbsp;{extra}</p>
    </div>
    <!-- END BLOCK : guild -->

    <!-- START BLOCK : not-a-guild -->
    <div class="guide-block">
        <p class="guide-title">Citi raksti</p>
        <a href="javascript:void(0);">
            <img src="{img-server}/bildes/runescape/guilds/other.png" title="Citi ģilžu raksti" alt="">
        </a>
        <ul>
            <!-- START BLOCK : not-guild-page -->
            <li><a class="page" href="{strid}">{title}</a></li>
            <!-- END BLOCK : not-guild-page -->
        </ul>
    </div>
    <!-- END BLOCK : not-a-guild -->
</div>
<!-- END BLOCK : guilds-block -->
