<?php namespace Prettus\Validator;

use Illuminate\Support\MessageBag;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class AbstractValidator.
 *
 * @package Prettus\Validator
 */
abstract class AbstractValidator implements ValidatorInterface
{
    /**
     * @var int
     */
    protected $id = null;

    /**
     * Validator.
     *
     * @var object
     */
    protected $validator;

    /**
     * Data to be validated.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Validation Rules.
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Validation Custom Messages.
     *
     * @var array
     */
    protected $messages = [];

    /**
     * Validation Custom Attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Validation errors.
     *
     * @var MessageBag
     */
    protected $errors;

    /**
     * Set Id.
     *
     * @param $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set data to validate.
     *
     * @param array $data
     *
     * @return $this
     */
    public function with(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Returns value of specific data key.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getData($key, $default = null)
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        return $default;
    }

    /**
     * Return errors.
     *
     * @return array
     */
    public function errors()
    {
        return $this->errorsBag()->all();
    }

    /**
     * Errors MessageBag.
     *
     * @return MessageBag
     */
    public function errorsBag()
    {
        if (! $this->errors) {
            $this->errors = new MessageBag;
        }

        return $this->errors;
    }

    /**
     * Pass the data and the rules to the validator.
     *
     * @param string $action
     *
     * @return bool
     */
    abstract public function passes($action = null);

    /**
     * Pass the data and the rules to the validator or throws ValidatorException.
     *
     * @throws ValidatorException
     *
     * @param string $action
     *
     * @return bool
     */
    public function passesOrFail($action = null)
    {
        if (! $this->passes($action)) {
            throw new ValidatorException($this->errorsBag());
        }

        return true;
    }

    /**
     * Get rule for validation by action ValidatorInterface::RULE_CREATE or ValidatorInterface::RULE_UPDATE.
     *
     * Default rule: ValidatorInterface::RULE_CREATE
     *
     * @param null $action
     *
     * @return array
     */
    public function getRules($action = null)
    {
        $rules = $this->rules;

        if (isset($this->rules[$action])) {
            $rules = $this->rules[$action];
        }

        return $this->parserValidationRules($rules, $this->id);
    }

    /**
     * Set Rules for Validation.
     *
     * @param array $rules
     *
     * @return $this
     */
    public function setRules(array $rules)
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * Get Custom error messages for validation.
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Set Custom error messages for Validation.
     *
     * @return $this
     */
    public function setMessages(array $messages)
    {
        $this->messages = $messages;

        return $this;
    }

    /**
     * Get Custom error attributes for validation.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Set Custom error attributes for Validation.
     *
     * @return $this
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Parser Validation Rules.
     *
     * @param $rules
     * @param null $id
     *
     * @return array
     */
    protected function parserValidationRules($rules, $id = null)
    {
        if ($id === null) {
            return $rules;
        }

        array_walk($rules, function (&$rules, $field) use ($id) {
            if (! is_array($rules)) {
                $rules = explode('|', $rules);
            }

            foreach ($rules as $ruleIdx => $rule) {
                // get name and parameters
                @list($name, $params) = array_pad(explode(':', $rule), 2, null);

                // only do someting for the unique rule
                if (strtolower($name) != 'unique') {
                    continue; // continue in foreach loop, nothing left to do here
                }

                $p = array_map('trim', explode(',', $params));

                // set field name to rules key ($field) (laravel convention)
                if (! isset($p[1])) {
                    $p[1] = $field;
                }

                // set 3rd parameter to id given to getValidationRules()
                $p[2] = $id;

                $params = implode(',', $p);
                $rules[$ruleIdx] = $name.':'.$params;
            }
        });

        return $rules;
    }
}
