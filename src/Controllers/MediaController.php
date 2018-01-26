<?php

namespace LaraPages\Admin\Controllers;

use Illuminate\Http\Request;
use File;

class MediaController extends BaseController
{
    public function encodeUrl($str)
    {
        return str_replace('%2F', '/', urlencode($str));
    }
    public static function folders($path = null, $id = null)
    {
        if (!$path) {
            $path = config('larapages.media_path');
        }
        $directories = File::directories($path);
        natcasesort($directories);
        // Initialize the response
        $response = '';

        foreach($directories as $directory) {
            // First item, add <ul>
            if (!$response) $response .= '<ul>';
            $size = 0;
            $files = 0;
            foreach(File::files($directory) as $file) {
                $files++;
                $size += filesize($file);
            }
            $response .= '<li data-id="'.urlencode($id.basename($directory)).'"><div><i></i><span>'.basename($directory).'</span><span class="right">'.$files.'</span><span class="right">'.number_format($size/1024000,2).' MB</span></div>';
            $response .= MediaController::folders($directory, $id.basename($directory).'/');
            $response .= '</li>';
        }
        // Add closing </ul> if there was anything added
        if ($response) $response .= '</ul>';
        return $response;
    }

    public function show($slug, $folder)
    {
        $this->checkSlug($slug, 'read');
        $folder = urldecode($folder);
        $files = File::files(config('larapages.media_path').'/'.$folder);
        natcasesort($files);

        $response = '<ul>';
        foreach ($files as $file) {
            $response .= '<li>';
            $response .= '<div class="img" style="background-image:url(\''.$this->encodeUrl(config('larapages.media_url').'/'.$folder.'/'.$file->getFilename()).'\')"></div>';
            $response .= $file->getFilename();
            $s = getimagesize($file);
            $response .= '<div>'.($s?$s[0].' x '.$s[1].', ':'').number_format($file->getSize()/1000,2).' kB</div>';
            $response .= '</li>';
        }
        $response .= '</ul>';
        return $response;
    }
}
