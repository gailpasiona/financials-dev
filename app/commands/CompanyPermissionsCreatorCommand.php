<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CompanyPermissionsCreatorCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'financials:create_company_permissions';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create Permissions for a Company Instance';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$permissions_data = array(
			array('name' => "mib_manage_rfp", 'display_name' => "MIB-Request for Payment"),
			array('name' => "mib_manage_apv", 'display_name' => "MIB-AP Voucher"),
			array('name' => "mib_manage_cv", 'display_name' => "MIB-Cheque Voucher"),
			array('name' => "mib_approve_rfp", 'display_name' => "MIB-Approve Request for Payment"),
			array('name' => "mib_approve_apv", 'display_name' => "MIB-Approve AP Voucher"),
			array('name' => "mib_approve_cv", 'display_name' => "MIB-Approve Cheque Voucher"),
			array('name' => "mib_access", 'display_name' => "MIB Access"));
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('name', InputArgument::REQUIRED, 'The name of the company.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			// array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}
