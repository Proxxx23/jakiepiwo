<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Answers extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'newsletter', 'answers'
    ];

}
