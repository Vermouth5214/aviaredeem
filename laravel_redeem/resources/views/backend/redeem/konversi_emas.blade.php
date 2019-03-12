<?php
	$breadcrumb = [];
	$breadcrumb[0]['title'] = 'Dashboard';
	$breadcrumb[0]['url'] = url('backend/dashboard');
	$breadcrumb[1]['title'] = 'Konversi Emas';
	$breadcrumb[1]['url'] = url('backend/konversi-emas');
?>

<!-- LAYOUT -->
@extends('backend.layouts.main')

<!-- TITLE -->
@section('title', 'Konversi Emas')

<!-- CONTENT -->
@section('content')
	<div class="page-title">
		<div class="title_left">
			<h3>Konversi Emas</h3>
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
                        <h2>Brosur : 
                            <br/>
                            <?php
                                $brosur = explode(";",$data_header[0]->brosur);
                                foreach ($brosur as $ctr=>$image):
                                    if ($ctr > 0):
                            ?>
                                <a style="font-size:17px;font-weight:bold;" href="<?=url('upload/Brosur/'.$image);?>" target="_blank"><i class="fa fa-paperclip" style="font-style:italic"></i> <?=$image;?></a><br/>
                            <?php
                                    endif;
                                endforeach;

                            ?>
                        </h2>
                        <br/><br/>
                        <div class="clearfix"></div>
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
                                $url = "backend/redeem-hadiah/".$data_omzet[0]->id."/konversi-emas";
                                $total = 0;
                            ?>
                            {{ Form::open(['url' => $url, 'method' => 'POST','class' => 'form-horizontal form-label-left', 'id' => 'form-submit']) }}
                            <h3>Total Emas (gr) : <span id="total_emas"><?=number_format($total_gram,0,',','.');?></span></h3>
                            <br/>
                            <h2>List Hadiah</h2>
                                <table class="table table-striped table-hover table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                <thead>
                                    <th class="text-center">Hadiah</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-center">Satuan</th>
                                </thead>
                                <tbody>
                            <?php
                                foreach ($data_konversi as $hadiah):
                            ?>      
                                    <tr>
                                        <td width = "60%" class="text-right">
                                            <input type="hidden" name="id[]" value="<?=$hadiah->id;?>">
                                            <?=$hadiah->nama_hadiah;?>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control jumlah" name="jumlah[]" min=0 value=0 required="required">
                                            <input type="hidden" value="<?=$hadiah->jumlah;?>" class="jumlah_emas" name="jumlah_emas[]">
                                        </td>
                                        <td>
                                            <?=$hadiah->satuan;?>
                                        </td>
                                    </tr>
                            <?php
                                endforeach;
                            ?>
                                </tbody>
                                </table>
                                <div class="form-group">
                                    <div class="col-sm-6 col-xs-12 col-sm-offset-6 text-right">
                                        <a href="<?=url('/backend/redeem-hadiah')?>" class="btn btn-warning">&nbsp;&nbsp;&nbsp;&nbsp;Cancel&nbsp;&nbsp;&nbsp;&nbsp;</a>
                                        <button type="submit" class="btn btn-primary btn-submit">&nbsp;&nbsp;&nbsp;&nbsp;Submit&nbsp;&nbsp;&nbsp;&nbsp;</button>
                                    </div>
                                </div>
                            {{ Form::close() }}                                
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
    <script>
        function numberWithCommas(x) {
            var parts = x.toString().split(".");
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            return parts.join(".");
        }
    </script>
    <script>
        function hitung_sub_total(){
            subtotal = 0;
            $('.jumlah').each(function () {
                val = parseFloat($(this).val()) | 0;
                jumlah = $(this).next().val();
                subtotal = subtotal + (val * jumlah);
            });
            return subtotal;
        }

        $('.jumlah').on('change',function(e){
            var total = <?=$total_gram;?>;
            var subtotal = hitung_sub_total();
            var sisa = total - subtotal;
            if (sisa < 0){
                $('#total_emas').html("<span class='red'>" + numberWithCommas(sisa) + "</span>");
            } else {
                $('#total_emas').html(numberWithCommas(sisa));
            }
            
        })

        $('#form-submit').on('submit',function(){
            if (confirm("Apakah anda yakin mau mensubmit data ini?")) {            
                var total = <?=$total_gram;?>;
                var subtotal = hitung_sub_total();
                var sisa = total - subtotal;
                if (sisa < 0){
                    alert('Penukaran emas melebihi omzet')
                    return false;
                }
                if (sisa > 0 ){
                    alert('Sisa omzet masih bisa dikonversikan')
                    return false;
                }
                return true;
            }
            return false;
        })
    </script>
@endsection