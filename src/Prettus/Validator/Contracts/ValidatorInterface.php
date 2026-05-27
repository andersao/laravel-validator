<?php

declare(strict_types=1);

namespace Prettus\Validator\Contracts;

use Illuminate\Contracts\Support\MessageBag;
use Prettus\Validator\Exceptions\ValidatorException;

interface ValidatorInterface
{
    public const RULE_CREATE = 'create';
    public const RULE_UPDATE = 'update';

    public function setId(int|string|null $id): static;

    public function with(array $input): static;

    public function passes(?string $action = null): bool;

    /**
     * @throws ValidatorException
     */
    public function passesOrFail(?string $action = null): bool;

    /**
     * @return array<int, string>
     */
    public function errors(): array;

    public function errorsBag(): MessageBag;

    public function setRules(array $rules): static;

    public function getRules(?string $action = null): array;
}
