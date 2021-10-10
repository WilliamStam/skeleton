<?php
declare (strict_types = 1);
namespace System\Helpers;


class Pagination {
    protected $records_per_page;
    protected $page_blocks ;
    protected $record_count;
    protected $page = 0;

    protected $DATA = array(
        "page"=>null,
        "limit"=>null,
        "current"=>null,
        "previous"=>null,
        "next"=>null,
        "last"=>null,
        "info"=>null,
        "records"=>null,
    );


    function __construct($records_per_page=10, $page_blocks=9) {
        $this->records_per_page = $records_per_page;
        $this->page_blocks = $page_blocks;
    }
    function __invoke($record_count=0,$page=1){
        $this->record_count = $record_count;
        $this->page = $page;


        return $this;
    }


    public function __get($key) {
        if (array_key_exists($key,(array)$this->DATA)){
            return $this->DATA[$key];
        }
        if (property_exists($this,$key)){
            return $this->$key;
        }
        return  null ;
    }


    public function calculate($total_rows, $page_num) {
        $arr = array();
        $arr['page'] = $page_num;

        // calculate last page

        $last_page = ceil($total_rows / $this->records_per_page);
        // make sure we are within limits
        $page_num = (int) $page_num;
        if ($page_num < 1) {
            $page_num = 1;
        } elseif ($page_num > $last_page) {
            $page_num = $last_page;
        }
        $upto = ($page_num - 1) * $this->records_per_page;
        if ($upto < 0) {
            $upto = 0;
        }

        $arr['offset'] = $upto ;
        $arr['limit'] = '' . $upto . ',' . $this->records_per_page;

        $arr['current'] = $page_num;
        if ($page_num == 1) {
            $arr['previous'] = $page_num;
        } else {
            $arr['previous'] = $page_num - 1;
        }

        if ($page_num == $last_page) {
            $arr['next'] = $last_page;
        } else {
            $arr['next'] = $page_num + 1;
        }

        $arr['last'] = $last_page;
        $arr['info'] = 'Page (' . $page_num . ' of ' . $last_page . ')';
        $arr['records'] = $total_rows;

        $arr['pages'] = $this->getSurroundingPages($this->page,$last_page,$arr['next'],$this->page_blocks);

        $this->DATA = $arr;


        return $this;
    }

    function result(){
        return $this->DATA;
    }

    private function getSurroundingPages($page_num, $last_page, $next, $amountofblocks) {
        $arr = array();
        $show = $amountofblocks; // how many boxes
        // at first
        if ($page_num == 1) {
            // case of 1 page only
            if ($next == $page_num) {
                return array(1);
            }

            for ($i = 0; $i < $show; $i++) {
                if ($i == $last_page) {
                    break;
                }

                $arr[] = $i + 1;
            }
            return $arr;
        }
        // at last
        if ($page_num == $last_page) {
            $start = $last_page - $show;
            if ($start < 1) {
                $start = 0;
            }

            for ($i = $start; $i < $last_page; $i++) {
                $arr[] =  $i + 1;
            }
            return $arr;
        }

        // at middle
        $start = $page_num - $show / 2;
        if ($start < 1) {
            $start = 0;
        }

        if ($last_page - $page_num == 1) {
            if (floor($start) > 0) {
                $arr[] = floor($start);
            }

        }
        for ($i = $start; $i < $page_num; $i++) {
            if (floor($i + 1) > 0) {
                $arr[] = floor($i + 1);
            }

        }

        for ($i = ($page_num + 1); $i < ($page_num + $show / 2 + 1); $i++) {
            if ($i == ($last_page + 1)) {
                break;
            }

            $arr[] = floor($i);
        }

        $a = array();
        foreach ($arr as $item) {
            if (count($a) < $show) {
                $a[] = $item;
            }

        }
        $arr = $a;

        $max = ($show - $last_page > 0) ? $last_page : $show;
        $sides = floor(($show - 1) / 2);

        if ($page_num > $last_page - floor(($show - 1) / 2)) {
            /*$arr['bingo_last']= array(
            "side" => $sides,
            "max"  => $max,
            "count"=> count($arr),
            "diff" => $max - count($arr)
            );*/
            $add = array();

            $g = 0;
            for ($i = $arr[0] - 1; $i >= 1; $i--) {
                if ($g++ < $max - count($arr)) {
                    $add[] = floor($i);
                }

            }
            $add = array_reverse($add);
            $bleh = array();
            //$arr['bingo_last']['add'] = $add;
            foreach ($add as $item) {
                $bleh[] = $item;
            }

            foreach ($arr as $item) {
                $bleh[] = $item;
            }

//test_array($bleh);
            $arr = $bleh;
        }
        if ($page_num < floor(($show - 1) / 2)) {
/*
$arr['bingo_first'] = array(
"side" => $sides,
"max"  => $max,
"count"=> count($arr),
"diff" => $max - count($arr)
);*/
            for ($i = count($arr) + 1; $i <= $max; $i++) {
                $arr[] = floor($i);
            }

        }

        //test_array($a);
        return $arr;
    }

    function pages(){
        return $this->DATA['pages'] ?? array();
    }


}