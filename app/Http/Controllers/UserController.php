<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{

    public static function middleware()
    {
        return [
            new Middleware('permission:view users', ['index']),
            new Middleware('permission:edit users', ['edit']),
            // new Middleware('permission:create users', ['create']),
            // new Middleware('permission:delete users', ['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users=User::latest()->paginate(10);
        return view('users.list',[
           
            'users'=>$users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user=User::findOrFail($id);
        $roles=Role::orderBy('name','asc')->get();

        $hasRoles=$user->roles->pluck('id');
        // dd($hasRoles);
        return view('users.edit',[
            'user'=>$user,
            'roles'=>$roles,
            'hasRoles'=>$hasRoles,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
    
        // Fix typo in 'validator'
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);
    
        if ($validator->fails()) {
            return redirect()->route('users.edit', $id)
                ->withInput()
                ->withErrors($validator);
        }
    
        // Update user data
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();
    
        // Sync roles
        $user->syncRoles($request->role);
    
        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        
    }
}
