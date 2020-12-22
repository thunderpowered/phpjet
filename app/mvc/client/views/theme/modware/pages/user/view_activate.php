<div class="grid">

    <div class="grid__item medium-up--one-half medium-up--push-one-quarter">
        <div class="content-block text-center">

            <div id="CustomerLoginForm" class="form-vertical">

                <?php if ($activate) { ?>

                    <form method="post" action="" id="customer_login" accept-charset="UTF-8">
                        <input type="hidden" name="activate" value="1"/>
                        <input type="hidden" name="token" value="<?= \CloudStore\App\Engine\Components\Request::Get('token') ?>"/>
                        <input type="hidden" name="csrf" value="<?= CloudStore\App\Engine\Components\Utils::generateToken() ?>"/>
                        <input type="hidden" name="utf8" value="✓">


                        <h1>Завершение регистрации</h1>


                        <?php if (\CloudStore\App\Engine\Components\Request::getSession('activate_message_bad')) { ?>
                            <div class="errors" id="ResetSuccess">
                                <?= \CloudStore\App\Engine\Components\Request::getSession('activate_message_bad') ?>
                            </div>
                        <?php } ?>


                        <label for="CustomerEmail" class="label--hidden">Пароль</label>
                        <input type="password" value="" name="activate_password" id="CustomerPassword" class=""
                               placeholder="Пароль">


                        <label for="CustomerPassword" class="label--hidden">Пароль еще раз</label>
                        <input type="password" value="" name="activate_repassword" id="CustomerPassword" class=""
                               placeholder="Пароль еще раз">


                        <p>
                            <input type="submit" class="btn" value="Войти">
                        </p>
                        <p><a href="<?= CloudStore\App\Engine\Core\Router::getHost() ?>">Вернуться к магазину</a></p>

                        <p><a href="<?= CloudStore\App\Engine\Core\Router::getHost() ?>/user/restore" id="RecoverPassword">Забыли пароль?</a></p>


                        <p><a href="<?= CloudStore\App\Engine\Core\Router::getHost() ?>/user/signup" id="customer_register_link">Создать
                                аккаунт</a></p>

                    </form>

                <?php } else { ?>
                    <p>Пользователь не существует, либо уже активирован</p>
                    <p><a href="<?= CloudStore\App\Engine\Core\Router::getHost() ?>">Вернуться к магазину</a></p>
                <?php } ?>
            </div>


            <div id="RecoverPasswordForm" class="hide">

                <h2 class="h1">Сброс пароля</h2>
                <p>Мы отправим Вам ссылку для сброса пароля.</p>

                <div class="form-vertical">
                    <!--          <form method="post" action="<?= CloudStore\App\Engine\Core\Router::getHost() ?>/account/recover" accept-charset="UTF-8"><input type="hidden" value="recover_customer_password" name="form_type"><input type="hidden" name="utf8" value="✓">
        
                    
        
                    
                    
        
                    <label for="RecoverEmail" class="label--hidden">Email</label>
                    <input type="email" value="" name="email" id="RecoverEmail" placeholder="Email" autocorrect="off" autocapitalize="off">
        
                    <p>
                      <input type="submit" class="btn" value="Отправить">
                    </p>
        
                    <button type="button" id="HideRecoverPasswordLink" class="text-link link-accent-color">Отмена</button>
                  </form>-->
                </div>

            </div>

        </div>


    </div>

</div>
