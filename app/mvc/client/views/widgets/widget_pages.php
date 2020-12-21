<?php if ($pages): ?>

    <ul class="footer__list">

        <?php foreach ($pages as $page): ?>

            <li class="footer__list-item"><a
                        href="<?php echo \CloudStore\App\Engine\Core\Router::getHost(); ?>/pages/<?php echo $page['pages_handle']; ?>"><?php echo $page['pages_title']; ?></a>
            </li>

        <?php endforeach; ?>

    </ul>

<?php endif; ?>