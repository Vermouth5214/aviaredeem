<?php
	$breadcrumb = [];
	$breadcrumb[0]['title'] = 'Dashboard';
	$breadcrumb[0]['url'] = url('backend/dashboard');
?>

<!-- LAYOUT -->
@extends('backend.layouts.main')

<!-- TITLE -->
@section('title', 'Dashboard')

<!-- CONTENT -->
@section('content')
    <div class="page-title">
        <div class="title_left">
            <h3>Dashboard</h3>
        </div>
        <div class="title_right">
        </div>
    </div>
    <div class="clearfix"></div>
    @include('backend.elements.breadcrumb',array('breadcrumb' => $breadcrumb))	
    <div class="row">
        <div class="col-xs-12">
            <table class="table table-striped">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Kode Campaign</th>
                    <th>Nama Campaign</th>
                    <th>Kedaluwarsa (Belum Klaim)</th>
                    <th>Kedaluwarsa (Belum Konversi)</th>
                    <th>Belum Klaim</th>
                    <th>Belum Konversi</th>
                  </tr>
                </thead>
                <tbody>
                    <?php
                        $i = 1;
                        foreach ($data as $item):
                    ?>
                        <tr>
                            <td class="text-right"><?=$i;?></td>
                            <td><?=$item->kode_campaign;?>
                            <td><?=$item->nama_campaign;?>
                            <td ><?=$item->jum_kadaluarsa_belum_klaim?></td>
                            <td><?=$item->jum_kadaluarsa_belum_konversi?></td>
                            <td><?=$item->jum_belum_klaim?></td>
                            <td><?=$item->jum_belum_konversi?></td>
                        </tr>
                    <?php
                            $i++;
                        endforeach;
                    ?>
                </tbody>
            </table>            
        </div>
    </div>
@endsection