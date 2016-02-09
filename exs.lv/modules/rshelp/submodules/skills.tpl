<!-- START BLOCK : list-tabs -->
<ul class="tabs" style="margin:0 0 15px">
    <li><a class="{tab-skills}" href="/{category-url}">Prasmes</a></li>
    <li><a class="{tab-xptable}" href="/{category-url}/xp-table">XP tabula</a></li>
    <li><a class="{tab-facts}" href="/{category-url}/facts">Prasmju fakti</a></li>
</ul>
<!-- END BLOCK : list-tabs -->

<!-- START BLOCK : skills-intro-text -->
<div class="rs-intro">  
    <span class="vc-ghost-item"></span>
    <img id="sage" class="vc-item" src="{img-server}/bildes/runescape/intro/lumby_sage.png" title="Lumbridge Sage" alt="Lumbridge Sage">
    <p class="vc-item" style="max-width:80%">Gīlinora ir neparasta zeme pat vienkāršiem iemītniekiem, kurus neinteresē nekas cits kā vien savu piemājas dobīšu uzaršana un izravēšana, kas neprasa daudz pūļu. Tomēr lielākiem dēkaiņiem, kas ceļo tālu un meklē piedzīvojumus, cīnās ar asinskāriem nezvēriem vai attīsta savu veiklību, papildu iemaņas ir ļoti būtiskas.<br><br>No vienkāršas ugunskura iekuršanas un ēdmaņas zvejošanas līdz nesalaužamu slēdzeņu atdarīšanai un nāvīgi maģisku virumu brūvēšanai. Un tas ir tikai sākums!<br><br>Līdz ar <strong style="color:#012542">{latest-skill}</strong> izlaišanu spēlē pavisam pieejamas <strong style="color:#012542">{skill-count}</strong> prasmes (max xp: <strong>{max-xp}</strong>).</p>
</div>
<div class="simple-note" style=">
    <img style="margin:3px 7px 0 0" src="{img-server}/bildes/runescape/star-p2p-small.png" alt="">Prasme pieejama tikai maksājošajiem spēlētājiem
</div>
<!-- END BLOCK : skills-intro-text -->

<!-- START BLOCK : no-guides-found -->
<p class="simple-note">
    Sadaļai nav pievienota neviena prasme.
</p>
<!-- END BLOCK : no-guides-found -->

<!-- START BLOCK : js-skill-pages -->
    <!-- START BLOCK : skill-page -->
    <a title="{title}" href="/read/{strid}">{short-title}</a><br>
    <!-- END BLOCK : skill-page -->
    <div class="skill-pages">
        <!-- START BLOCK : page-block -->
        <a class="skill-pager" href="/prasmes?skill={skill-id}&amp;page=2">Tālāk &raquo;&raquo;</a>
        <!-- END BLOCK : page-block -->
        <!-- START BLOCK : page-block-back-->
        <a class="skill-pager" href="/prasmes?skill={skill-id}&amp;page=1">&lsaquo;&lsaquo; Atpakaļ</a>
        <!-- END BLOCK : page-block-back -->
    </div>
<!-- END BLOCK : js-skill-pages -->

<!-- START BLOCK : skills -->
<!-- START BLOCK : skill -->
<div class="skill-block"{linebreak}>
    <div class="skill-info" style="width:50%">
        <img class="skill-icon" src="{img}" title="{cat_title}" alt="">
        <p class="skill-name">{cat_title} {members_only}</p>
        {info}
    </div>
    <div class="skill-links" style="width:45%">
        <p>Saistītie raksti</p>
        <div>
            <!-- START BLOCK : new-skill-guide -->
            <a title="{page_title}" href="/read/{strid}">{page_title}</a>
            <!-- END BLOCK : new-skill-guide -->
            <!-- START BLOCK : skill-pages -->   
            <div class="skill-pages">{next}</div>
            <!-- END BLOCK : skill-pages -->
        </div>        
    </div>
</div>
<!-- END BLOCK : skill -->
<div class="clearfix"></div>
<!-- END BLOCK : skills -->

<!-- START BLOCK : skills-facts -->
<p class="simple-note">
    Šajā sadaļā apkopoti ar prasmēm saistīti fakti.
