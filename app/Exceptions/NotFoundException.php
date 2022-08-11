<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class NotFoundException extends Exception
{
        /**
         * @var mixed|string
         */
    protected $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function render(): Response
    {
        return response($this->message, Response::HTTP_NOT_FOUND);
    }
}
