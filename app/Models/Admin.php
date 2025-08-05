<?php

namespace App\Models;

use PHPFramework\Model;

class Admin extends Model
{

    protected string $table = 'admins';
    public bool $timestamps = false;
    protected array $loaded = ['login', 'password'];
    protected array $fillable = ['login', 'password'];

    protected array $rules = [
        'required' => ['login', 'password'],
        'unique' => [
            ['login', 'admins,login'], // Указываем таблицу и поле для проверки уникальности
        ],
    ];

    protected array $labels = [
        'login' => 'Логин',
        'password' => 'Пароль',
    ];

}