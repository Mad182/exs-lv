<?php
/**
 *  RuneScape pamācību sadaļu (kvesti/prasmes u.tml.) administrācijas panelis.
 *
 *  Šis modulis iekļauj visas tās apakšsadaļas, kurās iespējams veikt izmaiņas
 *  RuneScape pamācību sadaļām, piemēram, pievienojot jaunas rakstu sērijas,
 *  mainot rakstu secību sērijā, izveidojot rakstu "placeholders" u.c.
 *
 *  Modulis izmanto exs mvc arhitektūru.
 */

require_once(CORE_PATH.'/modules/runescape/class.controller.php');
 
class Rsmod extends Controller {

	// [category->textid] => [file name]
	private $submodules = array(
		'all-quests'        => 'lists',
		'all-miniquests'    => 'lists',
		'all-minigames'     => 'lists',
		'all-distractions'  => 'lists',
		'all-guilds'        => 'lists',
		'all-unlisted'      => 'unlisted',
		'series'            => 'series',
		'skills'            => 'skills'
	);

	/**
	 *  Ielādēs atbilstošo apakšmoduli
	 */
	public function index() {
	
		$this->check_permission('mod');
		$this->tpl_options = 'no-left';
		
		if (array_key_exists($this->category->textid, $this->submodules)) {
			$file_name = $this->submodules[$this->category->textid];
			if ($this->category->textid == 'all-unlisted') {
				$this->subview('submodules/lists', 'sub-view');
			} else {
				$this->subview('submodules/'.$file_name, 'sub-view');
			}
			$this->submodule('submodules/'.$file_name);
		}
	}
}

init_mvc();
