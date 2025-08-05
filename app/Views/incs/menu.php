<ul class="navbar-nav me-auto mb-2 mb-lg-0 navbar-menu">
    <li class="nav-item">
        <a class="nav-link" aria-current="page" href="<?= base_url_check_admin('/'); ?>">Home</a>
    </li>

    <?php if (check_auth()): ?>
        <li class="nav-item">
            <a class="nav-link" aria-current="page" href="<?= base_url_check_admin('/dashboard'); ?>">Dashboard</a>
        </li>
    <?php else: ?>
        <li class="nav-item">
            <a class="nav-link" aria-current="page" href="<?= base_url_check_admin('/register'); ?>">Register</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" aria-current="page" href="<?= base_url_check_admin('/login'); ?>">Login</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" aria-current="page" href="<?= base_url_check_admin('/admin/login'); ?>">Login Admin</a>
        </li>
    <?php endif; ?>

    <li class="nav-item">
        <a class="nav-link" aria-current="page" href="<?= base_url_check_admin('/users'); ?>">Users</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" aria-current="page" href="<?= base_url_check_admin('/posts'); ?>">Posts</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" aria-current="page" href="<?= base_url_check_admin('/contact'); ?>">Contact</a>
    </li>
</ul>

