<?php
/**
 *  Funkcijas iOS lietotnes pieprasījumiem.
 */

/**
 *  Pieprasījuma atbildei pievieno informāciju par lietotāju.
 *  Izmantota brīdī, kad lietotājs veiksmīgi autentificējies.
 */
function api_append_profile_info() {
	global $db, $auth, $img_server, $online_users;
    
    $is_online = 0;
	$device = 0; // 0 - dators, 1 - mob., 2 - droīds, 3 - ios

	// vai lietotājs ir tiešsaistē?
	if ((!empty($online_users['onlineusers'][$auth->id])) || 
        (!empty($online_users['onlineusers']) && 
        in_array($auth->nick, $online_users['onlineusers']))) {	
		$is_online = 1;
	}

	// caur kādu ierīci lietotājs ielādējis saturu?
	if (!empty($online_users['iosusers']) && 
		in_array($auth->nick, $online_users['iosusers'])) {
		$device = 3;
	} else if (!empty($online_users['androidusers']) && 
		in_array($auth->nick, $online_users['androidusers'])) {
		$device = 2;
	} else if (!empty($online_users['mobileusers']) && 
		in_array($auth->nick, $online_users['mobileusers'])) {
		$device = 1;
	}

    $arr = array(
        'nick' => $auth->nick,
        'params' => $auth->id.'|'.$auth->level.'|'.$is_online.'|'.$device.'|0', // 0 - not banned, 1 - banned
        'avatar_url' => $img_server.'/userpic/medium/'.$auth->avatar
    );

	api_append(array('profile' => $arr));
}

/**
 *  Atgriež datus par norādīto lietotāju.
 *
 *  Lietotnē tie ir nepieciešami specifiskā formātā (ne-HTML), lai
 *  lietotne pēc tam spētu lietotājvārdu atbilstoši "izdaiļot" ar krāsām.
 *
 *  Ja tiek izmantoti noklusētie parametri, atgriež datus par to lietotāju,
 *  kas šo funkciju izsauc. Norādot parametrus, dati atbildīs norādītajam
 *  lietotājam.
 */
function api_fetch_user($user_id = 0, $nick = '-', $level = 0, $append_avatar = false) {
	global $auth, $online_users, $busers;

	// dati par autorizēto lietotāju
	if ($user_id == 0) {
		$user_id    = (int)$auth->id;
		$user_nick  = $auth->nick;
		$user_level = (int)$auth->level;

	// dati par norādīto lietotāju
	} else {
		$user_id    = (int)$user_id;
		$user_nick  = $nick;
		$user_level = (int)$level;
	}

	$is_online = 0;
	$is_banned = 0;
	$device = 0; // 0 - dators, 1 - mob., 2 - droīds, 3 - ios

	// vai lietotājs ir tiešsaistē?
	if ((!empty($online_users['onlineusers'][$user_id])) || 
        (!empty($online_users['onlineusers']) && 
        in_array($user_nick, $online_users['onlineusers']))) {	
		$is_online = 1;
	}

	// caur kādu ierīci lietotājs ielādējis saturu?
	if (!empty($online_users['iosusers']) && 
		in_array($user_nick, $online_users['iosusers'])) {
		$device = 3;
	} else if (!empty($online_users['androidusers']) && 
		in_array($user_nick, $online_users['androidusers'])) {
		$device = 2;
	} else if (!empty($online_users['mobileusers']) && 
		in_array($user_nick, $online_users['mobileusers'])) {
		$device = 1;
	}

	// vai lietotājs ir bloķēts un tā lietotājvārds jāpārsvītro?
	if (!empty($busers) && !empty($busers[$user_id])) {
		$is_banned = 1;
	}
    
    $arr = array(
        'nick' => $user_nick,
        'params' => $user_id.'|'.$user_level.'|'.$is_online.'|'.$device.'|'.$is_banned
    );
    
    if ($append_avatar) {
        $usr = get_user(($user_id === 0 ? $auth->id : $user_id));
        if ($usr) {
            $arr['avatar_url'] = api_get_user_avatar($usr, 'm');
        }
    }
    
    return $arr;
}

/**
 *  Pieprasījuma atbildei pievieno informāciju par lietotāja liegumu.
 *
 *  Šī funkcija tikai pievieno datus atbildei. Tas, vai lietotājam
 *  ir liegums, tiek noskaidrots jau iepriekš.
 *
 *  @param $type    1 - ip liegums, 2 - profila liegums
 *  @param query    ja $type = 1, datus ņem no šī query
 */
