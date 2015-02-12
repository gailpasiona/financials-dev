<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CompanyCreatorCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'financials:create_company';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create Additional Company Instance.';

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
		$company_name = $this->ask('Please enter the name of the new company: ');
		$company_alias = $this->ask('Please enter the alias of the new company: ');
		
		$filter = Company::validate(array('name' => $company_name, 'alias' => $company_alias));

		if($filter->passes()){
			$company = new Company;

			$company->name = $company_name;

			$company->alias = $company_alias;

			$company->save();

			$this->info('Company added! Please run create company permission command to complete company setup');
		}

		else $this->error($filter->messages());
		
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			//array('example', InputArgument::REQUIRED, 'An example argument.'),
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
			//array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}
