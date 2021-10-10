<?php

namespace System\Permissions;

class PermissionItem implements PermissionInterface {

    protected $id = "";
    protected $key = null;
    protected $label = null;
    protected $description = null;
    protected $parents = array();

    function __construct($obj) {

        $meta = PermissionHelper::getMeta($obj);

        if ($meta) {
            $this->id = $meta['id'] ?? null;
            $this->key = $meta['key'] ?? null;
            $this->label = $meta['label'] ?? null;
            $this->description = $meta['description'] ?? null;
            $this->parents = $meta['parents'] ?? array();
        }



    }

    function fromArray(
        $setup = array(
            "id" => null,
            "key" => null,
            "label" => null,
            "description" => null,
            "parents" => array(),
        )
    ): PermissionItem {
        $this->id = $setup['id'] ?? null;
        $this->key = $setup['key'] ?? null;
        $this->label = $setup['label'] ?? null;
        $this->description = $setup['description'] ?? null;
        $this->parents = $setup['parents'] ?? array();

        return $this;
    }


    function id(): string {
        return $this->id;
    }

    public function __get($key) {
        if (property_exists($this, $key)) {
            return $this->$key;
        }
        throw new \Exception("Property {$key} doesnt exist on object");
    }


}