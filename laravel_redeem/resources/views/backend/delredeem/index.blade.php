<?php
	$breadcrumb = [];
	$breadcrumb[0]['title'] = 'Dashboard';
	$breadcrumb[0]['url'] = url('backend/dashboard');
	$breadcrumb[1]['title'] = 'Delete Redeem';
    $breadcrumb[1]['url'] = url('backend/delete-redeem');
    
    $url = url('backend/delete-redeem');
    $method = 'POST';
?>

<!-- LAYOUT -->
@extends('backend.layouts.main')

<!-- TITLE -->
@section('title')
    Delete Redeem
@endsection

<!-- CONTENT -->
@section('content')
	<div class="page-title">
		<div class="title_left">
			<h3>Delete Redeem</h3>
		</div>
		<div class="title_right">
			<div class="col-md-4 col-sm-4 col-xs-8 form-group pull-right top_search">

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
                    @include('backend.elements.notification')
					{{ Form::open(['url' => $url, 'method' => $method,'class' => 'form-horizontal form-label-left']) }}
                        {!! csrf_field() !!}
                        <div class="row">
                            <div class="col-xs-12 col-sm-2 text-right" style="margin-top:7px;">
                                Kode Campaign
                            </div>
                            <div class="col-xs-12 col-sm-3">
								{{
								Form::select(
									'kode_campaign',
                                    $campaign,
                                    0,
									array(
                                        'class' => 'form-control',
                                        'id' => 'campaign'
									))
								}}
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-xs-12 col-sm-2 text-right" style="margin-top:7px;">
                                Agen
                            </div>
                            <div class="col-xs-12 col-sm-5">
								{{
								Form::select(
									'agen',
                                    $list_agen,
                                    0,
									array(
                                        'class' => 'form-control',
                                        'id' => 'list_agen'
									))
								}}
                            </div>
                        </div>
    					<div class="ln_solid"></div>
						<div class="form-group">
							<div class="col-sm-6 col-xs-12 col-sm-offset-2">
								<button type="submit" class="btn btn-primary btn-submit">Submit </button>
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
    <!-- select2 -->
    <link href="<?=url('vendors/select2/dist/css/select2.min.css');?>" rel="stylesheet">
@endsection

<!-- JAVASCRIPT -->
@section('script')
    <script src="<?=url('vendors/select2/dist/js/select2.min.js');?>"></script>
    <script>
        $('#campaign').select2();  
        $('#list_agen').select2();  
        $('.btn-submit').on('click', function(){
            if (confirm("Apakah anda yakin mau menghapus data ini?")) {
                return true;
    		}
	    	return false;
        });
    </script>
@endsection