</p>
<div id="skills-facts">
    <ul>
        <li>Katrā prasmē iespējams iegūt līdz pat <strong>200,000,000</strong> pieredzes punktiem.</li>
        <li>Dungeoneering un Invention ir vienīgās prasmes, kurās spēlētājs var uztrenēt <strong>120.</strong> līmeni. Citās prasmēs iespējams sasniegt tādu pašu pieredzes punktu apjomu, saglabājot <strong>99.</strong> līmeni.</li>
        <li>Pašreiz līdz ar <strong>Invention</strong> prasmes ieviešanu augstākais iegūstamais Total līmenis ir <strong>2,715</strong>. RuneScape Classic tas bija <strong>1782</strong>.</li>
        <li>Sasniedzot 99. līmeni, spēlētājs pie prasmes skolotāja par 99,000gp var iegādāties attiecīgās prasmes apmetni.</li>
        <li>120. līmenim nepieciešamais pieredzes punktu daudzums ir 8 reizes lielāks nekā 99. līmenim.</li>
        <li>Sasniedzot 99. līmeni vairāk nekā vienā prasmē, visi iegūtie prasmju apmetņi iegūst apzeltītas maliņas.</li>
        <li style="background-image:none"><img id="wagtail" src="/bildes/runescape/intro/tropical_wagtail.png" alt=""></li>
        <li>Apzeltītam apmetnim ir "boost" iespēja, ar kuru uz neilgu laiku attiecīgā prasme tiek palielināta līdz 100. līmenim.</li>
        <li>Spēlētājam kādā prasmē sasniedzot 99. līmeni, par to uzzina visi spēlētāji, kuri atrodas tajā pašā spēles serverī.</li>
        <li>Sasniedzot 120. Dungeoneering vai Invention līmeni, 99. līmeni visās prasmēs vai iegūstot spēju valkāt Completitionist Cape, sasniegums tiek izziņots visos RuneScape serveros.</li> 
        <li>Dažiem spēlētājiem <strong>Constitution</strong> līmenis ir mazāks par 10. 
            RuneScape Classic pirmssākumos noteikumu pārkāpējus sodīja ar prasmju līmeņu samazināšanu, kas nozīmē, ka arī to <strong>Hitpoints</strong> (Constitution sākotnējais nosaukums) prasme, tika samazināta. Šādi RuneScape profili tagad ir ļoti reti.</li>
    </ul>
</div>
<!-- END BLOCK : skills-facts -->

<!-- START BLOCK : skills-xp-table -->
<p class="simple-note">
    Šajā tabulā redzams nepieciešamais XP punktu daudzums katram iespējamajam prasmes līmenim.<br>
    Maksimālais iegūstamais XP punktu daudzums vienā prasmē ir 200,000,000.<br><br>
    - <a href="#xp-past-120">XP tabula pēc 120. prasmes&nbsp;<sup class="new">NEW</sup></a><br>
    - <a href="#xp-invention">Invention XP tabula&nbsp;<sup class="new">NEW</sup></a>
