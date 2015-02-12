<?php

use Zizaco\Confide\ConfideUser;
use Zizaco\Confide\ConfideUserInterface;
use Zizaco\Entrust\HasRole;

class User extends Eloquent implements ConfideUserInterface
{
    use ConfideUser, HasRole;

    protected $fillable = array('full_name', 'username', 'password');
    protected $hidden = array('pivot');

 //    public function roles()
	// {
	//     return $this->belongsToMany('Role','assigned_roles');
	// }

	/**
     * Detach all roles from a user
     * 
     * @return object
     */
    public function detachAllRoles()
    {
        DB::table('assigned_roles')->where('user_id', $this->id)->delete();

        return $this;
    }
}