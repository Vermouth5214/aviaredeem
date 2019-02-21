<?php
	$breadcrumb = [];
	$breadcrumb[0]['title'] = 'Dashboard';
	$breadcrumb[0]['url'] = url('backend/dashboard');
	$breadcrumb[1]['title'] = 'Master Campaign';
	$breadcrumb[1]['url'] = url('backend/campaign');
?>

<!-- LAYOUT -->
@extends('backend.layouts.main')

<!-- TITLE -->
@section('title', 'Master Campaign')

<!-- CONTENT -->
@section('content')
	<div class="page-title">
		<div class="title_left">
			<h3>Master Campaign</h3>
		</div>
		<div class="title_right">
			<div class="col-md-4 col-sm-4 col-xs-8 form-group pull-right top_search">
                <a href="<?=url('/backend/campaign/create');?>" class="btn-index btn btn-primary btn-block" title="Add"><i class="fa fa-plus"></i>&nbsp; Add</a>
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
                                <th>ID</th>
								<th>Kode</th>
                                <th>Nama</th>
                                <th>Jenis</th>
                                <th>Brosur</th>
								<th>Status</th>
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
            "lengthMenu": [[35, 75, 100], [35, 75, 100]],
            "pageLength": 35,
			ajax: "<?=url('backend/campaign/datatable');?>",
			columns: [
				{data: 'id', name: 'id'},
				{data: 'kode_campaign', name: 'kode_campaign'},
                {data: 'nama_campaign', name: 'nama_campaign'},
                {data: 'jenis', name: 'jenis'},
                {data: 'brosur', name: 'brosur'},
				{data:  'active', render: function ( data, type, row ) {
					var text = "";
					var label = "";
					if (data == 1){
						text = "Active";
						label = "success";
					} else 
                    if (data == 5){    
						text = "Need Approval";
						label = "error";
					} else {
						text = "Not Complete";
						label = "warning";
                    }
					return "<span class='badge badge-" + label + "'>"+ text + "</span>";
                }},				
				{data: 'action', name: 'action', orderable: false, searchable: false}
			],
            order: [[ 0, "desc" ]],
			responsive: true
		});
	</script>
@endsection