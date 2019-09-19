<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Role;
use App\Permission;
use DB,Auth;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pagination = 20;
        $roles = Role::orderBy('id','ASC')->paginate($pagination);
        return view('roles.index',compact('roles'))
            ->with('i', ($request->input('page', 1) - 1) * $pagination);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permission = Permission::get();
        return view('roles.create',compact('permission'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'display_name' => 'required',
            'description' => 'required',
            'permission' => 'required',
        ]);

        $name = trim($request->input('name'));
        $displayName = trim($request->input('display_name'));
        $displayNameExists = DB::table('roles')
            ->where('display_name','=',$displayName)
            ->count();
        $nameExists = DB::table('roles')
            ->where('name','=',$name)
            ->count();
        if($nameExists>0){
            return redirect()->route('roles.create')
                ->with('error','Role Name Already Exists')->withInput();
        }elseif($displayNameExists>0){
            return redirect()->route('roles.create')
                ->with('error','Display Name Already Exists')->withInput();
        }

        $role = new Role();
        $role->name = $request->input('name');
        $role->display_name = $displayName;
        $role->description = $request->input('description');
        $role->save();

        foreach ($request->input('permission') as $key => $value) {
            $role->attachPermission($value);
        }

        return redirect()->route('roles.index')
                        ->with('success','Role created successfully');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::find($id);
        $rolePermissions = Permission::join("permission_role","permission_role.permission_id","=","permissions.id")
            ->where("permission_role.role_id",$id)
            ->get();
        $allPermissions = Permission::join("permission_role","permission_role.permission_id","=","permissions.id")
            ->groupBy('permissions.id')->get();
//dd($allPermissions);
        return view('roles.show',compact('role','rolePermissions', 'allPermissions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $role = Role::find($id);


        /*check only admin can can edit admin role starts*/

        if($role->name=='admin'){
            if( ! Auth::user()->hasRole(['admin']) ){
                return view('errors.403',['message'=>'Only a super-admin can edit super-admin permissions']);
            }
        }

        /*check only admin can can edit admin role ends*/


        $permission = Permission::get();
        $rolePermissions = DB::table("permission_role")->where("permission_role.role_id",$id)
            ->lists('permission_role.permission_id','permission_role.permission_id');

        return view('roles.edit',compact('role','permission','rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'display_name' => 'required',
            'description' => 'required',
            'permission' => 'required',
        ]);


        $displayName = trim($request->input('display_name'));

        $role = Role::findOrFail($id);

        $nameExists = DB::table('roles')
            ->where('display_name','=',$displayName)
            ->where('display_name','!=',$role->display_name)
            ->count();
        if($nameExists>0){
            return redirect()->route('roles.edit',$role->id)
                ->with('error','Display Name Already Exists');
        }

        $role->display_name = $displayName;
        $role->description = $request->input('description');
        $role->save();

        DB::table("permission_role")->where("permission_role.role_id",$id)
            ->delete();

        foreach ($request->input('permission') as $key => $value) {
            $role->attachPermission($value);
        }

        return redirect()->route('roles.index')
                        ->with('success','Role updated successfully');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $usersExists = DB::table('role_user')->where('role_id','=',$id)->count();

        if($usersExists==0) {

            $role = DB::table('roles')->where('id','=',$id)->first();

            /*check only admin can can edit admin role starts*/
            if($role->name=='admin'){
                return redirect()->route('roles.index')
                    ->with('error', 'Super user role cannot be deleted');
            }
            /*check only admin can can edit admin role ends*/

            DB::table('roles')->where('id','=',$id)->delete();


            return redirect()->route('roles.index')
                ->with('success', 'Role deleted successfully');
        }else{
            return redirect()->route('roles.index')
                ->with('error', 'Role cannot be deleted : Users Exists with this role');
        }
    }

  /**
   * [ajaxFilter ajax filter and search for roles listing page]
   * @param  Request $request [description]
   * @return [type]           [description]
   */
    public function ajaxFilter(Request $request){
      $role = $request->input('rname');
      $query = Role::select('*');
      $paginate = 20;

      if($role != '')
      {
        $query->Where("display_name", "LIKE", "%{$role}%");
      }

      $roles = $query->orderBy('id','ASC')->paginate($paginate);

      $html = view('roles.partials.role-search-result-block',['roles'=>$roles])->render();

      if($request->ajax()){
        return response()->json([
            'status' => 'success',
            'html' => $html,
        ]);
      }else{
        $search = $request->input('rname');
        return view('roles.index',compact('roles', 'search'))
            ->with('i', ($request->input('page', 1) - 1) * $paginate);
      }

    }
}
