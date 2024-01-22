<?php

function file_strip($url) {

	$out = [];
	$data = file($url);
	if (!$data) {
		return false;
	}
	foreach ($data as $line) {
		$line = str_replace('    ', ' ', $line);
		$line = str_replace('   ', ' ', $line);
		$line = str_replace('  ', ' ', $line);
		$line = str_replace('	', '', $line);;
		$line = str_replace(">\n", ">", $line);
		$out[] = $line;
	}
	return $out;
}


define("T_BYFILE", 0);
define("T_BYVAR", 1);
define("TP_ROOTBLOCK", '_ROOT');

#[AllowDynamicProperties]
class TemplatePowerParser {

	protected $tpl_base;
	protected $tpl_include;
	protected $tpl_count;
	protected $parent = [];
	protected $defBlock = [];
	protected $rootBlockName;
	protected $ignore_stack;

	public function __contruct($tpl_file, $type) {
		$this->tpl_base = [$tpl_file, $type];
		$this->tpl_count = 0;
		$this->ignore_stack = [false];
	}

	protected function __errorAlert($message) {
		global $debug;
		if (!empty($debug)) {
			print '<br>' . $message . '<br>' . PHP_EOL;
		}
	}

	protected function __prepare() {
		$this->defBlock[TP_ROOTBLOCK] = [];
		$tplvar = $this->__prepareTemplate($this->tpl_base[0], $this->tpl_base[1]);

		$initdev["varrow"] = 0;
		$initdev["coderow"] = 0;
		$initdev["index"] = 0;
		$initdev["ignore"] = false;

		$this->__parseTemplate($tplvar, TP_ROOTBLOCK, $initdev);
		$this->__cleanUp();
	}

	protected function __cleanUp() {
		for ($i = 0; $i <= $this->tpl_count; $i++) {
			$tplvar = 'tpl_rawContent' . $i;
			unset($this->{$tplvar});
		}
	}

	protected function __prepareTemplate($tpl_file, $type) {
		$tplvar = 'tpl_rawContent' . $this->tpl_count;

		if ($type == T_BYVAR) {
			$this->{$tplvar}["content"] = preg_split("/\n/", $tpl_file, -1, PREG_SPLIT_DELIM_CAPTURE);
		} else {
			if (is_readable($tpl_file)) {
				$this->{$tplvar}["content"] = file_strip($tpl_file);
			} else {
				die($this->__errorAlert('Kļūda: Couldn\'t open or read [ ' . $tpl_file . ' ]!'));
			}
		}

		$this->{$tplvar}["size"] = sizeof($this->{$tplvar}["content"]);
		$this->tpl_count++;

		return $tplvar;
	}

