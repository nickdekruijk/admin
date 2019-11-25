<?php

namespace NickDeKruijk\Admin\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use DB;

class ModelController extends BaseController
{
    // Save the model with only the columns allowed and return the id and listviewRow html
    private function save($model, Request $request)
    {
        if (isset($request['__cloneFromId'])) {
            $clone = $this->model()->findOrFail($request['__cloneFromId']);
            if ($this->module('treeview')) {
                $model[$this->module('treeview')] = $clone[$this->module('treeview')];
            }
        }
        if (isset($request['__modelRoot']) && is_numeric($request['__modelRoot']) && $this->module('treeview')) {
            $model[$this->module('treeview')] = $request['__modelRoot'];
        }

        $sync = [];
        $morph = [];
        foreach($this->columns() as $columnId => $column) {
            if (isset($column['type']) && $column['type'] == 'pivot') {
                $sync[$column['model']] = $request[$columnId];
                if (!empty($column['morph'])) {
                    $morph[$column['model']] = $column['morph'];
                }
            } elseif (isset($column['type']) && $column['type'] == 'array') {
                // If column is of type array json decode it
                $model[$columnId] = json_decode($request[$columnId], true);
                // If the JSON input is invalid return a 422 validation error
                if (json_last_error()) {
                    return new JsonResponse([
                        'message' => 'The given data was invalid.',
                        'errors' => [
                            $columnId => 'JSON: ' . json_last_error_msg(),
                        ],
                    ], 422);
                }
            } elseif (isset($column['type']) && $column['type'] == 'password') {
                // If column is a password and user changed it then hash it
                if ($request[$columnId] && $request[$columnId] != '********') {
                    $model[$columnId] = bcrypt($request[$columnId]);
                }
            } else {
                $model[$columnId] = $request[$columnId];
            }
        }

        if ($this->module('sortable') && empty($model['sort'])) {
            $model['sort'] = $this->model()->max('sort') + 1;
        }

        $model->save();

        foreach($sync as $foreign => $values) {
            if (isset($morph[$foreign])) {
                $model->morphToMany($foreign, $morph[$foreign])->sync($values);
            } else {
                $model->belongsToMany($foreign)->sync($values);
            }
        }

        return [
            'active' => $this->module('active') ? $model[$this->module('active')]==true : true,
            'id' => $model->id,
            'listview' => $this->listviewData(request()->__modelRoot),
            'li' => $this->listviewRow($model),
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($slug, Request $request)
    {
        $this->checkSlug($slug, 'create');
        $this->validate($request, $this->validationRules());
        return $this->save($this->model(), $request);
    }

    // Filter out pivot type fields to prevent unknown column database errors
    public function filter_pivot($columns)
    {
        $filtered = $columns;
        foreach($filtered as $columnId => $column) {
            if ($column['type'] == 'pivot') {
                unset($filtered[$columnId]);
            }
        }
        return array_keys($filtered);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug, $id)
    {
        $this->checkSlug($slug, 'read');
        // Get the original values and not the altered values from model accessors
        $row = @$this->model()::findOrFail($id, $this->filter_pivot($this->columns()))->getOriginal();
        foreach($this->columns() as $columnId => $column) {
            // Output array columns with JSON_PRETTY_PRINT
            if ($column['type'] == 'array') {
                $row[$columnId] = json_encode(json_decode($row[$columnId]), JSON_PRETTY_PRINT);
            }
            // If column type is pivot return matching ids
            if ($column['type'] == 'pivot') {
                unset($row['"'.$columnId.'"']);
                $ids = [];
                if (!empty($column['morph'])) {
                    $pivotData = $this->model()::findOrFail($id)->morphToMany($column['model'], $column['morph'])->get();
                } else {
                    $pivotData = $this->model()::findOrFail($id)->belongsToMany($column['model'])->get();
                }
                foreach ($pivotData as $pivot) {
                    $ids[] = $pivot->id;
                }
                $row['_pivot.'.$columnId] = implode(',', $ids);
            }
            // If column is a password (and maybe even hidden) return it with a 'masked' values of ********
            if (isset($column['type']) && $column['type'] == 'password' && $row[$columnId]) {
                $row[$columnId] = '********';
            }
            // Strip zero time on date columns
            if ($column['type'] == 'date' && $row[$columnId]) {
                $row[$columnId] = str_replace(' 00:00:00', '', $row[$columnId]);
            }
        }
        return $row;
    }

    // Fetch a file from storage
    public function download($slug, $id, $column, $data)
    {
        $this->checkSlug($slug, 'read');
        $row = @$this->model()::findOrFail($id, $this->filter_pivot($this->columns()))->getOriginal();
        abort_if(empty($row[$column]), 404);
        $array = json_decode($row[$column]);
        abort_if(empty($array->$data) || !isset($array->$data->name) || !isset($array->$data->type) || !isset($array->$data->size) || !isset($array->$data->store), 404);
        $file = rtrim($this->columns('data')['storage_path'] ?? storage_path(), '/') . '/' . $array->$data->store;
        abort_if(!file_exists($file), 404);
        return response()->download($file, $array->$data->name);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($slug, Request $request, $id)
    {
        $this->checkSlug($slug, 'update');
        $this->validate($request, $this->validationRules(['id' => $id]));
        return $this->save($this->model()::findOrFail($id), $request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug, $id)
    {
        $this->checkSlug($slug, 'delete');
        $this->model()::findOrFail($id)->delete();
    }

    // This method is called after nestedSortable is done and parent changed
    public function changeParent($slug, Request $request, $id) {
        $this->checkSlug($slug, 'update');
        $row = $this->model()::findOrFail($id);

        // Get the parent and oldparent from Input, make null if needed
        $parent=$request->input('parent');
        $oldparent=$request->input('oldparent');
        if ($oldparent<1) $oldparent=null;
        if ($parent<1) $parent=null;

        // Check if oldparent matches the actual id for safety
        if ($row->parent != $oldparent) die('Invalid oldparent '.$oldparent);

        // Save the new parent
        $row->parent=$parent;
        $row->save();

        // Now sort the items too
        $this->sort($slug, $parent, $request);
    }

    // Sort the items
    public function sort($slug, $parent, Request $request)
    {
        $this->checkSlug($slug, 'update');
        $ids = $request->input('ids');
        // Get the row for each id and update the sort
        if ($parent<1) $parent = null;
        $sort = 0;
        $table = $this->model()->getTable();
        foreach(explode(',', $ids) as $id) {
            $sort++;
            DB::table($table)->where('id', $id)->update(['sort' => $sort]);
        }
    }
}
