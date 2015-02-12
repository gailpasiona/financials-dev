<?php
 
class AdminSeeder extends Seeder {
 
public function run()
{
// Delete any content in the roles table
	Eloquent::unguard();

	DB::table('users')->delete();
	 
	$data = array('username' => "admin", 'full_name' => "System Administrator", 'password' => Hash::make('password'), 'confirmed' => '1', 'created_at' => new DateTime);
	
	DB::table('users')->insert($data);
	//User::signup($data);
	$user = User::where('username','admin')->first();
    $role = Role::where('name','Administrator')->first();
	$user->roles()->attach( $role->id );
 
}
}