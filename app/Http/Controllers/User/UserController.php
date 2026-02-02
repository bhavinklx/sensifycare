<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function __construct()
    {
        date_default_timezone_set('Asia/Kolkata');
    }

    public function create()
    {
        $roleDetail = Role::pluck('name', 'name')->toArray();
        return view('admin.users.create', compact('roleDetail'));
    }

    public function insert(Request $request)
    {
        $validated              = $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|unique:users,email',
            'phone'             => 'required|string|max:10',
            'password'          => 'required|min:6',
            'roles'             => 'required|array'
        ]);

        $user = User::create([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'phone'             => $validated['phone'] ?? null,
            'password'          => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['roles']);

        Session::flash('successMsg', 'User added successfully');
        return response()->json(['redirect_url' => route('user-list')]);
    }

    public function edit($id)
    {
        $userDetail = User::findOrFail($id);
        $roleDetail = Role::pluck('name', 'name')->toArray();
        $userRole   = $userDetail->roles->pluck('name')->toArray();

        return view('admin.users.edit', compact('userDetail', 'roleDetail', 'userRole'));
    }

    public function update(Request $request)
    {
        $user                   = User::findOrFail($request->user_id);
        $validated              = $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|unique:users,email,' . $user->id,
            'phone'             => 'nullable|string|max:15',
            'roles'             => 'required|array'
        ]);

        $user->update($validated);
        $user->syncRoles($validated['roles']);

        Session::flash('successMsg', 'User updated successfully');
        return response()->json(['redirect_url' => route('user-list')]);
    }

    public function view()
    {
        return view("admin.users.list");
    }

    public function load_table(Request $request)
    {
        $userDetail = User::get();
        return DataTables::of($userDetail)
            ->editColumn("checkbox", function ($user){
                return '<div class="form-check m-0"> <input class="form-check-input check_class" type="checkbox" id="check[]" name="check[]" value="'.$user->id.'"> </div>';
            })
            ->editColumn("title", function ($user){
                return $user->name;
            })
            ->editColumn("email", function ($user){
                return $user->email;
            })
            ->editColumn("mobile", function ($user){
                return $user->phone;
            })
            ->editColumn("date", function ($user){
                return date('d-m-Y h:i A', strtotime($user->created_at));
            })
            ->editColumn("action", function ($user){
                $action = '<div class="d-inline-flex gap-1">';
                if (auth()->user()->hasPermissionTo('user-delete', 'web')) {
                    $action.= '<button class="btn btn-outline-danger btn-sm" onclick="openDeleteModal(' . $user->id . ');" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete Administrators"> <i class="ri-delete-bin-line"></i> </button>';
                }
                if (auth()->user()->hasPermissionTo('user-edit', 'web')) {
                    $action.= '<a href="'.route("user-edit", ['id' => $user->id]).'" class="btn btn-outline-success btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit Administrators"> <i class="ri-edit-box-line"></i> </a>';
                }
                if (auth()->user()->can('user-delete')) {
                    $action.= '<a href="'.route("user-changepassword", ['id' => $user->id]).'" title="Change Password"> <i class="fa fa-lock"></i> </a>';
                }
                $action.= '</div>';
                return $action;
            })
            //for add table row id
            ->setRowId('id')
            //for add table row class
            ->setRowClass(function () {
                return 'row1';
            })
            //for add table row data
            ->setRowData([
                'id' => 'test',
            ])
            //for add table row attr
            ->setRowAttr([
                'data-id' => function($user) {
                    return $user->id;
                }
            ])
            ->rawColumns(["checkbox", "action"])
            ->make(true);
    }

    public function changepassword($id)
    {
        $userDetail = User::findOrFail($id);
        return view('admin.users.changepassword', compact('userDetail'));
    }

    public function changepassword_update(Request $request)
    {
        $validated = $request->validate([
            'password' => 'required|min:6|confirmed'
        ]);

        User::findOrFail($request->user_id)
            ->update(['password'=> Hash::make($validated['password'])]);

        Session::flash('successMsg', 'Password updated successfully');
        return response()->json(['redirect_url' => route('user-list')]);
    }

    public function delete(Request $request)
    {
        User::findOrFail($request->user_id)->delete();
        return response()->json(['status' => true]);
    }
}
