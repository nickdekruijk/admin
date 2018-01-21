<?php

namespace LaraPages\Admin\Controllers;

use Illuminate\Http\Request;

class ModelController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $row = $this->model();
        foreach($this->columns() as $columnId => $column) {
            $row[$columnId] = $request[$columnId];
        }
        $row->save();
        return [
            'id' => $row->id,
            'li' => $this->listviewRow($row),
        ];
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
        return $this->model()::findOrFail($id, array_keys($this->columns()));
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
        $row = $this->model()::findOrFail($id);
        foreach($this->columns() as $columnId => $column) {
            $row[$columnId] = $request[$columnId];
        }
        $row->save();
        return [
            'id' => $row->id,
            'li' => $this->listviewRow($row),
        ];
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
}
