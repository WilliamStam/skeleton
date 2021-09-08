<?php

namespace System\Files\Handlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use System\Exceptions\ResourceNotFound;
use Intervention\Image\ImageManager;
use System\Files\AbstractFiles;
use System\Files\FilesInterface;

class Text extends AbstractFiles implements FilesInterface {

    protected $file_extensions = array(
        "txt"=>"text/text",
    );



}