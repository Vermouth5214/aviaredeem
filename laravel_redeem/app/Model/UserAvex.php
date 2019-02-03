<?php

namespace App\Model;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserAvex extends Authenticatable
{
	protected $table = 'tbuser';
    public $timestamps = false;

    protected $connection = 'mysql_customer_care';

}