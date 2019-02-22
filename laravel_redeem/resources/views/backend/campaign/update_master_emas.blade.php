<?php
	$breadcrumb = [];
	$breadcrumb[0]['title'] = 'Dashboard';
	$breadcrumb[0]['url'] = url('backend/dashboard');
	$breadcrumb[1]['title'] = 'Master Emas';
	$breadcrumb[1]['url'] = url('backend/campaign/edit-master-emas');
?>

<!-- LAYOUT -->
@extends('backend.layouts.main')

<!-- TITLE -->
@section('title', 'Master Emas')

<!-- CONTENT -->
@section('content')
	<div class="page-title">
		<div class="title_left">
			<h3>Master Emas</h3>
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
                    <div class="x_title">
                        <h2>Kode Campaign : <b><i><?=$data_header[0]->kode_campaign;?></i></b></h2>
                        <div class="clearfix"></div>
                        <h2>Nama Campaign : <b><i><?=$data_header[0]->nama_campaign;?></i></b></h2>
                        <div class="clearfix"></div>
                        <br/>
                        <p class="small blue">
                            - Kolom jumlah diisi sesuai dengan jumlah hadiah : Misal : Nama Hadiah = 100 Gram Emas , Jumlah = 100
                        </p>
                    </div>
                    <?php
                        $url = "backend/campaign/".$data_header[0]->id."/edit-master-emas";
                    ?>
                    {{ Form::open(['url' => $url, 'method' => 'POST','class' => 'form-horizontal form-label-left']) }}
                        {!! csrf_field() !!}
                        <div class="row">
                            <div class="col-xs-12 col-sm-2 text-center">
                                <b>Kode Catalogue</b>
                            </div>
                            <div class="col-xs-12 col-sm-2 text-center">
                                <b>Kode Barang</b>
                            </div>
                            <div class="col-xs-12 col-sm-3 text-center">
                                <b>Nama Hadiah</b>
                            </div>
                            <div class="col-xs-12 col-sm-1 text-center">
                                <b>Jumlah</b>
                            </div>
                            <div class="col-xs-12 col-sm-2 text-center">
                                <b>Harga / Poin</b>
                            </div>
                            <div class="col-xs-12 col-sm-1 text-center">
                                <b>Satuan</b>
                            </div>
                            <div class="col-xs-12 col-sm-1 text-center">
                                <b>Action</b>
                            </div>
                        </div>
                        <br/>
                        <div class="field_wrapper">
                            <?php
                                if ($data->count()){
                                    $i = 1;
                                    foreach ($data as $key => $value):
                            ?>
                            <div class="row" style="margin-bottom:10px;">
                                <div class="col-xs-12 col-sm-2">
                                    <input type="text" class="form-control" name="kode_catalogue[]" placeholder="Kode Catalogue" required="required" value="<?=$value->kode_catalogue;?>">
                                </div>
                                <div class="col-xs-12 col-sm-2">
                                    <input type="text" class="form-control" name="kode_hadiah[]" placeholder="Kode Barang" required="required" value="<?=$value->kode_hadiah;?>">
                                </div>
                                <div class="col-xs-12 col-sm-3">
                                    <input type="text" class="form-control" name="nama_hadiah[]" placeholder="Nama Hadiah" required="required" value="<?=$value->nama_hadiah;?>">
                                </div>
                                <div class="col-xs-12 col-sm-1">
                                    <input type="number" class="form-control" name="jumlah[]" placeholder=1 min=1 required="required" value="<?=$value->jumlah;?>">
                                </div>
                                <div class="col-xs-12 col-sm-2">
                                    <input type="number" class="form-control" name="harga[]" placeholder=1 min=1 required="required" value="<?=$value->harga;?>">
                                </div>
                                <div class="col-xs-12 col-sm-1">
                                    <input type="text" class="form-control" name="satuan[]" placeholder="Satuan" required="required" value="<?=$value->satuan;?>">
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
                                    <div class="col-xs-12 col-sm-3">
                                        <input type="text" class="form-control" name="nama_hadiah[]" placeholder="Nama Hadiah" required="required">
                                    </div>
                                    <div class="col-xs-12 col-sm-1">
                                        <input type="number" class="form-control" name="jumlah[]" placeholder=1 min=1 required="required">
                                    </div>
                                    <div class="col-xs-12 col-sm-2">
                                        <input type="number" class="form-control" name="harga[]" placeholder=1 min=1 required="required">
                                    </div>
                                    <div class="col-xs-12 col-sm-1">
                                        <input type="text" class="form-control" name="satuan[]" placeholder="Satuan" required="required">
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
							<div class="col-sm-6 col-xs-12 col-sm-offset-6 text-right">
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
            $(wrapper).append('<div class="row" style="margin-bottom:10px;"><div class="col-xs-12 col-sm-2"><input type="text" class="form-control" name="kode_catalogue[]" placeholder="Kode Catalogue" required="required"></div><div class="col-xs-12 col-sm-2"><input type="text" class="form-control" name="kode_hadiah[]" placeholder="Kode Barang" required="required"></div><div class="col-xs-12 col-sm-3"><input type="text" class="form-control" name="nama_hadiah[]" placeholder="Nama Hadiah" required="required"></div><div class="col-xs-12 col-sm-1"><input type="number" class="form-control" name="jumlah[]" placeholder=1 min=1 required="required"></div><div class="col-xs-12 col-sm-2"><input type="number" class="form-control" name="harga[]" placeholder=1 min=1 required="required"></div><div class="col-xs-12 col-sm-1"><input type="text" class="form-control" name="satuan[]" placeholder="Satuan" required="required"></div><div class="col-xs-12 col-sm-1"><a href="javascript:void(0);" class="remove_button btn btn-danger btn-block" title="Hapus Baris">-</a></div>'); 
        });
        $(wrapper).on('click', '.remove_button', function(e){ 
            if (confirm("Apakah anda yakin mau menghapus baris ini?")) {
                e.preventDefault();
                $(this).parent().parent().remove(); 
            }
        });
    });
</script>

@endsection