</p>
<table class="rslist xp-table">
    <tr class="listhead">
        <td style="width:80px">Līmenis</td>
        <td style="width:80px">Pieredze</td>
        <td style="width:80px">Līmenis</td>
        <td style="width:80px">Pieredze</td>
        <td style="width:80px">Līmenis</td>
        <td style="width:80px">Pieredze</td>
        <td style="width:80px">Līmenis</td>
        <td style="width:80px">Pieredze</td>
    </tr>
    <tr>
        <td class="number-2">1</td>
        <td>0</td>
        <td class="number-2">31</td>
        <td>14,833</td>
        <td class="number-2">61</td>
        <td>302,288</td>
        <td class="number-2">91</td>
        <td>5,902,831</td>
    </tr>
    <tr>
        <td class="number-2">2</td>
        <td>83</td>
        <td class="number-2">32</td>
        <td>16,456</td>
        <td class="number-2">62</td>
        <td>333,804</td>
        <td class="number-2">92</td>
        <td>6,517,253</td>
    </tr>
    <tr>
        <td class="number-2">3</td>
        <td>174</td>
        <td class="number-2">33</td>
        <td>18,247</td>
        <td class="number-2">63</td>
        <td>368,599</td>
        <td class="number-2">93</td>
        <td>7,195,629</td>
    </tr>
    <tr>
        <td class="number-2">4</td>
        <td>276</td>
        <td class="number-2">34</td>
        <td>20,224</td>
        <td class="number-2">64</td>
        <td>407,015</td>
        <td class="number-2">94</td>
        <td>7,944,614</td>
    </tr>
    <tr>
        <td class="number-2">5</td>
        <td>388</td>
        <td class="number-2">35</td>
        <td>22,406</td>
        <td class="number-2">65</td>
        <td>449,428</td>
        <td class="number-2">95</td>
        <td>8,771,558</td>
    </tr>
    <tr>
        <td class="number-2">6</td>
        <td>512</td>
        <td class="number-2">36</td>
        <td>24,815</td>
        <td class="number-2">66</td>
        <td>496,254</td>
        <td class="number-2">96</td>
        <td>9,684,577</td>
    </tr>
    <tr>
        <td class="number-2">7</td>
        <td>650</td>
        <td class="number-2">37</td>
        <td>27,473</td>
        <td class="number-2">67</td>
        <td>547,953</td>
        <td class="number-2">97</td>
        <td>10,692,629</td>
    </tr>
    <tr>
        <td class="number-2">8</td>
        <td>801</td>
        <td class="number-2">38</td>
        <td>30,408</td>
        <td class="number-2">68</td>
        <td>605,032</td>
        <td class="number-2">98</td>
        <td>11,805,606</td>
    </tr>
    <tr>
        <td class="number-2">9</td>
        <td>969</td>
        <td class="number-2">39</td>
        <td>33,648</td>
        <td class="number-2">69</td>
        <td>668,051</td>
        <td class="number-2">99</td>
        <td>13,034,431</td>
    </tr>
    <tr>
        <td class="number-2">10</td>
        <td>1,154</td>
        <td class="number-2">40</td>
        <td>37,224</td>
        <td class="number-2">70</td>
        <td>737,627</td>
        <td class="number-2">100</td>
        <td>14,391,160</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">11</td>
        <td>1,358</td>
        <td class="number-2">41</td>
        <td>41,171</td>
        <td class="number-2">71</td>
        <td>814,445</td>
        <td class="number-2">101</td>
        <td>15,889,109</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">12</td>
        <td>1,584</td>
        <td class="number-2">42</td>
        <td>45,529</td>
        <td class="number-2">72</td>
        <td>899,257</td>
        <td class="number-2">102</td>
        <td>17,542,976</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">13</td>
        <td>1,833</td>
        <td class="number-2">43</td>
        <td>50,339</td>
        <td class="number-2">73</td>
        <td>992,895</td>
        <td class="number-2">103</td>
        <td>19,368,992</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">14</td>
        <td>2,107</td>
        <td class="number-2">44</td>
        <td>55,649</td>
        <td class="number-2">74</td>
        <td>1,096,278</td>
        <td class="number-2">104</td>
        <td>21,385,073</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">15</td>
        <td>2,411</td>
        <td class="number-2">45</td>
        <td>61,512</td>
        <td class="number-2">75</td>
        <td>1,210,421</td>
        <td class="number-2">105</td>
        <td>23,611,006</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">16</td>
        <td>2,746</td>
        <td class="number-2">46</td>
        <td>67,983</td>
        <td class="number-2">76</td>
        <td>1,336,443</td>
        <td class="number-2">106</td>
        <td>26,068,632</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">17</td>
        <td>3,115</td>
        <td class="number-2">47</td>
        <td>75,127</td>
        <td class="number-2">77</td>
        <td>1,475,581</td>
        <td class="number-2">107</td>
        <td>28,782,069</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">18</td>
        <td>3,523</td>
        <td class="number-2">48</td>
        <td>83,014</td>
        <td class="number-2">78</td>
        <td>1,629,200</td>
        <td class="number-2">108</td>
        <td>31,777,943</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">19</td>
        <td>3,973</td>
        <td class="number-2">49</td>
        <td>91,721</td>
        <td class="number-2">79</td>
        <td>1,798,808</td>
        <td class="number-2">109</td>
        <td>35,085,654</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">20</td>
        <td>4,470</td>
        <td class="number-2">50</td>
        <td>101,333</td>
        <td class="number-2">80</td>
        <td>1,986,068</td>
        <td class="number-2">110</td>
        <td>38,737,661</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">21</td>
        <td>5,018</td>
        <td class="number-2">51</td>
        <td>111,945</td>
        <td class="number-2">81</td>
        <td>2,192,818</td>
        <td class="number-2">111</td>
        <td>42,769,801</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">22</td>
        <td>5,624</td>
        <td class="number-2">52</td>
        <td>123,660</td>
        <td class="number-2">82</td>
        <td>2,421,087</td>
        <td class="number-2">112</td>
        <td>47,221,641</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">23</td>
        <td>6,291</td>
        <td class="number-2">53</td>
        <td>136,594</td>
        <td class="number-2">83</td>
        <td>2,673,114</td>
        <td class="number-2">113</td>
        <td>52,136,869</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">24</td>
        <td>7,028</td>
        <td class="number-2">54</td>
        <td>150,872</td>
        <td class="number-2">84</td>
        <td>2,951,373</td>
        <td class="number-2">114</td>
        <td>57,563,718</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">25</td>
        <td>7,842</td>
        <td class="number-2">55</td>
        <td>166,636</td>
        <td class="number-2">85</td>
        <td>3,258,594</td>
        <td class="number-2">115</td>
        <td>63,555,443</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">26</td>
        <td>8,740</td>
        <td class="number-2">56</td>
        <td>184,040</td>
        <td class="number-2">86</td>
        <td>3,597,792</td>
        <td class="number-2">116</td>
        <td>70,170,840</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">27</td>
        <td>9,730</td>
        <td class="number-2">57</td>
        <td>203,254</td>
        <td id="xp-past-120" class="number-2">87</td>
        <td>3,972,294</td>
        <td class="number-2">117</td>
        <td>77,474,828</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">28</td>
        <td>10,824</td>
        <td class="number-2">58</td>
        <td>224,466</td>
        <td class="number-2">88</td>
        <td>4,385,776</td>
        <td class="number-2">118</td>
        <td>85,539,082</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">29</td>
        <td>12,031</td>
        <td class="number-2">59</td>
        <td>247,886</td>
        <td class="number-2">89</td>
        <td>4,842,295</td>
        <td class="number-2">119</td>
        <td>94,442,737</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">30</td>
        <td>13,363</td>
        <td class="number-2">60</td>
        <td>273,742</td>
        <td class="number-2">90</td>
        <td>5,346,332</td>
        <td class="number-2">120</td>
        <td>104,273,167</td>
    </tr>
