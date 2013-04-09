<h1>Aktuālās foruma sadaļas</h1>
<p>Izvēlies, kuras sadaļas vēlies redzēt zem &quot;Jaunākais lapā&quot;</p>
<form action="" method="post">
<table id="forum">
<!-- START BLOCK : forum-list-->
	<tr>
		<th class="first" colspan="2"><a href="/{textid}">{title}</a></th>
		<th>Sekot</th>
	</tr>
<!-- START BLOCK : forum-item-->
	<tr>
		<td>
			<a href="/{textid}"><img width="48" height="48" src="http://exs.lv/{icon}" alt="" /></a>
		</td>
		<td>
			<h3><a href="/{textid}">{title}</a></h3>
		</td>
		<td>
			<select name="forum[{id}]">
				<option value="0"{enabled}>Sekot</option>
				<option value="1"{disabled}>Ignorēt</option>
			</select>
		</td>
	</tr>
<!-- END BLOCK : forum-item-->
<!-- END BLOCK : forum-list-->
</table>
<p>
	<input type="submit" name="submit-ignore" value="Saglabāt" class="button primary" />
</p>
</form>
