<?php

namespace System\Files\Handlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use System\Exceptions\ResourceNotFound;
use Intervention\Image\ImageManager;
use System\Files\AbstractFiles;
use System\Files\FilesInterface;

class Image2 {
    protected $img;

    protected $height = null;
    protected $width = null;
    protected $crop = null;

    protected $headers = array();

    protected $file_extensions = array(
        "jpg" => "image/jpeg",
        "jpeg" => "image/jpeg",
        "png" => "image/png",
        "gif" => "image/gif",
        "ico" => "image/vnd.microsoft.icon",
    );


    protected $content_type = "image/jpg";

    protected $path;

    protected $options = array(
        "width"=>null,
        "height"=>null,
    );


    function process(ServerRequestInterface $request, string $path) {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (array_key_exists($ext, $this->file_extensions)) {
            $this->content_type = $this->file_extensions[$ext];
        }

        $query_params = $request->getQueryParams();
        if (array_key_exists("w",$query_params)){
            $this->options['width'] = $query_params['w'];
        }
        if (array_key_exists("width",$query_params)){
            $this->options['width'] = $query_params['width'];
        }
        if (array_key_exists("h",$query_params)){
            $this->options['height'] = $query_params['h'];
        }
        if (array_key_exists("height",$query_params)){
            $this->options['height'] = $query_params['height'];
        }


        $this->etag = md5($this->options['width'] . "|" . $this->options['height'] . "|" . $this->path);


        $this->headers["x-jam"] = "fish";

        return $this;
    }

    function response(ServerRequestInterface $request, ResponseInterface $response, string $path): ResponseInterface {
        $this->process($request, $path);

        $response = $response->withHeader("Content-type", $this->content_type);

        foreach ($this->headers as $header => $value) {
            $response = $response->withHeader($header, $value);
        }

        $manager = new ImageManager(array('driver' => 'GD'));
        $img = $manager->make($path);

        if ($this->options['height'] && $this->width) {
            $img->fit($this->options['width'], $this->options['height']);
        } else {
            if ($this->options['height'] || $this->options['width']) {
                $img->resize($this->options['width'], $this->options['height'], function ($constraint) {
                    $constraint->aspectRatio();
                });
            }
        }



        return $response->withBody($img->stream());
    }


}