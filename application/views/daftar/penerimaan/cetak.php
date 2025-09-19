<!DOCTYPE html>
<html>

<head>
    <title>Detail Penerimaan Barang</title>
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
        <h3>BUKTI PENERIMAAN BARANG</h3>
    </div>

    <table class="info-table">
        <tr>
            <th>No Penerimaan</th>
            <td>: <?php echo $penerimaan->no_penerimaan; ?></td>
        </tr>
        <tr>
            <th>Tanggal</th>
            <td>: <?php echo date('d-m-Y H:i', strtotime($penerimaan->tanggal_penerimaan)); ?></td>
        </tr>
        <tr>
            <th>Supplier</th>
            <td>: <?php echo $penerimaan->nama_supplier; ?></td>
        </tr>
        <tr>
            <th>Gudang</th>
            <td>: <?php echo $penerimaan->nama_gudang; ?></td>
        </tr>
        <tr>
            <th>Status</th>
            <td>: <?php echo $penerimaan->status; ?></td>
        </tr>
        <tr>
            <th>Keterangan</th>
            <td>: <?php echo $penerimaan->keterangan ?: '-'; ?></td>
        </tr>
    </table>

    <h4>Daftar Barang</h4>
    <table class="item-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Satuan</th>
                <th>Jumlah Dipesan</th>
                <th>Jumlah Diterima</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1;
            foreach ($detail as $row): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $row->nama_barang; ?></td>
                    <td><?php echo $row->satuan; ?></td>
                    <td><?php echo $row->jumlah_dipesan ?: '-'; ?></td>
                    <td><?php echo $row->jumlah_diterima; ?></td>
                    <td><?php echo $row->keterangan ?: '-'; ?></td>
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