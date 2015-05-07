<?php
// signature expiry in seconds, ie 3600=1hr
define('EXPIRE_SECS', 3600);

// encryption key, change this value, make sure its the same in both files
define('SEC', 'LockItUp!');


function invalidSig() {
    // output some text/html
    echo "Fake it till you make it";

    // or redirect (comment above, uncomment below)
    // header('Location: http://anotherpage.com', true, 301);
    exit();
}

// check signature
$sig = array();
$sig['raw'] = (!empty($_GET['sig']) ? $_GET['sig'] : invalidSig());

$sig['parts'] = explode('.', $sig['raw']);
if (count($sig['parts']) != 2) invalidSig();

$sig['utc'] = base64_decode(strtr($sig['parts'][0], '-_', '+/'));
if ($sig['utc'] === false || !is_numeric($sig['utc'])) invalidSig();
if (time() >= $sig['utc']+EXPIRE_SECS) invalidSig();

$sig['hash'] = base64_decode(strtr($sig['parts'][1], '-_', '+/'));
if ($sig['hash'] === false) invalidSig();

if (sha1($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].$sig['utc'].SEC, true) != $sig['hash'])
    invalidSig();

unset($sig);

// all done.

//insert your LP code below the next line
?>
