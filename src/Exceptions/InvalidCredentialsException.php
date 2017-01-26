<?php

namespace Alveum\DDEX\Exceptions;

class InvalidCredentialsException extends \Exception
{
    public $message = 'Invalid DDEX DPID credentials provided.';
}