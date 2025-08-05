<div class="container">
    <h2><?php _e('home_index_title'); ?></h2>
    <div class="company">
        <div class="company_title">Компании</div>
        <?php if(isset($companies)): ?>
        <?php foreach ($companies as $company): ?>
            <div id="company_<?= $company['id']?>" company_id="<?= $company['id']?>">
                <h2><a href="<?= base_url_check_admin('/company/' . $company['id']) ?>"><?= htmlspecialchars($company['name']) ?></a></h2>
                <?php if ($company['logo']): ?>
                    <img src="<?= '/photodata/companies/' . $company['logo'] ?>" width="100">
                <?php endif; ?>
                <p><?= mb_substr(htmlspecialchars($company['description']), 0, 100, 'UTF-8') ?>...</p>
                <?php if (check_auth()): ?> <!-- Админ && is_admin() -->
                    <button type="button" class="delete-button">Удалить компанию и все отзывы о ней</button>
                    <div class="success_block"></div>
                    <div class="error_block"></div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <?php endif; ?>

        <?php if(isset($pagination)): ?>
        <!-- Пагинация -->
        <div class="pagination">
            <?= $pagination ?>
        </div>
        <?php endif; ?>

    </div>
</div>