	protected function __parseTemplate($tplvar, $blockname, $initdev) {
		$coderow = $initdev["coderow"];
		$varrow = $initdev["varrow"];
		$index = $initdev["index"];

		while ($index < $this->{$tplvar}["size"]) {
			$ignreg = [];
			if (preg_match('/<!--[ ]?(START|END) IGNORE -->/', $this->{$tplvar}["content"][$index], $ignreg)) {
				if ($ignreg[1] == 'START') {
					array_push($this->ignore_stack, true);
				} else {
					array_pop($this->ignore_stack);
				}
			} else {
				if (!end($this->ignore_stack)) {
					$regs = [];
					if (preg_match('/<!--[ ]?(START|END|INCLUDE|REUSE) BLOCK : (.+)-->/', $this->{$tplvar}["content"][$index], $regs)) {
						//remove trailing and leading spaces
						$regs[2] = trim($regs[2]);

						if ($regs[1] == 'INCLUDE') {
							$include_defined = true;

							//check if the include file is assigned
							if (isset($this->tpl_include[$regs[2]])) {
								$tpl_file = $this->tpl_include[$regs[2]][0];
								$type = $this->tpl_include[$regs[2]][1];
							} else if (file_exists($regs[2])) {  //check if defined as constant in template
								$tpl_file = $regs[2];
								$type = T_BYFILE;
							} else {
								$include_defined = false;
							}

							if ($include_defined) {
								//initialize startvalues for recursive call
								$initdev["varrow"] = $varrow;
								$initdev["coderow"] = $coderow;
								$initdev["index"] = 0;
								$initdev["ignore"] = false;

								$tplvar2 = $this->__prepareTemplate($tpl_file, $type);
								$initdev = $this->__parseTemplate($tplvar2, $blockname, $initdev);

								$coderow = $initdev["coderow"];
								$varrow = $initdev["varrow"];
							}
						} else if ($regs[1] == 'REUSE') {
							$reuse_regs = [];
							//do match for 'AS'
							if (preg_match('/(.+) AS (.+)/', $regs[2], $reuse_regs)) {
								$originalbname = trim($reuse_regs[1]);
								$copybname = trim($reuse_regs[2]);

								//test if original block exist
								if (isset($this->defBlock[$originalbname])) {
									//copy block
									$this->defBlock[$copybname] = $this->defBlock[$originalbname];

									//tell the parent that he has a child block
									$this->defBlock[$blockname]["_B:" . $copybname] = '';

									//create index and parent info
									$this->index[$copybname] = 0;
									$this->parent[$copybname] = $blockname;
								} else {
									$this->__errorAlert('Kļūda: Can\'t find block \'' . $originalbname . '\' to REUSE as \'' . $copybname . '\'');
								}
							} else {
								$this->defBlock[$blockname]["_C:$coderow"] = $this->{$tplvar}["content"][$index];
								$coderow++;
							}
						} else {
							if ($regs[2] == $blockname) {
								break;
							} else {

								$this->defBlock[$regs[2]] = [];
								$this->defBlock[$blockname]["_B:" . $regs[2]] = '';

								$this->index[$regs[2]] = 0;
								$this->parent[$regs[2]] = $blockname;

								$index++;
								$initdev["varrow"] = 0;
								$initdev["coderow"] = 0;
								$initdev["index"] = $index;
								$initdev["ignore"] = false;

								$initdev = $this->__parseTemplate($tplvar, $regs[2], $initdev);

								$index = $initdev["index"];
							}
						}
					} else {

						$sstr = explode('{', $this->{$tplvar}["content"][$index]);
						reset($sstr);

						if (current($sstr) != '') {

							$this->defBlock[$blockname]["_C:$coderow"] = current($sstr);
							$coderow++;
						}

						while (next($sstr)) {

							$pos = strpos(current($sstr), "}");

							if (($pos !== false) && ($pos > 0)) {

								$strlength = strlen(current($sstr));
								$varname = substr(current($sstr), 0, $pos);

								if (strstr($varname, ' ')) {

									$this->defBlock[$blockname]["_C:$coderow"] = '{' . current($sstr);
									$coderow++;
								} else {

									$this->defBlock[$blockname]["_V:$varrow"] = $varname;
									$varrow++;

									if (($pos + 1) != $strlength) {

										$this->defBlock[$blockname]["_C:$coderow"] = substr(current($sstr), ($pos + 1), ($strlength - ($pos + 1)));
										$coderow++;
									}
								}
							} else {

								$this->defBlock[$blockname]["_C:$coderow"] = '{' . current($sstr);
								$coderow++;
							}
						}
					}
				} else {
					$this->defBlock[$blockname]["_C:$coderow"] = $this->{$tplvar}["content"][$index];
					$coderow++;
				}
			}
			$index++;
		}

		$initdev["varrow"] = $varrow;
		$initdev["coderow"] = $coderow;
		$initdev["index"] = $index;

		return $initdev;
	}

	public function assignInclude($iblockname, $value, $type = T_BYFILE) {
		$this->tpl_include["$iblockname"] = [$value, $type];
	}
}

class TemplatePower extends TemplatePowerParser {

	protected $index = [];  // $index[{blockname}]  = {indexnumber}
	protected $content = [];
	protected $currentBlock;
	protected $showUnAssigned;
	protected $serialized;
	protected $globalvars = [];
	protected $prepared;

	public function __construct($tpl_file = '', $type = T_BYFILE) {
		parent::__contruct($tpl_file, $type);

		$this->prepared = false;
		$this->showUnAssigned = false;
		$this->serialized = false;  //added: 26 April 2002
	}

	protected function __deSerializeTPL($stpl_file, $type) {
		if ($type == T_BYFILE) {
			if (is_readable($stpl_file)) {
				$serializedTPL = file($stpl_file);
			} else {
				die($this->__errorAlert('Kļūda: Can\'t open or read [ ' . $stpl_file . ' ]!'));
			}
		} else {
			$serializedTPL = $stpl_file;
		}

		$serializedStuff = unserialize(join('', $serializedTPL));

		$this->defBlock = $serializedStuff["defBlock"];
		$this->index = $serializedStuff["index"];
		$this->parent = $serializedStuff["parent"];
	}

	protected function __makeContentRoot() {
		$this->content[TP_ROOTBLOCK . "_0"][0] = [TP_ROOTBLOCK];
		$this->currentBlock = &$this->content[TP_ROOTBLOCK . "_0"][0];
	}

