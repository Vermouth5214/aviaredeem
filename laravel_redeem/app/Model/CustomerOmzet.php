<?php 

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CustomerOmzet extends Model {
	protected $table = 'customer_omzet';
    protected $hidden = ['created_at', 'updated_at'];
    
    protected $fillable = ['kode_campaign','kode_customer','periode_awal','periode_akhir','omzet','poin','user_modified','active'];

	public function campaign()
	{
		return $this->belongsTo('App\Model\CampaignH', 'id_campaign');
	}

	public function agen()
	{
		return $this->belongsTo('App\Model\UserAvex', 'kode_customer', 'reldag');
	}
    
}