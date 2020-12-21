<div class="myaccount_body">
    <h1>Профиль</h1>
    <?php if (\CloudStore\App\Engine\Components\Request::getSession('error_user_success') AND !\CloudStore\App\Engine\Components\Request::getSession('error_password')) { ?>
        <div class="form-success" id="ResetSuccess">
            <p>Все данные сохранены.</p>
        </div>
    <?php } elseif (\CloudStore\App\Engine\Components\Request::getSession('error_password')) { ?>
        <div class="errors" id="ResetSuccess">
            <p>Пароль не был сохранён. Информация ниже.</p>
        </div>
    <?php } ?>
    <form action="" method="post">
        <div class="myaccount_first">
            <div class="myaccount_first_left left">
                <h4>Личная информация</h4>
                <p>Идентификатор пользователя: <?= $user['users_id'] ?></p>
                <ul>
                    <li>
                        <label for="myaccount_name">Имя*</label>
                        <input value="<?= $user['users_name'] ?>" placeholder="Имя" id="myaccount_name" type="text"
                               name="myaccount_name"/>
                        <p style="color:red"
                           class="field__message--error"><?= \CloudStore\App\Engine\Components\Request::getSession('error_account_name') ?></p>
                    </li>
                    <li>
                        <label for="myaccount_patr">Отчество</label>
                        <input value="<?= $user['users_patronymic'] ?>" placeholder="Отчество" id="myaccount_patr"
                               type="text" name="myaccount_patr"/>
                    </li>
                    <li>
                        <label for="myaccount_name">Фамилия</label>
                        <input value="<?= $user['users_last_name'] ?>" placeholder="Фамилия" id="myaccount_name"
                               type="text" name="myaccount_last_name"/>
                    </li>
                    <li>
                        <div class="myaccount_first_left_left left">
                            <label for="myaccount_gender">Ваш пол</label>
                            <select id="myaccount_gender" name="myaccount_gender">
                                <?php $male = $user['users_gender'] === 'm' ? "selected" : "" ?>
                                <?php $female = $user['users_gender'] === 'w' ? "selected" : "" ?>
                                <option <?= $male ?> value="m">Мужской</option>
                                <option <?= $female ?> value="w">Женский</option>
                            </select>
                        </div>
                        <div class="myaccount_first_left_right right">
                            <label for="myaccount_name">Дата рождения</label>
                            <input value="<?= $user['users_date_of_birth'] ?>" id="myaccount_date" type="date"
                                   name="myaccount_date"/>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="myaccount_first_right right">
                <h4>Информация</h4>
                <div class="myaccount_first_left_image">
                    <ul>
                        <li><a href="/user/orders">Мои заказы</a></li>
                        <li><a href="/user/addresses">Мои адреса</a></li>
                        <li><a href="/user/invite">Пригласить пользователя (пригласил <?= $user['users_invited'] ?>)</a>
                        </li>
                        <li>Баллы: <?= $user['users_points'] ?></li>
                    </ul>
                </div>
                <div class="myaccount_first_left_select"></div>
            </div>
            <div class="clear"></div>
        </div>
        <div class="myaccount_second">
            <div class="myaccount_second_left left">
                <h4>Контактная информация</h3>
                    <ul>
                        <li>
                            <label for="myaccount_email">Электронная почта*</label>
                            <input value="<?= $user['users_email'] ?>" type="text" name="myaccount_email"
                                   placeholder="Электронная почта"/>
                            <p style="color:red"
                               class="field__message--error"><?= \CloudStore\App\Engine\Components\Request::getSession('error_account_email') ?></p>
                        </li>
                        <li>
                            <label for="myaccount_phone">Мобильный телефон</label>
                            <input value="<?= $user['users_phone'] ?>" type="text" name="myaccount_phone"
                                   placeholder="Мобильный телефон"/>
                            <p style="color:red"
                               class="field__message--error"><?= \CloudStore\App\Engine\Components\Request::getSession('error_account_phone') ?></p>
                        </li>
                        <li>
                            <label for="myaccount_act_phone">Телефон для связи</label>
                            <input value="<?= $user['users_act_phone'] ?>" type="text" name="myaccount_act_phone"
                                   placeholder="Телефон для связи"/>
                            <p style="color:red"
                               class="field__message--error"><?= \CloudStore\App\Engine\Components\Request::getSession('error_account_act_phone') ?></p>
                        </li>
                    </ul>
            </div>
            <div class="myaccount_second_right right">
                <h4>Изменение пароля</h3>
                    <ul>
                        <li>
                            <label for="myaccount_old_password">Старый пароль</label>
                            <input type="password" name="myaccount_old_password" placeholder="Старый пароль"/>
                            <p style="color:red"
                               class="field__message--error"><?= \CloudStore\App\Engine\Components\Request::getSession('error_account_old_password') ?></p>
                            <p style="color:green"
                               class="field__message--error"><?= \CloudStore\App\Engine\Components\Request::getSession('error_account_new_repassword_success') ?></p>
                        </li>
                        <li>
                            <label for="myaccount_new_password">Новый пароль</label>
                            <input type="password" name="myaccount_new_password" placeholder="Новый пароль"/>
                            <p style="color:red"
                               class="field__message--error"><?= \CloudStore\App\Engine\Components\Request::getSession('error_account_new_password') ?></p>
                        </li>
                        <li>
                            <label for="myaccount_new_repassword">Подтвердите пароль</label>
                            <input type="password" name="myaccount_new_repassword" placeholder="Подтвердите пароль"/>
                            <p style="color:red"
                               class="field__message--error"><?= \CloudStore\App\Engine\Components\Request::getSession('error_account_new_repassword') ?></p>
                        </li>
                    </ul>
            </div>
            <div class="clear"></div>
        </div>
        <p>* - обязательные поля</p>
        <div class="submit">
            <input type="submit" class="btn" value="Сохранить информацию"/>
        </div>
        <input type="hidden" name="user_account_change" value="1"/>
        <input type="hidden" name="csrf" value="<?= CloudStore\App\Engine\Components\Utils::generateToken() ?>"/>
    </form>
</div>

