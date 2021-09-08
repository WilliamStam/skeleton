<?php

namespace System\DB;

interface DBInterface {
    function connect();
    function exec($sql);

}