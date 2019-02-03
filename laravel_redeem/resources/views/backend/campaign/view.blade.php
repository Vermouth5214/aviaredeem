<?php
	$breadcrumb = [];
	$breadcrumb[0]['title'] = 'Dashboard';
	$breadcrumb[0]['url'] = url('backend/dashboard');
	$breadcrumb[1]['title'] = 'Master Campaign';
    $breadcrumb[1]['url'] = url('backend/campaign/');
	$breadcrumb[2]['title'] = 'View';
	$breadcrumb[2]['url'] = url('backend/campaign/'.$data_header[0]->id);
    
?>

<!-- LAYOUT -->
@extends('backend.layouts.main')

<!-- TITLE -->
@section('title', 'Master Campaign - View')

<!-- CONTENT -->
@section('content')
	<div class="page-title">
		<div class="title_left">
			<h3>Master Campaign - View</h3>
		</div>
		<div class="title_right">
			<div class="col-md-4 col-sm-4 col-xs-8 form-group pull-right top_search">
                @include('backend.elements.back_button',array('url' => '/backend/campaign'))
			</div>
		</div>
	</div>
	<div class="clearfix"></div>
	@include('backend.elements.breadcrumb',array('breadcrumb' => $breadcrumb))	
	<div class="row">
		<div class="col-xs-12">
			<div class="x_panel">
				<div class="x_content">
                    <div class="x_title">
                        <h2>Campaign Header</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <h5>Kode Campaign : <b><?=$data_header[0]->kode_campaign;?></b></h5>
                            <h5>Nama Campaign : <b><?=$data_header[0]->nama_campaign;?></b></h5>
                            <h5>Jenis : <b><?=strtoupper($data_header[0]->jenis);?></b></h5>
                            <h5>TPP : 
                            <?php
                                if ($data_header[0]->TPP == 0){
                                    echo "<b>Bukan</b>";
                                } else 
                                if ($data_header[0]->TPP == 1){
                                    echo "<b>Ya</b>";
                                }
                            ?></h5>
                            <h5>Brosur : <a href="<?=url('upload/Brosur/'.$data_header[0]->brosur);?>" target="_blank"><?=$data_header[0]->brosur;?></a></h5>
                            <h5>Status :
                            <?php
                                if ($data_header[0]->active == 1){
                                    echo "<span class='badge badge-success'>Active</span>";
                                } else {
                                    echo "<span class='badge badge-warning'>Not Complete</span>";
                                }
                            ?></h5>
                        </div>
                    </div>
                    <br/>
                    <div class="x_title">
                        <h2>List Hadiah</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <table class="table table-striped table-hover table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Catalogue</th>
                                        <th>Kode Hadiah</th>
                                        <th>Nama Hadiah</th>
                                        <th>Jumlah</th>
                                        <th>Harga</th>
                                        <th>Pilihan</th>
                                        <th>Emas</th>
                                    </tr>
                                </thead>
                                <?php
                                    $i = 1;
                                    foreach ($data_list_hadiah as $hadiah):
                                ?>
                                        <tr>
                                            <td class="text-right"><?=$i;?></td>
                                            <td><?=$hadiah->kode_catalogue;?></td>
                                            <td><?=$hadiah->kode_hadiah;?></td>
                                            <td><?=$hadiah->nama_hadiah;?></td>
                                            <td class="text-right"><?=$hadiah->jumlah;?></td>
                                            <td class="text-right"><?=number_format($hadiah->harga,0,',','.');?></td>
                                            <td class="text-center">
                                                <?php
                                                    $pilihan = "";
                                                    if ($hadiah->pilihan == 1){
                                                        $pilihan = "V";
                                                    }
                                                    echo $pilihan;
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                    $emas = "";
                                                    if ($hadiah->emas == 1){
                                                        $emas = "V";
                                                    }
                                                    echo $emas;
                                                ?>
                                            </td>
                                        </tr>
                                <?php
                                        $i++;
                                    endforeach;
                                ?>
                            </table>
                        </div>
                    </div>
                    <br/>
                    <div class="x_title">
                        <h2>Pembagian Hadiah Agen</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <table class="table table-striped table-hover table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Agen</th>
                                        <th>Cabang</th>
                                        <?php
                                            foreach ($list_hadiah_pilihan as $item):
                                        ?>
                                            <th class="text-center">
                                                <?=$item->nama_hadiah;?>
                                            </th>
                                        <?php
                                            endforeach;
                                        ?>
                                    </tr>
                                    <?php
                                        $i = 1;
                                        foreach ($data_pembagian_hadiah as $agen):
                                    ?>
                                    <tr>
                                            <td class="text-right"><?=$i;?></td>
                                            <td><?=$agen->kode_agen;?></td>
                                            <td><?=$agen->agen->cabang;?></td>
                                            <?php
                                                foreach ($list_hadiah_pilihan as $item):
                                                    $checked = "";
                                                    if ($item->id == $agen->id_campaign_d_hadiah){
                                                        $checked = "checked";
                                                    }
                                            ?>
                                                    <td class="text-center">
                                                        <input class="radio_<?=$item->id;?>" type="radio" name="hadiah_<?=$agen->kode_agen;?>" value="<?=$item->id;?>" <?=$checked;?> disabled>
                                                    </td>
                                            <?php
                                                endforeach;
                                            ?>
                                        </tr>
    
                                    <?php
                                            $i++;
                                        endforeach;
                                    ?>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <br/>
                    <div class="x_title">
                        <h2>Master Emas</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <table class="table table-striped table-hover table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Catalogue</th>
                                        <th>Kode Hadiah</th>
                                        <th>Nama Hadiah</th>
                                        <th>Jumlah</th>
                                        <th>Harga</th>
                                    </tr>
                                </thead>
                                <?php
                                    $i = 1;
                                    foreach ($data_master_emas as $hadiah):
                                ?>
                                        <tr>
                                            <td class="text-right"><?=$i;?></td>
                                            <td><?=$hadiah->kode_catalogue;?></td>
                                            <td><?=$hadiah->kode_hadiah;?></td>
                                            <td><?=$hadiah->nama_hadiah;?></td>
                                            <td class="text-right"><?=$hadiah->jumlah;?></td>
                                            <td class="text-right"><?=number_format($hadiah->harga,0,',','.');?></td>
                                        </tr>
                                <?php
                                        $i++;
                                    endforeach;
                                ?>
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