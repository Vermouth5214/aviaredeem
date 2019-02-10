<?php 

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TTOLast extends Model {
	protected $table = 'tto_last';
	protected $hidden = ['created_at', 'updated_at'];
	
}