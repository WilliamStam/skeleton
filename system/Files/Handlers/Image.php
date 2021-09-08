<?php

namespace System\Files\Handlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use System\Exceptions\ResourceNotFound;
use Intervention\Image\ImageManager;
use System\Files\AbstractFiles;
use System\Files\FilesInterface;

class Image extends AbstractFiles implements FilesInterface {
    protected $img;

    protected $height=null;
    protected $width=null;
    protected $crop=null;

    protected $file_extensions = array(
        "jpg"=>"image/jpeg",
        "jpeg"=>"image/jpeg",
        "png"=>"image/png",
        "gif"=>"image/gif",
        "ico"=>"image/vnd.microsoft.icon",
    );



    protected $content_type = "image/jpg";

    function handle(ServerRequestInterface $request,ResponseInterface $response,$path) : ResponseInterface {

        $manager = new ImageManager(array('driver' => 'GD'));
        $this->options();
        $this->etag = md5($this->width."|".$this->height."|".$path);




        $this->img = $manager->make($path);
        $this->content_type = $this->img->mime();



        if ($this->height && $this->width){
            $this->img->fit($this->width, $this->height);
        } else {
            if ($this->height || $this->width){
                $this->img->resize($this->width, $this->height, function ($constraint) {
                    $constraint->aspectRatio();
                });
            }

        }



        $response = $response->withHeader("Content-type",$this->getContentType($path));
        foreach ($this->getHeaders() as $header=>$value){
            $response = $response->withHeader($header,$value);
        }
        return $response->withBody($this->img->stream());
    }
//
//    function process() {
//        $manager = new ImageManager(array('driver' => 'GD'));
//        $this->img = $manager->make($this->path);
//
//        $this->options();
//        $this->etag = md5($this->width."|".$this->height."|".$this->path);
//
//        $this->contentType = $this->img->mime();
//        $this->headers["Content-type"] = $this->contentType;
//
//    }
//
//    function action(){
//        $this->options();
//
//        if ($this->height && $this->width){
//            $this->img->fit($this->width, $this->height);
//        } else {
//             $this->img->resize($this->width, $this->height, function ($constraint) {
//                $constraint->aspectRatio();
//            });
//        }
//
//    }
//
//    function getBody() {
//        $this->action();
//        return $this->img->response();
//    }
//
//    function getBodyStream() {
//        $this->action();
//
//        return $this->img->stream();
//    }
//
    function options(): Image {


        $height = $_GET['height'] ?? $_GET['h'] ?? null;
        if ($height) {
            $height = $height * 1;
            $this->height = $height;
        }

        $width = $_GET['width'] ?? $_GET['w'] ?? null;
        if ($width) {
            $width = $width * 1;
            $this->width = $width;
        }

        // TODO: impliment the crop part
//        $crop = $_GET['crop'] ?? $_GET['c'] ?? null;
//        if ($crop){
//            $crop_values = array(
//                "tl"=>"top_left",
//                "tc"=>"top_center",
//                "tr"=>"top_right",
//
//                "ml"=>"middle_left",
//                "mc"=>"middle_center",
//                "mr"=>"middle_right",
//
//                "bl"=>"bottom_left",
//                "bc"=>"bottom_center",
//                "br"=>"bottom_right",
//
//                "t" => "top_center",
//                "m" => "middle_center",
//                "b" => "bottom_center",
//
//                "0" =>null,
//                "1" =>"middle_center",
//            );
//
//
//
//            $this->crop = $crop_values[$crop] ?? null;
//        }


        return $this;
    }


}