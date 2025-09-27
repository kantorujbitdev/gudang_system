<!DOCTYPE html>
<html>

<head>
    <title><?php echo $title; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
        }

        .summary {
            margin-bottom: 20px;
        }

        .summary-box {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
        }

        .summary-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>LAPORAN SALES</h2>
        <p>Periode: <?php echo date('d-m-Y', strtotime($filter['tanggal_awal'])); ?> s/d
            <?php echo date('d-m-Y', strtotime($filter['tanggal_akhir'])); ?>
        </p>
    </div>

    <div class="summary">
        <div class="row">
            <div class="col-md-4">
                <div class="summary-box">
                    <div class="summary-title">Total Transaksi</div>
                    <div class="summary-value"><?php echo $summary->total_transaksi ?: 0; ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-box">
                    <div class="summary-title">Total Barang</div>
                    <div class="summary-value"><?php echo $summary->total_barang ?: 0; ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-box">
                    <div class="summary-title">Rata-rata</div>
                    <div class="summary-value">
                        <?php echo number_format(($summary->total_barang ?: 0) / ($summary->total_transaksi ?: 1), 0, ',', '.'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No Transaksi</th>
                <th>Tanggal</th>
                <th>Tujuan</th>
                <th>Barang</th>
                <th>Jumlah</th>
                <th>Satuan</th>
                <th>Status</th>
                <th>User</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1;
            foreach ($sales as $row): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $row->no_transaksi; ?></td>
                    <td><?php echo date('d-m-Y', strtotime($row->tanggal_pemindahan)); ?></td>
                    <td><?php echo $row->nama_tujuan; ?></td>
                    <td><?php echo $row->nama_barang; ?></td>
                    <td class="text-right"><?php echo $row->jumlah; ?></td>
                    <td><?php echo $row->satuan; ?></td>
                    <td><?php echo $row->status; ?></td>
                    <td><?php echo $row->nama_user; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: <?php echo date('d-m-Y H:i:s'); ?></p>
    </div>
</body>

</html>