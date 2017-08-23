<?php
/**
 *  API kolekciju modulī izmantotās funkcijas.
 *
 *  Šīs izsauc visu realizēto lietotņu API.
 */

/**
 *  Atbildei pievieno sarakstu ar exs.lv izmantotajiem smaidiņiem.
 */
function api_collections_smilies() {
    global $img_server;
	
    $smilies_arr = insert_smilies('', true);
    
    if (empty($smilies_arr)) {
        api_error('Smaidiņu lejupielāde neizdevās!');
        api_log('Neizdevās nolasīt masīvu ar exs smaidiņiem.');
    } else {
        
        // no saņemtā masīva izveido pareizāku masīvu :)
        $final_arr = null;
        
        foreach ($smilies_arr as $key => $value) {
            $final_arr[] = array(
                'code' => $key,
                'title' => $value,
                'url' => $img_server.'/bildes/fugue-icons/'.$value
            );
        }
        
        api_append(array(
            'smilies_count' => count($final_arr),
            'smilies' => $final_arr
        ));
    }
}

/**
 *  Atbildei pievieno sarakstu ar notifikācijās izmantotajiem attēliem.
 */
function api_collections_notifs() {
    global $img_server;
    
    $arr = array(
        array(
            'type' => 0,
            'title' => 'Atbilde komentāram rakstā.',
            'url' => $img_server.'/bildes/fugue-icons/icons-24/document-horizontal-reply.png'
        ),
        array(
            'type' => 1,
            'title' => 'Komentārs galerijā.',
            'url' => $img_server.'/bildes/fugue-icons/icons-24/image-reply.png'
        ),
        array(
            'type' => 2,
            'title' => 'Komentārs rakstā.',
            'url' => $img_server.'/bildes/fugue-icons/icons-24/document-horizontal-reply.png'
        ),
        array(
            'type' => 3,
            'title' => 'Atbilde miniblogā.',
            'url' => $img_server.'/bildes/fugue-icons/icons-24/sticky-note-reply.png'
        ),
        array(
            'type' => 4,
            'title' => 'Jauns biedrs tavā grupā.',
            'url' => $img_server.'/bildes/fugue-icons/icons-24/user.png'
        ),
        array(
            'type' => 5,
            'title' => 'Tevi aicina draudzēties.',
            'url' => $img_server.'/bildes/fugue-icons/icons-24/address-book.png'
        ),
        array(
            'type' => 6,
            'title' => 'Tev ir jauns draugs.',
            'url' => $img_server.'/bildes/fugue-icons/icons-24/address-book-blue.png'
        ),
        array(
            'type' => 7,
            'title' => 'Tu saņēmi medaļu.',
            'url' => $img_server.'/bildes/fugue-icons/icons-24/star.png'
        ),
        array(
            'type' => 8,
            'title' => 'Atbilde grupā.',
            'url' => $img_server.'/bildes/fugue-icons/icons-24/sticky-note-reply.png'
        ),
        array(
            'type' => 9,
            'title' => 'Saņemta vēstule.',
            'url' => $img_server.'/bildes/fugue-icons/icons-24/mail.png'
        ),
        array(
            'type' => 10,
            'title' => 'Jauns brīdinājums.',
            'url' => $img_server.'/bildes/fugue-icons/icons-24/cross-circle.png'
        ),
        array(
            'type' => 11,
            'title' => 'Noņemts brīdinājums.',
            'url' => $img_server.'/bildes/fugue-icons/icons-24/information.png'
        ),
        array(
            'type' => 12,
            'title' => 'Jaunumi no exs.lv.',
            'url' => $img_server.'/bildes/fugue-icons/icons-24/ruler.png'
        ),
        array(
            'type' => 13,
            'title' => 'Tevi pieminēja grupā.',
            'url' => $img_server.'/bildes/fugue-icons/icons-24/at.png'
        ),
        array(
            'type' => 14,
            'title' => 'Tevi pieminēja miniblogā ārpus grupas.',
            'url' => $img_server.'/bildes/fugue-icons/icons-24/at.png'
        ),
        array(
            'type' => 15,
            'title' => 'Tevi pieminēja rakstā (vai pie "junk" attēla).',
            'url' => $img_server.'/bildes/fugue-icons/icons-24/at.png'
        ),
        array(
            'type' => 16,
            'title' => 'Tevi pieminēja galerijā.',
            'url' => $img_server.'/bildes/fugue-icons/icons-24/at.png'
        )
    );
    
    api_append(array(
        'list_size' => count($arr),
        'notifications' => $arr
    ));
}
