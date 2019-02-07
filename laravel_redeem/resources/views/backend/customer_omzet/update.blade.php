<?php
	$breadcrumb = [];
	$breadcrumb[0]['title'] = 'Dashboard';
	$breadcrumb[0]['url'] = url('backend/dashboard');
	$breadcrumb[1]['title'] = 'Master Customer Omzet';
	$breadcrumb[1]['url'] = url('backend/master-omzet');	
	$breadcrumb[2]['title'] = 'Add';
	$breadcrumb[2]['url'] = url('backend/master-omzet/create');
	if (isset($data)){
		$breadcrumb[2]['title'] = 'Edit';
		$breadcrumb[2]['url'] = url('backend/master-omzet/'.$data[0]->id.'/edit');
	}
?>

<!-- LAYOUT -->
@extends('backend.layouts.main')

<!-- TITLE -->
@section('title')
	<?php
		$mode = "Create";
		if (isset($data)){
			$mode = "Edit";
		}
	?>
    Master Customer Omzet - <?=$mode;?>
@endsection

<!-- CONTENT -->
@section('content')
	<?php
        $kode_campaign = old('kode_campaign');
        $kode_customer = old('kode_customer');
        $periode_awal = date('d-m-Y');
        $periode_akhir = date('d-m-Y');
        $omzet_tepat_waktu = 0;
        $disc_pembelian = 0;
        $disc_penjualan = 0;
        $omzet = 0;
        $poin = 0;
		$method = "POST";
		$mode = "Create";
		$url = "backend/master-omzet/";
		if (isset($data)){
            $kode_campaign = $data[0]->kode_campaign;
            $kode_customer = $data[0]->kode_customer;
            $periode_awal = date('d-m-Y',strtotime($data[0]->periode_awal));
            $periode_akhir = date('d-m-Y',strtotime($data[0]->periode_akhir));
            $omzet_tepat_waktu = $data[0]->omzet_tepat_waktu;
            $disc_pembelian = $data[0]->disc_pembelian;
            $disc_penjualan = $data[0]->disc_penjualan;
            $omzet = $data[0]->omzet_netto;
            $poin = $data[0]->poin;
			$method = "PUT";
			$mode = "Edit";
			$url = "backend/master-omzet/".$data[0]->id;
		}
	?>
	<div class="page-title">
		<div class="title_left">
			<h3>Master Customer Omzet - <?=$mode;?></h3>
		</div>
		<div class="title_right">
			<div class="col-md-4 col-sm-4 col-xs-8 form-group pull-right top_search">
                @include('backend.elements.back_button',array('url' => '/backend/master-omzet'))
			</div>
        </div>
        <div class="clearfix"></div>
		@include('backend.elements.breadcrumb',array('breadcrumb' => $breadcrumb))
	</div>
	<div class="clearfix"></div>
	<br/><br/>	
	<div class="row">
		<div class="col-xs-12">
			<div class="x_panel">
				<div class="x_content">
					{{ Form::open(['url' => $url, 'method' => $method,'class' => 'form-horizontal form-label-left']) }}
                        {!! csrf_field() !!}
                        @include('backend.elements.notification')
                        <p class="small blue">
                            - Untuk angka gunakan karakter titik sebagai tanda koma
                        </p>
						<div class="form-group">
							<label class="control-label col-sm-3 col-xs-12">Kode Campaign <span class="required">*</span></label>
							<div class="col-sm-5 col-xs-12">
								<input type="text" name="kode_campaign" required="required" class="form-control" value="<?=$kode_campaign;?>" autofocus>
							</div>
                        </div>
						<div class="form-group">
							<label class="control-label col-sm-3 col-xs-12">Kode Customer <span class="required">*</span></label>
							<div class="col-sm-5 col-xs-12">
								<input type="text" name="kode_customer" required="required" class="form-control" value="<?=$kode_customer;?>">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3 col-xs-12">Periode Awal <span class="required">*</span></label>
							<div class="col-sm-3 col-xs-12">
                                <div class='input-group date'>
                                    <input type='text' class="form-control" name="periode_awal" value=<?=$periode_awal;?> required="required" />
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3 col-xs-12">Periode Akhir <span class="required">*</span></label>
							<div class="col-sm-3 col-xs-12">
                                <div class='input-group date'>
                                    <input type='text' class="form-control" name="periode_akhir" value=<?=$periode_akhir;?> required="required" />
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
							</div>
                        </div>
						<div class="form-group">
							<label class="control-label col-sm-3 col-xs-12">Omzet Tepat Waktu</label>
							<div class="col-sm-4 col-xs-12">
								<input type="number" name="omzet_tepat_waktu" class="form-control" value="<?=$omzet_tepat_waktu;?>" min=0 required="required">
							</div>
                        </div>
						<div class="form-group">
							<label class="control-label col-sm-3 col-xs-12">Disc Pembelian</label>
							<div class="col-sm-2 col-xs-12">
								<input type="number" name="disc_pembelian" class="form-control" value="<?=$disc_pembelian;?>" min=0 required="required" step=0.00001>
							</div>
                        </div>
						<div class="form-group">
							<label class="control-label col-sm-3 col-xs-12">Disc Penjualan</label>
							<div class="col-sm-2 col-xs-12">
								<input type="number" name="disc_penjualan" class="form-control" value="<?=$disc_penjualan;?>" min=0 required="required" step=0.00001>
							</div>
                        </div>
						<div class="form-group">
							<label class="control-label col-sm-3 col-xs-12">Omzet Netto dengan Disc</label>
							<div class="col-sm-4 col-xs-12">
								<input type="number" name="omzet" class="form-control" value="<?=$omzet;?>" min=0 required="required">
							</div>
                        </div>
						<div class="form-group">
							<label class="control-label col-sm-3 col-xs-12">Poin </label>
							<div class="col-sm-3 col-xs-12">
								<input type="number" name="poin" class="form-control" value="<?=$poin;?>" min=0 required="required">
							</div>
                        </div>
						<div class="ln_solid"></div>
						<div class="form-group">
							<div class="col-sm-6 col-xs-12 col-sm-offset-6">
								<a href="<?=url('/backend/master-omzet')?>" class="btn btn-warning">&nbsp;&nbsp;&nbsp;&nbsp;Cancel&nbsp;&nbsp;&nbsp;&nbsp;</a>
								<button type="submit" class="btn btn-primary">&nbsp;&nbsp;&nbsp;&nbsp;Submit&nbsp;&nbsp;&nbsp;&nbsp;</button>
							</div>
						</div>
					{{ Form::close() }}
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
        $('.date').datetimepicker({
            format: 'DD-MM-YYYY'
        });
    </script>
@endsection