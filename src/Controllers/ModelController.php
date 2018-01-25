<?php

namespace LaraPages\Admin\Controllers;

use Illuminate\Http\Request;

class ModelController extends BaseController
{
    // Save the model with only the columns allowed and return the id and listviewRow html
    private function save($model, Request $request)
    {
        foreach($this->columns() as $columnId => $column) {
            if (isset($column['type']) && $column['type']=='password') {
                // If column is a password and user changed it then hash it
                if ($request[$columnId] && $request[$columnId]!='********') {
                    $model[$columnId] = bcrypt($request[$columnId]);
                }
            } else {
                $model[$columnId] = $request[$columnId];
            }
        }
        $model->save();
        return [
            'active' => $this->module('active') ? $model[$this->module('active')]==true : true,
            'id' => $model->id,
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
        foreach(explode(',', $ids) as $id) {
            $sort++;
            $row = $this->model()::findOrFail($id);
            if ($row->parent != $parent) die('Invalid parent');
            $row->sort = $sort;
            $row->save();
        }
    }
}
