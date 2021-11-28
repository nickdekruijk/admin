<?php

namespace NickDeKruijk\Admin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class AssetController extends Controller
{
    public function stylesheets()
    {
        $content = file_get_contents(__DIR__ . '/../admin.css');
        $response = new Response($content, 200, ['Content-Type' => 'text/css']);
        return $response;
    }
}
