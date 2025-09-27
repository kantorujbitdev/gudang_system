<!-- Load Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    $(document).ready(function () {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: 'Pilih opsi'
        });

        // Create chart for sales by period
        var ctx = document.getElementById('salesChart').getContext('2d');
        var salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [
                    <?php foreach ($sales_by_period as $row): ?>
                                            '<?php echo date('d-m-Y', strtotime($row->tanggal)); ?>',
                    <?php endforeach; ?>
                ],
                datasets: [{
                    label: 'Total Barang',
                    data: [
                        <?php foreach ($sales_by_period as $row): ?>
                                                <?php echo $row->total_barang; ?>,
                        <?php endforeach; ?>
                    ],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>