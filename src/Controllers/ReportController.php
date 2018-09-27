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
}
