<div class="container">

    <h1><?= $title ?? ''; ?></h1>

    <div class="row">

        <div class="col-md-6 offset-md-3">

            <div class="register_company">
                <form action="<?= base_url_check_admin('/register/company'); ?>" method="post" class="ajax-form" enctype="multipart/form-data">

                    <?= get_csrf_field(); ?>

                    <div class="mb-3">Компания</div>

                    <div class="mb-3">
                        <label for="name" class="form-label"><?php _e('user_register_company_name'); ?></label>
                        <input name="name" type="text" class="form-control <?= get_validation_class('name'); ?>" id="name" placeholder="Name" value="<?= old('name'); ?>">
                        <?= get_errors('name'); ?>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label"><?php _e('user_register_company_description'); ?></label>
                        <input name="description" type="text" class="form-control <?= get_validation_class('description'); ?>" id="description" placeholder="description" value="<?= old('description'); ?>">
                        <?= get_errors('description'); ?>
                    </div>

                    <div class="mb-3">
                        <label for="logo" class="form-label"><?php _e('user_register_company_logo'); ?></label>
                        <input name="logo" type="file" class="form-control <?= get_validation_class('logo'); ?>" id="logo" placeholder="logo"> 
                        <!-- Надо ли добавлять old? -->
                        <?= get_errors('logo'); ?>
                    </div>save

                    <button type="submit" class="btn btn-warning"><?php _e('user_register_btn_company'); ?></button>
                </form>  
            </div>

            <?php
            session()->remove('form_data');
            session()->remove('form_errors');
            ?>

        </div>

    </div>

</div>
