<?php

namespace PHPFramework;

use Valitron\Validator;
use Symfony\Component\Validator\Validation;

abstract class Model
{

    protected array $loaded = [];  // Поля, которые будут загружаться из запроса
    public array $attributes = []; // Атрибуты для формы
    protected array $errors = [];  // Список ошибок

    /**
     * Загружает значения из запроса по указанным ключам.
     * 
     * @param array $key Ключи для выборки.
     * @return Значение $key
     */
    public function loadDataOne($key)
    {
        return request()->getDataVal([$key])[$key];
    }

    /**
     * Загружает значения из запроса по указанным ключам.
     * 
     * @param array $keys Ключи для выборки.
     * @return array Ассоциативный массив вида [ключ => значение]. 
     */
    public function loadDataVal(array $keys): array
    {
        return request()->getDataVal($keys);
    }

    /**
     * Загружает значения из JSON запроса по указанным ключам.
     * 
     * @param array $keys Ключи для выборки.
     * @return array Ассоциативный массив вида [ключ => значение]. 
     */
    public function loadJson($keys = null, mixed $default = null): mixed
    {
        return request()->getJson($keys);
    }

    public function loadData($fileKey = '', $fileValue = ''): void
    {
        $data = request()->getData($fileKey, $fileValue);

        foreach ($this->loaded as $field) {
            if (isset($data[$field])) {
                $this->attributes[$field] = valid_data($data[$field]);
            } else {
                $this->attributes[$field] = '';
            }
        }
    }

    public function loadDataJson($fileKey = '', $fileValue = ''): void
    {
        $data = request()->getDataJson($fileKey, $fileValue);

        foreach ($this->loaded as $field) {
            if (isset($data[$field])) {
                $this->attributes[$field] = valid_data($data[$field]);
            } else {
                $this->attributes[$field] = '';
            }
        }
    }

    /* public function validate($data = [], $rules = [], $labels = []): bool
    {
        if (!$data) {
            $data = $this->attributes;
        }
        if (!$rules) {
            $rules = $this->rules;
        }
        if (!$labels) {
            $labels = $this->labels;
        }

        Validator::addRule('unique', function ($field, $value, array $params, array $fields) {
            $data = explode(',', $params[0]);
            return !(db()->findOne($data[0], $value, $data[1]));
            //dd($field, $value, $params, $data, $user);
        }, 'must be unique');

        Validator::langDir(WWW . '/lang');
        Validator::lang('ru');
        $validator = new Validator($data);
        $validator->rules($rules);
        $validator->labels($labels);
        if ($validator->validate()) {
            return true;
        } else {
            $this->errors = $validator->errors();
            return false;
        }
    } */

    public function validate($obj): bool
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $violations = $validator->validate($obj);
        $errors = [];

        foreach ($violations as $violation) {
            $property = $violation->getPropertyPath();
            $errors[$property][] = $violation->getMessage();
        }

        $this->errors = $errors;

        if(count($this->errors)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Безопасный откат с проверкой
     */
    public function secureRollbackVerification() {
        if (db()->entityManager()->getConnection()->isTransactionActive()) {
            db()->entityManager()->rollback();
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function listErrors(): string
    {
        $output = '<ul class="list-unstyled">';
        foreach ($this->errors as $field_errors) {
            foreach ($field_errors as $error) {
                $error = htmlspecialchars($error, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                $output .= "<li>$error</li>";
            }
        }
        $output .= "</ul>";
        return $output;
    }

}