<?php

namespace Financials\Repos;

interface RfpRepositoryInterface{
	
	public function selectAll();

	public function find($id);
}