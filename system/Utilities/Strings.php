<?php

namespace System\Utilities;
class Strings {

    function __construct() {

    }

    static function fixDirSlashes(string $path, $slashes = DIRECTORY_SEPARATOR): string {
        return str_replace(array(
            "/",
            "//",
            "///",
            "\\",
            "\\\\",
            "\\\\\\",
        ), $slashes, $path);
    }

    static function toAscii($str, $delimiter = '-', $replace = array()) {
        if (!empty($replace)) {
            $str = str_replace((array) $replace, ' ', $str);
        }

        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", $delimiter, $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/|+ -]+/", $delimiter, $clean);

        return $clean;
    }

    static function getMimeType($filename) {
        $idx = explode('.', $filename);
        $count_explode = count((array) $idx);
        $idx = strtolower($idx[$count_explode - 1]);

        $mimet = array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            'docx' => 'application/msword',
            'xlsx' => 'application/vnd.ms-excel',
            'pptx' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',

            // fonts
            'ttf' => 'font/ttf',
            'eot' => 'font/eot',
            'otf' => 'font/otf',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
        );

        if (isset($mimet[$idx])) {
            return $mimet[$idx];
        } else {
            return FALSE;
        }
    }

    static function url_encode($str) {
        return urlencode(base64_encode($str));
    }

    static function url_decode($str) {
        return base64_decode(urldecode($str));
    }

    static function token($length) {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet); // edited

        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[random_int(0, $max - 1)];
        }

        return $token;
    }

    static function timesince($tsmp) {
        if (!$tsmp) {
            return "";
        }
        $diffu = array(
            'seconds' => 2,
            'minutes' => 120,
            'hours' => 7200,
            'days' => 172800,
            'months' => 5259487,
            'years' => 63113851,
        );
        $diff = time() - strtotime($tsmp);
        $dt = '0 seconds ago';
        foreach ($diffu as $u => $n) {
            if ($diff > $n) {
                $dt = floor($diff / (.5 * $n)) . ' ' . $u . ' ago';
            }
        }

        return $dt;
    }
    static function secondsToHuman($inputSeconds){

        if (($inputSeconds*1) < 1){
                        return floor($inputSeconds*1000) . " ms";
        }

        $secondsInAMinute = 60;
        $secondsInAnHour = 60 * $secondsInAMinute;
        $secondsInADay = 24 * $secondsInAnHour;

        // Extract days
        $days = floor($inputSeconds / $secondsInADay);

        // Extract hours
        $hourSeconds = $inputSeconds % $secondsInADay;
        $hours = floor($hourSeconds / $secondsInAnHour);

        // Extract minutes
        $minuteSeconds = $hourSeconds % $secondsInAnHour;
        $minutes = floor($minuteSeconds / $secondsInAMinute);

        // Extract the remaining seconds
        $remainingSeconds = $minuteSeconds % $secondsInAMinute;
        $seconds = ceil($remainingSeconds);

        
       

        // Format and return
        $timeParts = [];
        $sections = [
            'day' => (int)$days,
            'hour' => (int)$hours,
            'minute' => (int)$minutes,
            'second' => (int)$seconds,
        ];
    

        foreach ($sections as $name => $value){
            if ($value > 0){
                $timeParts[] = $value. ' '.$name.($value == 1 ? '' : 's');
            }
        }

        return implode(', ', $timeParts);
    }

    static function stringToColorCode($str) {
        if (!$str) {
            return "cccccc";
        }

        $code = dechex(crc32($str));
        $code = substr($code, 0, 6);

        if (!$code) {
            $code = "cccccc";
        }

        return $code;
    }
    static function maskString($str){
        if (strlen($str)>2){
            $mask = substr($str,0, 2) . str_repeat("*", strlen($str)-2);
        } else {
            $mask = str_repeat("*", strlen($str));
        }
        
        return $mask;
    }

    static function fixJSON($json) {
        $regex = <<<'REGEX'
~
    "[^"\\]*(?:\\.|[^"\\]*)*"
    (*SKIP)(*F)
  | '([^'\\]*(?:\\.|[^'\\]*)*)'
~x
REGEX;

        return preg_replace_callback($regex, function($matches) {
            return '"' . preg_replace('~\\\\.(*SKIP)(*F)|"~', '\\"', $matches[1]) . '"';
        }, $json);
    }

}