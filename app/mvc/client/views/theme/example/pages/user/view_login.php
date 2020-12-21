<div class="grid">

    <div class="grid__item medium-up--one-half medium-up--push-one-quarter">
        <div class="content-block text-center">
            <div class="form-success hide" id="ResetSuccess">
                Мы отправили Вам письмо по электронной почте со ссылкой для обновления Вашего пароля.
            </div>
            <div id="CustomerLoginForm" class="form-vertical">
                <form method="post" action="/user/login" id="customer_login" accept-charset="UTF-8">
                    <input type="hidden" name="login" value="1"/>
                    <input type="hidden" name="csrf" value="<?= CloudStore\App\Engine\Components\Utils::generateToken() ?>"/>
                    <input type="hidden" name="utf8" value="✓">

                    <h1>Логин</h1>

                    <?php if (\CloudStore\App\Engine\Components\Request::getSession("sign_message") === "success"): ?>

                        <div class="form-success" id="ResetSuccess">
                            Регистрация прошла успешно, теперь Вы можете войти.
                        </div>

                        <?php \CloudStore\App\Engine\Components\Request::eraseFullSession('sign'); ?>
                    <?php endif; ?>

                    <?php if (\CloudStore\App\Engine\Components\Request::getSession('error_login_message') === 'error') { ?>
                        <div class="errors" id="ResetSuccess">
                            <p>Ошибка авторизации. Проверьте, пожалуйста, вводимые данные.</p>
                        </div>
                    <?php } ?>
                    <?php if (\CloudStore\App\Engine\Components\Request::getSession('success_password')) { ?>
                        <div class="form-success" id="ResetSuccess">
                            <?= \CloudStore\App\Engine\Components\Request::getSession('success_password') ?>
                        </div>
                    <?php } ?>
                    <?php \CloudStore\App\Engine\Components\Request::eraseFullSession('success'); ?>

                    <label for="CustomerEmail" class="label--hidden">Email</label>
                    <input type="email" name="login_email" id="CustomerEmail" class="" placeholder="Email"
                           autocorrect="off" autocapitalize="off" autofocus="">


                    <label for="CustomerPassword" class="label--hidden">Пароль</label>
                    <input type="password" value="" name="login_password" id="CustomerPassword" class=""
                           placeholder="Пароль">


                    <p>
                        <input type="submit" class="btn" value="Войти">
                    </p>
                    <p><a href="<?= CloudStore\App\Engine\Core\Router::getHost() ?>">Вернуться к магазину</a></p>

                    <p><a href="<?= CloudStore\App\Engine\Core\Router::getHost() ?>/user/restore" id="RecoverPassword">Забыли пароль?</a></p>


                    <p><a href="<?= CloudStore\App\Engine\Core\Router::getHost() ?>/user/signup" id="customer_register_link">Создать аккаунт</a>
                    </p>

                </form>
            </div>

        </div>


    </div>

</div>
