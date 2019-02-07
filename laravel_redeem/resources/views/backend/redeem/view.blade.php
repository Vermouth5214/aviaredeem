<?php
	$breadcrumb = [];
	$breadcrumb[0]['title'] = 'Dashboard';
	$breadcrumb[0]['url'] = url('backend/dashboard');
	$breadcrumb[1]['title'] = 'View';
	$breadcrumb[1]['url'] = url('backend/view');
?>

<!-- LAYOUT -->
@extends('backend.layouts.main')

<!-- TITLE -->
@section('title', 'View')

<!-- CONTENT -->
@section('content')
	<div class="page-title">
		<div class="title_left">
			<h3>View</h3>
		</div>
		<div class="title_right">
			<div class="col-md-4 col-sm-4 col-xs-8 form-group pull-right top_search">
                @include('backend.elements.back_button',array('url' => '/backend/redeem-hadiah'))
			</div>
		</div>
	</div>
	<div class="clearfix"></div>
	@include('backend.elements.breadcrumb',array('breadcrumb' => $breadcrumb))	
	<div class="row">
		<div class="col-xs-12">
			<div class="x_panel">
				<div class="x_content">
                    @include('backend.elements.notification')
                    <div class="x_title">
                        <h2>Kode Campaign : <b><i><?=$data_header[0]->kode_campaign;?></i></b></h2>
                        <div class="clearfix"></div>
                        <h2>Nama Campaign : <b><i><?=$data_header[0]->nama_campaign;?></i></b></h2>
                        <div class="clearfix"></div>
                        <h2>Brosur : <b><i>
                            <?php
                                if ($data_header[0]->brosur != ""):
                            ?>
                                <a style="font-size:17px;font-weight:bold;" href="<?=url('upload/Brosur/'.$data_header[0]->brosur);?>" target='_blank'><i class="fa fa-paperclip" style="font-style:italic"> <?=$data_header[0]->brosur;?></i></a>
                            <?php
                                endif;
                            ?>
                        </i></b></h2>
                        <br/><br/>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-xs-12">
                            <?php
                                if ($data_header[0]->jenis == "omzet"):
                            ?>
                            <table class="table table-striped table-hover table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                <thead>
                                    <th class="text-center">Omzet tepat waktu</th>
                                    <th class="text-center">Disc pembelian</th>
                                    <th class="text-center">Omzet netto dgn disc <?=$data_omzet[0]->disc_penjualan;?></th>
                                </thead>
                                <tbody>
                                    <td class="text-right"><?=number_format($data_omzet[0]->omzet_tepat_waktu,0,',','.');?></td>
                                    <td class="text-right"><?=$data_omzet[0]->disc_pembelian;?></td>
                                    <td class="text-right"><?=number_format($data_omzet[0]->omzet_netto,0,',','.');?></td>
                                </tbody>
                            </table>
                            <?php
                                endif;
                            ?>
                            <?php
                                if ($data_header[0]->jenis == "poin"):
                            ?>
                                <h2>Poin : <b><i><?=number_format($data_omzet[0]->poin,0,',','.');?></i></b></h2>
                            <?php
                                endif;
                            ?>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-xs-12">
                            <?php
                                if ($data_header[0]->jenis == "omzet"):
                                    $total = $data_omzet[0]->omzet_netto;
                            ?>
                            <h3>Sisa Omzet : <span id="omzet_poin"><?=number_format($data_omzet[0]->omzet_netto,0,',','.');?></span></h3>
                            <?php
                                endif;
                            ?>
                            <?php
                                if ($data_header[0]->jenis == "poin"):
                                    $total = $data_omzet[0]->poin;
                            ?>
                            <h3>Sisa Poin : <span id="omzet_poin"><?=number_format($data_omzet[0]->poin,0,',','.');?></span></h3>
                            <?php
                                endif;
                            ?>
                            <?php
                                if ($data_header[0]->TPP == 1):
                            ?>
                            <i><p class="small blue">Sisa Omzet / Poin akan dikonversikan ke dalam hadiah Tambahan Potongan Penjualan</p></i>
                            <?php
                                endif;
                            ?>
                            <br/>
                            <h2>List Hadiah</h2>
                            
                        </div>
                    </div>
				</div>
			</div>
		</div>					
	</div>
@endsection

<!-- CSS -->
@section('css')

@endsection

<!-- JAVASCRIPT -->
@section('script')

@endsection