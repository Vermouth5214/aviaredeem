<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportExcell implements WithMultipleSheets
{
    use Exportable;

    public function __construct($heading_emas, $data_emas_header, $heading_detail_emas, $data_emas_detail, $heading_non_emas, $data_non_emas_header, $heading_detail_non_emas, $data_non_emas_detail)
    {
        $this->heading_emas = $heading_emas;
        $this->data_emas_header = $data_emas_header;
        $this->heading_detail_emas = $heading_detail_emas;
        $this->data_emas_detail = $data_emas_detail;
        $this->heading_non_emas = $heading_non_emas;
        $this->data_non_emas_header = $data_non_emas_header;
        $this->heading_detail_non_emas = $heading_detail_non_emas;
        $this->data_non_emas_detail = $data_non_emas_detail;
    }
    
    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new Sheet($this->heading_emas, $this->data_emas_header,'Header Emas');
        $sheets[] = new Sheet($this->heading_detail_emas, $this->data_emas_detail,'Detail Emas');
        $sheets[] = new Sheet($this->heading_non_emas, $this->data_non_emas_header,'Header Non Emas');
        $sheets[] = new Sheet($this->heading_detail_non_emas, $this->data_non_emas_detail,'Detail Non Emas');

        return $sheets;
    }   

}