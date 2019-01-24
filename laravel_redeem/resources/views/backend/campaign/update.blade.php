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
        $TPP = 0;
        $brosur = "";
		$method = "POST";
		$mode = "Create";
		$url = "backend/campaign/";
		if (isset($data)){
            $kode_campaign = $data[0]->kode_campaign;
            $nama_campaign = $data[0]->nama_campaign;
            $jenis = $data[0]->jenis;
            $TPP = $data[0]->TPP;
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
                    @include('backend.elements.notification');
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
                                            if ($TPP == 1){
                                                $checked = "checked";
                                            }
                                        ?>
                                        <input type="checkbox" name="TPP" value=1 <?=$checked;?>>
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
                            <p class="small blue">
                                - Untuk hadiah emas, Kode Catalogue dan Kode Barang boleh diisi apa saja (disarankan - agar rapi)<br/>
                                - Kolom jumlah diisi sesuai dengan jumlah hadiah : Misal : Nama Hadiah = 2 Gram Emas , Jumlah = 2<br/>
                                - Hadiah emas harap memberi centang pada kolom emas<br/>
                                - Kolom pilihan harap dicentang untuk hadiah-hadiah yang berbeda di tiap agennya (misal : voucher / minyak) <br/>
                                - Jika hadiah sama untuk semua agen kolom pilihan tidak perlu dicentang
                            </p>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-2 text-center">
                                <b>Kode Catalogue</b>
                            </div>
                            <div class="col-xs-12 col-sm-2 text-center">
                                <b>Kode Barang</b>
                            </div>
                            <div class="col-xs-12 col-sm-2 text-center">
                                <b>Nama Hadiah</b>
                            </div>
                            <div class="col-xs-12 col-sm-1 text-center">
                                <b>Jumlah</b>
                            </div>
                            <div class="col-xs-12 col-sm-2 text-center">
                                <b>Harga</b>
                            </div>
                            <div class="col-xs-12 col-sm-1 text-center">
                                <b>Pilihan</b>
                            </div>
                            <div class="col-xs-12 col-sm-1 text-center">
                                <b>Emas</b>
                            </div>
                            <div class="col-xs-12 col-sm-1 text-center">
                                <b>Action</b>
                            </div>
                        </div>
                        <br/>
                        <div class="field_wrapper">
                            <?php
                                if (isset($data)){
                                    $i = 1;
                                    foreach ($detail as $key => $value):
                            ?>
                            <div class="row" style="margin-bottom:10px;">
                                <div class="col-xs-12 col-sm-2">
                                    <input type="text" class="form-control" name="kode_catalogue[]" placeholder="Kode Catalogue" required="required" value="<?=$value->kode_catalogue;?>">
                                </div>
                                <div class="col-xs-12 col-sm-2">
                                    <input type="text" class="form-control" name="kode_hadiah[]" placeholder="Kode Barang" required="required" value="<?=$value->kode_hadiah;?>">
                                </div>
                                <div class="col-xs-12 col-sm-2">
                                    <input type="text" class="form-control" name="nama_hadiah[]" placeholder="Nama Hadiah" required="required" value="<?=$value->nama_hadiah;?>">
                                </div>
                                <div class="col-xs-12 col-sm-1">
                                    <input type="number" class="form-control" name="jumlah[]" placeholder=1 min=1 required="required" value="<?=$value->jumlah;?>">
                                </div>
                                <div class="col-xs-12 col-sm-2">
                                    <input type="number" class="form-control" name="harga[]" placeholder=1 min=1 required="required" value="<?=$value->harga;?>">
                                </div>
                                <div class="col-xs-12 col-sm-1 text-center">
                                    <?php
                                        $checked = "";
                                        if ($value->pilihan == 1){
                                            $checked = "checked";
                                        }
                                    ?>
                                    <input type="checkbox" name="pilihan[]" <?=$checked;?>>
                                </div>
                                <div class="col-xs-12 col-sm-1 text-center">
                                    <?php
                                        $checked = "";
                                        if ($value->emas == 1){
                                            $checked = "checked";
                                        }
                                    ?>
                                    <input type="checkbox" name="emas[]" <?=$checked;?>>
                                </div>
                                <div class="col-xs-12 col-sm-1">
                                    <?php
                                        if ($i == 1){
                                    ?>
                                    <a href="javascript:void(0);" class="add_button btn btn-primary btn-block" title="Tambah Baris">+</a>       
                                    <?php
                                        } else {
                                    ?>
                                    <a href="javascript:void(0);" class="remove_button btn btn-danger btn-block" title="Hapus Baris">-</a>
                                    <?php
                                        }
                                    ?>

                                </div>
                            </div>
                            <?php
                                    $i++;
                                    endforeach;
                                } else {
                            ?>
                            <div class="row" style="margin-bottom:10px;">
                                <div class="col-xs-12 col-sm-2">
                                    <input type="text" class="form-control" name="kode_catalogue[]" placeholder="Kode Catalogue" required="required">
                                </div>
                                <div class="col-xs-12 col-sm-2">
                                    <input type="text" class="form-control" name="kode_hadiah[]" placeholder="Kode Barang" required="required">
                                </div>
                                <div class="col-xs-12 col-sm-2">
                                    <input type="text" class="form-control" name="nama_hadiah[]" placeholder="Nama Hadiah" required="required">
                                </div>
                                <div class="col-xs-12 col-sm-1">
                                    <input type="number" class="form-control" name="jumlah[]" placeholder=1 min=1 required="required">
                                </div>
                                <div class="col-xs-12 col-sm-2">
                                    <input type="number" class="form-control" name="harga[]" placeholder=1 min=1 required="required">
                                </div>
                                <div class="col-xs-12 col-sm-1 text-center">
                                    <input type="checkbox" name="pilihan[]">
                                </div>
                                <div class="col-xs-12 col-sm-1 text-center">
                                    <input type="checkbox" name="emas[]">
                                </div>
                                <div class="col-xs-12 col-sm-1">
                                    <a href="javascript:void(0);" class="add_button btn btn-primary btn-block" title="Tambah Baris">+</a>
                                </div>
                            </div>
                            <?php
                                }
                            ?>
                        </div>
                        <br/>
						<div class="form-group">
							<div class="col-sm-6 col-xs-12 col-sm-offset-6">
								<a href="<?=url('/backend/campaign')?>" class="btn btn-warning">&nbsp;&nbsp;&nbsp;&nbsp;Cancel&nbsp;&nbsp;&nbsp;&nbsp;</a>
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
    <script type="text/javascript">
        $(document).ready(function(){
            var addButton = $('.add_button');
            var wrapper = $('.field_wrapper');
            $(addButton).click(function(){ //Once add button is clicked
                $(wrapper).append('<div class="row" style="margin-bottom:10px;"><div class="col-xs-12 col-sm-2"><input type="text" class="form-control" name="kode_catalogue[]" placeholder="Kode Catalogue" required="required"></div><div class="col-xs-12 col-sm-2"><input type="text" class="form-control" name="kode_hadiah[]" placeholder="Kode Barang" required="required"></div><div class="col-xs-12 col-sm-2"><input type="text" class="form-control" name="nama_hadiah[]" placeholder="Nama Hadiah" required="required"></div><div class="col-xs-12 col-sm-1"><input type="number" class="form-control" name="jumlah[]" placeholder=1 min=1 required="required"></div><div class="col-xs-12 col-sm-2"><input type="number" class="form-control" name="harga[]" placeholder=1 min=1 required="required"></div><div class="col-xs-12 col-sm-1 text-center"><input type="checkbox" name="pilihan[]"></div><div class="col-xs-12 col-sm-1 text-center"><input type="checkbox" name="emas[]"></div><div class="col-xs-12 col-sm-1"><a href="javascript:void(0);" class="remove_button btn btn-danger btn-block" title="Hapus Baris">-</a></div>'); 
            });
            $(wrapper).on('click', '.remove_button', function(e){ 
                if (confirm("Apakah anda yakin mau menghapus baris ini?")) {
                    e.preventDefault();
                    $(this).parent().parent().remove(); 
                }
            });
            $("form").submit(function () {

                var this_master = $(this);

                this_master.find('input[type="checkbox"]').each( function () {
                    var checkbox_this = $(this);

                    if( checkbox_this.is(":checked") == true ) {
                        checkbox_this.attr('value','1');
                    } else {
                        checkbox_this.prop('checked',true);
                        //DONT' ITS JUST CHECK THE CHECKBOX TO SUBMIT FORM DATA    
                        checkbox_this.attr('value','0');
                    }
                })
            })            
        });

    </script>
@endsection