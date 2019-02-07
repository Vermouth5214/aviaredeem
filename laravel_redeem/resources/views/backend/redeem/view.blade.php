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
                                    $sisa = 0;
                                    $subtotal = 0;
                                    foreach ($data_redeem as $detail):
                                        $subtotal = $subtotal + ($detail->jumlah * $detail->campaign_hadiah->harga);
                                    endforeach;
                                    $sisa = $total - $subtotal;
                            ?>
                            <h3>Sisa Omzet : <span id="omzet_poin"><?=number_format($sisa,0,',','.');?></span></h3>
                            <?php
                                endif;
                            ?>
                            <?php
                                if ($data_header[0]->jenis == "poin"):
                                    $total = $data_omzet[0]->poin;
                                    $sisa = 0;
                                    $subtotal = 0;
                                    foreach ($data_redeem as $detail):
                                        $subtotal = $subtotal + ($detail->jumlah * $detail->campaign_hadiah->harga);
                                    endforeach;
                                    $sisa = $total - $subtotal;
                            ?>
                            <h3>Sisa Poin : <span id="omzet_poin"><?=number_format($sisa,0,',','.');?></span></h3>
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
                            <h2>Daftar Redeem</h2>
                            <table class="table table-striped table-hover table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                <thead>
                                    <th class="text-center">Hadiah</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-center">Harga / Poin</th>
                                </thead>
                                <tbody>
                                    <?php
                                        $total = 0;
                                        foreach ($data_redeem as $hadiah):
                                    ?>      
                                            <tr>
                                                <td width = "60%" class="text-right">
                                                    <?=$hadiah->campaign_hadiah->nama_hadiah;?>
                                                </td>
                                                <td class="text-right">
                                                    <?=number_format($hadiah->jumlah,0,',','.');?>
                                                </td>
                                                <td class="text-right">
                                                    <?=number_format($hadiah->campaign_hadiah->harga,0,',','.');?>
                                                </td>
                                            </tr>
                                    <?php
                                        $total = $total + ($hadiah->jumlah * $hadiah->campaign_hadiah->harga);
                                        endforeach;
                                    ?>
                                </tbody>
                                <thead>
                                    <th class="text-right" colspan=2>Grand Total</th>
                                    <th class="text-right">  <?=number_format($total,0,',','.');?></th>
                                </thead>
                            </table>
                            <br/>
                            <h2>Daftar Konversi Emas</h2>
                            <table class="table table-striped table-hover table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                <thead>
                                    <th class="text-center">Hadiah</th>
                                    <th class="text-center">Jumlah</th>
                                </thead>
                                <tbody>
                                    <?php
                                        foreach ($data_konversi as $hadiah):
                                    ?>      
                                            <tr>
                                                <td width = "60%" class="text-right">
                                                    <?=$hadiah->campaign_hadiah->nama_hadiah;?>
                                                </td>
                                                <td class="text-right">
                                                    <?=$hadiah->jumlah;?>
                                                </td>
                                            </tr>
                                    <?php
                                        endforeach;
                                    ?>
                                </tbody>
                            </table>
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