<?php

declare(strict_types=1);

namespace Prettus\Validator;

use Illuminate\Contracts\Validation\Factory;

class LaravelValidator extends AbstractValidator
{
    public function __construct(protected Factory $validator)
    {
        parent::__construct();
    }

    public function passes(?string $action = null): bool
    {
        $validator = $this->validator->make(
            $this->data,
            $this->getRules($action),
            $this->getMessages(),
            $this->getAttributes(),
        );

        if ($validator->fails()) {
            $this->errors = $validator->messages();

            return false;
        }

        return true;
    }
}
