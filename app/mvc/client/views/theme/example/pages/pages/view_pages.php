<h1 class="small--text-center"><?= $content['pages_title'] ?></h1>

<div class="grid">
    <div class="grid__item">
        <div class="row">
            <div class="full textForAffixMenu article rte content-block content-block--large">

                <?= $content['pages_body'] ?>

            </div>
        </div>

        <?php if (CloudStore\App\Engine\Core\System::getControllerObject()::GetForm()) { ?>

            <div class="content-block content-block--large">
                <div class="contact-form form-vertical">
                    <form method="post" action="" id="contact_form" class="contact-form" accept-charset="UTF-8"><input
                                type="hidden" value="contact" name="form_type"><input type="hidden" name="utf8"
                                                                                      value="✓">
                        <?php if (\CloudStore\App\Engine\Components\Request::Post('contact') AND \CloudStore\App\Engine\Components\Request::getSession('contact_message') === 'success') { ?>
                            <p class="form-success"> Спасибо, что связались с нами. Мы ответим Вам в ближайшее
                                время.</p>
                        <?php } elseif (\CloudStore\App\Engine\Components\Request::Post('contact') AND \CloudStore\App\Engine\Components\Request::getSession('contact_message') === 'error') { ?>
                            <div class="errors">
                                <ul>
                                    <li>Все поля должны быть заполнены.</li>
                                </ul>
                            </div>
                        <?php } ?>
                        <div class="grid grid--half-gutters">
                            <div class="grid__item medium-up--one-half">
                                <label for="ContactFormName" class="label--hidden">Имя</label>
                                <input type="text" id="ContactFormName" name="contact_name" placeholder="Имя" value="">
                            </div>
                            <div class="grid__item medium-up--one-half">
                                <label for="ContactFormEmail" class="label--hidden">Email</label>
                                <input type="email" id="ContactFormEmail" name="contact_email" placeholder="Email"
                                       autocorrect="off" autocapitalize="off" value="">
                            </div>
                        </div>

                        <label for="ContactFormPhone" class="label--hidden">Номер телефона</label>
                        <input type="tel" id="ContactFormPhone" name="contact_phone" placeholder="Номер телефона"
                               pattern="[0-9\-]*" value="">

                        <label for="ContactFormMessage" class="label--hidden">Сообщение</label>
                        <textarea rows="10" id="ContactFormMessage" name="contact_body"
                                  placeholder="Сообщение"></textarea>

                        <input type="hidden" value="1" name="contact"/>
                        <input type="hidden" name="csrf" value="<?= CloudStore\App\Engine\Components\Utils::generateToken() ?>"/>
                        <input type="submit" class="btn" value="Отправить">
                    </form>
                </div>
            </div>

        <?php } ?>
        <!--    <div class="einstein_helper">Нашли ошибку в тексте? Выделите ее и нажмите CTRL+ENTER</div>-->
    </div>

</div>