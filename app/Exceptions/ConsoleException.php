<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\Console\Exception\ExceptionInterface;

class ConsoleException extends Exception implements ExceptionInterface
{
}
