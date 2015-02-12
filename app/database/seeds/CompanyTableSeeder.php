<?php
 
class CompanyTableSeeder extends Seeder {
 
public function run()
{
// Delete any content in the roles table
	Eloquent::unguard();
	DB::table('companies')->delete();
	 
	$companies_data = array(
	array('name' => "MIB 1", 'alias' => "mib1"),
	array('name' => "MIB 2", 'alias' => "mib2"),
	array('name' => "CSISI", 'alias' => "csisi"),
	array('name' => "MIBSTC", 'alias' => "mibstc")
	);

	foreach ($companies_data AS $company) {
		Company::create($company);
		}
	}
 
}