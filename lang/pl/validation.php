<?php

declare(strict_types=1);

return [
 'min' => [
        'string' => 'Pole :attribute musi mieć co najmniej :min znaków.',
        'numeric' => 'Pole :attribute musi być nie mniejsze niż :min.',
        'array' => 'Pole :attribute musi mieć co najmniej :min elementów.',
    ],

    'required' => ' :attribute jest wymagana.',
    'date' => 'Pole :attribute nie jest prawidłową datą.',
    'after' => 'Pole :attribute musi być datą po :date.',
    'after_or_equal' => ':attribute musi być datą nie wcześniejszą niż :date.',
    'before' => ':attribute musi być datą przed :date.',
    'before_or_equal' => 'Pole :attribute musi być datą nie późniejszą niż :date.',
    'confirmed' => 'Pole :attribute nie zgadza się.',

    'attributes' => [
        'title' => 'Tytuł',
        'email' => 'E-mail',
        'name' => 'Nazwa',
        'first_name' => 'Imie',
        'password' => 'Hasło',
        'start' => 'Data rozpoczęcia',
        'end' => 'Data zakończenia',
        'location' => 'Lokalizacja',
    ],
];
