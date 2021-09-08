<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;

use System\Core\System;
use System\Core\Profiler;

class CacheControlMiddleware {
    /**
     * Cache-Control type (public or private)
     *
     * @var string
     */
    protected $type;

    /**
     * Cache-Control max age in seconds
     *
     * @var int
     */
    protected $maxAge;

    /**
     * Cache-Control includes must-revalidate flag
     *
     * @var bool
     */
    protected $mustRevalidate;

    /**
     * Create new HTTP cache
     *
     * @param string $type The cache type: "public" or "private"
     * @param int $maxAge The maximum age of client-side cache
     * @param bool $mustRevalidate must-revalidate
     */
    public function __construct(string $type = 'private', int $maxAge = 86400, bool $mustRevalidate = false, bool $disableCacheing = false, Profiler $profiler = null) {
        $this->type = $type;
        $this->maxAge = $maxAge;
        $this->mustRevalidate = $mustRevalidate;
        $this->disableCacheing = $disableCacheing;
        $this->profiler = $profiler;
//        var_dump($this->mustRevalidate);
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke(Request $request, RequestHandler $handler): Response {
//        var_dump("start CacheControlMiddleware");
        $GLOBALS['output'](get_class($this) . " start");
        if (!$this->disableCacheing) {

        }
        $response = $handler->handle($request);

        $profiler = $this->profiler->start(__CLASS__ . "::" . __FUNCTION__, __NAMESPACE__);
        // Cache-Control header
        if ($this->disableCacheing) {
            $response = $response->withHeader(
                'Cache-Control', 'no-cache');
        } else {
            if (!$response->hasHeader('Cache-Control')) {
                if ($this->maxAge === 0) {
                    $response = $response->withHeader(
                        'Cache-Control',
                        sprintf(
                            '%s, no-cache%s',
                            $this->type,
                            $this->mustRevalidate ? ', must-revalidate' : ''
                        )
                    );
                } else {
                    $response = $response->withHeader(
                        'Cache-Control',
                        sprintf(
                            '%s, max-age=%s%s',
                            $this->type,
                            $this->maxAge,
                            $this->mustRevalidate ? ', must-revalidate' : ''
                        )
                    );
                }
            }
            // ETag header and conditional GET check
            $etag = $response->getHeader('ETag');
            $etag = reset($etag);

            if ($etag) {
                $ifNoneMatch = $request->getHeaderLine('If-None-Match');
                if ($ifNoneMatch) {
                    $etagList = preg_split('@\s*,\s*@', $ifNoneMatch);
                    if (is_array($etagList) && (in_array($etag, $etagList) || in_array('*', $etagList))) {
                        return $response->withStatus(304);
                    }
                }
            }


            // Last-Modified header and conditional GET check
            $lastModified = $response->getHeaderLine('Last-Modified');

            if ($lastModified) {
                if (!is_numeric($lastModified)) {
                    $lastModified = strtotime($lastModified);
                }

                $ifModifiedSince = $request->getHeaderLine('If-Modified-Since');

                if ($ifModifiedSince && $lastModified <= strtotime($ifModifiedSince)) {
                    return $response->withStatus(304);
                }
            }
        }
$GLOBALS['output'](get_class($this) . " end");
//var_dump("end CacheControlMiddleware");

        $profiler->stop();
        return $response;
    }

}