function api_fetch_ban($type = 1, $ip_banned = null) {
	global $db, $auth;
	global $json_page, $json_banned;

	$type = (int)$type;
	if ($type !== 1 && $type !== 2) {
		return false;
	}
	
	$json_banned = $type;
	
	// profila liegums
	if ($type === 2) {
	
		$prof_banned = $db->get_row("
			SELECT * FROM `banned` 
			WHERE `active` = 1 AND `user_id` = ".(int)$auth->id."
            LIMIT 1
		");
		
		if (!$prof_banned) {
			api_error('Neizdevās atlasīt lieguma informāciju.');
		} else {
			$from_user = get_user($prof_banned->author);
			$to_user = get_user($prof_banned->user_id);
			
			if ($from_user && $to_user) {
				api_append(array(
					'ip' => $prof_banned->ip,
					'to_user' => api_fetch_user($to_user->id,
						$to_user->nick, $to_user->level),
					'reason' => $prof_banned->reason,
					'from_user' => api_fetch_user($from_user->id,
						$from_user->nick, $from_user->level),
					'date_from' => date('d.m.Y, H:i', $prof_banned->time),
					'date_to' => date('d.m.Y, H:i', $prof_banned->time + 
						$prof_banned->length),
					'remaining' => strTime($prof_banned->time + 
						$prof_banned->length - time())
				));
			}
		}
		
	// ip liegums
	} else if ($type === 1 && $ip_banned != null) {
	
		$from_user = get_user($ip_banned->author);
		$to_user = get_user($ip_banned->user_id);
		
		if ($from_user && $to_user) {
			api_append(array(
				'ip' => $ip_banned->ip,
				'to_user' => api_fetch_user($to_user->id,
					$to_user->nick, $to_user->level),
				'reason' => $ip_banned->reason,
				'from_user' => api_fetch_user($from_user->id,
					$from_user->nick, $from_user->level),
				'date_from' => date('d.m.Y, H:i', $ip_banned->time),
				'date_to' => date('d.m.Y, H:i', $ip_banned->time + 
						$ip_banned->length),
				'remaining' => strTime($ip_banned->time + 
					$ip_banned->length - time())
			));
		}
	}
}

/**
 *  Tiešsaistē esošie lietotāji.
 *
 *  Atgriezīs sarakstu ar tiešsaistē esošajiem lietotājiem 
 *  atvērtajā apakšprojektā pēdējās x sekundēs.
 */
function api_fetch_online($force = false) {
	global $db, $m, $auth, $api_lang;
	global $online_users, $busers;
	
	// laiks sekundēs, kurā lietotāju uzskata par tiešsaistē esošu
	$online_seconds = 300;
	
	$data = array();
	
	// satura nolasīšana no memcached
	if ($force || !($data = $m->get('api-online-'.$api_lang))) {

		$online = null;
		$classes = null;
		
		$last_seen = date('Y-m-d H:i:s', time() - $online_seconds);
		
		$lastseen = $db->get_results("
			SELECT
				DISTINCT(`visits`.`user_id`) AS `user_id`,
				`users`.`nick`,
				`users`.`level`
			FROM `visits`
				JOIN `users` ON `visits`.`user_id` = `users`.`id`
			WHERE
				`visits`.`site_id` = ".$api_lang." AND
				`visits`.`lastseen` > '".$last_seen."'
			ORDER BY
				`users`.`nick` ASC
		");

		if (!$lastseen) {
			api_append(array(
				'online' => 0,
				'registered' => 0,
				'users' => array()
			));
			return false;
		}
		
		$cnt_registered = 0;

		foreach ($lastseen as $user) {       

			// noteiks ierīci, no kādas lietotājs pieslēdzies
			$device = 0;
			if (!empty($online_users['androidusers']) && 
				in_array($user->nick, $online_users['androidusers'])) {
				$device = 2; // androīda apps
			} else if (!empty($online_users['iosusers']) && 
				in_array($user->nick, $online_users['iosusers'])) {
				$device = 3; // ios apps
			} else if (!empty($online_users['mobileusers']) && 
				in_array($user->nick, $online_users['mobileusers'])) {
				$device = 1; // mob. tel.
			} else {
				$device = 0; // dators
			}
			
			// pārbauda, vai lietotājs ir bloķēts
			$is_banned = 0;
			if (!empty($busers) && !empty($busers[$user->user_id])) {
				$is_banned = 1;
			}
		
			$online[] = array(
				'nick' => $user->nick,
                'params' => $user->user_id.'|'.$user->level.'|1|'.$device.'|'.$is_banned
			);
			
			$cnt_registered++;
		}

		$data = array(
			'online' => (int)$auth->hosts_online,
			'registered' => (int)$cnt_registered,
			'users' => $online
		);
		
		$m->set('api-online-'.$api_lang, $data, false, 15);
	}
	
	api_append($data);
}
