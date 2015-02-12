<?php
 
class CoaTableSeeder extends CoaBaseSeeder
{
public function __construct()
{
$this->table = 'coa_accounts'; // Your database table name
$this->filename = app_path().'/database/csv/STC.csv'; // Filename and location of data in csv file
}
 
}