<?php

namespace System\Core;

use System\Utilities\Arrays;
use System\Utilities\Strings;


class System {
    private $hive = array();
    private $profiler;
    private $template;

    function __construct(array $hive = array(), Profiler $profiler = null, Templater $template = null) {
        $this->hive = $hive;
        $this->profiler = $profiler;
        $this->template = $template;
    }


    public function profile(string $label, string $component = "") : ProfilerItem {
        return $this->profiler->start($label,$component);
    }



    private function cut($key): array {
        return preg_split('/\[\h*[\'"]?(.+?)[\'"]?\h*\]|(->)|\./',
            $key, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
        );
    }

    public function __get($key) {
        return $this->get($key);
    }

    public function getHive(): array {
        return $this->hive;
    }

    function get($key, $default = null) {
        $parts = $this->cut($key);
        $key_base = array_shift($parts);
        $path = implode(".", $parts);

        $hive = $this->hive;

        if (array_key_exists($key, $hive)) {
            return ($hive[$key]);
        }


        if (array_key_exists($key_base, $hive)) {

            if (is_object($hive[$key_base])){

                if (method_exists($hive[$key_base],"toArray")){
                    $hive[$key_base] = $hive[$key_base]->toArray();
                } else {
                    var_dump(get_object_vars($hive[$key_base]));
                    $hive[$key_base] = get_object_vars($hive[$key_base]);
                }
            }
            return Arrays::getValueByKey($path, (array)$hive[$key_base]) ?? $default;;
        }
        return $default;

    }

    function set($key, $value): System {
        $this->hive[$key] = $value;
        return $this;
    }

    function debug(): void {
        $args = func_get_args();
        switch (func_num_args()) {
            case 0:
                exit();
                break;
            case 1:
                $args = ($args[0]);
                break;
        }


        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        $backtraceLevel = 0;

        $backtrace = $backtrace[$backtraceLevel];


        if (is_array($args)) {
            $return = array(
                "args" => $args,
                "file" => $backtrace['file'],
                "line" => $backtrace['line'],
            );

            echo json_encode($return, JSON_PRETTY_PRINT);

            exit();
        } else {

            $object = new \stdClass();
            $object->args = $args;
            $object->file = $backtrace['file'];
            $object->line = $backtrace['line'];

            ini_set("xdebug.var_display_max_children", "-1");
            ini_set("xdebug.var_display_max_data", "-1");
            ini_set("xdebug.var_display_max_depth", "-1");
            if (php_sapi_name() !== 'cli') {
                header("Content-Type: text/html");
            }

            echo "<pre>" . PHP_EOL;
            var_dump($object);
            echo "</pre>";
            exit();
        }


    }

    function isAjax() {
        if ($this->get("GET.ajax")){
            return true;
        }
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === strtolower('XMLHttpRequest');
    }

    function ip() {
        return $this->clean((isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : isset($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
    }

    function agent() {
        // TODO: implement function
    }


    function clean($input, $allowed_tags = array()) {
        // TODO: implement function

        return $input;
    }





}