<?php

namespace App;

use Valitron\Validator as ValitronValidator;

class Validator
{
    public function validate(array $urlData): array|bool
    {
        $validator = new ValitronValidator($urlData);

        $validator
                ->rule("required", "name")
                ->message("URL не должен быть пустым")
                ->rule("url", "name")
                ->message("Некорректный URL")
                ->stopOnFirstFail();

        $validator->validate();

        return $validator->errors();
    }
}