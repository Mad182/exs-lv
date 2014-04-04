<?php

/**
 * Modificēts phpBB 2 bbkoda parseris
 *
 * Tiek izmantots miniblogiem
 */
define('BBCODE_UID_LEN', 10);
define('BBCODE_NOSMILIES_START', '<!-- no smilies start -->');
define('BBCODE_NOSMILIES_END', '<!-- no smilies end -->');
global $board_config, $phpbb_root_path, $phpEx, $lang;

$urls_local = array('http://exs.lv');

define('AUTOURL', time());

class BBCode {

	var $text = '';
	var $html = '';
	var $allow_html = true;
	var $allow_styling = true;
	var $allow_bbcode = true;
	var $allow_smilies = false;
	var $is_sig = false;
	var $code_counter = 0;
	var $allowed_bbcode = array(
		// simple tags
		'b' => array(
			'nested' => true,
			'inurl' => true,
			'allow_empty' => false,
		),
		'i' => array(
			'nested' => true,
			'inurl' => true,
			'allow_empty' => false,
		),
		'u' => array(
			'nested' => true,
			'inurl' => true,
			'allow_empty' => false,
		),
		'strong' => array(
			'nested' => true,
			'inurl' => true,
			'allow_empty' => false,
		),
		'em' => array(
			'nested' => true,
			'inurl' => true,
			'allow_empty' => false,
		),
		'strike' => array(
			'nested' => true,
			'inurl' => true,
			'allow_empty' => false,
		),
		'sup' => array(
			'nested' => true,
			'inurl' => true,
			'allow_empty' => false,
		),
		'sub' => array(
			'nested' => true,
			'inurl' => true,
			'allow_empty' => false,
		),
		'span' => array(
			'nested' => true,
			'inurl' => false,
		),
		'center' => array(
			'nested' => true,
			'inurl' => false,
			'allow_empty' => false,
		),
		'size' => array(
			'nested' => true,
			'inurl' => true,
			'allow_empty' => false,
		),
		'cell' => array(
			'nested' => true,
			'inurl' => true,
			'allow_empty' => false,
		),
		'hr' => array(
			'nested' => true,
			'inurl' => true,
			'allow_empty' => true,
		),
		/*
		  'table' => array(
		  'nested' => true,
		  'inurl' => true,
		  'allow_empty' => false,
		  ),
		 */
		'align' => array(
			'nested' => true,
			'inurl' => false,
			'allow_empty' => false,
		),
		'font' => array(
			'nested' => true,
			'inurl' => true,
			'allow_empty' => false,
		),
		'marquee' => array(
			'nested' => true,
			'inurl' => false,
			'allow_empty' => false,
		),
		'img' => array(
			'nested' => false,
			'inurl' => true,
		),
		'albumimg' => array(
			'nested' => false,
			'inurl' => true,
		),
		'color' => array(
			'nested' => true,
			'inurl' => true,
			'allow_empty' => false,
		),
		'url' => array(
			'nested' => false,
			'inurl' => false,
		),
		'a' => array(
			'nested' => false,
			'inurl' => false,
		),
		'email' => array(
			'nested' => false,
			'inurl' => false,
		),
		'list' => array(
			'nested' => true,
			'inurl' => false,
		),
		'ul' => array(
			'nested' => true,
			'inurl' => false,
		),
		'ol' => array(
			'nested' => true,
			'inurl' => false,
		),
		'*' => array(
			'nested' => true,
			'inurl' => false,
		),
		'li' => array(
			'nested' => true,
			'inurl' => false,
		),
		'quote' => array(
			'nested' => true,
			'inurl' => false,
		),
		'ot' => array(
			'nested' => true,
			'inurl' => false,
		),
		'code' => array(
			'nested' => false,
			'inurl' => false,
		),
	);
	var $allowed_html = array(
		'b' => array(
			'nested' => true,
			'inurl' => true,
			'allow_empty' => false,
		),
		'i' => array(
			'nested' => true,
			'inurl' => true,
			'allow_empty' => false,
		),
		'u' => array(
			'nested' => true,
			'inurl' => true,
			'allow_empty' => false,
		),
		'strong' => array(
			'nested' => true,
			'inurl' => true,
		),
		'allow_empty' => false,
		'em' => array(
			'nested' => true,
			'inurl' => true,
			'allow_empty' => false,
		),
		'strike' => array(
			'nested' => true,
			'inurl' => true,
			'allow_empty' => false,
		),
		'sup' => array(
			'nested' => true,
			'inurl' => true,
			'allow_empty' => false,
		),
		'sub' => array(
			'nested' => true,
			'inurl' => true,
			'allow_empty' => false,
		),
		'span' => array(
			'nested' => true,
			'inurl' => true,
		),
		'center' => array(
			'nested' => true,
			'inurl' => true,
			'allow_empty' => false,
		),
		'hr' => array(
			'nested' => true,
			'inurl' => true,
			'allow_empty' => false,
		),
		'a' => array(
			'nested' => false,
			'inurl' => false,
		),
		'ul' => array(
			'nested' => true,
			'inurl' => false,
		),
		'ol' => array(
			'nested' => true,
			'inurl' => false,
		),
		'li' => array(
			'nested' => true,
			'inurl' => false,
		),
		'blockquote' => array(
			'nested' => true,
			'inurl' => false,
		),
		'div' => array(
			'nested' => true,
			'inurl' => false,
		),
		'table' => array(
			'nested' => true,
			'inurl' => false,
		),
		'td' => array(
			'nested' => true,
			'inurl' => false,
		),
		'tr' => array(
			'nested' => true,
			'inurl' => false,
		),
		'span' => array(
			'nested' => true,
			'inurl' => false,
		),
	);
	var $allowed_smilies = array();
	var $replaced_smilies = array();
	var $data = array();
	var $tag = '';
	var $params = array();

	/*
	  Process bbcode/html tag.
	  This is the only function you would want to modify to add your own bbcode/html tags.
	  Note: this bbcode parser doesn't make any differece of bbcode and html, so <b> and [b] are treated exactly same way
	 */

