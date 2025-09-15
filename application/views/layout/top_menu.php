<!-- Top Menu Horizontal -->
<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#topMenu" aria-controls="topMenu"
        aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="topMenu">
        <ul class="navbar-nav mr-auto">
            <!-- Dashboard -->
            <li
                class="nav-item <?php echo $this->uri->segment(1) == 'dashboard' || $this->uri->segment(1) == '' ? 'active' : ''; ?>">
                <a class="nav-link" href="<?php echo base_url('dashboard'); ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>

            <!-- Setup Dropdown -->
            <li class="nav-item dropdown <?php echo $this->uri->segment(1) == 'setup' ? 'active' : ''; ?>">
                <a class="nav-link dropdown-toggle" href="#" onclick="toggleDropdown(this); return false;">
                    <i class="fas fa-database"></i> Setup
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="<?php echo base_url('setup/kategori'); ?>">
                        <i class="fas fa-tags"></i> Kategori Barang
                    </a>
                    <a class="dropdown-item" href="<?php echo base_url('setup/barang'); ?>">
                        <i class="fas fa-box"></i> Barang
                    </a>
                    <a class="dropdown-item" href="<?php echo base_url('setup/gudang'); ?>">
                        <i class="fas fa-warehouse"></i> Gudang
                    </a>
                    <a class="dropdown-item" href="<?php echo base_url('setup/pelanggan'); ?>">
                        <i class="fas fa-users"></i> Pelanggan
                    </a>
                    <a class="dropdown-item" href="<?php echo base_url('setup/supplier'); ?>">
                        <i class="fas fa-truck"></i> Supplier
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?php echo base_url('setup/user'); ?>">
                        <i class="fas fa-user-cog"></i> User Management
                    </a>
                </div>
            </li>

            <!-- Aktifitas Dropdown -->
            <li class="nav-item dropdown <?php echo $this->uri->segment(1) == 'aktifitas' ? 'active' : ''; ?>">
                <a class="nav-link dropdown-toggle" href="#" onclick="toggleDropdown(this); return false;">
                    <i class="fas fa-exchange-alt"></i> Aktifitas
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="<?php echo base_url('aktifitas/pemindahan'); ?>">
                        <i class="fas fa-truck-loading"></i> Pemindahan Barang
                    </a>
                    <a class="dropdown-item" href="<?php echo base_url('aktifitas/penerimaan'); ?>">
                        <i class="fas fa-clipboard-check"></i> Penerimaan Barang
                    </a>
                    <a class="dropdown-item" href="<?php echo base_url('aktifitas/retur_penjualan'); ?>">
                        <i class="fas fa-undo-alt"></i> Retur Penjualan
                    </a>
                    <a class="dropdown-item" href="<?php echo base_url('aktifitas/retur_pembelian'); ?>">
                        <i class="fas fa-undo"></i> Retur Pembelian
                    </a>
                </div>
            </li>

            <!-- Daftar Dropdown -->
            <li class="nav-item dropdown <?php echo $this->uri->segment(1) == 'daftar' ? 'active' : ''; ?>">
                <a class="nav-link dropdown-toggle" href="#" onclick="toggleDropdown(this); return false;">
                    <i class="fas fa-list"></i> Daftar
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="<?php echo base_url('daftar/pemindahan'); ?>">
                        <i class="fas fa-truck"></i> Pemindahan Barang
                    </a>
                    <a class="dropdown-item" href="<?php echo base_url('daftar/penerimaan'); ?>">
                        <i class="fas fa-clipboard-list"></i> Penerimaan Barang
                    </a>
                    <a class="dropdown-item" href="<?php echo base_url('daftar/retur_penjualan'); ?>">
                        <i class="fas fa-undo-alt"></i> Retur Penjualan
                    </a>
                    <a class="dropdown-item" href="<?php echo base_url('daftar/retur_pembelian'); ?>">
                        <i class="fas fa-undo"></i> Retur Pembelian
                    </a>
                </div>
            </li>

            <!-- Laporan Dropdown -->
            <li class="nav-item dropdown <?php echo $this->uri->segment(1) == 'laporan' ? 'active' : ''; ?>">
                <a class="nav-link dropdown-toggle" href="#" onclick="toggleDropdown(this); return false;">
                    <i class="fas fa-chart-bar"></i> Laporan
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="<?php echo base_url('laporan/sales'); ?>">
                        <i class="fas fa-chart-line"></i> Sales
                    </a>
                    <a class="dropdown-item" href="<?php echo base_url('laporan/packing'); ?>">
                        <i class="fas fa-boxes"></i> Packing
                    </a>
                    <a class="dropdown-item" href="<?php echo base_url('laporan/mutasi'); ?>">
                        <i class="fas fa-exchange-alt"></i> Mutasi Barang
                    </a>
                    <a class="dropdown-item" href="<?php echo base_url('laporan/summary'); ?>">
                        <i class="fas fa-file-invoice-dollar"></i> Ringkasan Stok
                    </a>
                </div>
            </li>

            <!-- Pengaturan Dropdown -->
            <li class="nav-item dropdown <?php echo $this->uri->segment(1) == 'pengaturan' ? 'active' : ''; ?>">
                <a class="nav-link dropdown-toggle" href="#" onclick="toggleDropdown(this); return false;">
                    <i class="fas fa-cog"></i> Pengaturan
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="<?php echo base_url('pengaturan/stok_awal'); ?>">
                        <i class="fas fa-dolly-flatbed"></i> Stok Awal
                    </a>
                    <a class="dropdown-item" href="<?php echo base_url('pengaturan/hak_akses'); ?>">
                        <i class="fas fa-user-shield"></i> Hak Akses
                    </a>
                    <a class="dropdown-item" href="<?php echo base_url('pengaturan/approval'); ?>">
                        <i class="fas fa-tasks"></i> Approval Flow
                    </a>
                    <a class="dropdown-item" href="<?php echo base_url('pengaturan/sistem'); ?>">
                        <i class="fas fa-sliders-h"></i> Pengaturan Sistem
                    </a>
                </div>
            </li>
        </ul>

        <!-- Company Info (Untuk non Super Admin) -->
        <?php if ($this->session->userdata('id_role') != 1): ?>
            <span class="navbar-text">
                <i class="fas fa-building"></i>
                <?php echo $this->session->userdata('nama_perusahaan'); ?>
            </span>
        <?php endif; ?>
    </div>
</nav>

<!-- Simple JavaScript for dropdown -->
<script>
    function toggleDropdown(element) {
        // Close all dropdowns
        document.querySelectorAll('.dropdown-menu').forEach(function (menu) {
            menu.classList.remove('show');
        });
        document.querySelectorAll('.dropdown-toggle').forEach(function (toggle) {
            toggle.classList.remove('show');
        });

        // Open clicked dropdown
        element.classList.add('show');
        element.nextElementSibling.classList.add('show');
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function (event) {
        if (!event.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu').forEach(function (menu) {
                menu.classList.remove('show');
            });
            document.querySelectorAll('.dropdown-toggle').forEach(function (toggle) {
                toggle.classList.remove('show');
            });
        }
    });
</script>