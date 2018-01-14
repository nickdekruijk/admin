<?php
// Thank you Barryvdh\Debugbar for this concept!

namespace LaraPages\Admin\Controllers;

use Illuminate\Http\Response;

class AssetController extends BaseController
{
    // Return all javascript files as one
    public function js()
    {
        $content = file_get_contents(__DIR__.'/../js/base.js');

        $response = new Response(
            $content, 200, [
                'Content-Type' => 'text/javascript',
            ]
        );

        return $this->cacheResponse($response);
    }

    // Return all stylesheet files as one
    public function css()
    {
        $content = file_get_contents(__DIR__.'/../css/base.css');

        $response = new Response(
            $content, 200, [
                'Content-Type' => 'text/css',
            ]
        );

        return $this->cacheResponse($response);
    }

    // Cache the response 1 year (31536000 sec)
    protected function cacheResponse(Response $response)
    {
        $response->setSharedMaxAge(31536000);
        $response->setMaxAge(31536000);
        $response->setExpires(new \DateTime('+1 year'));

        return $response;
    }
}