	function process_tag(&$item) {
		global $lang, $db;
		$tag = $item['tag'];
		//echo 'process_tag(', $tag, ')<br />';
		$start = substr($this->text, $item['start'], $item['start_len']);
		$end = substr($this->text, $item['end'], $item['end_len']);
		$content = substr($this->text, $item['start'] + $item['start_len'], $item['end'] - $item['start'] - $item['start_len']);
		$error = array(
			'valid' => false,
			'start' => $this->process_text($start),
			'end' => $this->process_text($end)
		);
		if (isset($item['valid']) && $item['valid'] == false) {
			return $error;
		}
		// check if empty item is allowed
		if (!strlen($content)) {
			$allow_empty = true;
			if ($item['is_html'] && isset($this->allowed_html[$tag]['allow_empty']) && !$this->allowed_html[$tag]['allow_empty']) {
				$allow_empty = false;
			}
			if (!$item['is_html'] && isset($this->allowed_bbcode[$tag]['allow_empty']) && !$this->allowed_bbcode[$tag]['allow_empty']) {
				$allow_empty = false;
			}
			if (!$allow_empty) {
				return array(
					'valid' => true,
					'html' => '',
					'end' => '',
					'allow_nested' => false,
				);
			}
		}
		// check if nested item is allowed
		if ($item['iteration']) {
			if ($item['is_html'] && !$this->allowed_html[$tag]['nested']) {
				return $error;
			}
			if (!$item['is_html'] && !$this->allowed_bbcode[$tag]['nested']) {
				return $error;
			}
		}

		// Simple tags: B, U, I, EM, STRONG, SPAN, CENTER, STRIKE
		if ($tag === 'b' ||
				$tag === 'i' ||
				$tag === 'u' ||
				$tag === 'em' ||
				$tag === 'strike' ||
				$tag === 'sup' ||
				$tag === 'sub' ||
				$tag === 'strong' ||
				$tag === 'span' ||
				$tag === 'center'
		) {
			$extras = $this->allow_styling ? array('style', 'class') : array('class');
			$html = '<' . $tag . $this->add_extras($item['params'], $extras) . '>';
			return array(
				'valid' => true,
				'start' => $html,
				'end' => '</' . $tag . '>'
			);
		}

		// Single tags: HR
		if ($tag === 'hr') {
			$extras = $this->allow_styling ? array('style', 'class') : array();
			if (isset($item['params']['param'])) {
				$color = $item['params']['param'];
			} elseif (isset($item['params']['color'])) {
				$color = $item['params']['color'];
			}
			$color = $this->valid_color($color);
			if ($color === false) {
				$html = '<' . $tag . ' />';
			} else {
				$html = '<' . $tag . ' color="' . $color . '" />';
			}
			return array(
				'valid' => true,
				'html' => $html
			);
		}

		// ALIGN
		if ($tag === 'align') {
			$extras = $this->allow_styling ? array('style', 'class') : array();
			if (isset($item['params']['param'])) {
				$align = $item['params']['param'];
			} elseif (isset($item['params']['align'])) {
				$align = $item['params']['align'];
			} else {
				$align === 'left';
			}
			if ($align === 'left' || $align === 'right' || $align === 'center' || $align === 'justify') {
				$html = '<div style="text-align:' . $align . '">';
				return array(
					'valid' => true,
					'start' => $html,
					'end' => '</div>',
				);
			}
		}

		// MARQUEE
		if ($tag === 'marquee') {
			if ($this->is_sig) {
				return $error;
			}
			$extras = $this->allow_styling ? array('style', 'class') : array();
			if (isset($item['params']['param'])) {
				$direction = $item['params']['param'];
			} elseif (isset($item['params']['direction'])) {
				$direction = $item['params']['direction'];
			} else {
				if ($direction === 'up' || $direction === 'down' || $direction === 'left' || $direction === 'right') {
					$direction = $direction;
				} else {
					$direction = 'right';
				}
			}

			if (isset($item['params']['scrolldelay'])) {
				$scrolldelay = $item['params']['scrolldelay'];
				if ((intval($scrolldelay) > 10) && (intval($scrolldelay) < 601)) {
					$scrolldelay = $scrolldelay;
				} else {
					$scrolldelay = '120';
				}
			} else {
				$scrolldelay = '120';
			}

			if (isset($item['params']['behavior'])) {
				$behavior = $item['params']['behavior'];
				if ($behavior === 'alternate' || $behavior === 'slide') {
					$behavior = $behavior;
				} else {
					$behavior = 'scroll';
				}
			} else {
				$behavior = 'scroll';
			}

			$html = '<marquee behavior="' . $behavior . '" direction="' . $direction . '" scrolldelay="' . $scrolldelay . '">';
			return array(
				'valid' => true,
				'start' => $html,
				'end' => '</marquee>',
			);
		}

		// FONT
		if ($tag === 'font') {
			$extras = $this->allow_styling ? array('style', 'class') : array();
			if (isset($item['params']['param'])) {
				$font = $item['params']['param'];
			} elseif (isset($item['params']['font'])) {
				$font = $item['params']['font'];
			} else {
				$font = 'Verdana';
			}

			if ($font === 'Arial' ||
					$font === 'Arial Black' ||
					$font === 'Comic Sans MS' ||
					$font === 'Courier New' ||
					$font === 'Impact' ||
					$font === 'Lucida Console' ||
					$font === 'Lucida Sans Unicode' ||
					$font === 'Microsoft Sans Serif' ||
					$font === 'Symbol' ||
					$font === 'Tahoma' ||
					$font === 'Times New Roman' ||
					$font === 'Traditional Arabic' ||
					$font === 'Trebuchet MS' ||
					$font === 'Verdana' ||
					$font === 'Webdings' ||
					$font === 'Wingdings') {
				$font = $font;
			} else {
				$font = 'Verdana';
			}
			$html = '<span style="font-family:' . $font . '">';
			return array(
				'valid' => true,
				'start' => $html,
				'end' => '</span>',
			);
		}

		// TABLE
		if ($tag === 'table') {
			if ($this->is_sig) {
				return $error;
			}
			// additional allowed parameters
			$extras = $this->allow_styling ? array('class', 'align', 'width', 'height', 'border', 'cellspacing', 'cellpadding') : array('class', 'align', 'width');
			if (isset($item['params']['param'])) {
				$table_class = $item['params']['param'];
			} else {
				$table_class = 'empty-table';
			}

			for ($i = 0; $i < count($extras); $i++) {
				if (!empty($item['params'][$extras[$i]])) {
					if ($extras[$i] === 'style') {
						$style = $this->valid_style($item['params']['style']);
						if ($style !== false) {
							$params['style'] = $style;
						}
					} else {
						$params[$extras[$i]] = $item['params'][$extras[$i]];
					}
				}
			}
			if (!isset($params['class'])) {
				$params['class'] = $table_class;
			}
			// generate html
			$html = '<table';
			foreach ($params as $var => $value) {
				$html .= ' ' . $var . '="' . $this->process_text($value) . '"';
			}
			$html .= ' >' . $content;
			return array(
				'valid' => true,
				'html' => $html,
				'end' => '</table>',
			);
		}

		// IMG
		if ($tag === 'img') {
			if ($this->is_sig) {
				return $error;
			}
			// main parameters
			$params = array(
				'src' => false,
				'alt' => false,
			);
			// additional allowed parameters
			$extras = $this->allow_styling ? array('width', 'height', 'border', 'style', 'class', 'title', 'align') : array('width', 'height', 'border', 'title', 'align');
			// [img=blah]blah2[/img]
			if (isset($item['params']['param'])) {
				$params['src'] = $item['params']['param'];
				$params['alt'] = $content;
			}
			// [img src=blah alt=blah width=123][/img]
			elseif (isset($item['params']['src'])) {
				$params['src'] = $item['params']['src'];
				$params['alt'] = isset($item['params']['alt']) ? $item['params']['alt'] : $content;
				for ($i = 0; $i < count($extras); $i++) {
					if (!empty($item['params'][$extras[$i]])) {
						if ($extras[$i] === 'style') {
							$style = $this->valid_style($item['params']['style']);
							if ($style !== false) {
								$params['style'] = $style;
							}
						} else {
							$params[$extras[$i]] = $item['params'][$extras[$i]];
						}
					}
				}
			}
			// [img]blah[/img], [img width=blah]blah[/img]
			elseif (!empty($content)) {
				$params['src'] = $content;
				$params['alt'] = isset($item['params']['alt']) ? $item['params']['alt'] : (isset($params['title']) ? $params['title'] : '');
				for ($i = 0; $i < count($extras); $i++) {
					if (!empty($item['params'][$extras[$i]])) {
						if ($extras[$i] === 'style') {
							$style = $this->valid_style($item['params']['style']);
							if ($style !== false) {
								$params['style'] = $style;
							}
						} else {
							$params[$extras[$i]] = $item['params'][$extras[$i]];
						}
					}
				}
			}
			// generate html
			$html = '<img';
			foreach ($params as $var => $value) {
				$html .= ' ' . $var . '="' . $this->process_text($value) . '"';
			}
			if (!isset($params['title'])) {
				$html .= ' title="' . $this->process_text($params['alt']) . '"';
			}
			$html .= ' />';
			// add url
			if (empty($item['inurl'])) {
				$html = '<a href="' . $params['src'] . '" class="lightbox" target="_blank" title="Atvērsies jaunā logā">' . $html . '</a>';
			}
			return array(
				'valid' => true,
				'html' => $html,
				'allow_nested' => false,
			);
		}

		// ALBUMIMG
		if ($tag === 'albumimg') {
			if ($this->is_sig) {
				return $error;
			}
			// main parameters
			$params = array(
				'src' => false,
				'alt' => false,
			);
			// additional allowed parameters
			$extras = $this->allow_styling ? array('width', 'height', 'border', 'style', 'class', 'title', 'align') : array('width', 'height', 'border', 'title', 'align');
			// [albumimg=blah]blah2[/albumimg]
			if (isset($item['params']['param'])) {
				$params['src'] = $item['params']['param'];
				$pic_url = $item['params']['param'];
				$params['alt'] = $content;
			}
			// [albumimg src=blah alt=blah width=123][/albumimg]
			elseif (isset($item['params']['src'])) {
				$params['src'] = $item['params']['src'];
				$pic_url = $item['params']['src'];
				$params['alt'] = isset($item['params']['alt']) ? $item['params']['alt'] : $content;
				for ($i = 0; $i < count($extras); $i++) {
					if (!empty($item['params'][$extras[$i]])) {
						if ($extras[$i] === 'style') {
							$style = $this->valid_style($item['params']['style']);
							if ($style !== false) {
								$params['style'] = $style;
							}
						} else {
							$params[$extras[$i]] = $item['params'][$extras[$i]];
						}
					}
				}
			}
			// [albumimg]blah[/albumimg], [albumimg width=blah]blah[/albumimg]
			elseif (!empty($content)) {
				$params['src'] = $content;
				$pic_url = $content;
				$params['alt'] = isset($item['params']['alt']) ? $item['params']['alt'] : (isset($params['title']) ? $params['title'] : '');
				for ($i = 0; $i < count($extras); $i++) {
					if (!empty($item['params'][$extras[$i]])) {
						if ($extras[$i] === 'style') {
							$style = $this->valid_style($item['params']['style']);
							if ($style !== false) {
								$params['style'] = $style;
							}
						} else {
							$params[$extras[$i]] = $item['params'][$extras[$i]];
						}
					}
				}
			}
			// generate html
			$pic_url = 'album_showpage.php?pic_id=' . $pic_url;
			if (isset($item['params']['mode'])) {
				$pic_mode = $item['params']['mode'];
				if ($pic_mode === 'full') {
					$params['src'] = 'album_picm.php?pic_id=' . $params['src'];
				} else {
					$params['src'] = 'album_thumbnail.php?pic_id=' . $params['src'];
				}
			} else {
				$params['src'] = 'album_thumbnail.php?pic_id=' . $params['src'];
			}
			$html = '<img';
			foreach ($params as $var => $value) {
				$html .= ' ' . $var . '="' . $this->process_text($value) . '"';
			}
			if (!isset($params['title'])) {
				$html .= ' title="' . $this->process_text($params['alt']) . '"';
			}
			$html .= ' />';
			// add url
			if (empty($item['inurl'])) {
				$html = '<a href="' . $pic_url . '" title="' . $lang['Click_enlarge_pic'] . '">' . $html . '</a>';
			}
			return array(
				'valid' => true,
				'html' => $html,
				'allow_nested' => false,
			);
		}

		// LIST
		if ($tag === 'list' || $tag === 'ul' || $tag === 'ol') {
			if ($this->is_sig) {
				return $error;
			}
			$extras = $this->allow_styling ? array('style', 'class') : array();
			// check if nested tags are all [*]
			$nested_count = 0;
			for ($i = 0; $i < count($item['items']); $i++) {
				$tag2 = $item['items'][$i]['tag'];
				if ($tag2 === '*' || $tag2 === 'li') {
					$nested_count++;
				}
			}
			if (!$nested_count) {
				// no <li> items. return error
				return $error;
			}
			// replace "list" with html tag
			if ($tag === 'list') {
				if (isset($item['params']['param']) || isset($item['params']['type'])) {
					$tag = 'ol';
				} else {
					$tag = 'ul';
				}
			}
			// valid tag. process subitems to make sure there are no extra items and remove all code between elements
			$last_item = false;
			for ($i = 0; $i < count($item['items']); $i++) {
				$item2 = &$item['items'][$i];
				$tag2 = $item2['tag'];
				if ($tag2 === '*' || $tag2 === 'li') {
					// mark as valid
					$item2['list_valid'] = true;
					if ($last_item === false) {
						// change start position to end of [list]
						$pos2 = $item2['start'] + $item2['start_len'];
						$item2['start'] = $pos;
						$item2['start_len'] = $pos2 - $pos;
						$item2['first_entry'] = true;
					}
					$last_item = &$item['items'][$i];
				}
			}
			// generate html
			$html = '<' . $tag;
			if (isset($item['params']['param'])) {
				$html .= ' type="' . htmlspecialchars($item['params']['param']) . '"';
			} elseif (isset($item['params']['type'])) {
				$html .= ' type="' . htmlspecialchars($item['params']['type']) . '"';
			}
			$html .= $this->add_extras($item['params'], $extras) . '>';
			return array(
				'valid' => true,
				'start' => $html,
				'end' => '</li></' . $tag . '>'
			);
		}

		// [*], LI
		if ($tag === '*' || $tag === 'li') {
			if ($this->is_sig) {
				return $error;
			}
			$extras = $this->allow_styling ? array('style', 'class') : array();
			// if not marked as valid return error
			if (empty($item['list_valid'])) {
				return $error;
			}
			$html = '<li';
			if (empty($item['first_entry'])) {
				// add closing tag for previous list entry
				$html = '</li>' . $html;
			}
			$html .= $this->add_extras($item['params'], $extras) . '>';
			return array(
				'valid' => true,
				'start' => $html,
				'end' => '',
			);
		}

		// COLOR
		if ($tag === 'color') {
			$extras = $this->allow_styling ? array('class') : array();
			$color = '';
			if (isset($item['params']['param'])) {
				$color = $item['params']['param'];
			} elseif (isset($item['params']['color'])) {
				$color = $item['params']['color'];
			} else {
				return $error;
			}
			$color = $this->valid_color($color);
			if ($color === false) {
				return $error;
			}
			$html = '<span style="' . ($this->allow_styling && isset($item['params']['style']) ? htmlspecialchars($this->valid_style($item['params']['style'], '')) : '') . 'color: ' . $color . ';"' . $this->add_extras($item['params'], $extras) . '>';
			return array(
				'valid' => true,
				'start' => $html,
				'end' => '</span>',
			);
		}

		// GLOW
		if ($tag === 'glow') {
			$color = '';
			if (isset($item['params']['param'])) {
				$color = $item['params']['param'];
			} elseif (isset($item['params']['color'])) {
				$color = $item['params']['color'];
			} else {
				return $error;
			}
			$color = $this->valid_color($color);
			if ($color === false) {
				return $error;
			}
			$html = '<span style="filter: glow(color=' . $color . '); height:20;">';
			return array(
				'valid' => true,
				'start' => $html,
				'end' => '</span>',
			);
		}

		// SHADOW
		if ($tag === 'shadow') {
			$color = '';
			if (isset($item['params']['param'])) {
				$color = $item['params']['param'];
			} elseif (isset($item['params']['color'])) {
				$color = $item['params']['color'];
			} else {
				return $error;
			}
			$color = $this->valid_color($color);
			if ($color === false) {
				return $error;
			}
			$html = '<span style="filter: shadow(color=' . $color . '); height:20;">';
			return array(
				'valid' => true,
				'start' => $html,
				'end' => '</span>',
			);
		}

		// HIGHLIGHT
		if ($tag === 'highlight') {
			$extras = $this->allow_styling ? array('class') : array();
			$color = '#FFFFAA';
			if (isset($item['params']['param'])) {
				$color = $item['params']['param'];
			} elseif (isset($item['params']['highlight'])) {
				$color = $item['params']['highlight'];
			} else {
				return $error;
			}
			$color = $this->valid_color($color);
			if ($color === false) {
				return $error;
			}
			$html = '<span style="' . ($this->allow_styling && isset($item['params']['style']) ? htmlspecialchars($this->valid_style($item['params']['style'], '')) : '') . 'background-color: ' . $color . ';"' . $this->add_extras($item['params'], $extras) . '>';
			return array(
				'valid' => true,
				'start' => $html,
				'end' => '</span>',
			);
		}

		// SIZE
		if ($tag === 'size') {
			$extras = $this->allow_styling ? array('class') : array();
			$size = 0;
			if (isset($item['params']['param'])) {
				$size = intval($item['params']['param']);
			} elseif (isset($item['params']['size'])) {
				$size = intval($item['params']['size']);
			} else {
				return $error;
			}
			if ($size > 0 && $size < 7) {
				// vBulletin-style sizes
				switch ($size) {
					case 1: $size = 7;
						break;
					case 2: $size = 8;
						break;
					case 3: $size = 10;
						break;
					case 4: $size = 12;
						break;
					case 5: $size = 15;
						break;
					case 6: $size = 24;
						break;
				}
			}
			if ($size < 6 || $size > 36) {
				return $error;
			}
			$html = '<span style="' . ($this->allow_styling && isset($item['params']['style']) ? htmlspecialchars($this->valid_style($item['params']['style'], '')) : '') . 'font-size: ' . $size . 'px;"' . $this->add_extras($item['params'], $extras) . '>';
			return array(
				'valid' => true,
				'start' => $html,
				'end' => '</span>',
			);
		}

		// CELL
		if ($tag === 'cell') {
			$extras = $this->allow_styling ? array('class') : array('class');
			if (isset($item['params']['width'])) {
				$width = ' width: ' . intval($item['params']['width']) . 'px;';
			}
			if (isset($item['params']['height'])) {
				$height = ' height: ' . intval($item['params']['height']) . 'px;';
			}
			if (isset($item['params']['bgcolor'])) {
				$bgcolor = $item['params']['bgcolor'];
				$bgcolor = $this->valid_color($bgcolor);
				if ($bgcolor != false) {
					$bgcolor = ' background-color: ' . $bgcolor . ';';
				}
			}
			$html = '<div style="' . ($this->allow_styling && isset($item['params']['style']) ? htmlspecialchars($this->valid_style($item['params']['style'], '')) : '') . $height . $width . $bgcolor . '"' . $this->add_extras($item['params'], $extras) . '>';
			return array(
				'valid' => true,
				'start' => $html,
				'end' => '</div>',
			);
		}

		// URL, A
		if ($tag === 'url' || $tag === 'a') {
			$extras = $this->allow_styling ? array('style', 'class', 'name', 'title') : array('name', 'title');
			$allow_nested = true;
			$strip_text = false;
			$show_content = true;
			$url = '';
			// get url
			if (!empty($item['params']['param'])) {
				$url = $item['params']['param'];
			} elseif (!empty($item['params']['href'])) {
				$url = $item['params']['href'];
			} elseif (!$item['is_html']) {
				$url = $content;
				$allow_nested = false;
				$strip_text = true;
			} else {
				return $error;
			}
			if ($url === $content && strlen($content) > 72) {
				$content = substr($content, 0, 44) . '...' . substr($content, strlen($content) - 15);
				$show_content = false;
			}
			// check if its email
			if (substr(strtolower($url), 0, 7) === 'mailto:') {
				$item['tag'] = 'email';
				return $this->process_tag($item);
			}
			// check for invalid urls
			$url = $this->valid_url($url, '');
			if (empty($url)) {
				return $error;
			}
			// check nested items
			if (!$allow_nested) {
				for ($i = 0; $i < count($item['items']); $i++) {
					$item['items'][$i]['valid'] = false;
				}
			} else {
				for ($i = 0; $i < count($item['next']); $i++) {
					$tag2 = $item['next'][$i]['tag'];
					$is_html = $item['next'][$i]['item']['is_html'];
					$item['next'][$i]['item']['inurl'] = true;
					if ($is_html && !$this->allowed_html[$tag2]['inurl']) {
						$item['next'][$i]['item']['valid'] = false;
					}
					if (!$is_html && !$this->allowed_bbcode[$tag2]['inurl']) {
						$item['next'][$i]['item']['valid'] = false;
					}
				}
			}
			// check for incomplete url
			if (substr(strtolower($url), 0, 4) === 'www.') {
				$url = 'http://' . $url;
			}
			// remove extra characters at the end
			$last_char = substr($url, strlen($url) - 1);
			$last_char_i = ord($last_char);
			if (($last_char_i > 32 && $last_char_i < 47) || ($last_char_i > 57 && $last_char_i < 65)) {
				$url = substr($url, 0, strlen($url) - 1);
			}
			// check if url is local
			$url_local = false;
			global $urls_local;
			for ($i = 0; $i < count($urls_local); $i++) {
				if (strlen($url) > strlen($urls_local[$i]) && strpos($url, $urls_local[$i]) === 0) {
					$url_local = true;
				}
			}
			if (!$url_local) {
				if (strpos($url, ':') === false) {
					$url_local = true;
				}
			}
			// generate html
			$extension = pathinfo($url);
			if ($extension['extension'] != 'jpg' && $extension['extension'] != 'png' && $extension['extension'] != 'gif' or stristr($url, '?')) {
				$html = '<a href="' . htmlspecialchars($url) . '"' . ($url_local ? '' : ' target="_blank" rel="nofollow"') . $this->add_extras($item['params'], $extras) . '>';
			} else {
				$html = '<a class="lightbox" href="' . htmlspecialchars($url) . '"' . ($url_local ? '' : ' target="_blank" rel="nofollow"') . $this->add_extras($item['params'], $extras) . '>';
			}
			if ($show_content) {
				return array(
					'valid' => true,
					'start' => $html,
					'end' => '</a>',
				);
			} else {
				return array(
					'valid' => true,
					'html' => $html . $content . '</a>',
					'allow_nested' => false,
				);
			}
		}

		// EMAIL
		if ($tag === 'email') {
			$extras = $this->allow_styling ? array('style', 'class', 'name', 'title') : array('name', 'title');
			$allow_nested = true;
			$strip_text = false;
			$url = '';
			// get url
			if (!empty($item['params']['param'])) {
				$url = $item['params']['param'];
			} elseif (!empty($item['params']['href'])) {
				$url = $item['params']['href'];
			} elseif (!empty($item['params']['addr'])) {
				$url = $item['params']['addr'];
			} else {
				$url = $content;
				$pos = strpos($url, '?');
				if ($pos) {
					$content = substr($url, 0, $pos);
				}
				if (substr(strtolower($url), 0, 7) === 'mailto:') {
					$content = substr($content, 7);
				}
				$allow_nested = false;
				$strip_text = true;
			}
			if (empty($url)) {
				return $error;
			}
			// disable nested items
			for ($i = 0; $i < count($item['items']); $i++) {
				$item['items'][$i]['valid'] = false;
			}
			// generate html
			if (substr(strtolower($url), 0, 7) === 'mailto:') {
				$url = substr($url, 7);
			}
			$email = '<a' . ($this->allow_styling && isset($item['params']['class']) ? '' : ' class="post-email"') . ' href="mailto:' . htmlspecialchars($url) . '"' . $this->add_extras($item['params'], $extras) . '>' . $content . '</a>';
			$pos = strpos($url, '?');
			if ($pos) {
				$str = substr($url, 0, $pos);
			} else {
				$str = $url;
			}
			$noscript = '<noscript>' . htmlspecialchars(str_replace(array('@', '.'), array(' [at] ', ' [dot] '), $str)) . '</noscript>';
			// make javascript from it
			$html = BBCODE_NOSMILIES_START . '<script language="JavaScript" type="text/javascript">' . "\n" . '' . "\n";
			for ($i = 0; $i < strlen($email); $i+=5) {
				$str = substr($email, $i, 5);
				$html .= 'document.write(\'' . addslashes($str) . '\');' . "\n";
			}
			$html .= "\n" . '' . "\n" . '</script>' . $noscript . BBCODE_NOSMILIES_END;
			return array(
				'valid' => true,
				'html' => $html,
				'allow_nested' => false,
			);
		}

		// QUOTE
		if ($tag === 'quote' || $tag === 'blockquote' || $tag === 'ot') {
			global $config_mg;
			if ($this->is_sig) {
				return $error;
			}
			if ($item['iteration'] > ($config_mg['quote_iterations'])) {
				return $error;
			}
			// check user
			$user = '';
			$post_rev = '';
			if (isset($item['params']['param'])) {
				$user = htmlspecialchars($item['params']['param']);
			} elseif (isset($item['params']['user'])) {
				$user = htmlspecialchars($item['params']['user']);
				if (isset($item['params']['userid']) && intval($item['params']['userid'])) {
					$user = '<a href="/user/' . intval($item['params']['userid']) . '">' . $user . '</a>';
				}
			}
			// generate html
			$html = '<blockquote class="quote"';
			if (isset($item['params']['post']) && intval($item['params']['post'])) {
				$post_rev = '[<a href="#_somewhat" onclick="javascript:open_postreview(\'show_post.php?p=' . intval($item['params']['post']) . '\');" class="genmed">' . $lang['ReviewPost'] . '</a>]';
				$html .= ' cite="' . VIEWTOPIC_MG . '?p=' . intval($item['params']['post']) . '#' . intval($item['params']['post']) . '"';
			}
			$html .= '>';
			if ($user) {
				if ($tag === 'ot') {
					$html .= '<div class="quote-user"><span class="error-message">Citējot</span>&nbsp;' . $user . ':&nbsp;' . $post_rev . '</div>';
				} else {
					$userid = $db->get_var("SELECT id FROM users WHERE nick = ('" . sanitize(trim($user)) . "') LIMIT 1");
					if ($userid) {
						$html .= '<div class="quote-user"><a class="profile-url" href="/user/' . $userid . '" title="' . $user . '">' . $user . '</a>&nbsp;' . rakstīja . ':&nbsp;' . $post_rev . '</div>';
					} else {
						$html .= '<div class="quote-user">' . $user . '&nbsp;' . rakstīja . ':&nbsp;' . $post_rev . '</div>';
					}
				}
			} else {
				if ($tag === 'ot') {
					$html .= '<div class="quote-nouser">&nbsp;<span class="error-message">' . Citāts . '</span>:</div>';
				} else {
					$html .= '<div class="quote-nouser">' . Citāts . ':</div>';
				}
			}
			$html .= '<div class="quote-message">';
			return array(
				'valid' => true,
				'start' => $html,
				'end' => '</div></blockquote>'
			);
		}

		// CODE
		if ($tag === 'code') {

			$text = $this->process_text($content, false, true);
			$search = array(
				'  ',
				"\t",
				"\n",
				"\r"
			);
			$replace = array(
				' &nbsp;',
				' &nbsp; &nbsp;',
				'<br />',
				''
			);
			$text = str_replace($search, $replace, $text);


			if (substr($text, 0, 1) === "\n") {
				$text = substr($text, 1);
			} elseif (substr($text, 0, 2) === "\r\n") {
				$text = substr($text, 2);
			}
			// convert to list

			$html = '<code class="prettyprint">' . $text . '</code>';
			$this->code_counter++;
			return array(
				'valid' => true,
				'html' => $html,
				'allow_nested' => false
			);
		}

		// Invalid tag
		return $error;
	}

// Check if bbcode tag is valid
	function valid_tag($tag, $is_html) {
		if ($is_html) {
			return (isset($this->allowed_html[$tag]) && preg_match('/^[a-z]+$/', $tag)) ? true : false;
		} else {
			return (isset($this->allowed_bbcode[$tag]) && (preg_match('/^[a-z]+$/', $tag) || $tag === '*')) ? true : false;
		}
	}

// Check if parameter name is valid
	function valid_param($param) {
		return preg_match('/^[a-z]+$/', $param);
	}

// Check if color is valid
	function valid_color($color) {
		$color = strtolower($color);
		if (preg_match('/^[a-z]+$/', $color)) {
			// text color
			return $color;
		}
		if (substr($color, 0, 1) === '#') {
			// normal color
			if (preg_match('/^[0-9a-f]+$/', substr($color, 1))) {
				if (strlen($color) == 4 || strlen($color) == 7) {
					return $color;
				}
			}
			return false;
		}
		// rgb color
		if (substr($color, 0, 4) === 'rgb(' && preg_match('/^rgb\([0-9]+,[0-9]+,[0-9]+\)$/', $color)) {
			$colors = explode(',', substr($color, 4, strlen($color) - 5));
			for ($i = 0; $i < 3; $i++) {
				if ($colors[$i] > 255) {
					return false;
				}
			}
			return sprintf('#%02X%02X%02X', $colors[0], $colors[1], $colors[2]);
		}
		// color with missing #
		if (preg_match('/^[0-9a-f]+$/', $color)) {
			if (strlen($color) == 3 || strlen($color) == 6) {
				return '#' . $color;
			}
		}
		return false;
	}

// Parse style
	function valid_style($style, $error = false) {
		$style = str_replace(array('\\', '"', '@'), array('', '', ''), $style);
		$str = strtolower($style);
		if (strpos($str, 'expression') !== false || strpos($str, 'javascript:') !== false || strpos($str, 'vbscript:') !== false || strpos($str, 'about:') !== false) {
			// attempt to use javascript
			return $error;
		}
		if (strpos($str, '//') !== false) {
			// attempt to use external file
			return $error;
		}
		if (strpos($str, '!important') !== false) {
			// attempt to completely mess up forum layout?
			return $error;
		}
		return $style;
	}

// Validate url
	function valid_url($url, $error = '') {
		$str = strtolower($url);
		if (substr($str, 0, 11) === 'javascript:') {
			// attempt to use javascript
			return $error;
		}
		if (substr($str, 0, 9) === 'vbscript:') {
			// attempt to use vbscript
			return $error;
		}
		if (substr($str, 0, 6) === 'about:') {
			// attempt to use about: url
			return $error;
		}
		return $url;
	}

// Add extras
	function add_extras($params, $extras) {
		$html = '';
		for ($i = 0; $i < count($extras); $i++) {
			if (isset($params[$extras[$i]])) {
				if ($extras[$i] === 'style') {
					$style = $this->valid_style($params['style']);
					if ($style !== false) {
						$html .= ' style="' . htmlspecialchars($style) . '"';
					}
				} else {
					$html .= ' ' . $extras[$i] . '="' . htmlspecialchars($params[$extras[$i]]) . '"';
				}
			}
		}
		return $html;
	}

// Splits string to tag and parameters
	function extract_params($tag, $is_html) {
		$this->tag = $tag;
		$this->params = array();
		$tag = str_replace("\t", ' ', $tag);
		// get parameters
		$pos_eq = strpos($tag, '=');
		$pos_space = strpos($tag, ' ');
		if ($pos_space !== false && $pos_eq !== false && $pos_space < $pos_eq) {
			// mutiple parameters
			$param_start = 0;
			$param_str = substr($tag, $pos_space + 1);
			$param_len = strlen($param_str);
			$this->tag = strtolower(substr($tag, 0, $pos_space));
			if (!$this->valid_tag($this->tag, $is_html)) {
				return false;
			}
			while ($param_start < $param_len) {
				// find entry for '='
				$pos = strpos($param_str, '=', $param_start);
				if ($pos === false) {
					return false;
				} else {
					// get parameter name
					$str = substr($param_str, $param_start, $pos - $param_start);
					if (!$this->valid_param($str)) {
						return false;
					}
					// get value
					$pos++;
					$quoted = false;
					if (substr($param_str, $pos, 1) === '"') {
						$pos2 = strpos($param_str, '"', $pos + 1);
						if ($pos2 === false) {
							// invalid quote. search for space instead
							$pos2 = strpos($param_str, ' ', $pos + 1);
						} else {
							$pos++;
							$quoted = true;
						}
					} else {
						$pos2 = strpos($param_str, ' ', $pos);
					}
					// end not found. counting until end of expression
					if ($pos2 === false) {
						$pos2 = $param_len;
					}
					$this->params[$str] = substr($param_str, $pos, $pos2 - $pos);
					$param_start = $pos2 + 1;
					if ($quoted) {
						$param_start++;
					}
				}
			}
		} elseif ($pos_eq !== false) {
			// single parameter
			$str = substr($tag, $pos_eq + 1);
			$this->tag = strtolower(substr($tag, 0, $pos_eq));
			if (!$this->valid_tag($this->tag, $is_html)) {
				return false;
			}
			if (strlen($str) > 1 && substr($str, 0, 1) === '"' && substr($str, strlen($str) - 1) === '"') {
				$str = substr($str, 1, strlen($str) - 2);
			}
			if (trim($str) !== $str) {
				return false;
			}
			$this->params['param'] = $str;
		} else {
			// no parameters
			$this->tag = strtolower($tag);
			if (!$this->valid_tag($this->tag, $is_html)) {
				return false;
			}
		}
		return true;
	}

// Recusive function that converts text to bbcode tree
	function push($start, $level, $prev_tags) {
		//echo '<b>push</b>(', $start, ', ', $level, ', (', implode(',', $prev_tags), '))<br />';
		$items = array();
		$pos_start_bbcode = $this->allow_bbcode ? strpos($this->text, '[', $start) : false;
		$pos_start_html = $this->allow_html ? strpos($this->text, '<', $start) : false;
		while ($pos_start_bbcode !== false || $pos_start_html !== false) {
			$pos_start = $pos_start_bbcode === false ? $pos_start_html : ($pos_start_html === false ? $pos_start_bbcode : min($pos_start_bbcode, $pos_start_html));
			$is_html = $pos_start_html === $pos_start ? true : false;
			$prev_start = $start;
			// found tag. get data.
			$pos_end = strpos($this->text, $is_html ? '>' : ']', $pos_start);
			if ($pos_end === false) {
				$tag_valid = false;
			} else {
				$code = substr($this->text, $pos_start, $pos_end - $pos_start + 1);
				// check if tag is valid and get type of tag
				$tag_valid = true;
				$tag_closing = false;
				$tag_self_closing = false;
				if (strlen($code) < 3) {
					$tag_valid = false;
				} elseif (!$is_html && strpos($code, '[', 1) !== false) {
					$tag_valid = false;
				} elseif ($is_html && strpos($code, '<', 1) !== false) {
					$tag_valid = false;
				} elseif (!$is_html && strpos($code, "\n") !== false) {
					$tag_valid = false;
				} elseif (substr($code, 0, 2) === ($is_html ? '</' : '[/')) {
					$tag_closing = true;
					$tag = substr($code, 2, strlen($code) - 3);
				} elseif (substr($code, strlen($code) - 3) === ($is_html ? ' />' : ' /]')) {
					$tag_self_closing = true;
					$tag = substr($code, 1, strlen($code) - 4);
				} else {
					$tag = substr($code, 1, strlen($code) - 2);
				}
				// do not process tag if it requires too much recursion
				if ($level > 10 && (!$tag_closing && !$tag_self_closing)) {
					$tag_valid = false;
				}
				// special tags
				if ($code === '[*]' || $code === '[hr]') {
					$tag_self_closing = true;
				}
			}
			if ($tag_valid) {
				$start = $pos_end;
				$params = array();
				if (!$tag_closing) {
					if (!$this->extract_params($tag, $is_html)) {
						$tag_valid = false;
					} else {
						$tag = $this->tag;
						$params = $this->params;
					}
				} else {
					if (strpos($tag, ' autourl=' . AUTOURL)) {
						$tag = str_replace(' autourl=' . AUTOURL, '', $tag);
					}
					$tag = strtolower($tag);
					if (!$this->valid_tag($tag, $is_html)) {
						$tag_valid = false;
					}
				}
			}
			if ($tag_valid) {
				if ($tag_closing) {
					// check if this is correct closing tag
					if (in_array($tag, $prev_tags)) {
						return array(
							'items' => $items,
							'tag' => $tag,
							'pos' => $pos_end,
							'start' => $pos_start,
							'len' => strlen($code)
						);
					}
				} elseif ($tag_self_closing) {
					// found self-closing tag
					$items[] = array(
						'tag' => $tag,
						'code' => $code,
						'params' => $params,
						'start' => $pos_start,
						'start_len' => strlen($code),
						'end' => $pos_end + 1,
						'end_len' => 0,
						'level' => $level + 1,
						'iteration' => 0,
						'self_closing' => 1,
						'prev' => array(),
						'next' => array(),
						'is_html' => $is_html,
						'items' => array()
					);
				} else {
					// found correct tag. call recursive search
					$result = $this->push($pos_end, $level + 1, array_merge($prev_tags, array($tag)));
					if ($result['tag'] === $tag) {
						// found correctly finished tag
						$items[] = array(
							'tag' => $tag,
							'code' => $code,
							'params' => $params,
							'start' => $pos_start,
							'start_len' => strlen($code),
							'end' => $result['start'],
							'end_len' => $result['len'],
							'level' => $level + 1,
							'iteration' => 0,
							'self_closing' => 2,
							'prev' => array(),
							'next' => array(),
							'is_html' => $is_html,
							'items' => $result['items']
						);
						$start = $result['pos'];
					} else {
						$items = array_merge($items, $result['items']);
						return array(
							'items' => $items,
							'tag' => $result['tag'],
							'pos' => $result['pos'],
							'start' => $result['start'],
							'len' => $result['len']
						);
					}
				}
			} else {
				$start = $pos_start + 1;
			}
			$pos_start_bbcode = $this->allow_bbcode ? strpos($this->text, '[', $start) : false;
			$pos_start_html = $this->allow_html ? strpos($this->text, '<', $start) : false;
		}
		return array(
			'items' => $items,
			'tag' => false,
		);
	}

// Debug fuction. Prints tree of bbcode
	function debug($items) {
		for ($i = 0; $i < count($items); $i++) {
			$item = $items[$i];
			if ($item['tag']) {
				for ($j = 0; $j < $item['level']; $j++) {
					echo '-';
				}
				echo ' ', $item['tag'], ' (';
				$first = true;
				foreach ($item['params'] as $var => $value) {
					if (!$first)
						echo ', ';
					$first = false;
					echo $var, '="', htmlspecialchars($value), '"';
				}
				echo ")<br />\n";
				$this->debug($item['items']);
			}
		}
	}

// Post-processing. Adds previous/next items to every item.
	function add_pointers(&$items, $prev_tags) {
		$tags = array();
		for ($i = 0; $i < count($items); $i++) {
			$item = &$items[$i];
			$tags[] = array(
				'tag' => $item['tag'],
				'item' => &$items[$i]
			);
			$iterations = 0;
			for ($j = 0; $j < count($prev_tags); $j++) {
				if ($prev_tags[$j]['tag'] === $item['tag']) {
					$iterations++;
				}
			}
			$item['iteration'] = $iterations;
			$item['prev'] = $prev_tags;
			// todo: check if subitems are allowed
			// parse sub-items
			if (count($item['items'])) {
				$arr = array(
					'tag' => $item['tag'],
					'item' => &$items[$i]
				);
				$item['next'] = $this->add_pointers($item['items'], array_merge($prev_tags, array($arr)));
				$tags = array_merge($tags, $item['next']);
			}
		}
		return $tags;
	}

// Process text
	function process_text($text, $br = true, $chars = true) {
		$search = array(
			'[url autourl=' . AUTOURL . ']',
			'[/url autourl=' . AUTOURL . ']',
			'[email autourl=' . AUTOURL . ']',
			'[/email autourl=' . AUTOURL . ']'
		);
		$replace = array('', '', '', '');
		$text = str_replace($search, $replace, $text);
		if ($chars) {
			$text = htmlspecialchars($text);
			$text = str_replace('&amp;#', '&#', $text);
		} else {
			$text = str_replace(
					array('&amp;', '>', '<', '"', '&amp;#'), array('&amp;amp;', '&gt;', '&lt;', '&quot;', '&#'), $text);
		}
		if ($br) {
			$text = str_replace("\n", "<br />\n", $text);
		}
		return $text;
	}

// Process tree
	function process($start, $end, &$items) {
		$html = '';
		for ($i = 0; $i < count($items); $i++) {
			$item = &$items[$i];
			// check code before item
			if ($item['start'] > $start) {
				$html .= $this->process_text(substr($this->text, $start, $item['start'] - $start));
			}
			// process tag
			$result = $this->process_tag($item);
			if ($result['valid'] && !isset($result['html'])) {
				$html .= $result['start'];
				if (!isset($result['allow_nested']) || $result['allow_nested']) {
					// process code inside tag
					$html .= $this->process($item['start'] + $item['start_len'], $item['end'], $item['items']);
				}
				$html .= $result['end'];
			} elseif ($result['valid']) {
				$html .= $result['html'];
			} else {
				// invalid tag. show html code for it and process nested tags
				$item['valid'] = false;
				if ($item['start_len']) {
					$html .= $this->process_text(substr($this->text, $item['start'], $item['start_len']));
				}
				$html .= $this->process($item['start'] + $item['start_len'], $item['end'], $item['items']);
				if ($item['end_len']) {
					$html .= $this->process_text(substr($this->text, $item['end'], $item['end_len']));
				}
			}
			$start = $item['end'] + $item['end_len'];
		}
		// process code after item
		if ($start < $end) {
			$html .= $this->process_text(substr($this->text, $start, $end - $start));
		}
		return $html;
	}

// Prepare smilies list
	function prepare_smilies() {
		if (!$this->allow_smilies) {
			return;
		}
		$this->replaced_smilies = array();
		for ($i = 0; $i < count($this->allowed_smilies); $i++) {
			if (strpos($this->text, $this->allowed_smilies[$i]['code']) !== false) {
				$this->replaced_smilies[] = $this->allowed_smilies[$i];
			}
		}
	}

// Process smilies
	function process_smilies() {
		$valid_chars_prev = array('', ' ', "\n", "\r", "\t", '>');
		$valid_chars_next = array('', ' ', "\n", "\r", "\t", '<');
		if (!$this->allow_smilies && !count($this->replaced_smilies)) {
			return;
		}
		for ($i = 0; $i < count($this->replaced_smilies); $i++) {
			$code = $this->replaced_smilies[$i]['code'];
			$text = $this->replaced_smilies[$i]['replace'];
			$code_len = strlen($code);
			$text_len = strlen($text);
			$pos = strpos($this->html, $code);
			while ($pos !== false) {
				$valid = false;
				// check previous character
				$prev_char = $pos > 0 ? substr($this->html, $pos - 1, 1) : '';
				if (in_array($prev_char, $valid_chars_prev)) {
					// check next character
					$next_char = substr($this->html, $pos + $code_len, 1);
					if (in_array($next_char, $valid_chars_next)) {
						// make sure we aren't inside html code
						$pos1 = strpos($this->html, '<', $pos + $code_len);
						$pos2 = strpos($this->html, '>', $pos + $code_len);
						if ($pos2 === false || ($pos1 && $pos1 < $pos2)) {
							// make sure we aren't inside nosmilies zone
							$pos1 = strpos($this->html, BBCODE_NOSMILIES_START, $pos + $code_len);
							$pos2 = strpos($this->html, BBCODE_NOSMILIES_END, $pos + $code_len);
							if ($pos2 === false || ($pos1 && $pos1 < $pos2)) {
								$valid = true;
							}
						}
					}
				}
				if ($valid) {
					$this->html = substr($this->html, 0, $pos) . $text . substr($this->html, $pos + $code_len);
					$pos += $text_len;
				} else {
					$pos++;
				}
				$pos = strpos($this->html, $code, $pos);
			}
		}
	}

// Make urls clickable
	function process_urls() {
		// characters allowed in email
		$chars = array();
		for ($i = 224; $i < 256; $i++) {
			if ($i != 247) {
				$chars .= chr($i);
			}
		}
		// search and replace arrays
		$search = array(
			"/([\s>])((https?|ftp):\/\/|www\.)([^ \r\n\(\)\^\$!`\"'\|\[\]\{\}<>]+)/si",
			"/([\s>])([_a-zA-Z0-9\-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9\-{$chars}]+(\.[a-zA-Z0-9\-{$chars}]+)*(\.[a-zA-Z]{2,}))/si",
		);
		$replace = array(
			"\\1[url autourl=" . AUTOURL . "]\\2\\4[/url autourl=" . AUTOURL . "]",
			"\\1[email autourl=" . AUTOURL . "]\\2[/email autourl=" . AUTOURL . "]",
		);
		$this->text = preg_replace($search, $replace, ' ' . $this->text . ' ');
		$this->text = substr($this->text, 1, strlen($this->text) - 2);
	}

// Converts text to html code
	function parse($text, $id = false) {
		if (defined('IN_PHPBB')) {
			$search = array(
				$id ? ':' . $id : '',
				'code:1]',
				'list:o]',
			);
			$replace = array(
				'',
				'code]',
				'list]',
			);
			$text = str_replace($search, $replace, $text);
			if ($id) {
				$text = undo_htmlspecialchars($text);
			}
		}
		// reset variables
		$this->text = $text;
		$this->data = array();
		$this->html = '';
		$this->prepare_smilies();
		$this->process_urls();
		$this->code_counter = 0;
		// if bbcode and html are disabled then return unprocessed text
		if (!$this->allow_bbcode && !$this->allow_html) {
			$this->html = $this->text;
			$this->process_smilies();
			return $this->html;
		}
		// convert to tree structure
		$result = $this->push(0, 0, array());
		$this->data = $result['items'];

		/*
		  ob_start();
		  $this->debug($this->data);
		  $str = ob_get_contents();
		  ob_end_clean();
		  $this->html = 'Debug:<br />' . $str;
		  return $this->html;
		 */

		// add prev/next pointers and count iterations
		$this->add_pointers($this->data, array());
		// convert to html
		$this->html = $this->process(0, strlen($this->text), $this->data);
		$this->process_smilies();

		return $this->html;
	}

}

