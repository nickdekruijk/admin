<?php

namespace NickDeKruijk\Admin\Controllers;

use DB;

class ReportController extends BaseController
{
    public static function reports($lp)
    {
        $lp->checkSlug($lp->slug, 'read');
        $response = '<ul>';
        foreach ($lp->module('queries') as $queryId => $query) {
            $response .= '<li data-url="'.route('report', ['slug' => $lp->slug, 'id' => str_slug($queryId)]).'">';
            $response .= '<div><i></i><span>'.$queryId.'</span></div>';
            $response .= '</li>';
        }
        $response .= '</ul>';
        return $response;
    }

    private function getQueryData($slug, $id)
    {
        foreach ($this->module('queries') as $queryId => $query) {
            if (str_slug($queryId) == $id) {
                if (env('DB_CONNECTION')=='mysql') $set = DB::select('SET SESSION group_concat_max_len = 1024000');
                return DB::select($query);
            }
        }
        abort(404);
    }

    public function show($slug, $id)
    {
        $this->checkSlug($slug, 'read');
        $data = $this->getQueryData($slug, $id);
        $response = '';
        foreach ($data as $row) {
            if (!$response) {
                $response .= '<tr>';
                foreach ($row as $name => $column) {
                    $response .= '<th>';
                    $response .= ucfirst(htmlspecialchars(str_replace('_', ' ', $name)));
                    $response .= '</th>';
                }
                $response .= '</tr>';
            }
            $response .= '<tr>';
            foreach ($row as $column) {
                $response .= '<td><div>'.htmlspecialchars($column).'</div></td>';
            }
            $response .= '</tr>';
        }
        return '<table class="report">'.$response.'</table>';
    }

    public function csv($slug, $id)
    {
        $this->checkSlug($slug, 'read');
        $data = $this->getQueryData($slug, $id);
        $out = fopen('php://memory', 'w');
        fputcsv($out, array_keys(get_object_vars($data[0])));
        foreach($data as $line) {
            fputcsv($out, get_object_vars($line), $this->module('download_csv_delimiter') ?: ',');
        }
        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);
        return response($csv)->header('Content-type', 'text/csv')->header('Content-disposition', 'attachment;filename='. $id . '.csv');
    }
}
