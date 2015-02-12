<?php

namespace Financials\Repos;

interface SupplierRepositoryInterface{
	
	public function selectAll();

	public function find($id);
}