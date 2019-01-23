<?php
	$breadcrumb = [];
	$breadcrumb[0]['title'] = 'Dashboard';
	$breadcrumb[0]['url'] = url('backend/dashboard');
	$breadcrumb[1]['title'] = 'Master Customer Omzet';
    $breadcrumb[1]['url'] = url('backend/master-omzet');
	$breadcrumb[2]['title'] = 'Upload';
	$breadcrumb[2]['url'] = url('backend/master-omzet/upload');
    
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
			<div class="col-md-4 col-sm-4 col-xs-8 form-group pull-right top_search">
                @include('backend.elements.back_button',array('url' => '/backend/master-omzet'))
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
                    {{ Form::open(['url' => 'backend/master-omzet/upload', 'method' => 'POST','class' => 'form-horizontal', 'files' => true]) }}
                    <div class="form-group">
                        <label class="control-label col-sm-3 col-xs-12">Upload file : <span class="required">*</span><br/>
                            <p class="small blue">Isi dari file excel harus tanpa header</p>
                            <p class="small blue">Urutan : Kode Campaign, Kode Customer, Periode Awal, Periode Akhir, Omzet, Poin</p>
                        </label>
                        <div class="col-sm-5 col-xs-12">
                            <input type="file" name="upload_file" required="required" class="form-control" autofocus>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-12 col-sm-3 col-sm-offset-5">
                            <button type="submit" class="btn btn-primary btn-block">Upload </button>
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