<?php
	$breadcrumb = [];
	$breadcrumb[0]['title'] = 'Dashboard';
	$breadcrumb[0]['url'] = url('backend/dashboard');
	$breadcrumb[1]['title'] = 'Redeem Hadiah';
	$breadcrumb[1]['url'] = url('backend/redeem-hadiah');
?>

<!-- LAYOUT -->
@extends('backend.layouts.main')

<!-- TITLE -->
@section('title', 'Redeem Hadiah')

<!-- CONTENT -->
@section('content')
	<div class="page-title">
		<div class="title_left">
			<h3>Redeem Hadiah</h3>
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
                    <table class="table table-striped table-hover table-bordered dt-responsive nowrap dataTable" cellspacing="0" width="100%">
						<thead>
							<tr>
                                <th>Status</th>                                
								<th>Kode</th>
                                <th>Nama</th>
                                <th>Jenis</th>
                                <th>Tgl Klaim Awal</th>
                                <th>Tgl Klaim Akhir</th>
                                <th>Brosur</th>
                                <th>Omzet</th>
                                <th>Poin</th>
								<th>Actions</th>
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
			ajax: "<?=url('backend/redeem-hadiah/datatable');?>",
			columns: [
				{data:  'status', render: function ( data, type, row ) {
					var text = "";
					var label = "";
					if (data == 1){
						text = "Claimed";
						label = "success";
					} else 
                    if (data == 2){    
						text = "Expired";
						label = "error";
					} else 
                    if (data == 3){
                        text = "Not Yet Claimed";
                        label = "info";
                    }
					return "<span class='badge badge-" + label + "'>"+ text + "</span>";
                }},
				{data: 'kode_campaign', name: 'kode_campaign'},
                {data: 'nama_campaign', name: 'nama_campaign'},
                {data: 'jenis', name: 'jenis'},
                {data: 'periode_awal', name: 'periode_awal'},
                {data: 'periode_akhir', name: 'periode_akhir'},
                {data: 'brosur', name: 'brosur'},
                {data: 'omzet_netto', name: 'omzet_netto'},
                {data: 'poin', name: 'poin'},
				{data: 'action', name: 'action', orderable: false, searchable: false}
			],
            order: [[ 0, "desc" ]],
			responsive: false
		});
	</script>
@endsection