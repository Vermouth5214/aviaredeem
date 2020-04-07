<table>
    <thead>
        <tr>
            <th><b>Kode Campaign</b></th>
            <th><b>Kode Customer</b></th>
            <th><b>Cabang</b></th>
            <th><b>Omzet / Poin</b></th>
            <th><b>Kode Hadiah</b></th>
            <th><b>Nama Hadiah</b></th>
            <th><b>Jumlah (buah)</b></th>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach ($data as $detail):
        ?>
            <tr>
                <td><?=$detail->kode_campaign;?></td>
                <td><?=$detail->kode_customer;?></td>
                <td><?=$detail->cabang;?></td>
                <td class="text-right">
                    <?php
                        $omzet = $detail->poin;
                        if ($detail->omzet_netto > 0){
                            $omzet = $detail->omzet_netto;
                        }
                        echo $omzet;
                    ?>
                </td>
                <td><?=$detail->kode_hadiah;?></td>
                <td><?=$detail->nama_hadiah;?></td>
                <td class="text-right">
                    <?php
                        $total = $detail->jumlah_paket;
                        if ($detail->emas == 0){
                            $total = $detail->jumlah_total;
                        }
                        echo $total;
                    ?>
                </td>
            </tr>
        <?php
            endforeach;
        ?>
    </tbody>
</table>
