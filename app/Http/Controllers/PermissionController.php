<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public  function index(){
        $permissions=Permission::orderBy('created_at','desc')->paginate(10);
        return view('permission.list',[
            'permissions'=>$permissions
        ]);
    }
    public  function create(){
        return view('permission.create');
    }
    public function store(Request $request)
    {
        // Validator yaratish
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions|min:3' // Tablitsangiz nomini to'g'ri yozing
        ]);

        if ($validator->passes()) {
            Permission::create(['name' => $request->name]);
            return redirect()->route('permission.index')->with('success', 'Permission added successfully.');
        } else {
            return redirect()->route('permission.create')->withInput()->withErrors($validator);
        }
    }

    public  function edit($id){
        $permission=Permission::findOrFail($id);
        return view('permission.edit',[
            'permission'=>$permission
        ]);
    }
    public  function update($id,Request $request){
        $permission=Permission::findOrFail($id);
        $validator=Validator::make($request->all(),[
            'name'=>'required|min:3|unique:permissions,name'
        ]);
        if ($validator->passes()) {
            $permission->name=$request->name;
            $permission->save();
            return redirect()->route('permission.index')->with('success', 'Permission updated successfully.');
        } else {
            return redirect()->route('permission.create')->withInput()->withErrors($validator);
        }
    }
    public  function destroy(Request $request){
        $id=$request->id;
        $permission=Permission::find($id);
        if($permission==null){
            session()->flash('error','Permssion not found');
            return response()->json([
                'status'=>false,
            ]);
        }
        $permission->delete();
        session()->flash('success','Permssion delete successfully.');
        return response()->json([
            'status'=>true,
        ]);
    }
}
