<?php
/**
 *  Android page apakšmodulis
 *
 *  Kaut ko šeit darīs saistībā ar autorizāciju (varbūt).
 */

// pa tiešo šeit nebūs nekādas skatīšanās
!isset($sub_include) and die('Error loading page!');

echo json_encode( array(
    'state'     => 'error',
    'message'   => '/login!'
)); 