<?php

namespace NickDeKruijk\Admin\Controllers;

use Illuminate\Http\Request;
use File;

class MediaController extends BaseController
{
    public static function uploadLimit()
    {
        $max = ini_get('upload_max_filesize') > (int) ini_get('post_max_size') ? (int) ini_get('post_max_size') : (int) ini_get('upload_max_filesize');
        if (config('admin.media_upload_limit') < $max) {
            $max = config('admin.media_upload_limit');
        }
        return $max;
    }

    public function encodeUrl($str)
    {
        return str_replace('%2F', '/', rawurlencode($str));
    }

    private static function folderRow($directory)
    {
        $size = 0;
        $files = 0;
        foreach (glob($directory . '/*') as $file) {
            $files++;
            $size += filesize($file);
        }
        return '<div><i></i><span>' . basename($directory) . '</span><span class="right">' . $files . '</span><span class="right">' . number_format($size / 1024000, 2) . ' MB</span></div>';
    }

    public static function folders($slug = 'media', $path = null, $id = null, $depth = 0)
    {
        if (!$path) {
            $path = config('admin.media_path');
        }
        $directories = glob($path . '/*', GLOB_ONLYDIR);
        natcasesort($directories);
        // Initialize the response
        $response = '';

        foreach ($directories as $directory) {
            // First item, add <ul>
            if (!$response) {
                $response .= '<ul' . (config('admin.modules.' . $slug . '.expanded') > 0 && config('admin.modules.' . $slug . '.expanded') <= $depth ? ' class="closed"' : '') . '>';
            }
            $response .= '<li class="' . (in_array($id . basename($directory), config('admin.modules.' . $slug . '.hidden') ?? []) ? 'hidden' : '') . '" data-id="' . urlencode($id . basename($directory)) . '">';
            $response .= MediaController::folderRow($directory);
            $response .= MediaController::folders($slug, $directory, $id . basename($directory) . '/', $depth + 1);
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

    public static function leadingSlash($str)
    {
        return '/' . ltrim($str, '/');
    }

    public function show($slug, $folder)
    {
        $this->checkSlug($slug, 'read');
        $folder = urldecode($folder);
        $files = File::files(config('admin.media_path') . '/' . $folder);
        natcasesort($files);

        $preview = ['jpg', 'png', 'gif', 'jpeg', 'svg'];
        // Check if Safari version is 9 or higher so we can preview PDF thumbnails
        $ua = @$_SERVER['HTTP_USER_AGENT'];
        $safari = strpos($ua, 'Safari') && !strpos($ua, 'Chrome');
        $p = strpos($ua, 'Version/');
        $safariVersion = substr($ua, $p + 8, strpos($ua, '.', $p) - $p - 8);
        if ($safariVersion >= 9) $preview[] = 'pdf';

        $response = '';
        foreach ($files as $file) {
            $response .= '<li data-file="' . rawurlencode($folder . '/' . $file->getFilename()) . '">';
            $extension = strtolower($file->getExtension());
            if (in_array($extension, $preview)) {
                // Use nickdekruijk/imageresize admin-thumbnails template if present
                if (config('imageresize.templates.admin-thumbnails') && config('imageresize.route')) {
                    $source = $this->trailingSlash($this->leadingSlash(config('imageresize.route'))) . 'admin-thumbnails/';
                } else {
                    $source = $this->trailingSlash(config('admin.media_url'));
                }
                $response .= '<div class="img" style="background-image:url(\'' . $this->encodeUrl($source . $folder . '/' . $file->getFilename()) . '\')">';
            } else {
                $response .= '<div class="img">' . $extension;
            }
            $response .= '</div>';
            $response .= '<span class="filename">' . $file->getFilename() . '</span>';
            $s = @getimagesize($file);
            $response .= '<div class="details">' . ($s ? $s[0] . ' x ' . $s[1] . ', ' : '') . number_format($file->getSize() / 1000, 2) . ' kB</div>';
            if ($this->can('delete')) {
                $response .= '<button class="delete button small is-red" data-confirm="' . trans('admin::base.delete') . '"><i class="fa fa-trash"></i></button>';
            }
            if ($this->can('update')) {
                $response .= '<button class="rename button small" data-prompt="' . trans('admin::base.rename') . '"><i class="fa fa-info"></i></button>';
            }
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
            return '{"status":"' . trans('admin::base.uploaderror') . '"}';
        }
        $upl = $request->file('upl');

        // Check if it had an error
        if ($upl->getError()) {
            return '{"status":"error ' . $upl->getError() . ': ' . str_replace('"', '\\"', $upl->getErrorMessage()) . '"}';
        }

        // Check if filesize is allowed
        if ($upl->getSize() > $this->uploadLimit() * 1024 * 1024) {
            return '{"status":"' . trans('admin::base.filetoobig') . '"}';
        }

        // Check if extension is allowed
        if (!in_array(strtolower($upl->getClientOriginalExtension()), config('admin.media_allowed_extensions'))) {
            return '{"status":"' . trans('admin::base.extnotallowed') . '"}';
        }

        $filename = $upl->getClientOriginalName();
        // If file exists add a number until file is available
        if (config('admin.media_upload_incremental')) {
            $postfix = false;
            while (file_exists(config('admin.media_path') . '/' . $folder . '/' . $filename)) {
                if (!$postfix) $postfix = 2;
                else $postfix++;
                $filename = pathinfo($upl->getClientOriginalName(), PATHINFO_FILENAME) . '_' . $postfix . '.' . $upl->getClientOriginalExtension();
            }
        }
        $request->file('upl')->move(config('admin.media_path') . '/' . $folder, $filename);
        return ['status' => 'success', 'folderRow' => $this->folderRow(config('admin.media_path') . '/' . $folder)];
    }

    public function destroy($slug, $folder, Request $request)
    {
        $this->checkSlug($slug, 'delete');
        $folder = urldecode($folder);
        $file = config('admin.media_path') . '/' . $folder . '/' . $request->filename;
        if (!file_exists($file)) return 'File not found ' . $file;
        if (substr(realpath($file), 0, strlen(realpath(config('admin.media_path')))) !== realpath(config('admin.media_path'))) return 'Error ' . realpath($file);
        unlink($file);
        return $this->folderRow(dirname($file));
    }

    public function update($slug, $folder, Request $request)
    {
        $this->checkSlug($slug, 'delete');
        $folder = urldecode($folder);
        $file = config('admin.media_path') . '/' . $folder . '/' . $request->filename;
        $newname = config('admin.media_path') . '/' . $folder . '/' . $request->newname;
        if (!file_exists($file)) return 'File not found ' . $file;
        if ($file == $newname) return;
        if (file_exists($newname)) return 'File already exists ' . $newname;
        if (substr(realpath($file), 0, strlen(realpath(config('admin.media_path')))) !== realpath(config('admin.media_path'))) return 'Error file ' . realpath($file);
        // Check if extension is allowed
        $extension = strtolower(substr($newname, strrpos($newname, '.') + 1));
        if (!in_array($extension, config('admin.media_allowed_extensions'))) {
            return trans('admin::base.extnotallowed') . ': .' . $extension;
        }
        rename($file, $newname);
    }

    public function newFolder($slug, $folder, Request $request)
    {
        $this->checkSlug($slug, 'create');
        $folder = $this->trailingSlash(config('admin.media_path') . '/' . urldecode($folder));
        $response = [];
        $newfolder = $folder . $request->folder;
        if (substr(realpath($folder), 0, strlen(realpath(config('admin.media_path')))) !== realpath(config('admin.media_path'))) abort(400, 'realpath failed');
        if (strpos($request->folder, '.') !== false) abort(422, 'No . allowed in foldername');
        if (file_exists($newfolder)) abort(409, $request->folder . ' already exists');
        mkdir($newfolder);
        return $this->folders();
    }

    public function destroyFolder($slug, $folder, Request $request)
    {
        $this->checkSlug($slug, 'create');
        $folder = $this->trailingSlash(config('admin.media_path') . '/' . urldecode($folder));
        if (!file_exists($folder)) abort(404, 'Does not exists');
        if (!is_dir($folder)) abort(400, 'Is not a directory');
        if (count(File::allFiles($folder))) abort(409, 'Directory is not empty');
        rmdir($folder);
    }
}
