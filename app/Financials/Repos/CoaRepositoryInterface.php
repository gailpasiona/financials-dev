<?php

namespace Financials\Repos;

interface CoaRepositoryInterface{
	
	public function selectAll();

	public function find($id);
}