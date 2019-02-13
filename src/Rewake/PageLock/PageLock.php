<?php

namespace Rewake\PageLock;


class PageLock
{
    private $baseUrl = 'http://mytracker.com/page/?camp=23lkkjlk2j';
    private $salt = 'LockItUp!';
    private $token = 'sig';
    private $expireSeconds = 3600;
    private $time;

    public function __construct()
    {

    }

    public function generate()
    {
        //set your destination page here, ie your landing page or tracker link
//        define('TARGET_PAGE', 'http://mytracker.com/page/?camp=23lkkjlk2j');

        // encryption key, change this value, make sure its the same in both files
//        define('SEC', 'LockItUp!');
//        define('TOKEN', 'sig');

        // Set time
        $this->time = time();

        // Set remote address & UA if not set
        // TODO: we could potentially throw error here?
        $remote = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : '127.0.0.1';
        $ua = isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : 'NOTSET';

        // Build signature
        $sig = base64_encode($this->time) . '.' . base64_encode(sha1($remote . $ua . $this->time . $this->salt, true));

        // Return url encoded signature
        return urlencode(strtr($sig, '+/', '-_'));
    }

    public function validate($signature)
    {
        // signature expiry in seconds, ie 3600=1hr
//        define('EXPIRE_SECS', 3600);

        // encryption key, change this value, make sure its the same in both files
//        define('SEC', 'LockItUp!');
//        define('TOKEN', 'sig');

//        function invalidSig()
//        {
//            // output some text/html
//            echo "Fake it till you make it";
//            // or redirect (comment above, uncomment below)
//            // header('Location: http://anotherpage.com', true, 301);
//            exit();
//        }

        // check signature
        $sig = array();
        $sig['raw'] = (!empty($_GET[TOKEN]) ? $_GET[TOKEN] : invalidSig());
        $sig['parts'] = explode('.', $sig['raw']);

        if (count($sig['parts']) != 2) invalidSig();

        $sig['utc'] = base64_decode(strtr($sig['parts'][0], '-_', '+/'));

        if ($sig['utc'] === false || !is_numeric($sig['utc'])) invalidSig();

        if (time() >= $sig['utc'] + EXPIRE_SECS) invalidSig();

        $sig['hash'] = base64_decode(strtr($sig['parts'][1], '-_', '+/'));

        if ($sig['hash'] === false) {

            throw new InvalidSignatureException();
        }

        if (sha1($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . $sig['utc'] . SEC, true) != $sig['hash'])
            invalidSig();

        unset($sig);
    }

    public function formUrl($signature, $baseUrl = null)
    {
        // See if we have a base url override
        if (empty($baseUrl)) {

            // Store base url
            $this->baseUrl = $baseUrl;
        }

        // Determine url concat char
        // TODO: we can eventually parse url and reform to ensure this is a "proper" check, but works for now
        if (strpos($this->baseUrl, '?') !== false) {

            $urlConcatChar = '&';

        } else {

            $urlConcatChar = '?';
        }

        // Return redirect URL
        return $this->baseUrl . $urlConcatChar . $this->token . '=' . $signature . '&' . $_SERVER['QUERY_STRING'];

        // perform redirect
        // TODO: this doesn't belong here... just keeping as a reference
//        header('Location: ' . $goto, true, 301);
    }
}