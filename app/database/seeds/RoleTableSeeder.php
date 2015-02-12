<?php
 
class RoleTableSeeder extends Seeder {
 
public function run()
{
// Delete any content in the roles table
	Eloquent::unguard();

	DB::table('roles')->delete();
	 
	$role_data = array('name' => "Administrator");
	
	Role::create($role_data);
}
}