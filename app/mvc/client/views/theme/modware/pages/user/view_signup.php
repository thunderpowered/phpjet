<div class="grid">

    <div class="grid__item medium-up--one-half medium-up--push-one-quarter">
        <div class="content-block text-center">
            <h1>Создать Аккаунт</h1>

            <div class="form-vertical text-center">
                <?php if (\CloudStore\App\Engine\Components\Request::Get('ref')) { ?>
                <form method="post" action="/user/signup?ref=<?= \CloudStore\App\Engine\Components\Request::Get('ref') ?>"
                      id="create_customer"
                      accept-charset="UTF-8">
                    <?php } else { ?>
                    <form method="post" action="/user/signup" id="create_customer" accept-charset="UTF-8">
                        <?php } ?>
                        <input type="hidden" name="signup" value="1"/>
                        <input type="hidden" name="csrf" value="<?= CloudStore\App\Engine\Components\Utils::generateToken() ?>"/>
                        <input type="hidden" name="utf8" value="✓">

                        <?php if (\CloudStore\App\Engine\Components\Request::Post('signup') AND \CloudStore\App\Engine\Components\Request::getSession('sign_message') === 'success') { ?>
                            <div class="form-success">
                                <ul>
                                    <li>
                                        Мы отправили Вам письмо по электронной почте, пожалуйста, перейдите по ссылке
                                        для подтверждения адреса электронной почты.
                                    </li>
                                </ul>
                            </div>
                        <?php } elseif (\CloudStore\App\Engine\Components\Request::Post('signup') AND \CloudStore\App\Engine\Components\Request::getSession('sign_message') === 'error') { ?>
                            <div class="errors">
                                <ul>
                                    <li>
                                        <?= \CloudStore\App\Engine\Components\Request::getSession('sign_message_text') ?>
                                    </li>
                                </ul>
                            </div>
                        <?php } ?>

                        <label for="FirstName" class="label--hidden">Имя</label>
                        <input value="<?= \CloudStore\App\Engine\Components\Request::getSession('customer_first_name') ?>"
                               type="text"
                               name="customer_first_name" id="FirstName" placeholder="Имя" autofocus="">

                        <label for="LastName" class="label--hidden">Фамилия</label>
                        <input value="<?= \CloudStore\App\Engine\Components\Request::getSession('customer_last_name') ?>"
                               type="text"
                               name="customer_last_name" id="LastName" placeholder="Фамилия">

                        <label for="Email" class="label--hidden">Email</label>
                        <input value="<?= \CloudStore\App\Engine\Components\Request::getSession('customer_email') ?>" type="email"
                               name="customer_email"
                               id="Email" class="" placeholder="Email" autocorrect="off" autocapitalize="off">

                        <label for="CreatePassword" class="label--hidden">Пароль</label>
                        <input value="<?= \CloudStore\App\Engine\Components\Request::getSession('customer_password') ?>"
                               type="password"
                               name="customer_password" id="CreatePassword" class="" placeholder="Пароль">

                        <p>
                            <input type="submit" value="Создать" class="btn">
                        </p>
                        <a href="<?= CloudStore\App\Engine\Core\Router::getHost() ?>">Вернуться к Магазину</a>

                    </form>
            </div>
        </div>
    </div>
</div>

