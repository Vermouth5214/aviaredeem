<?php
	$breadcrumb = [];
	$breadcrumb[0]['title'] = 'Dashboard';
	$breadcrumb[0]['url'] = url('backend/dashboard');
	$breadcrumb[1]['title'] = 'Master Campaign';
	$breadcrumb[1]['url'] = url('backend/campaign');	
	$breadcrumb[2]['title'] = 'Add';
	$breadcrumb[2]['url'] = url('backend/campaign/create');
	if (isset($data)){
		$breadcrumb[2]['title'] = 'Edit';
		$breadcrumb[2]['url'] = url('backend/campaign/'.$data[0]->id.'/edit');
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
    Master Campaign - <?=$mode;?>
@endsection

<!-- CONTENT -->
@section('content')
	<?php
        $kode_campaign = old('kode_campaign');
        $nama_campaign = old('nama_campaign');
        $jenis = "omzet";
        $TTP = 0;
        $brosur = "";
		$method = "POST";
		$mode = "Create";
		$url = "backend/campaign/";
		if (isset($data)){
            $kode_campaign = $data[0]->kode_campaign;
            $nama_campaign = $data[0]->nama_campaign;
            $jenis = $data[0]->jenis;
            $TTP = $data[0]->TTP;
            $brosur = $data[0]->brosur;
			$method = "PUT";
			$mode = "Edit";
			$url = "backend/campaign/".$data[0]->id;
		}
	?>
	<div class="page-title">
		<div class="title_left">
			<h3>Master Campaign - <?=$mode;?></h3>
		</div>
		<div class="title_right">
			<div class="col-md-4 col-sm-4 col-xs-8 form-group pull-right top_search">
                @include('backend.elements.back_button',array('url' => '/backend/campaign'))
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
                <div class="x_title">
                    <h2>Form Campaign - Header</h2>
                    <div class="clearfix"></div>
                </div>
				<div class="x_content">
					{{ Form::open(['url' => $url, 'method' => $method,'class' => 'form-horizontal form-label-left', 'files' => true]) }}
						{!! csrf_field() !!}
						<div class="form-group">
							<label class="control-label col-sm-3 col-xs-12"> <span class="required">Kode Campaign * :</span></label>
							<div class="col-sm-3 col-xs-12">
								<input type="text" name="kode_campaign" required="required" class="form-control" value="<?=$kode_campaign;?>" autofocus>
							</div>
                        </div>
						<div class="form-group">
							<label class="control-label col-sm-3 col-xs-12"> <span class="required">Nama Campaign * :</span></label>
							<div class="col-sm-6 col-xs-12">
								<input type="text" name="nama_campaign" required="required" class="form-control" value="<?=$nama_campaign;?>">
							</div>
                        </div>
						<div class="form-group">
							<label class="control-label col-sm-3 col-xs-12">Jenis : </label>
							<div class="col-sm-2 col-xs-12">
								{{
								Form::select(
									'jenis',
									['omzet' => 'Omzet', 'poin' => 'Poin'],
									$jenis,
									array(
										'class' => 'form-control',
									))
								}}								
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3 col-xs-12"> <span class="required">TTP : </span></label>
							<div class="col-sm-2 col-xs-12">
                                <div class="checkbox">
                                    <label>
                                        <?php
                                            $checked = "";
                                            if ($TTP == 1){
                                                $checked = "checked";
                                            }
                                        ?>
                                        <input type="checkbox" name="TTP" value=1 <?=$checked;?>>
                                    </label>
                                </div>
							</div>
                        </div>
						<div class="form-group">
							<label class="control-label col-sm-3 col-xs-12">Brosur : </label>
							<div class="col-sm-5 col-xs-12">
                                <input type="file" name="brosur" class="form-control" capture="filesystem" accept="image/jpg, image/jpeg">
                                <b><p class="small blue">
                                    Ekstensi file .jpg / .jpeg
                                </p></b>
                                <?php
                                    if ($brosur != ""):
                                ?>
                                    <a style="font-size:17px;font-weight:bold;" href="<?=url('upload/Brosur/'.$brosur);?>" target='_blank'><i class="fa fa-paperclip" style="font-style:italic"> <?=$brosur;?></i></a>
                                <?php
                                    endif;
                                ?>
							</div>
                        </div>
                        <br/><br/>
                        <div class="x_title">
                            <h2>List Hadiah</h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 c "
                        </div>


						<div class="form-group">
							<div class="col-sm-6 col-xs-12 col-sm-offset-3">
								<a href="<?=url('/backend/campaign')?>" class="btn btn-warning">Cancel</a>
								<button type="submit" class="btn btn-primary">Submit </button>
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