	protected function __assign($varname, $value) {
		if (sizeof($regs = explode('.', $varname)) == 2) { //this is faster then preg_match
			$ind_blockname = $regs[0] . '_' . $this->index[$regs[0]];

			$lastitem = sizeof($this->content[$ind_blockname]);

			$lastitem > 1 ? $lastitem-- : $lastitem = 0;

			$block = &$this->content[$ind_blockname][$lastitem];
			$varname = $regs[1];
		} else {
			$block = &$this->currentBlock;
		}
		$block["_V:$varname"] = $value;
	}

	protected function __assignGlobal($varname, $value) {
		$this->globalvars[$varname] = $value;
	}

	protected function __outputContent($blockname) {
		$numrows = sizeof($this->content[$blockname]);

		for ($i = 0; $i < $numrows; $i++) {
			$defblockname = $this->content[$blockname][$i][0];

			for (reset($this->defBlock[$defblockname]); $k = key($this->defBlock[$defblockname]); next($this->defBlock[$defblockname])) {
				if ($k[1] == 'C') {
					print $this->defBlock[$defblockname][$k];
				} else if ($k[1] == 'V') {
					$defValue = $this->defBlock[$defblockname][$k];

					if (!isset($this->content[$blockname][$i]["_V:" . $defValue])) {
						if (isset($this->globalvars[$defValue])) {
							$value = $this->globalvars[$defValue];
						} else {
							$value = $this->showUnAssigned ? '{' . $defValue . '}' : '';
						}
					} else {
						$value = $this->content[$blockname][$i]["_V:" . $defValue];
					}
					print $value;
				} else if ($k[1] == 'B') {
					if (isset($this->content[$blockname][$i][$k])) {
						$this->__outputContent($this->content[$blockname][$i][$k]);
					}
				}
			}
		}
	}

	public function serializedBase() {
		$this->serialized = true;
		$this->__deSerializeTPL($this->tpl_base[0], $this->tpl_base[1]);
	}

	public function showUnAssigned($state = true) {
		$this->showUnAssigned = $state;
	}

	public function prepare() {
		if (!$this->serialized) {
			parent::__prepare();
		}

		$this->prepared = true;
		$this->index[TP_ROOTBLOCK] = 0;
		$this->__makeContentRoot();
	}

	public function newBlock($blockname) {
		$parent = &$this->content[$this->parent[$blockname] . '_' . $this->index[$this->parent[$blockname]]];

		$lastitem = sizeof($parent);
		$lastitem > 1 ? $lastitem-- : $lastitem = 0;

		$ind_blockname = $blockname . '_' . $this->index[$blockname];

		if (!isset($parent[$lastitem]["_B:$blockname"])) {
			$this->index[$blockname] += 1;

			$ind_blockname = $blockname . '_' . $this->index[$blockname];

			if (!isset($this->content[$ind_blockname])) {
				$this->content[$ind_blockname] = [];
			}

			$parent[$lastitem]["_B:$blockname"] = $ind_blockname;
		}

		$blocksize = sizeof($this->content[$ind_blockname]);

		$this->content[$ind_blockname][$blocksize] = [$blockname];

		$this->currentBlock = &$this->content[$ind_blockname][$blocksize];
	}

	public function assignGlobal($varname, $value = '') {
		if (is_array($varname)) {
			foreach ($varname as $var => $value) {
				$this->__assignGlobal($var, $value);
			}
		} else {
			$this->__assignGlobal($varname, $value);
		}
	}

	public function assign($varname, $value = '') {
		if (is_array($varname)) {
			foreach ($varname as $var => $value) {
				$this->__assign($var, $value);
			}
		} else {
			$this->__assign($varname, $value);
		}
	}

	public function assignAll($data, $html = false) {
		$assign = [];
		foreach ($data as $key => $val) {
			if ($html) {
				$val = h($val);
			}
			$assign[$key] = $val;
		}
		$this->assign($assign);
	}

	public function gotoBlock($blockname) {
		if (isset($this->defBlock[$blockname])) {
			$ind_blockname = $blockname . '_' . $this->index[$blockname];

			$lastitem = sizeof($this->content[$ind_blockname]);

			$lastitem > 1 ? $lastitem-- : $lastitem = 0;

			$this->currentBlock = &$this->content[$ind_blockname][$lastitem];
		}
	}

	public function printToScreen() {
		if ($this->prepared) {
			$this->__outputContent(TP_ROOTBLOCK . '_0');
		} else {
			$this->__errorAlert('Kļūda: Template isn\'t prepared!');
		}
	}

	public function getOutputContent() {
		ob_start();
		$this->printToScreen();
		return ob_get_clean();
	}
}
