<?php
    echo $_SERVER['HTTP_USER_AGENT'];
 
    // Using get_browser() with return_array set to TRUE 
    $mybrowser = get_browser(null, true);
    print_r($mybrowser);
?>