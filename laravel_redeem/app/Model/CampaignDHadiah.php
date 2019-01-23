<?php 

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CampaignDHadiah extends Model {
	protected $table = 'campaign_d_hadiah';
	protected $hidden = ['created_at', 'updated_at'];
	
	public function campaign()
	{
		return $this->belongsTo('App\Model\CampaignH', 'id_campaign');
	}
	
}