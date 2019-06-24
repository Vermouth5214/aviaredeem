<?php
	$breadcrumb = [];
	$breadcrumb[0]['title'] = 'Dashboard';
	$breadcrumb[0]['url'] = url('backend/dashboard');
	$breadcrumb[1]['title'] = 'Last TTO / TTP Number';
	$breadcrumb[1]['url'] = url('backend/last-tto');
?>

<!-- LAYOUT -->
@extends('backend.layouts.main')

<!-- TITLE -->
@section('title', 'Last TTO / TTP Number')

<!-- CONTENT -->
@section('content')
    <div class="page-title">
        <div class="title_left">
            <h3>Last TTO / TTP Number</h3>
        </div>
        <div class="title_right">
            <div class="col-md-4 col-sm-4 col-xs-4 form-group pull-right top_search">
                <a href="<?=url('/backend/last-tto/create');?>" class="btn-index btn btn-primary btn-block" title="Add"><i class="fa fa-plus"></i>&nbsp; Add</a>
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
                                <th>Id</th>
                                <th>No TTO / TTP</th>                                
								<th>Actions</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>					
    </div>
    {{ Form::close() }}
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
			ajax: "<?=url('backend/last-tto/datatable');?>",
			columns: [
                {data: 'id', name: 'id'},
                {data: 'no_tto', name: 'no_tto'},
				{data: 'action', name: 'action', orderable: false, searchable: false},
			],
            order: [[ 0, "desc" ]],
			responsive: false
		});

    </script>
@endsection
