<?php
	$breadcrumb = [];
	$breadcrumb[0]['title'] = 'Dashboard';
	$breadcrumb[0]['url'] = url('backend/dashboard');
	$breadcrumb[1]['title'] = 'Last TTO / TTP Number';
	$breadcrumb[1]['url'] = url('backend/last-tto');	
	$breadcrumb[2]['title'] = 'Add';
	$breadcrumb[2]['url'] = url('backend/last-tto/create');
	if (isset($data)){
		$breadcrumb[2]['title'] = 'Edit';
		$breadcrumb[2]['url'] = url('backend/last-tto/'.$data[0]->id.'/edit');
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
    Last TTO / TTP Number - <?=$mode;?>
@endsection

<!-- CONTENT -->
@section('content')
	<?php
        $no_tto = old('kode_campaign');
		$method = "POST";
		$mode = "Create";
		$url = "backend/last-tto/";
		if (isset($data)){
            $no_tto = $data[0]->no_tto;
			$method = "PUT";
			$mode = "Edit";
			$url = "backend/last-tto/".$data[0]->id;
		}
	?>
	<div class="page-title">
		<div class="title_left">
			<h3>Last TTO / TTP Number - <?=$mode;?></h3>
		</div>
		<div class="title_right">
			<div class="col-md-4 col-sm-4 col-xs-8 form-group pull-right top_search">
                @include('backend.elements.back_button',array('url' => '/backend/last-tto'))
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
						<div class="form-group">
							<label class="control-label col-sm-3 col-xs-12">No TTO / TTP <span class="required">*</span></label>
							<div class="col-sm-5 col-xs-12">
								<input type="text" name="no_tto" required="required" class="form-control" value="<?=$no_tto;?>" autofocus placeholder = 'Contoh : TTO-IPP-1906-20010'>
							</div>
                        </div>
						<div class="ln_solid"></div>
						<div class="form-group">
							<div class="col-sm-6 col-xs-12 col-sm-offset-6">
								<a href="<?=url('/backend/last-tto')?>" class="btn btn-warning">&nbsp;&nbsp;&nbsp;&nbsp;Cancel&nbsp;&nbsp;&nbsp;&nbsp;</a>
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

@endsection