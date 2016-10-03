<?php
/**
 *  Parent klase exs mvc arhitektūras modeļu klasēm.
 *
 *  Izmantota vairākās RuneScape sadaļās, kuru kods realizēts mvc formātā.
 */

class Model {
	
	/**
	 *  Pievienos modelim atsauces uz dažiem projekta globālajiem mainīgajiem.
	 */
	public function __construct() {
		$globals = [
			'db', 'auth', 'lang', 'debug', 'category', 'm'
		];
		foreach ($globals as $global) {
			global ${$global};
			$this->{$global} =& ${$global};
		}
	}
	
	/**
	 *  Nepieciešamos globālos mainīgos var piesaistīt arī manuāli.
	 *
	 *  @param array $arr   masīvs ar mainīgo nosaukumiem
	 */
	protected function globals($arr = null) {
		
		if (empty($arr) || !is_array($arr)) {
			return false;
		}
		
		foreach ($arr as $element) {
			global ${$element};
			$this->{$element} =& ${$element};
		}
	}
}
