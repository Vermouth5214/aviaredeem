<?php
	if (!empty($data)):
		$data = $data[0];
?>
	<div class="x_panel">
		<div class="x_content">
			<div class="form-group col-xs-12">
				<label class="control-label">ID :</label>
				<span class="form-control"><?=$data->id;?></span>
			</div>
			<div class="form-group col-xs-12">
				<label class="control-label">Kode Campaign :</label>
				<span class="form-control"><?=$data->kode_campaign;?></span>
            </div>
			<div class="form-group col-xs-12">
				<label class="control-label">Kode Customer :</label>
				<span class="form-control"><?=$data->kode_customer;?></span>
			</div>
			<div class="form-group col-xs-12">
				<label class="control-label">Periode Awal :</label>
				<span class="form-control"><?=date('d M Y', strtotime($data->periode_awal));?></span>
			</div>
			<div class="form-group col-xs-12">
				<label class="control-label">Periode Akhir :</label>
				<span class="form-control"><?=date('d M Y', strtotime($data->periode_akhir));?></span>
			</div>
			<div class="form-group col-xs-12">
				<label class="control-label">Omzet Tepat Waktu:</label>
				<span class="form-control"><?=number_format($data->omzet_tepat_waktu,0,',','.');?></span>
            </div>
			<div class="form-group col-xs-12">
				<label class="control-label">Disc Pembelian:</label>
				<span class="form-control"><?=number_format($data->disc_pembelian,5,',','.');?></span>
			</div>
			<div class="form-group col-xs-12">
				<label class="control-label">Omzet Netto dgn Disc:</label>
				<span class="form-control"><?=number_format($data->omzet_netto,0,',','.');?></span>
            </div>
			<div class="form-group col-xs-12">
				<label class="control-label">Poin :</label>
				<span class="form-control"><?=number_format($data->poin,0,',','.');?></span>
			</div>
			<div class="form-group col-xs-12">
				<label class="control-label">Date Created :</label>
				<span class="form-control"><?=date('d M Y H:i:s', strtotime($data->created_at));?></span>
			</div>
			<div class="form-group col-xs-12">
				<label class="control-label">Last Modified :</label>
				<span class="form-control"><?=date('d M Y H:i:s', strtotime($data->updated_at));?></span>
			</div>
			<div class="form-group col-xs-12">
				<label class="control-label">Last Modified by :</label>
				<span class="form-control"><?=$data->user_modified;?></span>
			</div>
		</div>
	</div>
<?php
	endif;
?>

