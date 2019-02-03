<?php
	$breadcrumb = [];
	$breadcrumb[0]['title'] = 'Dashboard';
	$breadcrumb[0]['url'] = url('backend/dashboard');
	$breadcrumb[1]['title'] = 'Pembagian List Hadiah Pilihan';
	$breadcrumb[1]['url'] = url('backend/campaign/edit-pembagian-hadiah');
?>

<!-- LAYOUT -->
@extends('backend.layouts.main')

<!-- TITLE -->
@section('title', 'Pembagian List Hadiah Pilihan')

<!-- CONTENT -->
@section('content')
	<div class="page-title">
		<div class="title_left">
			<h3>Pembagian List Hadiah Pilihan</h3>
		</div>
		<div class="title_right">
			<div class="col-md-4 col-sm-4 col-xs-8 form-group pull-right top_search">
                @include('backend.elements.back_button',array('url' => '/backend/campaign'))
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
                    </div>
                    <?php
                        $url = "backend/campaign/".$data_header[0]->id."/edit-pembagian-hadiah";
                    ?>
                    {{ Form::open(['url' => $url, 'method' => 'POST','class' => 'form-horizontal form-label-left']) }}
                        {!! csrf_field() !!}
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th align="center">Agen</th>
                                    <th align="center">Cabang</th>
                                    <?php
                                        foreach ($list_hadiah_pilihan as $item):
                                    ?>
                                        <th align="center">
                                            <input class="check_class" type="checkbox" id="check_all_<?=$item->id;?>">
                                            <?=$item->nama_hadiah;?>
                                        </th>
                                    <?php
                                        endforeach;
                                    ?>
                                </tr>
                            </thead>
                            <?php
                                //jika mode update
                                if ($data->count()){
                                    foreach ($data as $agen):
                            ?>
                                    <tr>
                                        <td><?=$agen->kode_agen;?></td>
                                        <td><?=$agen->agen->cabang;?></td>
                                        <?php
                                            foreach ($list_hadiah_pilihan as $item):
                                                $checked = "";
                                                if ($item->id == $agen->id_campaign_d_hadiah){
                                                    $checked = "checked";
                                                }
                                        ?>
                                                <td class="text-center">
                                                    <input class="radio_<?=$item->id;?>" required="required" type="radio" name="hadiah_<?=$agen->kode_agen;?>" value="<?=$item->id;?>" <?=$checked;?>>
                                                </td>
                                        <?php
                                            endforeach;
                                        ?>
                                    </tr>
                            <?php
                                    endforeach;
                                } else {
                                    //jika mode insert
                                    foreach ($list_agen as $agen):
                            ?>
                                    <tr>
                                        <td><?=$agen->reldag;?></td>
                                        <td><?=$agen->cabang;?></td>
                                        <?php
                                            foreach ($list_hadiah_pilihan as $item):
                                        ?>
                                                <td class="text-center">
                                                    <input class="radio_<?=$item->id;?>" required="required" type="radio" name="hadiah_<?=$agen->reldag;?>" value="<?=$item->id;?>">
                                                </td>
                                        <?php
                                            endforeach;
                                        ?>
                                    </tr>
                            <?php
                                    endforeach;
                                }
                            ?>
                        </table>
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
    <?php
        foreach ($list_hadiah_pilihan as $item):
    ?>
    <script>
        $('#check_all_' + <?=$item->id;?>).on('change', function(){
            if ($(this).is(':checked')) {
                $(".check_class").prop("checked", false);
                $(this).prop("checked", true);
                $('.radio_' + <?=$item->id;?>).prop('checked', true);
            } else {
                $('.radio_' + <?=$item->id;?>).prop('checked', false);
            }            
        })
    </script>
    <?php
        endforeach;
    ?>
@endsection