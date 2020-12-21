<h1>Пригласить друга</h1>
<div class="invite-body">
    <form method="post" action="">
        <div class="invite_block">
            <?php if ($error) { ?>
                <div class="errors">
                    <ul>
                        <li>
                            Произошла неизвестная ошибка. Попробуйте, пожалуйста, позже.
                        </li>
                    </ul>
                </div>
            <?php } ?>
            <?php if (\CloudStore\App\Engine\Components\Request::getSession('error_success_email')) { ?>
                <div class="form-success">
                    <ul>
                        <li>
                            <?= \CloudStore\App\Engine\Components\Request::getSession('error_success_email') ?>
                        </li>
                    </ul>
                </div>
            <?php } ?>
            <?php if (\CloudStore\App\Engine\Components\Request::getSession('error_email')) { ?>
                <div class="errors">
                    <ul>
                        <li>
                            <?= \CloudStore\App\Engine\Components\Request::getSession('error_email') ?>
                        </li>
                    </ul>
                </div>
            <?php } ?>
            <ul>
                <li>
                    <label for="invite_email">E-Mail пользователя</label>
                    <input value="<?= \CloudStore\App\Engine\Components\Request::getSession('invite_email') ?>" id="invite_email"
                           type="text"
                           name="invite_email" placeholder="Email пользователя"/>
                </li>
                <li>
                    <label for="invite_text">Сообщение пользователю</label>
                    <textarea id="invite_text"
                              name="invite_text"><?= \CloudStore\App\Engine\Components\Request::getSession('invite_text') ?></textarea>
                </li>
                <li>
                    <input type="submit" class="btn" name="invite_submit" value="Отправить приглашение"/>
                </li>
            </ul>
        </div>
        <input type="hidden" name="csrf" value="<?= CloudStore\App\Engine\Components\Utils::generateToken() ?>"/>
        <input type="hidden" name="invite" value="1"/>
    </form>
</div>
