<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(){
        $roles=Role::orderBy('name','asc')->paginate(10);
        return view('roles.list',[
            'roles'=>$roles
        ]);
    }
    public function create(){
        $permission=Permission::orderBy('name','asc')->get();
        return view('roles.create',[
            'permissions'=>$permission
        ]);   
    }
    public function store(Request $request){
        // dd($request);
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles|min:3' // Tablitsangiz nomini to'g'ri yozing
        ]);

        if ($validator->passes()) {
            $role=Role::create(['name' => $request->name]);
            if(!empty($request->permission)){
                foreach($request->permission as $name){
                    $role->givePermissionTo($name);
                }
            }


            return redirect()->route('roles.index')->with('success', 'Roles added successfully.');
        } else {
            return redirect()->route('roles.create')->withInput()->withErrors($validator);
        }
    }

    public function edit($id){
        $role=Role::findOrFail($id);
        $hasPermissions=$role->permissions->pluck('name');
        $permissions=Permission::orderBy('name','asc')->get();
        // dd($permissions);
        return view('roles.edit',[
            'permissions'=>$permissions,
            'hasPermissions'=>$hasPermissions,
            'role'=>$role
        ]);
    }
    public function update($id,Request $request){
        $role=Role::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name,'.$id.',id' // Tablitsangiz nomini to'g'ri yozing
        ]);

        if ($validator->passes()) {
            $role->name=$request->name;
            $role->save();

            if(!empty($request->permission)){
                $role->syncPermissions($request->permission);
            }else{
                $role->syncPermissions([]);
            }

            return redirect()->route('roles.index')->with('success', 'Roles updated successfully.');
        } else {
            return redirect()->route('roles.edit',$id)->withInput()->withErrors($validator);
        }
    }
     
    public function destroy($id)
    {
        $role = Role::find($id);
        
        if (!$role) {
            session()->flash('error', 'Role not found');
            return response()->json([
                'status' => false,
                'message' => 'Role not found'
            ], 404); // Send 404 status code
        }
    
        $role->delete();
        session()->flash('success', 'Role deleted successfully');
        return response()->json([
            'status' => true,
            'message' => 'Role deleted successfully'
        ], 200); // Send 200 status code
    }
    
}

