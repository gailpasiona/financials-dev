<?php namespace Financials\Providers;

use Illuminate\Support\Serviceprovider;

class FinancialsServiceProvider extends ServiceProvider {
	
	public function register() {
		$this->app->bind('Financials\Repos\RfpRepositoryInterface', 'Financials\Repos\RfpRepository');

		$this->app->bind('Financials\Repos\PurchasesRepositoryInterface', 'Financials\Repos\PurchasesRepository');

		$this->app->bind('Financials\Repos\RegisterRepositoryInterface', 'Financials\Repos\RegisterRepository');

		$this->app->bind('Financials\Repos\CVRepositoryInterface', 'Financials\Repos\CVRepository');

		$this->app->bind('Financials\Repos\JournalRepositoryInterface', 'Financials\Repos\JournalRepository');

		$this->app->bind('Financials\Repos\GenLedgerRepositoryInterface', 'Financials\Repos\GenLedgerRepository');

		$this->app->bind('Financials\Repos\SubLedgerRepositoryInterface', 'Financials\Repos\SubLedgerRepository');

		$this->app->bind('Financials\Repos\SupplierRepositoryInterface', 'Financials\Repos\SupplierRepository');

		$this->app->bind('Financials\Repos\InvoiceLineRepositoryInterface', 'Financials\Repos\InvoiceLineRepository');

		$this->app->bind('Financials\Register', function(){
			return new \Financials\Repos\RegisterRepository;
		});

		$this->app->bind('Financials\Rfp', function(){
			return new \Financials\Repos\RfpRepository;
		});

		$this->app->bind('Financials\Coa', function(){
			return new \Financials\Repos\CoaRepository;
		});

		$this->app->bind('Financials\Purchases', function(){
			return new \Financials\Repos\PurchasesRepository;
		});

		$this->app->bind('Financials\Journal', function(){
			return new \Financials\Repos\JournalRepository;
		});

		$this->app->bind('Financials\GenLedger', function(){
			return new \Financials\Repos\GenLedgerRepository;
		});

		$this->app->bind('Financials\SubLedger', function(){
			return new \Financials\Repos\SubLedgerRepository;
		});

		$this->app->bind('Financials\Supplier', function(){
			return new \Financials\Repos\SupplierRepository;
		});

		$this->app->bind('Financials\InvoiceLine', function(){
			return new \Financials\Repos\InvoiceLineRepository;
		});
	}

}