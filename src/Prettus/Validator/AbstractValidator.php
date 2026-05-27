<?php

declare(strict_types=1);

namespace Prettus\Validator;

use Illuminate\Contracts\Support\MessageBag as MessageBagContract;
use Illuminate\Support\MessageBag;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;

abstract class AbstractValidator implements ValidatorInterface
{
    protected int|string|null $id = null;

    protected array $data = [];

    protected array $rules = [];

    protected array $messages = [];

    protected array $attributes = [];

    protected MessageBagContract $errors;

    public function __construct()
    {
        $this->errors = new MessageBag();
    }

    public function setId(int|string|null $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function with(array $input): static
    {
        $this->data = $input;

        return $this;
    }

    /**
     * @return array<int, string>
     */
    public function errors(): array
    {
        return $this->errorsBag()->all();
    }

    public function errorsBag(): MessageBagContract
    {
        return $this->errors;
    }

    abstract public function passes(?string $action = null): bool;

    /**
     * @throws ValidatorException
     */
    public function passesOrFail(?string $action = null): bool
    {
        if (! $this->passes($action)) {
            throw new ValidatorException($this->errorsBag());
        }

        return true;
    }

    public function getRules(?string $action = null): array
    {
        $rules = $this->rules;

        if ($action !== null && isset($this->rules[$action])) {
            $rules = $this->rules[$action];
        }

        return $this->parserValidationRules($rules, $this->id);
    }

    public function setRules(array $rules): static
    {
        $this->rules = $rules;

        return $this;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function setMessages(array $messages): static
    {
        $this->messages = $messages;

        return $this;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes): static
    {
        $this->attributes = $attributes;

        return $this;
    }

    protected function parserValidationRules(array $rules, int|string|null $id = null): array
    {
        if ($id === null) {
            return $rules;
        }

        foreach ($rules as $field => $fieldRules) {
            $fieldRules = is_array($fieldRules) ? $fieldRules : explode('|', $fieldRules);

            foreach ($fieldRules as $idx => $rule) {
                [$name, $params] = array_pad(explode(':', $rule, 2), 2, null);

                if (strtolower($name) !== 'unique') {
                    continue;
                }

                $parts = $params === null ? [] : array_map('trim', explode(',', $params));
                $parts[0] = $parts[0] ?? '';
                $parts[1] = $parts[1] ?? $field;
                $parts[2] = (string) $id;

                $fieldRules[$idx] = $name.':'.implode(',', $parts);
            }

            $rules[$field] = $fieldRules;
        }

        return $rules;
    }
}
