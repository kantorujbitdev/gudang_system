<!-- Load Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    $(document).ready(function () {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: 'Pilih opsi'
        });

        // Create chart for packing by period
        var ctx = document.getElementById('packingChart').getContext('2d');
        var packingChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [
                    <?php foreach ($packing_by_period as $row): ?>
                                    '<?php echo date('d-m-Y', strtotime($row->tanggal)); ?>',
                    <?php endforeach; ?>
                ],
                datasets: [{
                    label: 'Total Packing',
                    data: [
                        <?php foreach ($packing_by_period as $row): ?>
                                        <?php echo $row->total_packing; ?>,
                        <?php endforeach; ?>
                    ],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    yAxisID: 'y'
                }, {
                    label: 'Total Barang',
                    data: [
                        <?php foreach ($packing_by_period as $row): ?>
                                        <?php echo $row->total_barang; ?>,
                        <?php endforeach; ?>
                    ],
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Packing'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Barang'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });
    });
</script>