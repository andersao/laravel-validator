<?php

declare(strict_types=1);

namespace Prettus\Validator\Exceptions;

use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\MessageBag;

class ValidatorException extends Exception implements Arrayable, Jsonable
{
    public function __construct(protected readonly MessageBag $messageBag)
    {
    }

    public function getMessageBag(): MessageBag
    {
        return $this->messageBag;
    }

    /**
     * @return array{error: string, error_description: MessageBag}
     */
    public function toArray(): array
    {
        return [
            'error'             => 'validation_exception',
            'error_description' => $this->getMessageBag(),
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options | JSON_THROW_ON_ERROR);
    }
}
