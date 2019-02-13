<?php

// Require autoloader
require_once dirname(dirname(dirname(__FILE__))) . '/vendor/autoload.php';

// Import lib(s)
use Rewake\PageLock\PageLock;

// Instantiate PageLock
$pl = new PageLock();

/**
 * 1. Generate Signature, and then form URL, redirect, etc as needed
 */

// Generate and store signature
$signature = $pl->generate();

// Form URL using signature
$redirectUrl = $pl->formUrl("http://www.mysite.com", $signature);

// Redirect User
// header("Location: {$redirectUrl}", true, 301);


// Fake the query string for example
$url = parse_url($redirectUrl);
parse_str($url['query'], $_REQUEST);


/**
 * 2. Validate Signature
 */

// Get signature from URL
$givenSignature = $_REQUEST['s'];

$pl->validate($givenSignature);

$debug_breakpoint = true;