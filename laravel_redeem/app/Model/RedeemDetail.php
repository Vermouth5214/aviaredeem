<?php 

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RedeemDetail extends Model {
	protected $table = 'redeem_detail';
    protected $hidden = ['created_at', 'updated_at'];
    
	public function campaign()
	{
		return $this->belongsTo('App\Model\CampaignH', 'id_campaign');
	}

	public function campaign_hadiah()
	{
		return $this->belongsTo('App\Model\CampaignDHadiah', 'id_campaign_d_hadiah');
	}

	public function agen()
	{
		return $this->belongsTo('App\Model\UserAvex', 'kode_agen', 'reldag');
	}

}