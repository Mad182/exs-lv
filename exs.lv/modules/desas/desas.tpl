<!-- START BLOCK : desas-->
<script src="/modules/desas/desas.js"></script>
<style>
	#desas {
		width: 171px;
		height: 171px;
		position: relative;
		border: 1px solid #ddd;
	}
	#desas .field {
		float: left;
		width: 55px;
		height: 55px;
		text-align: center;
		line-height: 55px;
		border: 1px solid #ddd;
		font-family:'Trebuchet MS',sans-serif;
		font-size: 22px;
		padding: 0;
		margin: 0;
	}
	#desas .field a {
		float: left;
		text-indent: -9999px;
		width: 55px;
		height: 55px;
		text-align: center;
		line-height: 55px;
	}
	#desas .field.mine {
		color: green;
	}
	#desas .field.other {
		color: red;
	}
	#desas .overlay {
		position: absolute;
		z-index: 100;
		width: 111px;
		height: 111px;
		font-size: 16px;
		padding: 30px 20px 10px;
		top: 9px;
		right: 9px;
		opacity:0.7;
		filter:alpha(opacity=70);
		border: 1px solid #000066;
		background: #333388;
		color: #fff;
	}
	#desas .alert {
		position: absolute;
		z-index: 100;
		height: 14px;
		line-height: 13px;
		font-size: 11px;
		right: 4px;
		top: 4px;
		text-align: center;
		color: #fff;
		padding: 3px 9px;
		border: 1px solid #660000;
		background: #883333;
		opacity:0.4;
		filter:alpha(opacity=40);
	}
	#start-desas {
		display: block;
		padding: 70px 0 30px;
		font-size: 16px;
		text-align: center;
	}
	#desas-info {
		font-size: 90%;
		line-height: 1.2;
	}
</style>
<h3>Desas</h3>
<div class="box">
	<div id="desas"><a href="/desas_server" id="start-desas">Sākt spēli</a></div>
	<div id="desas-info" style="display: none">
		Pretinieks: <span id="desas-opponent">Nav</span><br>
		Tavi stati: <span id="desas-my-win" class="rautors"></span>/<span id="desas-my-lose" class="admins"></span><br>
		Pretinieka stati: <span id="desas-op-win" class="rautors"></span>/<span id="desas-op-lose" class="admins"></span><br>
		<a href="/desas_server/drop">Pamest partiju</a>
	</div>
</div>
<!-- END BLOCK : desas-->
