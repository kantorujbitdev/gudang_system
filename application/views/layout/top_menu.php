<!-- Top Menu Horizontal -->
<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo site_url('dashboard'); ?>">
            <img src="<?php echo base_url('assets/images/logo.png'); ?>" height="30" alt="Logo">
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#topMenu"
            aria-controls="topMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="topMenu">
            <ul class="navbar-nav mr-auto">
                <!-- Dashboard -->
                <li class="nav-item <?php echo $this->uri->segment(1) == 'dashboard' ? 'active' : ''; ?>">
                    <a class="nav-link" href="<?php echo site_url('dashboard'); ?>">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>

                <!-- Setup Dropdown -->
                <li class="nav-item dropdown <?php echo $this->uri->segment(1) == 'setup' ? 'active' : ''; ?>">
                    <a class="nav-link dropdown-toggle" href="#" id="setupDropdown" role="button" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-database"></i> Setup
                    </a>
                    <div class="dropdown-menu" aria-labelledby="setupDropdown">
                        <a class="dropdown-item" href="<?php echo site_url('setup/kategori'); ?>">
                            <i class="fas fa-tags"></i> Kategori Barang
                        </a>
                        <a class="dropdown-item" href="<?php echo site_url('setup/barang'); ?>">
                            <i class="fas fa-box"></i> Barang
                        </a>
                        <a class="dropdown-item" href="<?php echo site_url('setup/gudang'); ?>">
                            <i class="fas fa-warehouse"></i> Gudang
                        </a>
                        <a class="dropdown-item" href="<?php echo site_url('setup/pelanggan'); ?>">
                            <i class="fas fa-users"></i> Pelanggan
                        </a>
                        <a class="dropdown-item" href="<?php echo site_url('setup/supplier'); ?>">
                            <i class="fas fa-truck"></i> Supplier
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?php echo site_url('setup/user'); ?>">
                            <i class="fas fa-user-cog"></i> User Management
                        </a>
                    </div>
                </li>

                <!-- Aktifitas Dropdown -->
                <li class="nav-item dropdown <?php echo $this->uri->segment(1) == 'aktifitas' ? 'active' : ''; ?>">
                    <a class="nav-link dropdown-toggle" href="#" id="aktifitasDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-exchange-alt"></i> Aktifitas
                    </a>
                    <div class="dropdown-menu" aria-labelledby="aktifitasDropdown">
                        <a class="dropdown-item" href="<?php echo site_url('aktifitas/pemindahan'); ?>">
                            <i class="fas fa-truck-loading"></i> Pemindahan Barang
                        </a>
                        <a class="dropdown-item" href="<?php echo site_url('aktifitas/penerimaan'); ?>">
                            <i class="fas fa-clipboard-check"></i> Penerimaan Barang
                        </a>
                        <a class="dropdown-item" href="<?php echo site_url('aktifitas/retur_penjualan'); ?>">
                            <i class="fas fa-undo-alt"></i> Retur Penjualan
                        </a>
                        <a class="dropdown-item" href="<?php echo site_url('aktifitas/retur_pembelian'); ?>">
                            <i class="fas fa-undo"></i> Retur Pembelian
                        </a>
                    </div>
                </li>

                <!-- Daftar Dropdown -->
                <li class="nav-item dropdown <?php echo $this->uri->segment(1) == 'daftar' ? 'active' : ''; ?>">
                    <a class="nav-link dropdown-toggle" href="#" id="daftarDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-list"></i> Daftar
                    </a>
                    <div class="dropdown-menu" aria-labelledby="daftarDropdown">
                        <a class="dropdown-item" href="<?php echo site_url('daftar/pemindahan'); ?>">
                            <i class="fas fa-truck"></i> Pemindahan Barang
                        </a>
                        <a class="dropdown-item" href="<?php echo site_url('daftar/penerimaan'); ?>">
                            <i class="fas fa-clipboard-list"></i> Penerimaan Barang
                        </a>
                        <a class="dropdown-item" href="<?php echo site_url('daftar/retur_penjualan'); ?>">
                            <i class="fas fa-undo-alt"></i> Retur Penjualan
                        </a>
                        <a class="dropdown-item" href="<?php echo site_url('daftar/retur_pembelian'); ?>">
                            <i class="fas fa-undo"></i> Retur Pembelian
                        </a>
                    </div>
                </li>

                <!-- Laporan Dropdown -->
                <li class="nav-item dropdown <?php echo $this->uri->segment(1) == 'laporan' ? 'active' : ''; ?>">
                    <a class="nav-link dropdown-toggle" href="#" id="laporanDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-chart-bar"></i> Laporan
                    </a>
                    <div class="dropdown-menu" aria-labelledby="laporanDropdown">
                        <a class="dropdown-item" href="<?php echo site_url('laporan/sales'); ?>">
                            <i class="fas fa-chart-line"></i> Sales
                        </a>
                        <a class="dropdown-item" href="<?php echo site_url('laporan/packing'); ?>">
                            <i class="fas fa-boxes"></i> Packing
                        </a>
                        <a class="dropdown-item" href="<?php echo site_url('laporan/mutasi'); ?>">
                            <i class="fas fa-exchange-alt"></i> Mutasi Barang
                        </a>
                        <a class="dropdown-item" href="<?php echo site_url('laporan/summary'); ?>">
                            <i class="fas fa-file-invoice-dollar"></i> Ringkasan Stok
                        </a>
                    </div>
                </li>

                <!-- Pengaturan Dropdown -->
                <li class="nav-item dropdown <?php echo $this->uri->segment(1) == 'pengaturan' ? 'active' : ''; ?>">
                    <a class="nav-link dropdown-toggle" href="#" id="pengaturanDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-cog"></i> Pengaturan
                    </a>
                    <div class="dropdown-menu" aria-labelledby="pengaturanDropdown">
                        <a class="dropdown-item" href="<?php echo site_url('pengaturan/stok_awal'); ?>">
                            <i class="fas fa-dolly-flatbed"></i> Stok Awal
                        </a>
                        <a class="dropdown-item" href="<?php echo site_url('pengaturan/hak_akses'); ?>">
                            <i class="fas fa-user-shield"></i> Hak Akses
                        </a>
                        <a class="dropdown-item" href="<?php echo site_url('pengaturan/approval'); ?>">
                            <i class="fas fa-tasks"></i> Approval Flow
                        </a>
                        <a class="dropdown-item" href="<?php echo site_url('pengaturan/sistem'); ?>">
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
    </div>
</nav>