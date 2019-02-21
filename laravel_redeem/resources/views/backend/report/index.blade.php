<?php
	$breadcrumb = [];
	$breadcrumb[0]['title'] = 'Dashboard';
	$breadcrumb[0]['url'] = url('backend/dashboard');
	$breadcrumb[1]['title'] = 'General Report';
	$breadcrumb[1]['url'] = url('backend/general-report');
?>

<!-- LAYOUT -->
@extends('backend.layouts.main')

<!-- TITLE -->
@section('title', 'General Report')

<!-- CONTENT -->
@section('content')
	<div class="page-title">
		<div class="title_left">
			<h3>General Report</h3>
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
                            <div class="col-xs-12 col-sm-2 text-right" style="margin-top:7px;">
                                Kode Campaign
                            </div>
                            <div class="col-xs-12 col-sm-5">
								{{
								Form::select(
									'kode_campaign',
									$campaign,
									$kode_campaign,
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
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-xs-12 col-sm-1" style="margin-top:7px;">
                                Periode
                            </div>
                            <div class="col-xs-12 col-sm-3 date">
                                <div class='input-group date' id='myDatepicker'>
                                    <input type='text' class="form-control" name="startDate" value=<?=$startDate;?> />
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-3 date">
                                <div class='input-group date' id='myDatepicker2'>
                                    <input type='text' class="form-control" name="endDate" value=<?=$endDate;?> />
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-1 text-right">
                                <?php
                                    $checked = "";
                                    if ($mode == "all"){
                                        $checked = "checked";
                                    }
                                ?>
                                <div class="checkbox">
                                    <input type="checkbox" name="mode" value="all" id="show-all" <?=$checked;?>>Tampilkan semua
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-2">
                                <input type="submit" class="btn btn-primary btn-block" name="submit" value="Submit">
                            </div>
                            <div class="col-xs-12 col-sm-2">
                                <?php
                                    $userinfo = Session::get('userinfo');
                                    if ($userinfo['uname'] != "mkt01"):
                                ?>
                                <input type="submit" class="btn btn-success btn-block" name="export" value="Generate TTO">
                                <?php
                                    endif;
                                ?>
                            </div>
                        </div>
                    </form>
                    <br/>
                    <p class="small blue"> - Proses Generate TTO hanya menggunakan parameter <b><i><u>Kode Campaign</u></i></b> dan <b><i><u>Periode</u></i></b><br/>
                        - TTO yang digerenate hanya untuk campaign dengan status <b>SUDAH KLAIM</b>
                    </p>
                    <br/>
                    <table class="table table-striped table-hover table-bordered dt-responsive nowrap dataTable" cellspacing="0" width="100%">
						<thead>
							<tr>
                                <th>Status</th>
                                <th>Action</th>
                                <th>Kode Customer</th>
                                <th>Cabang</th>
                                <th>Kode Campaign</th>
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
    <!-- select2 -->
    <link href="<?=url('vendors/select2/dist/css/select2.min.css');?>" rel="stylesheet">
@endsection

<!-- JAVASCRIPT -->
@section('script')
    <!-- select2 -->
    <script src="<?=url('vendors/select2/dist/js/select2.min.js');?>"></script>
    <script>
            $('#campaign').select2();
    </script>
	<script>
		$('.dataTable').dataTable({
			processing: true,
            serverSide: true,
            "lengthMenu": [[35, 75, 100], [35, 75, 100]],
            "pageLength": 35,
            ajax: "<?=url('backend/general-report/datatable?startDate='.$startDate.'&endDate='.$endDate.'&status='.$status.'&mode='.$mode.'&kode_campaign='.$kode_campaign);?>",
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
                {data: 'kode_customer', name: 'kode_customer'},
                {data: 'cabang', name: 'tbuser.cabang'},
				{data: 'kode_campaign', name: 'kode_campaign'},
                {data: 'nama_campaign', name: 'campaign_h.nama_campaign'},
                {data: 'jenis', name: 'campaign_h.jenis'},
                {data: 'periode_awal', name: 'periode_awal'},
                {data: 'periode_akhir', name: 'periode_akhir'},
                {data: 'brosur', name: 'campaign_h.brosur'},
                {data: 'omzet_netto', name: 'omzet_netto'},
                {data: 'poin', name: 'poin'}
			],
            order: [[ 0, "desc" ]],
			responsive: false
		});
    </script>
    <script>
        $('#myDatepicker').datetimepicker({
            format: 'DD-MM-YYYY'
        });

        $('#myDatepicker2').datetimepicker({
            format: 'DD-MM-YYYY'
        });

	</script>
@endsection