</table>

<!-- START BLOCK : xp-table-past-120 -->
<p class="simple-note" style="margin:30px 0 10px;">
    Augstāk par 120. līmeni nevienā prasmē iegūt nav iespējams, tomēr nepieciešamais XP apjoms tādiem ir aprēķināms.
</p>
<table class="rslist xp-table">
    <tr id="xp-invention" class="listhead">
        <td style="width:80px">Līmenis</td>
        <td style="width:80px">Pieredze</td>
        <td style="width:80px">Līmenis</td>
        <td style="width:80px">Pieredze</td>
    </tr>
    <tr>
        <td class="number-2">121</td>
        <td>115,126,838</td>
        <td class="number-2">124</td>
        <td>154,948,977</td>
    </tr>
    <tr>
        <td class="number-2">122</td>
        <td>127,110,260</td>
        <td class="number-2">125</td>
        <td>171,077,457</td>
    </tr>
        <td class="number-2">123</td>
        <td>140,341,028</td>
        <td class="number-2">126</td>
        <td>188,884,740</td>
    <tr>
    </tr>
</table>
<!-- END BLOCK : xp-table-past-120 -->

<!-- START BLOCK : invention-xp-table -->
<p class="simple-note" style="margin:30px 0 10px;">
    <img style="float:left;margin-right:7px;" src="/bildes/runescape/skills/invention.png" title="Invention" alt="Invention logo"> 2016. gada 25. janvārī iznāca pirmā elites klases prasme - <strong>Invention</strong>.<br>Šajā prasmē katrā līmenī nepieciešamais XP apjoms ir citāds.
