<?php
	$breadcrumb = [];
	$breadcrumb[0]['title'] = 'Dashboard';
	$breadcrumb[0]['url'] = url('backend/dashboard');
	$breadcrumb[1]['title'] = 'Report Redeem per Campaign';
	$breadcrumb[1]['url'] = url('backend/report-redeem');
?>

<!-- LAYOUT -->
@extends('backend.layouts.main')

<!-- TITLE -->
@section('title', 'Report Redeem per Campaign')

<!-- CONTENT -->
@section('content')
	<div class="page-title">
		<div class="title_left">
			<h3>Report Redeem per Campaign</h3>
		</div>
		<div class="title_right">
			<div class="col-md-4 col-sm-4 col-xs-8 form-group pull-right top_search">
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
                    <form id="form-work" class="form-horizontal" role="form" autocomplete="off" method="GET">
                        <div class="row">
                            <div class="col-xs-12 col-sm-2 text-right" style="margin-top:7px;">
                                Kode Campaign
                            </div>
                            <div class="col-xs-12 col-sm-5">
								{{
								Form::select(
									'kode_campaign',
									$campaign,
									$kode_campaign,
									array(
                                        'class' => 'form-control',
                                        'id' => 'campaign'
									))
								}}
                            </div>
                            <div class="col-xs-12 col-sm-2">
                                <input type="submit" class="btn btn-primary btn-block" name="submit" value="Submit">
                            </div>
                            <div class="col-xs-12 col-sm-2">
                                <?php
                                    $userinfo = Session::get('userinfo');
                                    if (($userinfo['uname'] != "mkt01") && ($userinfo['priv'] != "ADMIN")):
                                ?>
                                <input type="submit" class="btn btn-success btn-block" id="btn-export" name="export" value="Generate XLS">
                                <?php
                                    endif;
                                ?>
                            </div>
                        </div>
                    </form>
                    <br/>
                    <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered dt-responsive nowrap dataTable" cellspacing="0" width="100%">
						<thead>
							<tr>
                                <th>Kode Campaign</th>
                                <th>Kode Customer</th>
                                <th>Cabang</th>
                                <th>Omzet / Poin</th>
                                <th>Kode Hadiah</th>
                                <th>Nama Hadiah</th>
                                <th>Jumlah (buah)</th>
							</tr>
						</thead>
                        <tbody>
                            <?php
                                foreach ($data as $detail):
                            ?>
                                <tr>
                                    <td><?=$detail->kode_campaign;?></td>
                                    <td><?=$detail->kode_customer;?></td>
                                    <td><?=$detail->cabang;?></td>
                                    <td class="text-right">
                                        <?php
                                            $omzet = $detail->poin;
                                            if ($detail->omzet_netto > 0){
                                                $omzet = $detail->omzet_netto;
                                            }
                                            echo number_format($omzet,0,',','.');
                                        ?>
                                    </td>
                                    <td><?=$detail->kode_hadiah;?></td>
                                    <td><?=$detail->nama_hadiah;?></td>
                                    <td class="text-right">
                                        <?php
                                            $total = $detail->jumlah_paket;
                                            if ($detail->emas == 0){
                                                $total = $detail->jumlah_total;
                                            }
                                            echo number_format($total,0,',','.');
                                        ?>
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
@endsection

<!-- CSS -->
@section('css')
    <!-- select2 -->
    <link href="<?=url('vendors/select2/dist/css/select2.min.css');?>" rel="stylesheet">
@endsection

<!-- JAVASCRIPT -->
@section('script')
    <!-- select2 -->
    <script src="<?=url('vendors/select2/dist/js/select2.min.js');?>"></script>
    <script>
        $('#campaign').select2();

        $('#btn-export').on('click', function(e){
            var campaign = $('#campaign').val();
            if (campaign == 999){
                alert('Harap memilih Campaign terlebih dahulu');
                return false;
            }
            return true;
        });
	</script>
@endsection