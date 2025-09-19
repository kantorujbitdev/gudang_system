<!DOCTYPE html>
<html>

<head>
    <title>Detail Retur Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h3 {
            margin: 0;
            font-size: 18px;
        }

        .header p {
            margin: 5px 0;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-table th {
            width: 30%;
            text-align: left;
            padding: 5px;
        }

        .info-table td {
            padding: 5px;
        }

        .item-table {
            width: 100%;
            border-collapse: collapse;
        }

        .item-table th,
        .item-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .item-table th {
            background-color: #f2f2f2;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="header">
        <h3>PT. MAJU BERSAMA</h3>
        <p>Jl. Sudirman No. 10, Jakarta</p>
        <p>Telepon: 021-12345678</p>
        <hr>
        <h3>BUKTI RETUR PENJUALAN</h3>
    </div>

    <table class="info-table">
        <tr>
            <th>No Retur</th>
            <td>: <?php echo $retur->no_retur; ?></td>
        </tr>
        <tr>
            <th>Tanggal</th>
            <td>: <?php echo date('d-m-Y H:i', strtotime($retur->tanggal_retur)); ?></td>
        </tr>
        <tr>
            <th>No Invoice</th>
            <td>: <?php echo $retur->no_invoice; ?></td>
        </tr>
        <tr>
            <th>Pelanggan</th>
            <td>: <?php echo $retur->nama_pelanggan; ?></td>
        </tr>
        <tr>
            <th>Status</th>
            <td>: <?php echo $retur->status; ?></td>
        </tr>
        <tr>
            <th>Alasan Retur</th>
            <td>: <?php echo $retur->alasan_retur; ?></td>
        </tr>
    </table>

    <h4>Daftar Barang</h4>
    <table class="item-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Satuan</th>
                <th>Gudang</th>
                <th>Jumlah Retur</th>
                <th>Jumlah Disetujui</th>
                <th>Alasan</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1;
            foreach ($detail as $row): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $row->nama_barang; ?></td>
                    <td><?php echo $row->satuan; ?></td>
                    <td><?php echo $row->nama_gudang; ?></td>
                    <td><?php echo $row->jumlah_retur; ?></td>
                    <td><?php echo $row->jumlah_disetujui; ?></td>
                    <td><?php echo $row->alasan_barang ?: '-'; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: <?php echo date('d-m-Y H:i:s'); ?></p>
        <p> oleh: <?php echo $this->session->userdata('nama'); ?></p>
    </div>
</body>

</html>