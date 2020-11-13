<?php

namespace App\Http\Controllers\Backend;

use Session;
use App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Model\CampaignH;
use DB;
 
class DashboardController extends Controller {
	public function dashboard(Request $request) {
        $userinfo = Session::get('userinfo');
        if ($userinfo['priv'] == "VREDEEM"){
            return redirect('/backend/campaign');
        }
		$data = DB::select(
            "
                SELECT ch.* , ifnull(ca.jum_klaim,0) as jum_klaim, ifnull(cb.jum_kadaluarsa_belum_klaim,0) as jum_kadaluarsa_belum_klaim, ifnull(cc.jum_kadaluarsa_belum_konversi,0) as jum_kadaluarsa_belum_konversi, ifnull(cd.jum_belum_klaim,0) as jum_belum_klaim, ifnull(ce.jum_belum_konversi,0) as jum_belum_konversi
                FROM (
                    SELECT *
                    FROM campaign_h 
                    WHERE created_at > DATE_SUB(NOW(), INTERVAL 2 MONTH) and active = 1
                ) ch
                left join
                (select a.kode_campaign, count(a.kode_campaign) as jum_klaim from
                    (select `campaign_h`.`kode_campaign`, 
                            `customer_omzet`.`periode_awal`, 
                            `customer_omzet`.`periode_akhir`, count(distinct campaign_d_hadiah.id) as jum_emas, 
                            count(distinct redeem_detail.id) as jum_redeem_detail, 
                            count(distinct redeem_emas.id) as jum_redeem_emas, `customer_omzet`.`kode_customer` 
                        from `customer_omzet` 
                            left join `campaign_h` 
                            on `customer_omzet`.`kode_campaign` = `campaign_h`.`kode_campaign` 
                            left join `campaign_d_hadiah` 
                            on `campaign_d_hadiah`.`id_campaign` = `campaign_h`.`id` and `campaign_d_hadiah`.`emas` = 1 
                            left join `redeem_detail` 
                            on `redeem_detail`.`kode_customer` = `customer_omzet`.`kode_customer` and `redeem_detail`.`id_campaign` = `campaign_h`.`id` 
                            left join `redeem_emas` 
                            on `redeem_emas`.`kode_customer` = `customer_omzet`.`kode_customer` and `redeem_emas`.`id_campaign` = `campaign_h`.`id` 
                        where `campaign_h`.`active` = 1 and customer_omzet.active = 1 
                        and customer_omzet.periode_akhir < NOW() and customer_omzet.periode_akhir > DATE_SUB(NOW(), INTERVAL 2 MONTH)
                        group by `customer_omzet`.`kode_customer`, `customer_omzet`.`kode_campaign`
                        having ((jum_redeem_detail > 0 and jum_emas = 0) or jum_redeem_emas > 0) 
                    ) a
                    group by a.kode_campaign
                ) ca on (ch.kode_campaign = ca.kode_campaign)
                left join 
                (select b.kode_campaign, count(b.kode_campaign) as jum_kadaluarsa_belum_klaim from
                    (select `campaign_h`.`kode_campaign`, 
                            `customer_omzet`.`periode_awal`, 
                            `customer_omzet`.`periode_akhir`, count(distinct campaign_d_hadiah.id) as jum_emas, 
                            count(distinct redeem_detail.id) as jum_redeem_detail, 
                            count(distinct redeem_emas.id) as jum_redeem_emas, `customer_omzet`.`kode_customer` 
                        from `customer_omzet` 
                            left join `campaign_h` 
                            on `customer_omzet`.`kode_campaign` = `campaign_h`.`kode_campaign` 
                            left join `campaign_d_hadiah` 
                            on `campaign_d_hadiah`.`id_campaign` = `campaign_h`.`id` and `campaign_d_hadiah`.`emas` = 1 
                            left join `redeem_detail` 
                            on `redeem_detail`.`kode_customer` = `customer_omzet`.`kode_customer` and `redeem_detail`.`id_campaign` = `campaign_h`.`id` 
                            left join `redeem_emas` 
                            on `redeem_emas`.`kode_customer` = `customer_omzet`.`kode_customer` and `redeem_emas`.`id_campaign` = `campaign_h`.`id`  
                        where `campaign_h`.`active` = 1 and customer_omzet.active = 1
                        and customer_omzet.periode_akhir < NOW() and customer_omzet.periode_akhir > DATE_SUB(NOW(), INTERVAL 2 MONTH)
                        group by `customer_omzet`.`kode_customer`, `customer_omzet`.`kode_campaign`
                        having jum_redeem_detail = 0 
                    ) b
                    group by b.kode_campaign
                ) cb on (ch.kode_campaign = cb.kode_campaign)
                left join
                (select c.kode_campaign, count(c.kode_campaign) as jum_kadaluarsa_belum_konversi from
                    (select `campaign_h`.`kode_campaign`, 
                            `customer_omzet`.`periode_awal`, 
                            `customer_omzet`.`periode_akhir`, count(distinct campaign_d_hadiah.id) as jum_emas, 
                            count(distinct redeem_detail.id) as jum_redeem_detail, 
                            count(distinct redeem_emas.id) as jum_redeem_emas, `customer_omzet`.`kode_customer` 
                        from `customer_omzet` 
                            left join `campaign_h` 
                            on `customer_omzet`.`kode_campaign` = `campaign_h`.`kode_campaign` 
                            left join `campaign_d_hadiah` 
                            on `campaign_d_hadiah`.`id_campaign` = `campaign_h`.`id` and `campaign_d_hadiah`.`emas` = 1 
                            left join `redeem_detail` 
                            on `redeem_detail`.`kode_customer` = `customer_omzet`.`kode_customer` and `redeem_detail`.`id_campaign` = `campaign_h`.`id` 
                            left join `redeem_emas` 
                            on `redeem_emas`.`kode_customer` = `customer_omzet`.`kode_customer` and `redeem_emas`.`id_campaign` = `campaign_h`.`id`  
                        where `campaign_h`.`active` = 1 and customer_omzet.active = 1
                        and customer_omzet.periode_akhir < NOW() and customer_omzet.periode_akhir > DATE_SUB(NOW(), INTERVAL 2 MONTH)
                        group by `customer_omzet`.`kode_customer`, `customer_omzet`.`kode_campaign`
                        having (jum_redeem_detail > 0 and jum_redeem_emas = 0) and jum_emas > 0
                    ) c
                    group by c.kode_campaign
                ) cc on (ch.kode_campaign = cc.kode_campaign)
                left join
                (select d.kode_campaign, count(d.kode_campaign) as jum_belum_klaim from
                    (select `campaign_h`.`kode_campaign`, 
                            `customer_omzet`.`periode_awal`, 
                            `customer_omzet`.`periode_akhir`, count(distinct campaign_d_hadiah.id) as jum_emas, 
                            count(distinct redeem_detail.id) as jum_redeem_detail, 
                            count(distinct redeem_emas.id) as jum_redeem_emas, `customer_omzet`.`kode_customer` 
                        from `customer_omzet` 
                            left join `campaign_h` 
                            on `customer_omzet`.`kode_campaign` = `campaign_h`.`kode_campaign` 
                            left join `campaign_d_hadiah` 
                            on `campaign_d_hadiah`.`id_campaign` = `campaign_h`.`id` and `campaign_d_hadiah`.`emas` = 1 
                            left join `redeem_detail` 
                            on `redeem_detail`.`kode_customer` = `customer_omzet`.`kode_customer` and `redeem_detail`.`id_campaign` = `campaign_h`.`id` 
                            left join `redeem_emas` 
                            on `redeem_emas`.`kode_customer` = `customer_omzet`.`kode_customer` and `redeem_emas`.`id_campaign` = `campaign_h`.`id` 
                        where `campaign_h`.`active` = 1 and customer_omzet.active = 1
                        group by `customer_omzet`.`kode_customer`, `customer_omzet`.`kode_campaign`
                        having (jum_redeem_detail = 0 and customer_omzet.periode_akhir >= NOW())
                    ) d
                    group by d.kode_campaign
                ) cd on (ch.kode_campaign = cd.kode_campaign)
                left join
                (select e.kode_campaign, count(e.kode_campaign) as jum_belum_konversi from
                    (select `campaign_h`.`kode_campaign`, 
                            `customer_omzet`.`periode_awal`, 
                            `customer_omzet`.`periode_akhir`, count(distinct campaign_d_hadiah.id) as jum_emas, 
                            count(distinct redeem_detail.id) as jum_redeem_detail, 
                            count(distinct redeem_emas.id) as jum_redeem_emas, `customer_omzet`.`kode_customer` 
                        from `customer_omzet` 
                            left join `campaign_h` 
                            on `customer_omzet`.`kode_campaign` = `campaign_h`.`kode_campaign` 
                            left join `campaign_d_hadiah` 
                            on `campaign_d_hadiah`.`id_campaign` = `campaign_h`.`id` and `campaign_d_hadiah`.`emas` = 1 
                            left join `redeem_detail` 
                            on `redeem_detail`.`kode_customer` = `customer_omzet`.`kode_customer` and `redeem_detail`.`id_campaign` = `campaign_h`.`id` 
                            left join `redeem_emas` 
                            on `redeem_emas`.`kode_customer` = `customer_omzet`.`kode_customer` and `redeem_emas`.`id_campaign` = `campaign_h`.`id` 
                        where `campaign_h`.`active` = 1 and customer_omzet.active = 1
                        group by `customer_omzet`.`kode_customer`, `customer_omzet`.`kode_campaign`
                        having jum_emas > 0 and jum_redeem_detail > 0 and jum_redeem_emas = 0 and customer_omzet.periode_akhir >= NOW()
                    ) e
                    group by e.kode_campaign
                ) ce on (ch.kode_campaign = ce.kode_campaign)
                where (jum_kadaluarsa_belum_klaim > 0 or jum_kadaluarsa_belum_konversi > 0 or jum_belum_klaim > 0 or jum_belum_konversi > 0)
            "
        );
        view()->share('data', $data);
		return view ('backend.dashboard');
	}
}