</p>
<table class="rslist xp-table">
    <tr class="listhead">
        <td style="width:80px">Līmenis</td>
        <td style="width:80px">Pieredze</td>
        <td style="width:80px">Līmenis</td>
        <td style="width:80px">Pieredze</td>
        <td style="width:80px">Līmenis</td>
        <td style="width:80px">Pieredze</td>
        <td style="width:80px">Līmenis</td>
        <td style="width:80px">Pieredze</td>
    </tr>
    <tr>
        <td class="number-2">1</td>
        <td>0</td>
        <td class="number-2">31</td>
        <td>316,311</td>
        <td class="number-2">61</td>
        <td>4,758,122</td>
        <td class="number-2">91</td>
        <td>25,259,906</td>
    </tr>
    <tr>
        <td class="number-2">2</td>
        <td>830</td>
        <td class="number-2">32</td>
        <td>358,547</td>
        <td class="number-2">62</td>
        <td>5,096,111</td>
        <td class="number-2">92</td>
        <td>26,475,754</td>
    </tr>
    <tr>
        <td class="number-2">3</td>
        <td>1,861</td>
        <td class="number-2">33</td>
        <td>404,634</td>
        <td class="number-2">63</td>
        <td>5,449,685</td>
        <td class="number-2">93</td>
        <td>27,728,955</td>
    </tr>
    <tr>
        <td class="number-2">4</td>
        <td>2,902</td>
        <td class="number-2">34</td>
        <td>454,796</td>
        <td class="number-2">64</td>
        <td>5,819,299</td>
        <td class="number-2">94</td>
        <td>29,020,233</td>
    </tr>
    <tr>
        <td class="number-2">5</td>
        <td>3,980</td>
        <td class="number-2">35</td>
        <td>509,259</td>
        <td class="number-2">65</td>
        <td>6,205,407</td>
        <td class="number-2">95</td>
        <td>30,350,318</td>
    </tr>
    <tr>
        <td class="number-2">6</td>
        <td>5,126</td>
        <td class="number-2">36</td>
        <td>568,254</td>
        <td class="number-2">66</td>
        <td>6,608,473</td>
        <td class="number-2">96</td>
        <td>31,719,944</td>
    </tr>
    <tr>
        <td class="number-2">7</td>
        <td>6,390</td>
        <td class="number-2">37</td>
        <td>632,019</td>
        <td class="number-2">67</td>
        <td>7,028,964</td>
        <td class="number-2">97</td>
        <td>33,129,852</td>
    </tr>
    <tr>
        <td class="number-2">8</td>
        <td>7,787</td>
        <td class="number-2">38</td>
        <td>700,797</td>
        <td class="number-2">68</td>
        <td>7,467,354</td>
        <td class="number-2">98</td>
        <td>34,580,790</td>
    </tr>
    <tr>
        <td class="number-2">9</td>
        <td>9,400</td>
        <td class="number-2">39</td>
        <td>774,834</td>
        <td class="number-2">69</td>
        <td>7,924,122</td>
        <td class="number-2">99</td>
        <td>36,073,511</td>
    </tr>
    <tr>
        <td class="number-2">10</td>
        <td>11,275</td>
        <td class="number-2">40</td>
        <td>854,383</td>
        <td class="number-2">70</td>
        <td>8,399,751</td>
        <td class="number-2">100</td>
        <td>37,608,773</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">11</td>
        <td>13,605</td>
        <td class="number-2">41</td>
        <td>946,227</td>
        <td class="number-2">71</td>
        <td>8,925,664</td>
        <td class="number-2">101</td>
        <td>39,270,442</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">12</td>
        <td>16,372</td>
        <td class="number-2">42</td>
        <td>1,044,569</td>
        <td class="number-2">72</td>
        <td>9,472,665</td>
        <td class="number-2">102</td>
        <td>40,978,509</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">13</td>
        <td>19,656</td>
        <td class="number-2">43</td>
        <td>1,149,696</td>
        <td class="number-2">73</td>
        <td>10,041,285</td>
        <td class="number-2">103</td>
        <td>42,733,789</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">14</td>
        <td>23,546</td>
        <td class="number-2">44</td>
        <td>1,261,903</td>
        <td class="number-2">74</td>
        <td>10,632,061</td>
        <td class="number-2">104</td>
        <td>44,537,107</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">15</td>
        <td>28,138</td>
        <td class="number-2">45</td>
        <td>1,381,488</td>
        <td class="number-2">75</td>
        <td>11,245,538</td>
        <td class="number-2">105</td>
        <td>46,389,292</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">16</td>
        <td>33,520</td>
        <td class="number-2">46</td>
        <td>1,508,756</td>
        <td class="number-2">76</td>
        <td>11,882,262</td>
        <td class="number-2">106</td>
        <td>48,291,180</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">17</td>
        <td>39,809</td>
        <td class="number-2">47</td>
        <td>1,644,015</td>
        <td class="number-2">77</td>
        <td>12,542,789</td>
        <td class="number-2">107</td>
        <td>50,243,611</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">18</td>
        <td>47,109</td>
        <td class="number-2">48</td>
        <td>1,787,581</td>
        <td class="number-2">78</td>
        <td>13,227,679</td>
        <td class="number-2">108</td>
        <td>52,247,435</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">19</td>
        <td>55,535</td>
        <td class="number-2">49</td>
        <td>1,939,773</td>
        <td class="number-2">79</td>
        <td>13,937,496</td>
        <td class="number-2">109</td>
        <td>54,303,504</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">20</td>
        <td>64,802</td>
        <td class="number-2">50</td>
        <td>2,100,917</td>
        <td class="number-2">80</td>
        <td>14,672,812</td>
        <td class="number-2">110</td>
        <td>56,412,678</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">21</td>
        <td>77,190</td>
        <td class="number-2">51</td>
        <td>2,283,490</td>
        <td class="number-2">81</td>
        <td>15,478,994</td>
        <td class="number-2">111</td>
        <td>58,575,823</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">22</td>
        <td>90,811</td>
        <td class="number-2">52</td>
        <td>2,476,369</td>
        <td class="number-2">82</td>
        <td>16,313,404</td>
        <td class="number-2">112</td>
        <td>60,793,812</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">23</td>
        <td>106,221</td>
        <td class="number-2">53</td>
        <td>2,679,907</td>
        <td class="number-2">83</td>
        <td>17,176,661</td>
        <td class="number-2">113</td>
        <td>63,067,521</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">24</td>
        <td>123,573</td>
        <td class="number-2">54</td>
        <td>2,894,505</td>
        <td class="number-2">84</td>
        <td>18,069,395</td>
        <td class="number-2">114</td>
        <td>65,397,835</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">25</td>
        <td>143,025</td>
        <td class="number-2">55</td>
        <td>3,120,508</td>
        <td class="number-2">85</td>
        <td>18,992,239</td>
        <td class="number-2">115</td>
        <td>67,785,643</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">26</td>
        <td>164,742</td>
        <td class="number-2">56</td>
        <td>3,358,307</td>
        <td class="number-2">86</td>
        <td>19,945,843</td>
        <td class="number-2">116</td>
        <td>70,231,841</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">27</td>
        <td>188,893</td>
        <td class="number-2">57</td>
        <td>3,608,290</td>
        <td class="number-2">87</td>
        <td>20,930,821</td>
        <td class="number-2">117</td>
        <td>72,737,330</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">28</td>
        <td>215,651</td>
        <td class="number-2">58</td>
        <td>3,870,846</td>
        <td class="number-2">88</td>
        <td>21,947,856</td>
        <td class="number-2">118</td>
        <td>75,303,019</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">29</td>
        <td>245,196</td>
        <td class="number-2">59</td>
        <td>4,146,374</td>
        <td class="number-2">89</td>
        <td>22,997,593</td>
        <td class="number-2">119</td>
        <td>77,929,820</td>
    </tr>
    <tr class="hidden-row">
        <td class="number-2">30</td>
        <td>277,713</td>
        <td class="number-2">60</td>
        <td>4,435,275</td>
        <td class="number-2">90</td>
        <td>24,080,695</td>
        <td class="number-2">120</td>
        <td>80,618,654</td>
    </tr>
</table>
<!-- END BLOCK : invention-xp-table -->

<!-- END BLOCK : skills-xp-table -->
