<?php

namespace App\Imports;

use Session;
use App\Model\CustomerOmzet;
use App\Model\CampaignH;
use App\Model\UserAvex;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class OmzetImport implements ToCollection, WithMultipleSheets
{
    private $error = [];

    public function sheets(): array
    {
        return [
            0 => $this,
        ];
    }    

    public function collection(Collection $rows)
    {
        $i = 1;
        foreach ($rows as $row) 
        {
            $cek_campaign = CampaignH::where('kode_campaign', trim($row[0]))->where('active' , 1)->count();
            $cek_user = UserAvex::where('reldag', trim($row[1]))->count();
            $cek = CustomerOmzet::where('kode_campaign', trim($row[0]))->where('kode_customer', trim($row[1]))->where('active',1)->count();
            if ($cek_campaign == 0){
                $text = "Baris ".$i." : Kode Campaign tidak ditemukan";
                array_push($this->error,$text);
            } else 
            if ($cek_user == 0){
                $text = "Baris ".$i." : Kode Customer tidak ditemukan";
                array_push($this->error,$text);
            } else
            if ($cek > 0){
                //kembar
                $text = "Baris ".$i." : Data sudah ada";
                array_push($this->error,$text);
            } else
            if ($row[3] < $row[2]){
                //periode akhir < periode awal
                $text = "Baris ".$i." : Tanggal periode akhir lebih kecil dari periode awal";
                array_push($this->error,$text);
            } else 
            if (($row[7] == 0) && ($row[8] == 0)){
                //jika omzet 0 dan poin = 0
                $text = "Baris ".$i." : Omzet Netto dan Poin 0";
                array_push($this->error,$text);
            } else 
            if (($row[7] > 0) && ($row[8] > 0)){
                //jika omzet dan poin diisi
                $text = "Baris ".$i." : Omzet Netto dan Poin > 0";
                array_push($this->error,$text);
            } else 
            if (($row[7] < 0) || ($row[8] < 0)){
                //jika omzet lebih kecil 0 atau poin lebih kecil 0
                $text = "Baris ".$i." : Omzet Netto atau Poin < 0";
                array_push($this->error,$text);
            } else {
                $data = new CustomerOmzet;
                $data->kode_campaign = trim($row[0]);
                $data->kode_customer = trim($row[1]);
                $data->periode_awal = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[2]);
                $data->periode_akhir = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[3]);
                $data->omzet_tepat_waktu = $row[4] / 1;
                $data->disc_pembelian = $row[5] / 1;
                $data->disc_penjualan = $row[6] / 1;
                $data->omzet_netto = $row[7] / 1;
                $data->poin = $row[8] / 1;
                $data->active = 1;
                $data->user_modified = Session::get('userinfo')['uname'];
                $data->save();
            }
            $i++;
        }
    }

    public function getError(): array
    {
        return $this->error;
    }    

}