<?php

namespace Financials\Repos;

interface PurchasesRepositoryInterface{
	
	public function selectAll();

	public function find($id);

	public function updateById($id);
}