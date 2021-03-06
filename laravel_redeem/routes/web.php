<?php

use App\Model\CampaignDHadiah;
use App\Model\CampaignDBagi;
use App\Model\CampaignDEmas;
use App\Model\RedeemDetail;
use App\Model\RedeemEmas;
use App\Model\UserAvex;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
	return redirect('backend/');
});


//AUTO REDEEM OMZET / POIN tidak cukup
Route::get('/auto-redeem', function () {
    $data = DB::select("
        SELECT ch.id, c.kode_campaign, c.kode_customer, c.omzet_netto, c.poin, count(distinct rd.id) as jum_redeem, min_hadiah.harga
        FROM customer_omzet c 
        LEFT JOIN campaign_h ch on ch.kode_campaign = c.kode_campaign
        LEFT JOIN redeem_detail rd on rd.kode_customer = c.kode_customer and rd.id_campaign = ch.id
        LEFT JOIN (select id_campaign, min(harga) as harga from campaign_d_hadiah group by id_campaign) min_hadiah on min_hadiah.id_campaign = ch.id
        WHERE c.active = 1 and ch.active = 1
            AND '".date('Y-m-d')."' >= c.periode_awal and '".date('Y-m-d')."' <= c.periode_akhir
        GROUP BY c.kode_campaign, c.kode_customer
        HAVING jum_redeem = 0 and c.omzet_netto < harga and c.poin < harga
        ORDER BY ch.id ASC;    
    ");
    if ($data){
        foreach ($data as $detail): 
            $data_hadiah = CampaignDHadiah::where('id_campaign', $detail->id)->orderBy('id', 'ASC')->get();
            foreach ($data_hadiah as $hadiah):
                $insert_redeem = new RedeemDetail();
                $insert_redeem->kode_customer = $detail->kode_customer;
                $insert_redeem->id_campaign = $detail->id;
                $insert_redeem->id_campaign_hadiah = $hadiah->id;
                $insert_redeem->jumlah = 0;
                $insert_redeem->save();
            endforeach;

            $data_hadiah_emas = CampaignDEmas::where('id_campaign', $detail->id)->orderBy('id', 'ASC')->get();
            foreach ($data_hadiah_emas as $hadiah):
                $insert_redeem_emas = new RedeemEmas();
                $insert_redeem_emas->kode_customer = $detail->kode_customer;
                $insert_redeem_emas->id_campaign = $detail->id;
                $insert_redeem_emas->id_campaign_emas = $hadiah->id;
                $insert_redeem_emas->jumlah = 0;
                $insert_redeem_emas->save();
            endforeach;
        endforeach;
    }
}); 


//AUTO REDEEM jika cuma satu hadiah
Route::get('/auto-redeem-satu', function () {
    /* ==================================================
        - dilistkan seluruh hadiah dulu dari d_bagi di union sama dari d_hadiah yang pilihan = 0 

        - terus di cek jumlah hadiahnya (jumlah baris)
        - kalau cuma 1 langsung di auto redeem

        - kalau jumlah lebih dari 1 di cek kode hadiahnya
            - kalau kode beda abaikan
            - kalau kode sama
                - di redeemkan dari yang nominalnya paling besar
                - sisanya di cek an di nominal yang kecil    
    ====================================================*/

    //select campaign yang ga ada hadiah emas nya
    $data = DB::select("
                    select cop_emas0.*, count(distinct rd.id) as jum_redeem from
                    (
                        select c.kode_campaign, c.kode_customer, c.omzet_netto, c.poin, c_emas.jum_emas, c_emas.id
                            from customer_omzet c
                        left join
                            (
                            select ch.id, ch.kode_campaign, IFNULL(c_emas.jum_emas,0) as jum_emas
                                from campaign_h ch 
                                left join
                                    (select id_campaign, count(kode_hadiah) as jum_emas from campaign_d_hadiah where emas = 1 
                                        group by id_campaign) c_emas
                                on ch.id = c_emas.id_campaign
                                where ch.active = 1
                            having jum_emas = 0
                            ) c_emas
                            on c.kode_campaign = c_emas.kode_campaign
                        where jum_emas = 0
                        AND '".date('Y-m-d')."' >= c.periode_awal and '".date('Y-m-d')."' <= c.periode_akhir
                    ) cop_emas0
                    LEFT JOIN redeem_detail rd on rd.kode_customer = cop_emas0.kode_customer and rd.id_campaign = cop_emas0.id
                    GROUP BY cop_emas0.kode_campaign, cop_emas0.kode_customer
                    HAVING jum_redeem = 0
            ");

    if ($data){
        foreach ($data as $detail):
            $id_campaign = $detail->id;
            $kode_campaign = $detail->kode_campaign;
            $kode_customer = $detail->kode_customer;
            $omzet = $detail->omzet_netto;
            $poin = $detail->poin;

            //ngelist hadiah
            $data_hadiah = DB::select("
                select cdh.id, cdh.kode_hadiah, cdh.pilihan, cdh.id_campaign, cdh.harga
                    from campaign_d_bagi cdb 
                    left join campaign_d_hadiah cdh
                    on cdb.id_campaign_d_hadiah = cdh.id
                where cdb.id_campaign = '".$id_campaign."' and kode_agen = '".$kode_customer."'
                union all
                select id, kode_hadiah, pilihan, id_campaign, harga
                    from campaign_d_hadiah
                where id_campaign = '".$id_campaign."'
                    and pilihan = 0 and emas = 0
                order by id_campaign, harga DESC
            ");

            //cek jumlah baris hadiah
            $jumlah_hadiah = count($data_hadiah);

            //jika jumlah hadiah cuma 1 langsung di redeem kan
            if ($jumlah_hadiah == 1){
                foreach ($data_hadiah as $detail_hadiah):
                    $id_campaign_hadiah = $detail_hadiah->id;
                    $harga = $detail_hadiah->harga;
                endforeach;

                if ($omzet > 0){
                    $jum_redeem = floor($omzet / $harga);
                } else 
                if ($poin > 0){
                    $jum_redeem = floor($poin / $harga);
                }

                $insert_redeem = new RedeemDetail();
                $insert_redeem->kode_customer = $kode_customer;
                $insert_redeem->id_campaign = $id_campaign;
                $insert_redeem->id_campaign_hadiah = $id_campaign_hadiah;
                $insert_redeem->jumlah = $jum_redeem;
                $insert_redeem->save();
            } else 
            if ($jumlah_hadiah > 1){
                $one_code = false;
                $kode_hadiah = [];
                foreach ($data_hadiah as $detail_hadiah):
                    array_push($kode_hadiah, $detail_hadiah->kode_hadiah);
                endforeach;

                //cek jika kodenya sama semua baru diredeemkan
                if (count(array_unique($kode_hadiah)) == 1){
                    $totop = 0;
                    if ($omzet > 0){
                        $totop = $omzet;
                    } else 
                    if ($poin > 0){
                        $totop = $poin;
                    }

                    //jika kode hadiah nya sama maka di redeem kan dengan urutan nominal terbesar dulu
                    foreach ($data_hadiah as $detail_hadiah):
                        $id_campaign_hadiah = $detail_hadiah->id;
                        $harga = $detail_hadiah->harga;

                        $jum_redeem = floor($totop / $harga);
                        $insert_redeem = new RedeemDetail();
                        $insert_redeem->kode_customer = $kode_customer;
                        $insert_redeem->id_campaign = $id_campaign;
                        $insert_redeem->id_campaign_hadiah = $id_campaign_hadiah;
                        $insert_redeem->jumlah = $jum_redeem;
                        $insert_redeem->save();

                        $sisa = ($totop - $jum_redeem * $harga);
                        $totop = $sisa;
                    endforeach;
                }
            }
        endforeach;
    }
}); 

Route::get('/email-reminder', function () {
    $sekarang = date('Y-m-d');
    $sekarang_1 = $sekarang;
    $sekarang_2 = $sekarang;
    $sekarang_3 = $sekarang;

    // $sekarang = '2019-06-30';
    // $sekarang_1 = '2019-06-29';
    // $sekarang_2 = '2019-06-28';
    // $sekarang_3 = '2019-06-22';

    // EMAIL NOTIFIKASI SUDAH BISA MULAI REDEEM    
    $data_campaign = DB::select("
        select * 
        from customer_omzet 
        where active = 1
            and date_add(periode_awal, interval 1 day) = '".$sekarang_3."'
    ");
    foreach ($data_campaign as $data_email):
        $user = UserAvex::where('uname', $data_email->kode_customer."A")->first();
        $email = $user->email;
        $email_to = array_filter(explode(";", $email));
        $message_2 = " 
            Dear Customer,<br/><br/>
            Proses penukaran hadiah campaign <b><u>".$data_email->kode_campaign."</u></b> sudah bisa dilakukan,<br/><br/>
            Mohon segera melakukan penukaran hadiah pada program AviaRedeem.<br/><br/>
            
            Terima kasih<br/><br/>

            Harap jangan balas email ini karena kami tidak dapat menanggapi pesan yang dikirim ke alamat email ini.
        ";
        $headers = "From: Info Avian<info@avianbrands.com>\r\n";
        $headers .= "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        foreach ($email_to as $email):
            mail(trim($email), "Notifikasi Penukaran Hadiah Campaign ".$data_email->kode_campaign." di AviaRedeem Customer ".$data_email->kode_customer, $message_2, $headers);
        endforeach;
    endforeach;

    //================= BELUM KLAIM ====================//
    $data_belum_klaim_0 = DB::select("
        select  `campaign_h`.`kode_campaign`, 
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
        having jum_redeem_detail = 0 and customer_omzet.periode_akhir = '".$sekarang."'
    ");

    foreach ($data_belum_klaim_0 as $data_email):
        $user = UserAvex::where('uname', $data_email->kode_customer."A")->first();
        $email = $user->email;
        $email_to = array_filter(explode(";", $email));
        $message_2 = " 
            Dear Customer,<br/><br/>
            Batas waktu penukaran hadiah campaign <b><u>".$data_email->kode_campaign."</u></b> adalah hari ini,<br/><br/>
            Mohon segera melakukan penukaran hadiah pada program AviaRedeem.<br/><br/>
            
            Terima kasih<br/><br/>

            Harap jangan balas email ini karena kami tidak dapat menanggapi pesan yang dikirim ke alamat email ini.
        ";
        $headers = "From: Info Avian<info@avianbrands.com>\r\n";
        $headers .= "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        foreach ($email_to as $email):
            mail(trim($email), "Reminder Hari Terakhir Penukaran Hadiah Campaign ".$data_email->kode_campaign." di AviaRedeem Customer ".$data_email->kode_customer, $message_2, $headers);
        endforeach;
    endforeach;

    $data_belum_klaim_1 = DB::select("
        select  `campaign_h`.`kode_campaign`, 
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
        having jum_redeem_detail = 0 and date_sub(customer_omzet.periode_akhir, interval 1 day) = '".$sekarang_1."'        
    ");
    foreach ($data_belum_klaim_1 as $data_email):
        $user = UserAvex::where('uname', $data_email->kode_customer."A")->first();
        $email = $user->email;
        $email_to = array_filter(explode(";", $email));
        $message_2 = " 
            Dear Customer,<br/><br/>
            Batas waktu penukaran hadiah campaign <b><u>".$data_email->kode_campaign."</u></b> kurang 1 hari,<br/><br/>
            Mohon segera melakukan penukaran hadiah pada program AviaRedeem.<br/><br/>
            
            Terima kasih<br/><br/>

            Harap jangan balas email ini karena kami tidak dapat menanggapi pesan yang dikirim ke alamat email ini.
        ";
        $headers = "From: Info Avian<info@avianbrands.com>\r\n";
        $headers .= "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        foreach ($email_to as $email):
            mail(trim($email), "Reminder Kurang 1 Hari Penukaran Hadiah Campaign ".$data_email->kode_campaign." di AviaRedeem Customer ".$data_email->kode_customer, $message_2, $headers);
        endforeach;
    endforeach;


    $data_belum_klaim_2 = DB::select("
        select  `campaign_h`.`kode_campaign`, 
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
        having jum_redeem_detail = 0 and date_sub(customer_omzet.periode_akhir, interval 2 day) = '".$sekarang_2."'        
    ");
    foreach ($data_belum_klaim_2 as $data_email):
        $user = UserAvex::where('uname', $data_email->kode_customer."A")->first();
        $email = $user->email;
        $email_to = array_filter(explode(";", $email));
        $message_2 = " 
            Dear Customer,<br/><br/>
            Batas waktu penukaran hadiah campaign <b><u>".$data_email->kode_campaign."</u></b> kurang 2 hari,<br/><br/>
            Mohon segera melakukan penukaran hadiah pada program AviaRedeem.<br/><br/>
            
            Terima kasih<br/><br/>

            Harap jangan balas email ini karena kami tidak dapat menanggapi pesan yang dikirim ke alamat email ini.
        ";
        $headers = "From: Info Avian<info@avianbrands.com>\r\n";
        $headers .= "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        foreach ($email_to as $email):
            mail(trim($email), "Reminder Kurang 2 Hari Penukaran Hadiah Campaign ".$data_email->kode_campaign." di AviaRedeem Customer ".$data_email->kode_customer, $message_2, $headers);
        endforeach;
    endforeach;

    
    //================== BELUM KONVERSI =======================//
    $data_belum_konversi_0 = DB::select("
        select  `campaign_h`.`kode_campaign`, 
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
        having jum_emas > 0 and jum_redeem_detail > 0 and jum_redeem_emas = 0 and customer_omzet.periode_akhir = '".$sekarang."'
    ");

    foreach ($data_belum_konversi_0 as $data_email):
        $user = UserAvex::where('uname', $data_email->kode_customer."A")->first();
        $email = $user->email;
        $email_to = array_filter(explode(";", $email));
        $message_2 = " 
            Dear Customer,<br/><br/>
            Batas waktu konversi emas campaign <b><u>".$data_email->kode_campaign."</u></b> adalah hari ini,<br/><br/>
            Mohon segera melakukan konversi emas pada program AviaRedeem.<br/><br/>
            
            Terima kasih<br/><br/>

            Harap jangan balas email ini karena kami tidak dapat menanggapi pesan yang dikirim ke alamat email ini.
        ";
        $headers = "From: Info Avian<info@avianbrands.com>\r\n";
        $headers .= "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        foreach ($email_to as $email):
            mail(trim($email), "Reminder Hari Terakhir Konversi Emas Campaign ".$data_email->kode_campaign." di AviaRedeem Customer ".$data_email->kode_customer, $message_2, $headers);
        endforeach;
    endforeach;

    $data_belum_konversi_1 = DB::select("
        select  `campaign_h`.`kode_campaign`, 
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
        having jum_emas > 0 and jum_redeem_detail > 0 and jum_redeem_emas = 0 and date_sub(customer_omzet.periode_akhir, interval 1 day) = '".$sekarang_1."'
    ");
    foreach ($data_belum_konversi_1 as $data_email):
        $user = UserAvex::where('uname', $data_email->kode_customer."A")->first();
        $email = $user->email;
        $email_to = array_filter(explode(";", $email));
        $message_2 = " 
            Dear Customer,<br/><br/>
            Batas waktu konversi emas campaign <b><u>".$data_email->kode_campaign."</u></b> kurang 1 hari,<br/><br/>
            Mohon segera melakukan konversi emas pada program AviaRedeem.<br/><br/>
            
            Terima kasih<br/><br/>

            Harap jangan balas email ini karena kami tidak dapat menanggapi pesan yang dikirim ke alamat email ini.
        ";
        $headers = "From: Info Avian<info@avianbrands.com>\r\n";
        $headers .= "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        foreach ($email_to as $email):
            mail(trim($email), "Reminder Kurang 1 Hari Konversi Emas Campaign ".$data_email->kode_campaign." di AviaRedeem Customer ".$data_email->kode_customer, $message_2, $headers);
        endforeach;
    endforeach;


    $data_belum_konversi_2 = DB::select("
        select  `campaign_h`.`kode_campaign`, 
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
        having jum_emas > 0 and jum_redeem_detail > 0 and jum_redeem_emas = 0 and date_sub(customer_omzet.periode_akhir, interval 2 day) = '".$sekarang_2."'
    ");
    foreach ($data_belum_konversi_2 as $data_email):
        $user = UserAvex::where('uname', $data_email->kode_customer."A")->first();
        $email = $user->email;
        $email_to = array_filter(explode(";", $email));
        $message_2 = " 
            Dear Customer,<br/><br/>
            Batas waktu konversi emas campaign <b><u>".$data_email->kode_campaign."</u></b> kurang 2 hari,<br/><br/>
            Mohon segera melakukan konversi emas pada program AviaRedeem.<br/><br/>
            
            Terima kasih<br/><br/>

            Harap jangan balas email ini karena kami tidak dapat menanggapi pesan yang dikirim ke alamat email ini.
        ";
        $headers = "From: Info Avian<info@avianbrands.com>\r\n";
        $headers .= "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        foreach ($email_to as $email):
            mail(trim($email), "Reminder Kurang 2 Hari Konversi Emas Campaign ".$data_email->kode_campaign." di AviaRedeem Customer ".$data_email->kode_customer, $message_2, $headers);
        endforeach;
    endforeach;    

});

Route::get('/api/data/get-customer/{id}', function ($id) {
    $user = UserAvex::where('reldag', $id)->first();
    return $user;
});

Route::get('/api/notifikasi/waktu-klaim', function () {
    $sekarang = date('Y-m-d');
    $sekarang_1 = $sekarang;
    $sekarang_2 = $sekarang;
    $sekarang_3 = $sekarang;

    $data = [];
    // EMAIL NOTIFIKASI SUDAH BISA MULAI REDEEM    
    $data = DB::select("
        select * 
        from customer_omzet 
        where active = 1
            and date_add(periode_awal, interval 1 day) = '".$sekarang_3."'
    ");
    return $data;
});

Route::get('/api/notifikasi/belum-klaim', function () {
    $sekarang = date('Y-m-d');
    $sekarang_1 = $sekarang;
    $sekarang_2 = $sekarang;
    $sekarang_3 = $sekarang;

    $data = [];
    $data = DB::select("
        select  `campaign_h`.`kode_campaign`, 
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
        having jum_redeem_detail = 0 and customer_omzet.periode_akhir = '".$sekarang."'
    ");
    return $data;
});

Route::get('/api/notifikasi/belum-konversi', function () {
    $sekarang = date('Y-m-d');
    $sekarang_1 = $sekarang;
    $sekarang_2 = $sekarang;
    $sekarang_3 = $sekarang;

    $data = [];
    //================== BELUM KONVERSI =======================//
    $data = DB::select("
        select  `campaign_h`.`kode_campaign`, 
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
        having jum_emas > 0 and jum_redeem_detail > 0 and jum_redeem_emas = 0 and customer_omzet.periode_akhir = '".$sekarang."'
    ");
    return $data;
});


Route::get('/backup-database', function () {
    $tables = false;
    $host = "localhost";
    $user = env('DB_USERNAME');
    $pass = env('DB_PASSWORD');
    $name = env('DB_DATABASE');
    $backup_path = 'backup/backup_'.date('l').'.sql';

	set_time_limit(3000); $mysqli = new mysqli($host,$user,$pass,$name); $mysqli->select_db($name); $mysqli->query("SET NAMES 'utf8'");
	$queryTables = $mysqli->query('SHOW TABLES'); while($row = $queryTables->fetch_row()) { $target_tables[] = $row[0]; }	if($tables !== false) { $target_tables = array_intersect( $target_tables, $tables); } 
	$content = "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\r\nSET time_zone = \"+00:00\";\r\n\r\n\r\n/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\r\n/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\r\n/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\r\n/*!40101 SET NAMES utf8 */;\r\n--\r\n-- Database: `".$name."`\r\n--\r\n\r\n\r\n";
	foreach($target_tables as $table){
		if (empty($table)){ continue; } 
		$result	= $mysqli->query('SELECT * FROM `'.$table.'`');  	$fields_amount=$result->field_count;  $rows_num=$mysqli->affected_rows; 	$res = $mysqli->query('SHOW CREATE TABLE '.$table);	$TableMLine=$res->fetch_row(); 
		$content .= "\n\n".$TableMLine[1].";\n\n";   $TableMLine[1]=str_ireplace('CREATE TABLE `','CREATE TABLE IF NOT EXISTS `',$TableMLine[1]);
		for ($i = 0, $st_counter = 0; $i < $fields_amount;   $i++, $st_counter=0) {
			while($row = $result->fetch_row())	{ //when started (and every after 100 command cycle):
				if ($st_counter%100 == 0 || $st_counter == 0 )	{$content .= "\nINSERT INTO ".$table." VALUES";}
					$content .= "\n(";    for($j=0; $j<$fields_amount; $j++){ $row[$j] = str_replace("\n","\\n", addslashes($row[$j]) ); if (isset($row[$j])){$content .= '"'.$row[$j].'"' ;}  else{$content .= '""';}	   if ($j<($fields_amount-1)){$content.= ',';}   }        $content .=")";
				//every after 100 command cycle [or at last line] ....p.s. but should be inserted 1 cycle eariler
				if ( (($st_counter+1)%100==0 && $st_counter!=0) || $st_counter+1==$rows_num) {$content .= ";";} else {$content .= ",";}	$st_counter=$st_counter+1;
			}
		} $content .="\n\n\n";
	}
	$content .= "\r\n\r\n/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\r\n/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\r\n/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;";
    $myfile =  fopen($backup_path, "w");
    fwrite($myfile, $content);
    fclose($myfile);
});

Route::match(array('GET','POST'),'/backend/login','Backend\LoginController@index');
//logout
Route::get('/logout','Backend\LoginController@logout');

/* SUPER ADMIN */
Route::group(array('prefix' => 'backend','middleware'=> ['token_super_admin']), function()
{
	Route::get('/setting','Backend\SettingController@index');
    Route::post('/setting','Backend\SettingController@update');
    
});

/* ADMIN SUPER*/
Route::group(array('prefix' => 'backend','middleware'=> ['token_super']), function()
{
    /* MASTER CUSTOMER OMZET */
    Route::get('/master-omzet/upload','Backend\OmzetController@upload');	
    Route::post('/master-omzet/upload','Backend\OmzetController@upload_store');	
    Route::get('/master-omzet/datatable','Backend\OmzetController@datatable');	
    Route::post('/master-omzet/delete','Backend\OmzetController@deleteAll');
    Route::resource('master-omzet', 'Backend\OmzetController');

    Route::get('/last-tto/datatable','Backend\TTOController@datatable');	
    Route::resource('last-tto', 'Backend\TTOController');

    Route::get('/delete-redeem','Backend\DelRedeemController@index');	
    Route::post('/delete-redeem','Backend\DelRedeemController@delete');	

});


/* ADMIN */
Route::group(array('prefix' => 'backend','middleware'=> ['token_admin']), function()
{
    Route::get('/dashboard','Backend\DashboardController@dashboard');

    /* MASTER CAMPAIGN */

    /* APPROVAL */
    Route::post('/campaign/view/approval/{id}','Backend\CampaignController@view_approval');

    Route::get('/campaign/datatable','Backend\CampaignController@datatable');	
    Route::get('/campaign/{id}/edit-list-hadiah','Backend\CampaignController@edit_list_hadiah');
    Route::match(array('PUT','PATCH'),'/campaign/{id}/edit-list-hadiah','Backend\CampaignController@update_list_hadiah');
    Route::get('/campaign/{id}/edit-pembagian-hadiah','Backend\CampaignController@edit_pembagian_hadiah');
    Route::post('/campaign/{id}/edit-pembagian-hadiah','Backend\CampaignController@update_pembagian_hadiah');
    Route::get('/campaign/{id}/edit-master-emas','Backend\CampaignController@edit_master_emas');
    Route::post('/campaign/{id}/edit-master-emas','Backend\CampaignController@update_master_emas');
    Route::resource('campaign', 'Backend\CampaignController');
    
    /* GENERAL REPORT */
    Route::get('/general-report','Backend\ReportController@general_report');
    Route::get('/general-report/datatable','Backend\ReportController@datatable');
    Route::get('/general-report/view/{id}','Backend\ReportController@view');

    Route::get('/redeem-report','Backend\ReportController@redeem_report');

});


/* ADMIN DAN USER*/
Route::group(array('prefix' => 'backend','middleware'=> ['token_all']), function()
{
    Route::get('',function (){return Redirect::to('backend/dashboard');});
    Route::get('/user-guide', function () {
        return response()->file('upload/SOP Avia Redeem.pdf');
    });

});

/* USER AJA */
Route::group(array('prefix' => 'backend','middleware'=> ['token_user']), function()
{
    Route::get('/redeem-hadiah/datatable','Backend\RedeemController@datatable');
    Route::get('/redeem-hadiah','Backend\RedeemController@index');

    Route::get('/redeem-hadiah/{id}/klaim-hadiah','Backend\RedeemController@klaim_hadiah');
    Route::post('/redeem-hadiah/{id}/klaim-hadiah','Backend\RedeemController@klaim_hadiah_update');

    Route::get('/redeem-hadiah/{id}/edit/klaim-hadiah','Backend\RedeemController@edit_klaim_hadiah');
    Route::post('/redeem-hadiah/{id}/edit/klaim-hadiah','Backend\RedeemController@edit_klaim_hadiah_update');

    Route::get('/redeem-hadiah/{id}/konversi-emas','Backend\RedeemController@konversi_emas');
    Route::post('/redeem-hadiah/{id}/konversi-emas','Backend\RedeemController@konversi_emas_update');

    Route::get('/redeem-hadiah/{id}','Backend\RedeemController@view');
});