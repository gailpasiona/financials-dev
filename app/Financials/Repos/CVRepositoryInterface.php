<?php

namespace Financials\Repos;

interface CVRepositoryInterface{
	
	public function selectAll();

	public function find($id);
}