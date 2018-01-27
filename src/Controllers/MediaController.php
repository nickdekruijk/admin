<?php

namespace LaraPages\Admin\Controllers;

use Illuminate\Http\Request;
use File;

class MediaController extends BaseController
{
    public static function uploadLimit()
    {
        $max = ini_get('upload_max_filesize') > (int)ini_get('post_max_size') ? (int)ini_get('post_max_size') : (int)ini_get('upload_max_filesize');
        if (config('larapages.media_upload_limit') < $max) {
            $max = config('larapages.media_upload_limit');
        }
        return $max;
    }

    public function encodeUrl($str)
    {
        return str_replace('%2F', '/', rawurlencode($str));
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

    public static function trailingSlash($str)
    {
        return rtrim($str, '/') . '/';
    }

    public function show($slug, $folder)
    {
        $this->checkSlug($slug, 'read');
        $folder = urldecode($folder);
        $files = File::files(config('larapages.media_path').'/'.$folder);
        natcasesort($files);

        $preview = ['jpg', 'png', 'gif', 'jpeg'];
        // Check if Safari version is 9 or higher so we can preview PDF thumbnails
        $ua = @$_SERVER['HTTP_USER_AGENT'];
        $safari = strpos($ua, 'Safari') && !strpos($ua, 'Chrome');
        $p = strpos($ua, 'Version/');
        $safariVersion = substr($ua, $p+8, strpos($ua, '.', $p)-$p-8);
        if ($safariVersion >= 9) $preview[] = 'pdf';

        $response = '';
        foreach ($files as $file) {
            $response .= '<li>';
            $extension = strtolower($file->getExtension());
            if (in_array($extension, $preview))
                $response .= '<div class="img" style="background-image:url(\''.$this->encodeUrl($this->trailingSlash(config('larapages.media_url')).$folder.'/'.$file->getFilename()).'\')">';
            else
                $response .= '<div class="img">'.$extension;
            $response .= '</div>';
            $response .= '<span class="filename">'.$file->getFilename().'</span>';
            $s = getimagesize($file);
            $response .= '<div>'.($s?$s[0].' x '.$s[1].', ':'').number_format($file->getSize()/1000,2).' kB</div>';
            $response .= '<button class="delete button small is-red" data-confirm="'.trans('larapages::base.delete').'"><i class="fa fa-trash"></i></button>';
            $response .= '<button class="rename button small" data-prompt="'.trans('larapages::base.rename').'"><i class="fa fa-info"></i></button>';
            $response .= '</li>';
        }
        return $response;
    }

    public function store($slug, $folder, Request $request)
    {
        $this->checkSlug($slug, 'create');
        $folder = urldecode($folder);

        // Check if upload file exists
        if (!$request->hasFile('upl')) {
            die('{"status":"'.trans('larapages::base.uploaderror').'"}');
        }
        $upl=$request->file('upl');

        // Check if it had an error
        if ($upl->getError()) {
            die('{"status":"error '.$upl->getError().': '.str_replace('"','\\"', $upl->getErrorMessage()).'"}');
        }

        // Check if filesize is allowed
        if ($upl->getClientSize() > $this->uploadLimit()*1024*1024) {
            die('{"status":"'.trans('larapages::base.filetoobig').'"}');
        }

        // Check if extension is allowed
        if (!in_array(strtolower($upl->getClientOriginalExtension()), config('larapages.media_allowed_extensions'))) {
            die('{"status":"'.trans('larapages::base.extnotallowed').'"}');
        }

        $filename = $upl->getClientOriginalName();
        // If file exists add a number until file is available
        if (config('larapages.media_upload_incremental')) {
            $postfix = false;
            while (file_exists(config('larapages.media_path').'/'.$folder.'/'.$filename)) {
                if (!$postfix) $postfix = 2; else $postfix++;
                $filename = pathinfo($upl->getClientOriginalName(), PATHINFO_FILENAME).'_'.$postfix.'.'.$upl->getClientOriginalExtension();
            }
        }
        $request->file('upl')->move(config('larapages.media_path').'/'.$folder, $filename);
        die('{"status":"success"}');
        return $request->toArray();
    }

    public function destroy($slug, $folder, Request $request)
    {
        $this->checkSlug($slug, 'delete');
        $folder = urldecode($folder);
        $file = config('larapages.media_path').'/'.$folder.'/'.$request->filename;
        if (!file_exists($file)) die('File not found '.$file);
        if (substr(realpath($file), 0, strlen(config('larapages.media_path'))) !== config('larapages.media_path')) return 'Error '.realpath($file);
        unlink($file);
    }

    public function update($slug, $folder, Request $request)
    {
        $this->checkSlug($slug, 'delete');
        $folder = urldecode($folder);
        $file = config('larapages.media_path').'/'.$folder.'/'.$request->filename;
        $newname = config('larapages.media_path').'/'.$folder.'/'.$request->newname;
        if (!file_exists($file)) die('File not found '.$file);
        if ($file == $newname) return;
        if (file_exists($newname)) die('File already exists '.$newname);
        if (substr(realpath($file), 0, strlen(config('larapages.media_path'))) !== config('larapages.media_path')) return 'Error file '.realpath($file);
        rename($file, $newname);
    }
}
