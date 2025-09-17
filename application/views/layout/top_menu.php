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

// Load menu dari database berdasarkan role user
$this->load->model('Menu_model');
$menu_items = $this->Menu_model->get_menu_tree($role_id);

// Debug: log menu items
log_message('debug', 'Menu items for role ' . $role_id . ': ' . json_encode($menu_items));
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
            <?php foreach ($menu_items as $menu): ?>
                <?php if (empty($menu->children)): ?>
                    <!-- Menu tanpa sub menu -->
                    <li
                        class="nav-item <?php echo ($menu->url && $this->uri->segment(1) == explode('/', trim($menu->url, '/'))[0]) ? 'active' : ''; ?>">
                        <a class="nav-link" href="<?php echo $menu->url ? site_url($menu->url) : '#'; ?>">
                            <?php if ($menu->icon): ?>
                                <i class="<?php echo $menu->icon; ?>"></i>
                            <?php endif; ?>
                            <?php echo $menu->nama_menu; ?>
                        </a>
                    </li>
                <?php else: ?>
                    <!-- Menu dengan sub menu -->
                    <li
                        class="nav-item dropdown <?php echo ($menu->url && $this->uri->segment(1) == explode('/', trim($menu->url, '/'))[0]) ? 'active' : ''; ?>">
                        <a class="nav-link dropdown-toggle" href="#" role="button"
                            onclick="toggleDropdown(this); return false;">
                            <?php if ($menu->icon): ?>
                                <i class="<?php echo $menu->icon; ?>"></i>
                            <?php endif; ?>
                            <?php echo $menu->nama_menu; ?>
                        </a>
                        <div class="dropdown-menu">
                            <?php foreach ($menu->children as $child): ?>
                                <a class="dropdown-item" href="<?php echo $child->url ? site_url($child->url) : '#'; ?>">
                                    <?php if ($child->icon): ?>
                                        <i class="<?php echo $child->icon; ?>"></i>
                                    <?php endif; ?>
                                    <?php echo $child->nama_menu; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
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