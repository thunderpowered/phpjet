<!-- POPUP WINDOW -->
<!--
<div class="popup_main_correct js-user-popup-start js-user_help_popup__start">
    Подсказка
</div>
<div class="js-user_help_popup">
    <div class="user_help_popup__header">
        Помощь
    </div>
    <span class="user_help_popup__close popup_main_correct-close">&#215;</span>
    <div class="user_help_popup__body">
        <ul class="user_help_popup__body-tabs">
            <li data-tab="popup_video_tab" class="popup_tab popup-active">Подсказки</li>
            <li data-tab="popup_text_tab" class="popup_tab">Помощь</li>
        </ul>
        <div id="popup_video_tab" class="user_help_popup__body-main popup_tab_block-active">
            <span class="user_help_popup__body-video-title">При загрузкe ролика возникла ошибка</span>
            <div class="user_help_popup__body-video">
                <iframe class="user_help_popup__body-youtube-frame" width="100%" height="230px" src="" frameborder="0" allowfullscreen></iframe>
            </div>
            <div class="user_help_popup__body-video-list">
                <span class="user_help_popup__body-video-list-title">Другие ролики</span>
                <ul class="user_help_popup__body-video-list-links">
<?php $videos = $this->widget->widgetPopup->videos(); ?>

<?php if ($videos): ?>

    <?php foreach ($videos as $video): ?>

                                        <li data-popup-id="<?php echo $video['popup_id']; ?>" data-popup-name="<?php echo $video['popup_name']; ?>" data-src="<?php echo $video['popup_youtube']; ?>" class="popup-link-active user_help_popup__body-video-link"><?php echo $video['popup_name']; ?></li>

    <?php endforeach; ?>

<?php endif; ?>
                </ul>
            </div>
        </div>
        <div id="popup_text_tab" class="user_help_popup__body-main"></div>
    </div>
</div>
-->

<!-- POPUP FILTER -->
<div class="js-filter-popup">Найдено: <span class="js-filter-popup-count">33</span> <a class="js-filter-popup-go">Показать</a>
</div>