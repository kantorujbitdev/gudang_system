<?php
$role_id = $this->session->userdata('id_role');
$nama_role = $this->session->userdata('nama_role');
$nama = $this->session->userdata('nama');
switch ($role_id) {
    case 2:
        $profile_img = 'undraw_profile_sales.svg';
        break;
    case 3:
        $profile_img = 'undraw_profile_packing.svg';
        break;
    case 4:
        $profile_img = 'undraw_profile_retur.svg';
        break;
    case 1:
    case 5:
    default:
        $profile_img = 'undraw_profile.svg';
        break;
}
?>

<!-- Top Menu + User Info -->
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#topMenu" aria-controls="topMenu"
        aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Info User untuk layar kecil -->
    <ul class="navbar-nav d-block d-sm-none ml-auto">
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle d-flex align-items-center justify-content-end" href="#" id="userDropdown"
                role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 text-gray-600 small text-right">
                    <?php echo $nama ?> <br>(<?php echo $nama_role ?>)
                </span>
                <img class="img-profile rounded-circle ml-1"
                    src="<?php echo base_url('assets/images/profile/' . $profile_img) ?>"
                    style="width: 32px; height: 32px;">
            </a>

            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in bg-white"
                aria-labelledby="userDropdownMobile">
                <a class="dropdown-item" href="<?php echo site_url('setup/user/profile'); ?>">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i> Profile
                </a>
                <a class="dropdown-item" href="<?php echo site_url('setup/user/change_password'); ?>">
                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i> Ubah Password
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Logout
                </a>
            </div>
        </li>
    </ul>

    <!-- Menu Horizontal -->
    <div class="collapse navbar-collapse" id="topMenu">
        <ul class="navbar-nav mr-auto">
            <!-- Dashboard -->
            <li
                class="nav-item <?php echo $this->uri->segment(1) == 'dashboard' || $this->uri->segment(1) == '' ? 'active' : ''; ?>">
                <a class="nav-link" href="<?php echo base_url('dashboard'); ?>">
                    <img src="<?php echo base_url('assets/images/logo.png'); ?>" alt="Logo"
                        style="width:20px; height:20px; margin-right:8px;">
                    Dashboard
                </a>
            </li>

            <!-- Setup Dropdown -->
            <li class="nav-item dropdown <?php echo $this->uri->segment(1) == 'setup' ? 'active' : ''; ?>">
                <a class="nav-link dropdown-toggle" href="#" role="button"
                    onclick="toggleDropdown(this); return false;">
                    <i class="fas fa-database"></i> Setup
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="<?php echo base_url('setup/perusahaan'); ?>">
                        <i class="fas fa-building"></i> Perusahaan
                    </a>
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

            <!-- Daftar Dropdown -->
            <li class="nav-item dropdown <?php echo $this->uri->segment(1) == 'daftar' ? 'active' : ''; ?>">
                <a class="nav-link dropdown-toggle" href="#" role="button"
                    onclick="toggleDropdown(this); return false;">
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

            <!-- Aktifitas Dropdown -->
            <li class="nav-item dropdown <?php echo $this->uri->segment(1) == 'aktifitas' ? 'active' : ''; ?>">
                <a class="nav-link dropdown-toggle" href="#" role="button"
                    onclick="toggleDropdown(this); return false;">
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

            <!-- Laporan Dropdown -->
            <li class="nav-item dropdown <?php echo $this->uri->segment(1) == 'laporan' ? 'active' : ''; ?>">
                <a class="nav-link dropdown-toggle" href="#" role="button"
                    onclick="toggleDropdown(this); return false;">
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
                <a class="nav-link dropdown-toggle" href="#" role="button"
                    onclick="toggleDropdown(this); return false;">
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

        <!-- User Info (desktop) -->
        <ul class="navbar-nav ml-auto align-items-center pr-3 d-none d-sm-flex">
            <li class="nav-item dropdown no-arrow">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="mr-2 text-gray-600 small">
                        Hai,
                        <?php echo $nama ?> (<?php echo $nama_role ?>)

                    </span>
                    <img class="img-profile rounded-circle mr-1"
                        src="<?php echo base_url('assets/images/profile/' . $profile_img) ?>"
                        style="width: 32px; height: 32px;">
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in bg-white"
                    aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="<?php echo site_url('setup/user/profile'); ?>">
                        <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i> Profile
                    </a>
                    <a class="dropdown-item" href="<?php echo site_url('setup/user/change_password'); ?>">
                        <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i> Ubah Password
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Logout
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>

<!-- JS Dropdown -->
<script>
    function toggleDropdown(element) {
        const menu = element.nextElementSibling;
        const isOpen = menu.classList.contains('show');

        // Tutup semua dropdown dulu
        document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.remove('show'));
        document.querySelectorAll('.dropdown-toggle').forEach(t => t.classList.remove('show'));

        // Kalau sebelumnya belum terbuka, buka dropdown yg diklik
        if (!isOpen) {
            element.classList.add('show');
            menu.classList.add('show');
        }
    }

    document.addEventListener('click', function (event) {
        if (!event.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.remove('show'));
            document.querySelectorAll('.dropdown-toggle').forEach(toggle => toggle.classList.remove('show'));
        }
    });
</script>