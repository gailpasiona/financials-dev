<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->call('RoleTableSeeder');
		$this->command->info('System Admin Role Initialized!');

		$this->call('AdminSeeder');
		$this->command->info('Admin User set!');

		$this->call('PermissionTableSeeder');
		$this->command->info('Permission table seeded!');

		$this->call('CompanyTableSeeder');
		$this->command->info('Company table seeded!');

		
	}

}
