<?php

namespace App\Model;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
	protected $table = 'SFA_Customer';
    public $timestamps = false;

    protected $connection = 'sqlsrv';

}