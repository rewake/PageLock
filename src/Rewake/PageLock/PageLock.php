<?php

namespace Rewake\PageLock;


class PageLock
{
    private $defaultSalt = 'qV6c1KBGbxAJYNOB6qkXJQhehXPpo2qD';
    private $defaultUrlToken = 's';
    private $expireSeconds = 3600;
    private $throwsExceptions;

    public function __construct($throwExceptions = true)
    {
        // Set exception throw flag
        $this->throwsExceptions = $throwExceptions;
    }

    public function generate($salt = null)
    {
        // Set generation time
        $time = time();

        // Build and return signature
        return base64_encode($time . '.' . $this->userHash($time, $salt));
    }

    public function validate($signature, $salt = null)
    {
        // See if salt was provided
        if (is_null($salt)) {

            // Use default salt
            $salt = $this->defaultSalt;
        }

        // Decode signature to get raw data
        if (!$rawData = base64_decode($signature)) {

            $this->handleError("Signature could not be decoded");
        }

        // Get signature data in parts
        if ((!$data = explode('.', $rawData)) || empty($data) || count($data) != 2) {

            $this->handleError("Signature data was incomplete");
        }

        // Make sure lock has not expired
        if (!is_numeric($data[0]) || time() >= $data[0] + $this->expireSeconds) {

            $this->handleError("Page lock has expired");
        }

        // Validate lock hash
        if ($this->userHash($data[0], $salt) != $data[1]) {

            $this->handleError("Invalid user hash");
        }

        // If all validation has passed, return true
        return true;
    }

    public function formUrl($targetUrl, $signature, $token = null)
    {
        // Get URL parts from target URL
        $urlParts = parse_url($targetUrl);

        // See if url token was provided
        if (is_null($token)) {

            // Use default url token
            $token = $this->defaultUrlToken;
        }

        // Instantiate query string array
        $qs = [];

        // See if we have existing query string data in target URL
        if (isset($urlParts['query'])) {

            // Parse target URL query string data
            parse_str($urlParts['query'], $r);

            // Add target URL query string data to query string array
            $qs = array_merge($qs, $r);
        }

        // See if we have existing query string data
        if (isset($_SERVER['QUERY_STRING'])) {

            // Parse query string data
            parse_str($_SERVER['QUERY_STRING'], $r);

            // Add query string data to query string array
            $qs = array_merge($qs, $r);
        }

        // Add signature to query string array
        $qs[$token] = $signature;

        // Collapse query string
        $urlParts['query'] = http_build_query($qs);

        // Cleanup query string and parse str result array(s)
        unset($qs, $r);

        // Form & return redirect URL
        return
            ((isset($urlParts['scheme'])) ? $urlParts['scheme'] . '://' : '')
            .((isset($urlParts['user'])) ? $urlParts['user'] . ((isset($urlParts['pass'])) ? ':' . $urlParts['pass'] : '') .'@' : '')
            .((isset($urlParts['host'])) ? $urlParts['host'] : '')
            .((isset($urlParts['port'])) ? ':' . $urlParts['port'] : '')
            .((isset($urlParts['path'])) ? $urlParts['path'] : '')
            .((isset($urlParts['query'])) ? '?' . $urlParts['query'] : '')
            .((isset($urlParts['fragment'])) ? '#' . $urlParts['fragment'] : '')
            ;
    }

    protected function userHash($time, $salt = null)
    {
        // Set remote address & UA if not set
        // TODO: we could potentially throw error here or have a flag to enforce data exists?
        $remote = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : '127.0.0.1';
        $ua = isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : 'NOTSET';

        // See if salt was provided
        if (is_null($salt)) {

            // Use default salt
            $salt = $this->defaultSalt;
        }

        /**
         * TODO: Current "salting" or user information is not entropy safe and we can improve on it if necessary
         * Example: multiple signatures generated "sequentially", but at same time() will return the same signature
         */

        // Return lock hash
        return sha1($remote . $ua . $time . $salt, true);
    }

    protected function handleError($message)
    {
        // See if we should throw exceptions
        if ($this->throwsExceptions) {

            // Throw exception
            throw new InvalidSignatureException($message);

        } else {

            // Return false
            return false;
        }
    }
}