$bbcode = new BBCode();


// Need to initialize the random numbers only ONCE
mt_srand((double) microtime() * 1000000);

function make_bbcode_uid() {
// Unique ID for this message..

	$uid = md5(mt_rand());
	$uid = substr($uid, 0, BBCODE_UID_LEN);

	return $uid;
}

if (defined('SMILIES_TABLE')) {
	$sql = "SELECT * FROM " . SMILIES_TABLE;
	if (($result = $db->sql_query($sql)) !== false) {
		$smilies = $db->sql_fetchrowset($result);
		$bbcode->allowed_smilies = array();
		for ($i = 0; $i < count($smilies); $i++) {
			$arr = array(
				'code' => $smilies[$i]['code'],
				'replace' => '<img src="http://' . $_SERVER['HTTP_HOST'] . $board_config['script_path'] . 'images/smiles/' . $smilies[$i]['smile_url'] . '" alt="' . htmlspecialchars($smilies[$i]['emotion']) . '" />'
			);
			$bbcode->allowed_smilies[] = $arr;
		}
	}
}

function undo_htmlspecialchars($input, $full_undo = false) {
	if ($full_undo) {
		$input = str_replace('&nbsp;', '', $input);
	}
	$input = preg_replace("/&gt;/i", ">", $input);
	$input = preg_replace("/&lt;/i", "<", $input);
	$input = preg_replace("/&quot;/i", "\"", $input);
	$input = preg_replace("/&amp;/i", "&", $input);

	if ($full_undo) {
		if (preg_match_all('/&\#([0-9]+);/', $input, $matches) && count($matches)) {
			$list = array();
			for ($i = 0; $i < count($matches[1]); $i++) {
				$list[$matches[1][$i]] = true;
			}
			$search = array();
			$replace = array();
			foreach ($list as $var => $value) {
				$search[] = '&#' . $var . ';';
				$replace[] = chr($var);
			}
			$input = str_replace($search, $replace, $input);
		}
	}

	return $input;
}

