<?php

namespace app;

class Validator
{
    protected $data = [];
    protected $errors = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function validate(array $rules): bool
    {
        foreach ($rules as $field => $ruleset) {
            $ruleset = explode('|', $ruleset);
            foreach ($ruleset as $rule) {
                $ruleName = $rule;
                $params = [];

                if (strpos($rule, ':')) {
                    [$ruleName, $paramString] = explode(':', $rule);
                    $params = explode(',', $paramString);
                }

                if (method_exists($this, $ruleName)) {
                    $this->$ruleName($field, ...$params);
                }
            }
        }

        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    protected function required(string $field): void
    {
        if (!isset($this->data[$field]) || trim($this->data[$field]) === '') {
            $this->errors[$field][] = "$field is required.";
        }
    }

    protected function min(string $field, int $min): void
    {
        if (isset($this->data[$field]) && strlen($this->data[$field]) < $min) {
            $this->errors[$field][] = "$field must be at least $min characters.";
        }
    }

    protected function max(string $field, int $max): void
    {
        if (isset($this->data[$field]) && strlen($this->data[$field]) > $max) {
            $this->errors[$field][] = "$field cannot be more than $max characters.";
        }
    }

    protected function email(string $field): void
    {
        if (isset($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = "$field must be a valid email address.";
        }
    }

    protected function confirmed(string $field): void
    {
        $confirmField = $field . '_confirmation';
        if (isset($this->data[$field]) && (!isset($this->data[$confirmField]) || $this->data[$field] !== $this->data[$confirmField])) {
            $this->errors[$field][] = "$field confirmation does not match.";
        }
    }

    protected function numeric(string $field): void
    {
        if (isset($this->data[$field]) && !is_numeric($this->data[$field])) {
            $this->errors[$field][] = "$field must be a numeric value.";
        }
    }

    protected function unique(string $field, string $table, string $column): void
    {
        // Implement database check for uniqueness.
        // Example: SELECT COUNT(*) FROM $table WHERE $column = $this->data[$field]
        // For now, we'll simulate a unique check.

        $databaseRecords = [ // Simulated database records
            'existing@example.com',
            'user@example.com'
        ];

        if (in_array($this->data[$field], $databaseRecords)) {
            $this->errors[$field][] = "$field must be unique.";
        }
    }

    protected function regex(string $field, string $pattern): void
    {
        if (isset($this->data[$field]) && !preg_match($pattern, $this->data[$field])) {
            $this->errors[$field][] = "$field format is invalid.";
        }
    }
}
