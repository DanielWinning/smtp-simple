<div>
    <?php

    if ($_POST) {
        var_dump($_POST);
    }

    ?>
    <h1>
        SMTP Simple
    </h1>
    <form method="post" action="<?= site_url(); ?>/wp-admin/options.php" enctype="multipart/form-data">
        <?php
        settings_fields("smtp-simple-settings-group");
        do_settings_sections("smtp-simple-settings-admin");
        submit_button();
        ?>
    </form>
</div>