function make_clickable($text) {
	$text = preg_replace('#(script|about|applet|activex|chrome):#is', "\\1:", $text);
	$text = preg_replace('#(script|about|applet|activex|chrome):#is', "\\1&#058;", $text);

// pad it with a space so we can match things at the start of the 1st line.
	$ret = ' ' . $text;

// matches an "xxxx://yyyy" URL at the start of a line, or after a space.
// xxxx can only be alpha characters.
// yyyy is anything up to the first space, newline, comma, double quote or <
	$ret = preg_replace("#(^|[\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $ret);

// matches a "www|ftp.xxxx.yyyy[/zzzz]" kinda lazy URL thing
// Must contain at least 2 dots. xxxx contains either alphanum, or "-"
// zzzz is optional.. will contain everything up to the first space, newline,
// comma, double quote or <.
	$ret = preg_replace("#(^|[\n ])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $ret);


// matches an email@domain type address at the start of a line, or after a space.
// Note: Only the followed chars are valid; alphanums, "-", "_" and or ".".
	$ret = preg_replace("#(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $ret);

// Remove our padding..
	$ret = substr($ret, 1);

	return($ret);
}

function rainbow($text) {
//
// Returns text highlighted in rainbow colours
//

	if (!defined('RAINBOW_COLORS_LOADED')) {
		$colors = load_rainbow_colors();
	}
	$text = trim(stripslashes($text));
	$length = strlen($text);
	$result = '';
	$color_counter = 0;
	$TAG_OPEN = false;
	for ($i = 0; $i < $length; $i++) {
		$char = substr($text, $i, 1);
		if (!$TAG_OPEN) {
			if ($char == '<') {
				$TAG_OPEN = true;
				$result .= $char;
			} elseif (preg_match("#\S#i", $char)) {
				$color_counter++;
				$result .= '<span style="color: ' . $colors[$color_counter] . ';">' . $char . '</span>';
				$color_counter = ( $color_counter == 7 ) ? 0 : $color_counter;
			} else {
				$result .= $char;
			}
		} else {
			if ($char == '>') {
				$TAG_OPEN = false;
			}
			$result .= $char;
		}
	}
	return $result;
}

function load_rainbow_colors() {
	return array(
		1 => 'red',
		2 => 'orange',
		3 => 'yellow',
		4 => 'green',
		5 => 'blue',
		6 => 'indigo',
		7 => 'violet'
	);
}

// Mighty Gorgon Functions
function bbcode_killer_mg($text, $bbcode_uid) {
// Pad it with a space so we can distinguish between FALSE and matching the 1st char (index 0).
// This is important; bbencode_quote(), bbencode_list(), and bbencode_code() all depend on it.
	$text = " " . $text;

// First: If there isn't a "[" and a "]" in the message, don't bother.
	if (!(strpos($text, "[") && strpos($text, "]"))) {
		// Remove padding, return.
		$text = substr($text, 1);
		return $text;
	}

// Stripping out the $bbcode_uid
	$text = preg_replace("/\:(([a-z0-9]:)?)" . $bbcode_uid . "/s", "", $text);

// Strip simple tags
	$look_up_array = array(
		//"[code]", "[/code]",
		//"[php]","[/php]",
		//"[cpp]","[/cpp]",
		"[b]", "[/b]",
		"[u]", "[/u]",
		"[i]", "[/i]",
		"[list]", "[/list]",
		"[list=1]",
		"[list=a]",
		"[*]",
		"[url]", "[/url]",
		"[email]", "[/email]",
		"[img]", "[img align=left]", "[img align=right]", "[/img]",
		"[imgl]", "[/imgl]",
		"[imgr]", "[/imgr]",
		"[albumimg]", "[/albumimg]",
		"[albumimgl]", "[/albumimgl]",
		"[albumimgr]", "[/albumimgr]",
		"[blur]", "[/blur]",
		"[fade]", "[/fade]",
		"[rainbow]", "[/rainbow]",
		"[jiggle]", "[/jiggle]",
		"[pulse]", "[/pulse]",
		"[neon]", "[/neon]",
		"[updown]", "[/updown]",
		"[flipv]", "[/flipv]",
		"[fliph]", "[/fliph]",
		"[wave]", "[/wave]",
		"[offtopic]", "[/offtopic]",
		"[strike]", "[/strike]",
		"[sup]", "[/sup]",
		"[sub]", "[/sub]",
		"[spoil]", "[/spoil]",
		"[table]", "[/table]",
		"[tr]", "[/tr]",
		"[td]", "[/td]",
		"[em]", "[/em]",
		"[strong]", "[/strong]",
		"[center]", "[/center]",
		//"[]", "[/]",
		"[hr]",
	);


	$text = str_replace($look_up_array, "", $text);

// Colours
	$color_code = "(\#[0-9A-F]{6}|[a-z]+)";
	$look_up_array = array(
		"/\[color=" . $color_code . "\]/si", "/\[\/color\]/si",
		"/\[glow=" . $color_code . "\]/si", "/\[\/glow\]/si",
		"/\[shadow=" . $color_code . "\]/si", "/\[\/shadow\]/si",
		"/\[highlight=" . $color_code . "\]/si", "/\[\/highlight\]/si",
		"/\[size=([\-\+]?[1-3]?[0-9])\]/si", "/\[\/size\]/si",
		"/\[url=([a-z0-9\-\.,\?!%\*_\/:;~\\&$@\/=\+]+)\]/si", "/\[\/url\]/si",
		"/\[web=([a-z0-9\-\.,\?!%\*_\/:;~\\&$@\/=\+]+)\]/si", "/\[\/web\]/si",
		"/\[font=(Arial|Arial Black|Arial Bold|Arial Bold Italic|Arial Italic|Comic Sans MS|Comic Sans MS Bold|Courier New|Courier New Bold|Courier New Bold Italic|Courier New Italic|Impact|Lucida Console|Lucida Sans Unicode|Microsoft Sans Serif|Symbol|Tahoma|Tahoma Bold|Times New Roman|Times New Roman Bold|Times New Roman Bold Italic|Times New Roman Italic|Traditional Arabic|Trebuchet MS|Trebuchet MS Bold|Trebuchet MS Bold Italic|Trebuchet MS Italic|Verdana|Verdana Bold|Verdana Bold Italic|Verdana Italic|Webdings|Wingdings|)\]/si", "/\[\/font\]/si",
		"/\[marq=(left|right|up|down)\]/si", "/\[\/marq\]/si",
		"/\[marquee direction=(left|right|up|down)\]/si", "/\[\/marquee\]/si",
		"/\[align=(left|center|right|justify)\]/si", "/\[\/align\]/si",
	);

	$text = preg_replace($look_up_array, "", $text);

// [QUOTE] and [/QUOTE]
	/*
	  $text = str_replace("[quote]","", $text);
	  $text = str_replace("[/quote]", "", $text);
	  $text = preg_replace("/\[quote=(?:\"?([^\"]*)\"?)\]/si", "", $text);
	 */

// Remove our padding from the string..
	$text = substr($text, 1);

	return $text;
}

function plain_message_mg($text, $bbcode_uid) {
	$text = bbcode_killer_mg($text, $bbcode_uid);
//$text = preg_replace("/\r\n/", "<br />", $text);
	$text = preg_replace("/\r\n/", "\n", $text);
	$text = str_replace('<br />', "\n", $text);

	return $text;
}

function bbcuid_killer_mg($text, $bbcode_uid) {
// Stripping out the $bbcode_uid
	$text = preg_replace("/\:(([a-z0-9]:)?)" . $bbcode_uid . "/s", "", $text);
	return $text;
}

function bbcuid_killer2_mg($text, $bbcode_uid) {
// Stripping out the $bbcode_uid
	$text = preg_replace("/:(([a-z0-9]+:)?)" . $bbcode_uid . "\]/si", ']', $text);
	return $text;
}

function bbcode_killer_empty_uid($text) {
	preg_replace('/\:[0-9a-z\:]+\]/si', ']', $text);
}

function nl2any($string, $tag = 'p', $feed = '') {
// making tags
	$start_tag = "<$tag" . ($feed ? ' ' . $feed : '') . '>';
	$end_tag = "</$tag>";

// exploding string to lines
	$lines = preg_split('`[\n\r]+`', trim($string));

// making new string
	$string = '';
	foreach ($lines as $line)
		$string .= "$start_tag$line$end_tag\n";

	return $string;
}

function any2nl($string, $tag = 'p') {
//exploding
	preg_match_all("`<" . $tag . "[^>]*>(.*)</" . $tag . ">`Ui", $string, $results);
// reimploding without tags
	return implode("\n", array_filter($results[1]));
}

function br2nl($str) {
	$str = preg_replace("/(\r\n|\n|\r)/", "", $str);
	return preg_replace("=<br */?>=i", "\n", $str);
}

function nl2br_mg($text) {
	$text = preg_replace("/\r\n/", "\n", $text);
	$text = str_replace('<br />', "\n", $text);

	return $text;
}

function acronym_sort($a, $b) {
	if (strlen($a['acronym']) == strlen($b['acronym'])) {
		return 0;
	}

	return ( strlen($a['acronym']) > strlen($b['acronym']) ) ? -1 : 1;
}

function acronym_pass($message) {
	static $orig, $repl;

	if (!isset($orig)) {
		global $db, $board_config;
		$orig = $repl = array();

		$sql = 'SELECT * FROM ' . ACRONYMS_TABLE;
		if (!$result = $db->sql_query($sql)) {
			message_die(GENERAL_ERROR, "Couldn't obtain acronyms data", "", __LINE__, __FILE__, $sql);
		}

		$acronyms = $db->sql_fetchrowset($result);

		if (count($acronyms)) {
			usort($acronyms, 'acronym_sort');
		}

		for ($i = 0; $i < count($acronyms); $i++) {
			$orig[] = '#\b(' . phpbb_preg_quote($acronyms[$i]['acronym'], "/") . ')\b#';
			//$orig[] = "/(?<=.\W|\W.|^\W)" . phpbb_preg_quote($acronyms[$i]['acronym'], "/") . "(?=.\W|\W.|\W$)/";
			$repl[] = '<acronym title="' . $acronyms[$i]['description'] . '">' . $acronyms[$i]['acronym'] . '</acronym>';
			;
		}
	}

	if (count($orig)) {
		$segments = preg_split('#(<acronym.+?>.+?</acronym>|<.+?>)#s', $message, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

		$message = '';

		foreach ($segments as $seg) {
			if ($seg[0] != '<' && $seg[0] != '[') {
				$message .= str_replace('\"', '"', substr(preg_replace('#(\>(((?>([^><]+|(?R)))*)\<))#se', "preg_replace(\$orig, \$repl, '\\0')", '>' . $seg . '<'), 1, -1));
			} else {
				$message .= $seg;
			}
		}
	}

	return $message;
}
