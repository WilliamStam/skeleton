<?php

namespace System\Core;

use System\Utilities\Arrays;

class Settings {
    private $default = array();
    private $settings = array();

    function __construct(array $default_settings = array()) {
        $this->default = $default_settings;
    }

    function get($key, $default = null) {
        return Arrays::getValueByKey($key, (array)$this->toArray()) ?? $default;

    }

    function toArray(): array {
        return Arrays::merge($this->default, $this->settings);
    }

    function fromFile(string $file): Settings {
        $settings = array();
        $settingsFile = realpath($file);
        if (file_exists($settingsFile)) {
            $settings = require_once $settingsFile;
        }

//        var_dump("*CONFIG*");
        $this->settings = $settings;
        return $this;

    }

    function fromFiles(array $files = array()): Settings {
        $settings = array();
        foreach ($files as $file) {

            $settingsFile = realpath($file);
            if (file_exists($settingsFile)) {
                $settings = Arrays::merge($settings, require_once $settingsFile);
            }
        }


//        var_dump("*CONFIG*");
        $this->settings = $settings;
        return $this;

    }

}