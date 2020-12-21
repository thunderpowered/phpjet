<ul class="list--inline social-icons widget__social">

    <?php if (!empty($social)): ?>

        <?php foreach ($social as $item): ?>

            <?php
            $name = unserialize(base64_decode($item['settings_value']))['name'] ?? null;

            $icon = unserialize(base64_decode($item['settings_value']))['icon'] ?? null;

            if (empty($name))
                continue;

            ?>

            <li><a href="<?php echo $name; ?>"><i class="fa <?php echo $icon; ?>" aria-hidden="true"></i></a></li>

        <?php endforeach; ?>

    <?php endif; ?>

</ul>