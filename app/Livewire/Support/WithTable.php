<?php

namespace App\Livewire\Support;

use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

trait WithTable
{
    use WithPagination;

    public int $perPage = 10;
    public $sortBy;
    public string $sortDirection = 'asc';
    public string $globalSearch = '';
    /**
     * @var array <string, string>
     */
    public array $filters = [];
    public array $columnsVisible = [];
    public array $perPageOptions = [5, 10, 25, 50];
        /**
     * @var string[]
     */

    public function mountWithTable(): void
    {
        foreach ($this->columns() as $key => $column) {
            if (!isset($this->columnsVisible[$key])) {
                $this->columnsVisible[$key] = $column['default'] ?? true;
            }
        }
    }

    abstract public function columns(): array;

    /**
     * Sort the table by a column.
     *
     * @param string $column
     */
    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleColumn($key): void
    {
        $this->columnsVisible[$key] = !($this->columnsVisible[$key] ?? false);
    }

    #[Computed]
    public function model()
    {
        return $this->model::query();
    }

    public function getRows()
    {
        $query = $this->model();

        if (!empty($this->relationships)) {
            $query = $query->with($this->relationships);
        }

        if ($this->globalSearch) {
            $query = $query->where(function ($q) {
                foreach ($this->columns() as $key => $col) {
                    if (!empty($col['searchable'])) {
                        if (str_contains($key, '.')) {
                            [$relation, $field] = explode('.', $key);
                            $q->orWhereHas($relation, fn ($r) => $r->where($field, 'like', '%' . $this->globalSearch . '%'));
                        } else {
                            $q->orWhere($key, 'like', '%' . $this->globalSearch . '%');
                        }
                    }
                }
            });
        }

        // sloupcové filtry
        foreach ($this->filters as $key => $value) {
            if (strlen($value) > 0) {
                if (str_contains($key, '.')) {
                    [$relation, $field] = explode('.', $key);
                    $query->whereHas($relation, fn ($r) => $r->where($field, 'like', '%' . $value . '%'));
                } else {
                    $query->where($key, 'like', '%' . $value . '%');
                }
            }
        }

        if ($this->sortBy) {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        return $query->paginate($this->perPage);
    }

    // query string binding — optional
    public function queryStringWithTable(): array
    {
        return [
            'perPage',
            'sortBy',
            'sortDirection',
            'globalSearch',
            'filters',
            'columnsVisible'
        ];
    }


}
