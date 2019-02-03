<?php 

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CampaignDEmas extends Model {
	protected $table = 'campaign_d_emas';
	protected $hidden = ['created_at', 'updated_at'];
	
	public function campaign()
	{
		return $this->belongsTo('App\Model\CampaignH', 'id_campaign');
	}

	public function campaign_hadiah()
	{
		return $this->belongsTo('App\Model\CampaignDHadiah', 'id_campaign_d_hadiah');
	}
    
}