<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Barang</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo isset($total_barang) ? $total_barang : 0; ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-box fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Gudang</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo isset($total_gudang) ? $total_gudang : 0; ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-warehouse fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Stok Menipis</div>
                        <div class="row no-gutters align-items-center">
                            <div class="col-auto">
                                <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                    <?php echo isset($stok_menipis) ? count($stok_menipis) : 0; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Requests Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Transaksi Hari Ini</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo isset($transaksi_hari_ini) ? $transaksi_hari_ini : 0; ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Area Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Grafik Stok per Gudang</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="stokChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Pie Chart -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Transaksi Bulan Ini</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="transaksiChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <span class="mr-2">
                        <i class="fas fa-circle text-primary"></i> Masuk
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-success"></i> Keluar
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-info"></i> Transfer
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Stok Menipis -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Stok Menipis</h6>
            </div>
            <div class="card-body">
                <?php if (!empty($stok_menipis)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Barang</th>
                                    <th>Gudang</th>
                                    <th>Stok</th>
                                    <th>Min Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stok_menipis as $item): ?>
                                    <tr>
                                        <td><?php echo $item->nama_barang; ?></td>
                                        <td><?php echo $item->nama_gudang; ?></td>
                                        <td><span class="badge badge-warning"><?php echo $item->jumlah; ?></span></td>
                                        <td><?php echo $item->min_stok; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-center">Tidak ada stok yang menipis</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Aktivitas Terkini -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Aktivitas Terkini</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <!-- Timeline items akan di generate dari database -->
                    <div class="timeline-item">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <span class="timeline-time">10:30 AM</span>
                            <h3 class="timeline-title">Penerimaan Barang #PB001</h3>
                            <p class="timeline-text">50 unit barang telah diterima di Gudang A</p>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <span class="timeline-time">09:15 AM</span>
                            <h3 class="timeline-title">Penjualan #INV002</h3>
                            <p class="timeline-text">10 unit barang telah terjual ke Pelanggan XYZ</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart scripts -->
<!-- <script src="<?php echo base_url('assets/vendor/chart.js/Chart.min.js'); ?>"></script> -->
<script>
    // Stok Chart
    var stokCtx = document.getElementById("stokChart");
    var stokChart = new Chart(stokCtx, {
        type: 'bar',
        data: {
            labels: <?php echo isset($chart_stok) ? json_encode(array_column($chart_stok, 'nama_gudang')) : '[]'; ?>,
            datasets: [{
                label: "Total Stok",
                backgroundColor: "rgba(78, 115, 223, 0.8)",
                borderColor: "rgba(78, 115, 223, 1)",
                data: <?php echo isset($chart_stok) ? json_encode(array_column($chart_stok, 'total_stok')) : '[0]'; ?>,
            }],
        },
        options: {
            responsive: true,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            },
        }
    });

    // Transaksi Chart
    var transaksiCtx = document.getElementById("transaksiChart");
    var transaksiChart = new Chart(transaksiCtx, {
        type: 'doughnut',
        data: {
            labels: ['Masuk', 'Keluar', 'Transfer'],
            datasets: [{
                data: [
                    <?php echo isset($chart_transaksi['masuk']) ? $chart_transaksi['masuk'] : 0; ?>,
                    <?php echo isset($chart_transaksi['keluar']) ? $chart_transaksi['keluar'] : 0; ?>,
                    <?php echo isset($chart_transaksi['transfer']) ? $chart_transaksi['transfer'] : 0; ?>
                ],
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
                hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
            },
            legend: {
                display: false
            },
            cutoutPercentage: 80,
        },
    });
</script>