<?php

namespace Financials\Repos;

interface RegisterRepositoryInterface{
	
	public function selectAll();

	public function find($id);
}