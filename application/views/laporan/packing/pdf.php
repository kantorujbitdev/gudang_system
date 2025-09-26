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
    </style>
</head>

<body>
    <div class="header">
        <h2>LAPORAN PACKING</h2>
        <p>Periode: <?php echo date('d-m-Y', strtotime($filter['tanggal_awal'])); ?> s/d
            <?php echo date('d-m-Y', strtotime($filter['tanggal_akhir'])); ?>
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No Packing</th>
                <th>Tanggal</th>
                <th>User</th>
                <th>Referensi</th>
                <th>Barang</th>
                <th>Jumlah</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1;
            foreach ($packing as $row): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $row->id_packing; ?></td>
                    <td><?php echo date('d-m-Y', strtotime($row->tanggal_packing)); ?></td>
                    <td><?php echo $row->user_nama; ?></td>
                    <td><?php echo $row->tipe_referensi . ' #' . $row->id_referensi; ?></td>
                    <td><?php echo $row->nama_barang; ?></td>
                    <td class="text-right"><?php echo $row->jumlah; ?></td>
                    <td><?php echo $row->status; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: <?php echo date('d-m-Y H:i:s'); ?></p>
    </div>
</body>

</html>