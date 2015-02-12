<?php

class RoleController extends BaseController {

	public function loadform(){
		$permissions= Permission::select('id','display_name')->get();
		return View::make('templates.modals.roles')->with('roles', $permissions);
	}
	 public function load_roles(){
        $roles = Role::select('id','name')->get();
        return Response::json($roles->toArray());
    }

    public function role_users($role){
        // $users = DB::table('users')->where(DB::raw('NOT EXISTS (SELECT role_id FROM diffsigm_mibint_server._tbl_assigned_roles 
        //         WHERE diffsigm_mibint_server._tbl_assigned_roles.role_id = 1
        //         AND diffsigm_mibint_server._tbl_assigned_roles.user_id = diffsigm_mibint_server._tbl_users.id)'))->get();
        $users = DB::select(DB::raw('SELECT id,full_name FROM _tbl_users 
            WHERE NOT EXISTS (SELECT role_id FROM _tbl_assigned_roles 
                WHERE _tbl_assigned_roles.role_id = :role_id
                AND _tbl_assigned_roles.user_id = _tbl_users.id)'), 
                array('role_id' => $role));
            
        return View::make('templates.modals.roles_attach')->with('users_list', json_decode(json_encode($users), true))
                                                ->with('role', Role::find($role)->name);
    }

    public function role_members($role){
        $users = Role::find($role)->users()->get(array('users.id','users.full_name'));
        return View::make('templates.modals.roles_detach')->with('members', json_decode(json_encode($users), true))
                                                ->with('role', Role::find($role)->name);
    }

    public function role_permissions($role){
        $perms = Permission::all(array('id','name','display_name'));
        $role_perms = DB::table('permission_role')->where('role_id', $role)->get(array('permission_id'));//Role::with('perms')->find($role)->perms->toArray();
        $active_roles = array();
        foreach ($role_perms as $role_perm) {
            $val =  array_values(get_object_vars($role_perm));
            array_push($active_roles, $val[0]);
        }
       // if(isset($role_perms[0])) $active_roles = get_object_vars($role_perms);
        //var_dump($active_roles);
        return View::make('templates.modals.permissions_attach')->with('perms', json_decode(json_encode($perms),true))
                                               ->with('role', Role::find($role)->name)
                                               ->with('role_perms', array_values($active_roles));//Response::json($perms);
    }

    public function assign_permisions(){
        if(Input::has('perms')){
            $role = Role::where('name','=', Input::get('role'))->first();
            try{
                $role->perms()->sync(Input::get('perms'));
                return Response::json(array('status' => '1', 'message' => 'Role permissions updated!'));
            }catch(Exception $e){
                //$e->getMessage();
            }
        }
        else return Response::json(array('status' => '1', 'message' => 'Please select permissions for this role!'));
    }

	public function create(){
       $role = new Role();
       $role->name = Input::get( 'name' );
      // $role->display_name = Input::get( 'display_name' );

        if($role->validate()){
        	if($role->save()){
              //  $role->perms()->sync(Input::get('roles'));
                return Response::json(array('status' => '1', 'message' => 'Successfuly added new role!'));
            }
        }
        else{
            	$error = $role->errors()->all(':message');
            	return Response::json(array('status' => '0', 'error' => json_encode($error)));
        }
    }
    
    public function assign_users_to_role(){
        $users = Input::get('users');
        $resp_msg = NULL;
        if(isset($users)){
            foreach ($users as $user) {
                $model = User::find($user);
                $role = Role::where('name',Input::get('role'))->first();
                 if(!$model->hasRole($role->name)){
                    $model->attachRole($role);
                    if(is_null($resp_msg)) $resp_msg = "Role Assigned to User(s)";
                 }
             }
             if(is_null($resp_msg)) $resp_msg = "User(s) already a member of this role";
             return Response::json(array('status' => '1', 'message' => $resp_msg));
        }
        else return Response::json(array("status" => '1', 'message' => 'No selected user')); 
        
       // return Response::json(Input::all());
    }

    public function detach_users_from_role(){
        $users = Input::get('users');
        $resp_msg = null;
        if(isset($users)){
            foreach ($users as $user) {
                $model = User::find($user);
                $role = Role::where('name',Input::get('role'))->first();
                 if($model->hasRole($role->name)){
                    $model->detachRole($role);
                    if(is_null($resp_msg)) $resp_msg = "Role Removed from User(s)";
                }
             }
             if(is_null($resp_msg)) $resp_msg = "User(s) not a member of this role";
             return Response::json(array('status' => '1', 'message' => $resp_msg));
        }
        else return Response::json(array("status" => '1', 'message' => 'No selected user'));  
    }


    // func function grant_permission_to_role(){
    //  $role = Role::find(Input::get('name'));
    //  $role->perms()->sync(Input::get('roles'));
    // }
    
    // public function save(){
        
    //     $group = new Role();
        
    //     $group->name = Input::get( 'group_name' );
       
    //     if($group->validate()){
            
    //         if($group->save()){
    //             $group->perms()->sync(Input::get('permissions'));
                
    //             return Redirect::action('GroupController@create')
    //                         ->with('notice', 'User Group Successfuly added!');
    //         }
    //         else{
    //             $error = $user->errors()->all(':message');

    //             return Redirect::action('GroupController@create')
    //                         ->withInput(Input::all())
    //                         ->with( 'error', $error );
    //         }
    //     }
    //     else{
    //         //dd($group->errors()->all(':message'));
    //         return Redirect::action('GroupController@create')
    //                         ->withInput(Input::all())
    //                         ->with( 'error', $group->errors()->all(':message'));
    //     }
    // }
    
    // public function add_permission(Role $group, $perms){
    //     $group->perms()->sync($perms);
    // }
    
    // public function attach_user(){
    //     $group= Role::select('id','name')->get();
    //     $users = User::select('id','username')->get();
    //     return View::make('groups.assign_to_group')->with('roles',$group)
    //             ->with('users',$users);
    // }

    
    // public function update_user_role(){
    //     $groups = Role::select('id','name')->get();
    //     $users = User::select('id','username')->get();
    //     $message = null;
    //     $user = User::where('id',Input::get('user'))->first();
    //     $group = Role::where('id',Input::get('role'))->first();
        
    //     if(!$user->hasRole($group->name)){
    //         $user->attachRole($group);
    //         $message = "User's Role Updated!";
    //     }
    //     else
    //         $message = "User is already assigned to the role";
            
        
    //     return View::make('groups.assign_to_group')->with('roles',$groups)
    //             ->with('users',$users)
    //         ->with('notice', $message);
        
    // }
}