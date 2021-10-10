<?php

namespace System\Permissions;

use System\Utilities\Strings;

class PermissionHelper {


    static function getMeta($obj) {
        $response = false;

        if (is_array($obj)){
            $item = $obj;
        } else {
            if (property_exists($obj, "META")) {
                $item = $obj->META;
            } else {
                if (is_object($obj)) {
                    if (defined(get_class($obj) . '::META')) {
                        $item = $obj::META;
                    }
                } else {
                    if (defined(($obj) . '::META')) {
                        $item = $obj::META;
                    }
                }

            }
        }


        if ($item) {
            $item['id'] = array(
                Strings::toAscii($item['key'])
            );
            if (!array_key_exists("parents", $item)) {
                $item['parents'] = static::getParentList($obj);
            }
            foreach ($item['parents'] as $parent) {
                $item['id'][] = Strings::toAscii($parent['key']);
            }
            $item['id'] = implode(".", array_reverse($item['id']));
        }

        return $item;
    }

    private static function getParentList($item) {
        $values = array();

        $item_parent = self::_fetchPermissionParent($item);
        if ($item_parent) {
            if (defined($item_parent . '::META')) {
                $values[] = constant($item_parent . '::META');
            }
        }
        while ($item_parent != null) {
            $item_parent = self::_fetchPermissionParent($item_parent);
            if ($item_parent) {
                if (defined($item_parent . '::META')) {
                    $values[] = constant($item_parent . '::META');
                }
            }
        }
        return $values;
    }

    private static function _fetchPermissionParent($item) {

        $parent = null;
        if ($item) {
            if (is_array($item) ){
                if (array_key_exists("parent",$item)){
                    $parent = $item['parent'];
                }
            } elseif (property_exists($item, "PARENT")) {
                $parent = $item->PARENT;
            } else {
                if (is_object($item) && defined(get_class($item) . '::PARENT')) {
                    $item = get_class($item);
                }
                if (defined($item . '::PARENT')) {
                    $parent = constant($item . '::PARENT');
                }
            }
        }
        return $parent;
    }

}