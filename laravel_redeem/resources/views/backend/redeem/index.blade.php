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
                    <form id="form-work" class="form-horizontal" role="form" autocomplete="off" method="GET">
                        <div class="row">
                            <div class="col-xs-12 col-sm-1 text-right" style="margin-top:7px;">
                                Status 
                            </div>
                            <div class="col-xs-12 col-sm-5">
                                <select name="status" class="form-control">
                                    <?php
                                        $selected = "";
                                        if ($status == 999){
                                            $selected = "selected";
                                        }
                                    ?>
                                    <option value="999" <?=$selected;?>>Semua</option>
                                    <?php
                                        $selected = "";
                                        if ($status == 1){
                                            $selected = "selected";
                                        }
                                    ?>
                                    <option value="1" <?=$selected;?>>Sudah Klaim</option>
                                    <?php
                                        $selected = "";
                                        if ($status == 2){
                                            $selected = "selected";
                                        }
                                    ?>
                                    <option value="2" <?=$selected;?>>Kedaluwarsa</option>
                                    <?php
                                        $selected = "";
                                        if ($status == 3){
                                            $selected = "selected";
                                        }
                                    ?>
                                    <option value="3" <?=$selected;?>>Belum Klaim</option>
                                    <?php
                                        $selected = "";
                                        if ($status == 4){
                                            $selected = "selected";
                                        }
                                    ?>
                                    <option value="4" <?=$selected;?>>Belum Konversi</option>
                                </select>
                            </div>
                            <div class="col-xs-12 col-sm-2">
                                <input type="submit" class="btn btn-primary btn-block" value="Submit">
                            </div>
                        </div>
                    </form>
                    <br/>         
                    <p class="small blue">
                        - Harap melakukan proses konversi emas setelah melakukan klaim hadiah (jika ada)<br/>
                        - Proses klaim hadiah baru dianggap selesai setelah melakukan proses konversi emas (jika ada)<br/>
                    </p>
                    <br/>
                    <table class="table table-striped table-hover table-bordered dt-responsive nowrap dataTable" cellspacing="0" width="100%">
						<thead>
							<tr>
                                <th>Status</th>
                                <th>Action</th>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Jenis</th>
                                <th>Awal</th>
                                <th>Akhir</th>
                                <th>Brosur</th>
                                <th>Omzet Netto</th>
                                <th>Poin</th>
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
            ajax: "<?=url('backend/redeem-hadiah/datatable?status='.$status);?>",
			columns: [
				{data:  'status', render: function ( data, type, row ) {
					var text = "";
					var label = "";
					if (data == 1){
						text = "Sudah Klaim";
						label = "success";
					} else 
                    if (data == 2){    
						text = "Kedaluwarsa";
						label = "error";
					} else 
                    if (data == 3){
                        text = "Belum Klaim";
                        label = "info";
                    }
                    if (data == 4){
                        text = "Belum Konversi";
                        label = "warning";
                    }
					return "<span class='badge badge-" + label + "'>"+ text + "</span>";
                }},
				{data: 'action', name: 'action', orderable: false, searchable: false},                
				{data: 'kode_campaign', name: 'kode_campaign'},
                {data: 'nama_campaign', name: 'nama_campaign'},
                {data: 'jenis', name: 'jenis'},
                {data: 'periode_awal', name: 'periode_awal'},
                {data: 'periode_akhir', name: 'periode_akhir'},
                {data: 'brosur', name: 'brosur'},
                {data: 'omzet_netto', name: 'omzet_netto'},
                {data: 'poin', name: 'poin'}
			],
            order: [[ 0, "desc" ]],
			responsive: false
		});
	</script>
@endsection