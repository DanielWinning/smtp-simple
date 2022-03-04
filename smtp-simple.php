<?php

/*
Plugin Name: SMTP Simple
Plugin URI: https://dannywinning.co.uk
Description: A simple SMTP plugin for WordPress.
Version: 1.0.0
Author: Danny Winning
Author URI: https://dannywinning.co.uk
License: GPLv2 or later
Text Domain: smtp-simple
*/

if (!defined("ABSPATH")) {
    exit;
}

class SMTPSimple
{
    public string $host;
    public int $port;
    public string $username;
    public string $password;

    public function __construct()
    {
        $this->host = get_option("smtp_simple_host") ?? "";
        $this->port = get_option("smtp_simple_port") ?? 2525;
        $this->username = get_option("smtp_simple_username") ?? "";
        $this->password = get_option("smtp_simple_password") ?? "";

        add_action("phpmailer_init", array($this, "phpmailer_init"));
        add_action("admin_menu", array($this, "add_options_page"));
        add_action("admin_init", array($this, "register_settings"));
    }

    public function phpmailer_init($phpmailer)
    {
        $phpmailer->isSMTP();
        $phpmailer->Host = $this->host;
        $phpmailer->SMTPAuth = true;
        $phpmailer->Port = $this->port;
        $phpmailer->Username = $this->username;
        $phpmailer->Password = $this->password;
    }

    public function render_settings_page()
    {
        include plugin_dir_path(__FILE__) . "/templates/settings.php";
    }

    public function add_options_page()
    {
        add_menu_page("SMTP Simple Settings", "SMTP", "manage_options", "smtp-simple-settings-admin", array($this, "render_settings_page"), "dashicons-email-alt2", 99);
    }

    public function register_settings()
    {
        register_setting("smtp-simple-settings-group", "smtp_simple_host", function() {
            return $this->validate_field("smtp_host");
        });
        register_setting("smtp-simple-settings-group", "smtp_simple_port", function() {
            return $this->validate_field("smtp_port");
        });
        register_setting("smtp-simple-settings-group", "smtp_simple_username", function() {
            return $this->validate_field("smtp_username");
        });
        register_setting("smtp-simple-settings-group", "smtp_simple_password", function() {
            return $this->validate_field("smtp_password");
        });

        add_settings_section("smtp-simple-settings-section", "SMTP Settings", array($this, "render_settings_section"), "smtp-simple-settings-admin");
        add_settings_field("smtp-simple-host-field", "SMTP Host", function() {
            $this->render_field("smtp_simple_host");
        }, "smtp-simple-settings-admin", "smtp-simple-settings-section");
        add_settings_field("smtp-simple-port-field", "SMTP Port", function() {
            $this->render_field("smtp_simple_port");
        }, "smtp-simple-settings-admin", "smtp-simple-settings-section");
        add_settings_field("smtp-simple-username-field", "SMTP Username", function() {
            $this->render_field("smtp_simple_username");
        }, "smtp-simple-settings-admin", "smtp-simple-settings-section");
        add_settings_field("smtp-simple-password-field", "SMTP Password", function () {
            $this->render_field("smtp_simple_password");
        }, "smtp-simple-settings-admin", "smtp-simple-settings-section");
    }

    public function validate_field(string $field_name)
    {
        if (isset($_POST[$field_name])) {
            if ($field_name === "smtp_port") {
                return filter_var($_POST[$field_name], FILTER_VALIDATE_INT);
            }

            return filter_var($_POST[$field_name], FILTER_SANITIZE_STRING);
        } else {
            return "";
        }
    }

    public function render_settings_section()
    {
        include plugin_dir_path(__FILE__) . "/templates/sections/smtp-settings-section.html";
    }

    public function render_field(string $field_name)
    {
        $field_value = explode("_", $field_name);
        $field_value = end($field_value);
        $$field_value = get_option($field_name);
        include plugin_dir_path(__FILE__) . "/templates/fields/smtp-" . $field_value . "-field.php";
    }
}

new SMTPSimple();