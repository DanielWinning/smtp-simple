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

    public function __construct(/*array $options*/)
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
        register_setting("smtp-simple-settings-group", "smtp_simple_host", array($this, "validate_host"));
        register_setting("smtp-simple-settings-group", "smtp_simple_port", array($this, "validate_port"));
        register_setting("smtp-simple-settings-group", "smtp_simple_username", array($this, "validate_username"));
        register_setting("smtp-simple-settings-group", "smtp_simple_password", array($this, "validate_password"));

        add_settings_section("smtp-simple-settings-section", "SMTP Settings", array($this, "render_settings_section"), "smtp-simple-settings-admin");
        add_settings_field("smtp-simple-host-field", "SMTP Host", array($this, "render_host_field"), "smtp-simple-settings-admin", "smtp-simple-settings-section");
        add_settings_field("smtp-simple-port-field", "SMTP Port", array($this, "render_port_field"), "smtp-simple-settings-admin", "smtp-simple-settings-section");
        add_settings_field("smtp-simple-username-field", "SMTP Username", array($this, "render_username_field"), "smtp-simple-settings-admin", "smtp-simple-settings-section");
        add_settings_field("smtp-simple-password-field", "SMTP Password", array($this, "render_password_field"), "smtp-simple-settings-admin", "smtp-simple-settings-section");
    }

    public function validate_host(): string
    {
        return sanitize_text_field($_POST["smtp_host"]);
    }

    public function validate_port(): int
    {
        return intval($_POST["smtp_port"]);
    }

    public function validate_username(): string
    {
        return sanitize_text_field($_POST["smtp_username"]);
    }

    public function validate_password(): string
    {
        return sanitize_text_field($_POST["smtp_password"]);
    }

    public function render_settings_section()
    {
        include plugin_dir_path(__FILE__) . "/templates/sections/smtp-settings-section.php";
    }

    public function render_host_field()
    {
        $host = get_option("smtp_simple_host");
        include plugin_dir_path(__FILE__) . "/templates/fields/smtp-host-field.php";
    }

    public function render_port_field()
    {
        $port = get_option("smtp_simple_port");
        include plugin_dir_path(__FILE__) . "/templates/fields/smtp-port-field.php";
    }

    public function render_username_field()
    {
        $username = get_option("smtp_simple_username");
        include plugin_dir_path(__FILE__) . "/templates/fields/smtp-username-field.php";
    }

    public function render_password_field()
    {
        $password = get_option("smtp_simple_password");
        include plugin_dir_path(__FILE__) . "/templates/fields/smtp-password-field.php";
    }
}

$SMTPSimple = new SMTPSimple();