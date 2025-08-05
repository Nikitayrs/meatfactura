<div class="container">

    <h1><?= $title ?? ''; ?></h1>

    <div class="row">

        <div class="col-md-6 offset-md-3">

            <form id="admin_login_form" action="<?= base_href('/admin/login'); ?>" method="POST" class="ajax-form">

                <?= get_csrf_field(); ?>

                <div class="mb-3">
                    <label for="login" class="form-label">Логин</label>
                    <input name="login" type="name" class="form-control <?= get_validation_class('login'); ?>" id="login" placeholder="Login" value="<?= old('login'); ?>">
                    <?= get_errors('login'); ?>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input name="password" type="password" class="form-control <?= get_validation_class('password'); ?>" id="password" placeholder="Password">
                    <?= get_errors('password'); ?>
                </div>

                <div class="admin_login_success"></div>
                <div class="admin_login_error"></div>

                <button type="submit" class="btn btn-warning">Login</button>

            </form>

            <?php
            session()->remove('form_data');
            session()->remove('form_errors');
            ?>

        </div>

    </div>

</div>
