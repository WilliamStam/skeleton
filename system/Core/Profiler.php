<?php

namespace System\Core;

use Config;
use System\Helpers\Collection;
class Profiler extends Collection {


    function start(string $label, string $component = ""): ProfilerItem {
        return $this->add((new ProfilerItem($label, $component)));
    }

    function getTotalTime() {
        $start = $this->first()->getTimeStart();
        $end = $this->last()->getTimeEnd();

        return ($end - $start) * 1000;
    }
    function getTotalMemory() {
        $start = $this->first()->getMemoryStart();
        $end = $this->last()->getMemoryEnd();

        return ($end - $start);
    }

    function toArray() : array {
        $total_time = $this->getTotalTime();
        $total_memory = $this->getTotalMemory();
        $return = array();

        $start = null;
        $end = null;

        foreach ($this as $item) {

            if ($item->getTimeStart() < $start || $start == 0){
                $start = $item->getTimeStart();
            }
            if ($item->getTimeEnd() > $end || $end == 0){
                $end = $item->getTimeEnd();
            }
        }
        $total = $end - $start;


        foreach ($this as $item) {
            /**
             * @var $item ProfilerItem
             */
            $return_item = array(
                "label" => $item->getLabel(),
                "component" => $item->getComponent(),
                "time" => array(
                    "start" => $item->getTimeStart(),
                    "end" => $item->getTimeEnd(),
                    "total" => $item->getTime(),
                    "offset" => number_format((($item->getTimeStart() - $start) / $total) * 100, 2),
                    "percent" => number_format(($item->getTime() / $total_time) * 100, 2)
                ),
                "memory" => array(
                    "start" => $item->getMemoryStart(),
                    "end" => $item->getMemoryEnd(),
                    "total" => $item->getMemory(),
                    "percent" => number_format(($item->getMemory() / $total_memory) * 100, 2)
                ),
                "data"=>$item->getData()
            );

//            var_dump($return_item);
            if (floatval($return_item['time']['offset']) + floatval($return_item['time']['percent']) > 100){
                $return_item['time']['offset'] = 100 - floatval($return_item['time']['percent']);
            }

            $return_item['key'] = md5(json_encode($return_item));

            $return[] = $return_item;
        }
        return $return;
    }


}


class ProfilerItem {
    protected $time_start;
    protected $time_end;
    protected $mem_start;
    protected $mem_end;
    protected $label;
    protected $component;
    protected $data;

    function __construct($label = null, $component = null) {
        $this->setlabel($label);
        $this->setcomponent($component);

        $this->time_start = $this->_time();
        $this->mem_start = $this->_memory();
    }

    function setLabel($label) {
        $this->label = $label;
        return $this;
    }

    function setComponent($component) {
        $this->component = $component;
    }

    function getComponent() {
        return $this->component;
    }
    function setData() {

        $this->data = func_get_args();
        return $this;
    }
    function getData() {
        return $this->data;
    }


    function _time() {
        return (double)microtime(TRUE);
    }

    function _memory() {
        return (double)memory_get_usage();
    }

    function stop($label = null) {
        if ($label) {
            $this->setlabel($label);
        }
        $this->time_end = $this->_time();
        $this->mem_end = $this->_memory();

        return $this;

    }

    function getlabel() {
        return $this->label;
    }

    function getTimeStart() {
        return $this->time_start;
    }

    function getTimeEnd() {
        return $this->time_end;
    }

    function getTime() {
        return ($this->getTimeEnd() - $this->getTimeStart()) * 1000;
    }

    function getMemoryStart() {
        return $this->mem_start;
    }

    function getMemoryEnd() {
        return $this->mem_end;
    }

    function getMemory() {
        return $this->getMemoryEnd() - $this->getMemoryStart();
    }


}