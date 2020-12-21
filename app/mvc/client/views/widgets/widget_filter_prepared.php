<?php if (!empty($prepared_filters)): ?>

    <div style="padding: 20px 5px;" class="filter__fast-links">

        <?php foreach ($prepared_filters as $prepared_filter): ?>

            <a style="display: inline-block;padding: 3px 10px;border-radius: 14px;border: 1px solid #eaeaea;margin-right: 10px;text-decoration: none !important;"
               class="filter__fast-link"
               href="<?php echo \CloudStore\App\Engine\Core\Router::getHost(); ?>/filter/<?php echo $prepared_filter['filter_link']; ?>"
               title="<?php echo $prepared_filter['filter_name']; ?>"><?php echo $prepared_filter['filter_name']; ?></a>

        <?php endforeach; ?>

    </div>

<?php endif; ?>