<?php
namespace System\Schema;

interface SchemaInterface {
    function load($item);
    function item();
    function __invoke();
}