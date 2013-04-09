	<!-- START BLOCK : ig-newgame-->
	  <h2>Izveidot spēli</h2>

		<form id="new-igame" class="form" action="{page-url}" method="post">
		  <fieldset>
				<p>
					<label for="newgame-date">Datums (GGGG-MM-DD):</label><br />
					<input type="text" class="text" name="newgame-date" id="newgame-date" value="" />
				</p>
				<p>
					<input type="submit" name="submit" id="submit" value="Izveidot" />
				</p>
		  </fieldset>
		</form>
	<!-- END BLOCK : ig-newgame-->



	<!-- START BLOCK : ig-listgame-->
	  <h2>Spēļu saraksts</h2>

		<table class="main-table">
      <tr>
        <th>ID</th>
        <th>Datums</th>
      </tr>
		<!-- START BLOCK : ig-listgame-node-->
      <tr>
        <td>{listgame-id}</td>
        <td><a href="/?c={listgame-categoryid}&amp;game={listgame-id}">{listgame-date}</a></td>
     </tr>
		<!-- END BLOCK : ig-listgame-node-->
		</table>

	<!-- END BLOCK : ig-listgame-->
	
	
	<!-- START BLOCK : ig-listimages-->
	  <h2>Attēlu saraksts</h2>

		<table class="main-table">
      <tr>
        <th>ID</th>
        <th>Atbilde</th>
        <th>Līmenis</th>
        <th>Dzēst</th>
        <th>Attēls</th>
      </tr>
		<!-- START BLOCK : ig-listimages-node-->
      <tr>
        <td>{listimage-id}</td>
        <td>{listimage-title}</td>
        <td>{listimage-dif}</td>
        <td>
					<a class="delete" title="Dzēst" href="/?c={listimage-categoryid}&amp;game={listimage-game_id}&amp;delete={listimage-id}" onclick="return confirm_delete();">
						<img src="/bildes/x.png" alt="x" title="Dzēst" />
					</a>
				</td>
        <td><img src="{listimage-image}" alt="" /></td>
     </tr>
		<!-- END BLOCK : ig-listimages-node-->
		</table>

	<!-- END BLOCK : ig-listimages-->
	
	
	<!-- START BLOCK : ig-newimage-->
	  <h2>Pievienot attēlu spēlei</h2>

		<form id="new-igimage" class="form" action="{page-url}" method="post">
		  <fieldset>
				<!-- START BLOCK : ig-newimage-success-->
				<p class="success">Priekšmets pievienots!</p>
				<!-- END BLOCK : ig-newimage-success-->
				<p>
					<label for="newimage-title">Nosaukums (atbilde):</label><br />
					<input type="text" class="text" name="newimage-title" id="newimage-title" value="" />
				</p>
				<p>
					<label for="newimage-image">Attēls:</label><br />
					<input type="text" class="text" name="newimage-image" id="newimage-image" value="" />
				</p>
				<p>
					<label for="newimage-hint">Hints:</label><br />
					<input type="text" class="text" name="newimage-hint" id="newimage-hint" value="" />
				</p>
				<p>
					<label for="newimage-dif">Grūtības pakāpe:</label><br />
					<select class="text" name="newimage-dif" id="newimage-dif">
					  <option value="1">Viegls</option>
					  <option value="2" selected="selected">Vidējs</option>
					  <option value="3">Grūts</option>
					</select>
				</p>
				<p>
					<input type="submit" name="submit" id="submit" value="Pievienot" />
				</p>
		  </fieldset>
		</form>
		
	  <a href="/?c=200">Atpakaļ pie spēļu saraksta</a>
	<!-- END BLOCK : ig-newimage-->