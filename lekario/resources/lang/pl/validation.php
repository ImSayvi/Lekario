<?php

return [
    'accepted'             => 'Pole :attribute musi być zaakceptowane.',
    'active_url'           => 'Pole :attribute nie jest poprawnym adresem URL.',
    'after'                => 'Pole :attribute musi być datą po :date.',
    'after_or_equal'       => 'Pole :attribute musi być datą równą lub późniejszą niż :date.',
    'alpha'                => 'Pole :attribute może zawierać tylko litery.',
    'alpha_dash'           => 'Pole :attribute może zawierać tylko litery, cyfry i myślniki.',
    'alpha_num'            => 'Pole :attribute może zawierać tylko litery i cyfry.',
    'array'                => 'Pole :attribute musi być tablicą.',
    'before'               => 'Pole :attribute musi być datą przed :date.',
    'before_or_equal'      => 'Pole :attribute musi być datą równą lub wcześniejszą niż :date.',
    'between'              => [
        'numeric' => 'Pole :attribute musi być między :min a :max.',
        'file'    => 'Plik :attribute musi mieć rozmiar między :min a :max kilobajtów.',
        'string'  => 'Pole :attribute musi mieć od :min do :max znaków.',
        'array'   => 'Pole :attribute musi mieć od :min do :max elementów.',
    ],
    'boolean'              => 'Pole :attribute musi mieć wartość true lub false.',
    'confirmed'            => 'Potwierdzenie pola :attribute nie pasuje.',
    'date'                 => 'Pole :attribute nie jest poprawną datą.',
    'email'                => 'Pole :attribute musi być poprawnym adresem e-mail.',
    'max'                  => [
        'numeric' => 'Pole :attribute nie może być większe niż :max.',
        'file'    => 'Plik :attribute nie może być większy niż :max kilobajtów.',
        'string'  => 'Pole :attribute nie może mieć więcej niż :max znaków.',
        'array'   => 'Pole :attribute nie może mieć więcej niż :max elementów.',
    ],
    'min'                  => [
        'numeric' => 'Pole :attribute musi mieć co najmniej :min.',
        'file'    => 'Plik :attribute musi mieć co najmniej :min kilobajtów.',
        'string'  => 'Pole :attribute musi mieć co najmniej :min znaków.',
        'array'   => 'Pole :attribute musi mieć co najmniej :min elementów.',
    ],
    'required'             => 'Pole :attribute jest wymagane.',
    'string'               => 'Pole :attribute musi być tekstem.',
    'unique'               => 'Pole :attribute jest już zajęte.',
    'confirmed'            => 'Potwierdzenie pola :attribute nie zgadza się.',
    // ... dodaj resztę komunikatów jakie chcesz
];
