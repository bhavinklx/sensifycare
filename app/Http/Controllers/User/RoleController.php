<?php

namespace App\Http\Controllers\User;

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        date_default_timezone_set('Asia/Kolkata');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permission = Permission::all();
        return view('admin.roles.create', compact('permission'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function insert(Request $request)
    {
        $request->validate([
            'name'              => 'required|unique:roles,name',
            'permission'        => 'required|array',
        ]);

        $role = Role::create([
            'name'              => $request->name,
            'created_at'        => Carbon::now(),
        ]);
        $role->syncPermissions($request->permission);

        Session::flash('successMsg', 'Role details added successfully');

        return response()->json(['redirect_url' => route('role-list')]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permission = Permission::all();
        $rolePermissions = DB::table('role_has_permissions')
            ->where('role_id', $id)
            ->pluck('permission_id')
            ->toArray();
        return view('admin.roles.edit', compact('role', 'permission', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'name'              => 'required',
            'permission'        => 'required|array',
        ]);

        $role                   = Role::findOrFail($request->role_id);
        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permission);

        Session::flash('successMsg', 'Role details updated successfully');
        return response()->json(['redirect_url' => route('role-list')]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function view(Request $request)
    {
        return view('admin.roles.list');
    }

    public function load_table(Request $request)
    {
        $roleDetail = Role::get();
        return DataTables::of($roleDetail)
            ->editColumn("checkbox", function ($role){
                return '<div class="form-check m-0"> <input class="form-check-input check_class" type="checkbox" id="check[]" name="check[]" value="'.$role->id.'"> </div>';
            })
            ->editColumn("title", function ($role){
                return $role->name;
            })
            ->editColumn("date", function ($role){
                return date('d-m-Y h:i:s A', strtotime($role->created_at));
            })
            ->editColumn("action", function ($role){
                $action = '<div class="d-inline-flex gap-1">';
                if (auth()->user()->hasPermissionTo('user-delete', 'web')) {
                    $action.= '<button class="btn btn-outline-danger btn-sm" onclick="openDeleteModal(' . $role->id . ');" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Delete Role"> <i class="ri-delete-bin-line"></i> </button>';
                }
                if (auth()->user()->hasPermissionTo('user-edit', 'web')) {
                    $action.= '<a href="'.route("role-edit", ['id' => $role->id]).'" class="btn btn-outline-success btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Edit Role"> <i class="ri-edit-box-line"></i> </a>';
                }
                $action.= '</div>';
                return $action;
            })
            //for add table row class
            ->setRowClass(function () {
                return 'row1';
            })
            //for add table row attr
            ->setRowAttr([
                "id" => function ($role) {
                    return 'row_' . $role->id;
                },
                'data-id' => function($role) {
                    return $role->id;
                }
            ])
            ->rawColumns(["checkbox", "status", "action"])
            ->make(true);
    }

    public function delete(Request $request)
    {
        Role::findOrFail($request->role_id)->delete();
        return response()->json(['status' => true]);
    }

    public function access_denied()
    {
        return view('admin.access-denied');
    }
}
