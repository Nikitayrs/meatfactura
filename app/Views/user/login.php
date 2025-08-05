<div class="container">

    <h1><?= $title ?? ''; ?></h1>

    <div class="row">

        <div class="col-md-6 offset-md-3">

            <form action="<?= base_url_check_admin('/login'); ?>" method="post" class="ajax-form">

                <?= get_csrf_field(); ?>

                <div class="mb-3">
                    <label for="name" class="form-label">Название компании</label>
                    <input name="name" type="name" class="form-control <?= get_validation_class('name'); ?>" id="name" placeholder="Name" value="<?= old('name'); ?>">
                    <?= get_errors('name'); ?>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input name="password" type="password" class="form-control <?= get_validation_class('password'); ?>" id="password" placeholder="Password">
                    <?= get_errors('password'); ?>
                </div>

                <button type="submit" class="btn btn-warning">Login</button>

            </form>

            <?php
            session()->remove('form_data');
            session()->remove('form_errors');
            ?>

        </div>

    </div>

</div>
