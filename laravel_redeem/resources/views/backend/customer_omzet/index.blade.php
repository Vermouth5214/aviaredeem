<?php
	$breadcrumb = [];
	$breadcrumb[0]['title'] = 'Dashboard';
	$breadcrumb[0]['url'] = url('backend/dashboard');
	$breadcrumb[1]['title'] = 'Master Customer Omzet';
	$breadcrumb[1]['url'] = url('backend/master-omzet');
?>

<!-- LAYOUT -->
@extends('backend.layouts.main')

<!-- TITLE -->
@section('title', 'Master Customer Omzet')

<!-- CONTENT -->
@section('content')
	<div class="page-title">
		<div class="title_left">
			<h3>Master Customer Omzet</h3>
		</div>
		<div class="title_right">
			<div class="col-md-4 col-sm-4 col-xs-6 form-group pull-right top_search">
                <a href="<?=url('/backend/master-omzet/create');?>" class="btn-index btn btn-primary btn-block" title="Add"><i class="fa fa-plus"></i>&nbsp; Add</a>
            </div>
			<div class="col-md-4 col-sm-4 col-xs-6 form-group pull-right top_search">
                <a href="<?=url('/backend/master-omzet/upload');?>" class="btn-index btn btn-success btn-block" title="Add"><i class="fa fa-upload"></i>&nbsp; Upload</a>
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
                    @if (Session::has('error'))
                        <div class="row">
                            <div class="col-xs-12 alert alert-danger alert-dismissible" role="alert">
                                <?php
                                    foreach (Session::get('error') as $error):
                                        echo $error."<br/>";
                                    endforeach;
                                ?>
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            </div>
                        </div>
                    @endif
                    <table class="table table-striped table-hover table-bordered dt-responsive nowrap dataTable" cellspacing="0" width="100%">
						<thead>
							<tr>
								<th>Actions</th>                                
                                <th>Kode Campaign</th>
                                <th>Kode Customer</th>
                                <th>Periode Awal</th>
                                <th>Periode Akhir</th>
                                <th>Omzet Tepat Waktu</th>
                                <th>Disc Pembelian</th>
                                <th>Disc Penjualan</th>
                                <th>Omzet Netto</th>
                                <th>Poin</th>
							</tr>
						</thead>
					</table>
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
		$('.dataTable').dataTable({
			processing: true,
			serverSide: true,
			ajax: "<?=url('backend/master-omzet/datatable');?>",
			columns: [
				{data: 'action', name: 'action', orderable: false, searchable: false},
				{data: 'kode_campaign', name: 'kode_campaign'},
                {data: 'kode_customer', name: 'kode_customer'},
                {data: 'periode_awal', name: 'periode_awal'},
                {data: 'periode_akhir', name: 'periode_akhir'},
                {data: 'omzet_tepat_waktu', name: 'omzet_tepat_waktu'},
                {data: 'disc_pembelian', name: 'disc_pembelian'},
                {data: 'disc_penjualan', name: 'disc_penjualan'},
                {data: 'omzet_netto', name: 'omzet_netto'},
                {data: 'poin', name: 'poin'}
			],
            order: [[ 0, "desc" ]],
			responsive: false
		});
	</script>
@endsection
