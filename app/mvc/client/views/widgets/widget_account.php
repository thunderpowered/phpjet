<ul class="header__overhead-links ul--horizontal">
    <?php if (\CloudStore\App\Engine\Components\Request::getSession("user_is_logged")): ?>
        <li>
            <a class="header__overhead-account" href="<?php echo \CloudStore\App\Engine\Core\Router::getHost(); ?>/user/login"><i
                        class="fa fa-user"
                        aria-hidden="true"></i>
                Аккаунт</a>
        </li>
        <li>
            <a class="header__overhead-register"
               href="<?php echo \CloudStore\App\Engine\Core\Router::getHost(); ?>/user/logout"><i
                        class="fa fa-power-off" aria-hidden="true"></i> Выйти</a>
        </li>
    <?php else: ?>
        <li>
            <a class="header__overhead-account" href="<?php echo \CloudStore\App\Engine\Core\Router::getHost(); ?>/user/login"><i
                        class="fa fa-user"
                        aria-hidden="true"></i>
                Войти</a>
        </li>
        <li>
            <a class="header__overhead-register"
               href="<?php echo \CloudStore\App\Engine\Core\Router::getHost(); ?>/user/signup"><i
                        class="fa fa-user-plus" aria-hidden="true"></i> Регистрация</a>
        </li>
    <?php endif; ?>
</ul>