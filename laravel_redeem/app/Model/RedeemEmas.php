<?php 

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RedeemEmas extends Model {
	protected $table = 'redeem_emas';
    protected $hidden = ['created_at', 'updated_at'];
    
	public function campaign()
	{
		return $this->belongsTo('App\Model\CampaignH', 'id_campaign');
	}

	public function campaign_hadiah()
	{
		return $this->belongsTo('App\Model\CampaignDEmas', 'id_campaign_emas');
	}

	public function agen()
	{
		return $this->belongsTo('App\Model\UserAvex', 'kode_agen', 'reldag');
	}

}