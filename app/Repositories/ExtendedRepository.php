<?php namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;

abstract class ExtendedRepository extends BaseRepository
{
	protected $filters = [];
	protected $skipFilters = false;

    public function includeRelations($relations)
    {
        $this->presenter->parseIncludes($relations);
        return $this;
    }

	public function parserResult($result)
	{
		$result = $this->applyFilters($result);
		return parent::parserResult($result);
	}

	public function addFilter($filter)
	{
		$this->filters[] = $filter;
		return $this;
	}

	public function skipFilters($status = true)
	{
		$this->skipFilters = $status;
		return $this;
	}

	protected function applyFilters($result)
	{
		if ($this->skipFilters) {
			return $result;
		}

		foreach ($this->filters as $filter) {
			$result = $filter->apply($result);
		}
		
		return $result;
	}
}
