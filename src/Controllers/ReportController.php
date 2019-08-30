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
                if (is_array($query)) {
                    $data =  DB::select($query['query']);
                    // Apply json_decode() if needed
                    if (isset($query['json'])) {
                        foreach (explode(',', $query['json']) as $json) {
                            foreach($data as $id => $row) {
                                $data[$id]->$json = (array)json_decode($row->$json);
                            }
                        }
                        if (!isset($query['index'])) {
                            $index = [];
                            foreach($data as $id => $row) {
                                foreach($row as $key => $value) {
                                    if (is_array($value)) {
                                        foreach($value as $key2 => $value2) {
                                            $index[$key . '.' . $key2] = $key . '.' . $key2;
                                        }
                                    } else {
                                        $index[$key] = $key;
                                    }
                                }
                            }
                            $query['index'] = implode(',', $index);
                        }
                    }
                    // Return only index columns if present
                    if (isset($query['index'])) {
                        $indexed = [];
                        foreach (explode(',', $query['index']) as $index) {
                            $index = explode(':', $index);
                            $as = $index[1] ?? null;
                            $index = explode('.', $index[0]);
                            foreach ($data as $id => $row) {
                                $row = (array)$row;
                                if (!isset($indexed[$id])) {
                                    $indexed[$id] = [];
                                }
                                if (count($index) > 1) {
                                    $value = $row[$index[0]][$index[1]];
                                    $key = $as ?: $index[1];
                                } else {
                                    $key = $as ?: $index[0];
                                    $value = $row[$index[0]];
                                }
                                $indexed[$id][$key] = $value;
                            }
                        }
                        $data = $indexed;
                    }
                    return $data;
                } else {
                    return DB::select($query);
                }
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
