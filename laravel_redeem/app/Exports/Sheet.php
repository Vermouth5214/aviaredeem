<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class Sheet implements FromView, ShouldAutoSize, WithTitle
{
    use Exportable;

    public function __construct($heading, $data, $title)
    {
        $this->heading = $heading;
        $this->data = $data;
        $this->title = $title;
    }
    
    public function view(): View
    {
        return view('exports.detail', [
            'data' => $this->data,
            'header' => $this->heading
        ]);           
    }   

    public function title(): string
    {
        return $this->title;
    }    
}