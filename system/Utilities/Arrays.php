<?php

namespace System\Utilities;

class Arrays {


    public static function merge($a, $b) {
        $args = func_get_args();
        $res = array_shift($args);
        while (!empty($args)) {
            foreach (array_shift($args) as $k => $v) {
                if (is_int($k)) {
                    if (array_key_exists($k, $res)) {
                        $res[] = $v;
                    } else {
                        $res[$k] = $v;
                    }
                } else {
                    if (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
                        $res[$k] = self::merge($res[$k], $v);
                    } else {
                        $res[$k] = $v;
                    }
                }
            }
        }

        return $res;
    }

    public static function replace() {
        $args = func_get_args();
        $return = array_shift($args);

        foreach ($args as $arg) {
            $return = self::_replace($return, $arg);
        }

        return $return;

    }

    private static function _replace($source, $replace) {

        $return = array();

        foreach ($source as $k => $v) {
            if (isset($replace[$k])) {
                if (is_array($v)) {
                    if (count(array_filter(array_keys($v), 'is_string')) > 0) {
                        $v = self::replace($v, $replace[$k]);
                    } else {
                        $v = $replace[$k];
                    }

                } else {
                    $v = $replace[$k];
                }

            }
            $return[$k] = $v;
        }

        return $return;
    }

    static function removeKeyFromArray(&$array, $key_to_remove) {
        foreach ($array as $key => &$value) {
            if ($key === $key_to_remove) {
                unset($array[$key]);
            } elseif (is_array($value)) {
                self::removeKeyFromArray($value, $key_to_remove);
            }
        }
    }

    static function getValueByKey($key, array $data, $default = null) {
        // @assert $key is a non-empty string
        // @assert $data is a loop-able array
        // @otherwise return $default value
        if (!is_string($key) || empty($key) || !count($data)) {
            return $default;
        }

        // @assert $key contains a dot notated string
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);

            foreach ($keys as $innerKey) {
                // @assert $data[$innerKey] is available to continue
                // @otherwise return $default value
                if (!array_key_exists($innerKey, $data)) {
                    return $default;
                }

                $data = $data[$innerKey];
            }

            return $data;
        }

        // @fallback returning value of $key in $data or $default value
        return array_key_exists($key, $data) ? $data[$key] : $default;
    }

    static function hierarchy(array $array, $idKeyName = 'id', $parentIdKey = 'parent_id', $childNodesField = 'children') {
        $indexed = array();
        // first pass - get the array indexed by the primary id
        foreach ($array as $row) {
            $indexed[$row[$idKeyName]] = $row;
            $indexed[$row[$idKeyName]][$childNodesField] = array();
        }
        // second pass
        $root = array();
        foreach ($indexed as $id => $row) {


            $indexed[$row[$parentIdKey]][$childNodesField][$id] = &$indexed[$id];


            if (!$row[$parentIdKey]) {

                $root[$id] = &$indexed[$id];

            }
        }
        return $root;
    }
    static function hierarchyCounter($arr,$countField="counter",$countNodes="items",$childNodesField="children") {
        $result = array();

        foreach ($arr as $item) {
            if ($countNodes){
                $item[$countField] = count($item[$countNodes]);
            }

            $item[$childNodesField] = self::hierarchyCounter($item[$childNodesField],$countField,$countNodes,$childNodesField);

            foreach ($item[$childNodesField] as $c) {
                $item[$countField] = $item[$countField] + $c[$countField];
            }

            $result[] = $item;
        }

        return $result;
    }


}