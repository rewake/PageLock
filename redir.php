<?php
//set your destination page here, ie your landing page or tracker link
define('TARGET_PAGE', 'http://mytracker.com/page/?camp=23lkkjlk2j');

// encryption key, change this value, make sure its the same in both files
define('SEC', 'LockItUp!');

// build signature
$utc=time();
$sig = base64_encode($utc).'.'.base64_encode(sha1($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].$utc.SEC, true));
$encSig = urlencode(strtr($sig, '+/', '-_'));

// build redirect URL
$goto = TARGET_PAGE;
if (strpos($goto, '?') !== false)
    $goto .= '&sig='.$encSig.'&'.$_SERVER['QUERY_STRING'];
else
    $goto .= '?sig='.$encSig.'&'.$_SERVER['QUERY_STRING'];

// perform redirect
header('Location: '.$goto, true, 301);
