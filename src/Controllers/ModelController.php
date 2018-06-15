<?php

namespace LaraPages\Admin\Controllers;

use Illuminate\Http\Request;
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
        foreach($this->columns() as $columnId => $column) {
            if (isset($column['type']) && $column['type'] == 'pivot') {
                $model->belongsToMany($column['model'])->sync($request[$columnId]);
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
        $row = $this->model()::findOrFail($id, array_keys($this->columns()))->getOriginal();
        foreach($this->columns() as $columnId => $column) {
            // If column type is pivot return matching ids
            if ($column['type'] == 'pivot') {
                unset($row['"'.$columnId.'"']);
                $ids = [];
                foreach ($this->model()::findOrFail($id)->belongsToMany($column['model'])->get() as $pivot) {
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
