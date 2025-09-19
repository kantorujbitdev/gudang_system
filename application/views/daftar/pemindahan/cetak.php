<!DOCTYPE html>
<html>

<head>
    <title>Detail Pemindahan Barang</title>
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
        <h3>BUKTI PEMINDAHAN BARANG</h3>
    </div>

    <table class="info-table">
        <tr>
            <th>No Transfer</th>
            <td>: <?php echo $pemindahan->no_transfer; ?></td>
        </tr>
        <tr>
            <th>Tanggal</th>
            <td>: <?php echo date('d-m-Y H:i', strtotime($pemindahan->tanggal)); ?></td>
        </tr>
        <tr>
            <th>Gudang Asal</th>
            <td>: <?php echo $pemindahan->gudang_asal; ?></td>
        </tr>
        <tr>
            <th>Tujuan</th>
            <td>:
                <?php if ($pemindahan->id_gudang_tujuan): ?>
                    <?php echo $pemindahan->gudang_tujuan; ?>
                <?php elseif ($pemindahan->id_pelanggan): ?>
                    <?php echo $pemindahan->nama_pelanggan; ?>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th>Status</th>
            <td>: <?php echo $pemindahan->status; ?></td>
        </tr>
        <tr>
            <th>Keterangan</th>
            <td>: <?php echo $pemindahan->keterangan ?: '-'; ?></td>
        </tr>
    </table>

    <h4>Daftar Barang</h4>
    <table class="item-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Satuan</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1;
            foreach ($detail as $row): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $row->nama_barang; ?></td>
                    <td><?php echo $row->satuan; ?></td>
                    <td><?php echo $row->jumlah; ?></td>
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