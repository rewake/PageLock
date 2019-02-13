<?php

namespace Rewake\PageLock;


class InvalidSignatureException extends \Exception
{
    public $message = "PageLock signature could not be validated";
}