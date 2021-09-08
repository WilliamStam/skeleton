<?php

namespace System\Files\Handlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use System\Exceptions\ResourceNotFound;
use Intervention\Image\ImageManager;
use System\Files\AbstractFiles;
use System\Files\FilesInterface;

class Javascript extends AbstractFiles implements FilesInterface {

    protected $file_extensions = array(
        "js"=>"application/javascript",
    );



}