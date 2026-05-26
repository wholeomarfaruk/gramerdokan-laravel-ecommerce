<?php

namespace App\Livewire\Admin\Customers;

use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerList extends Component
{
    use WithPagination;

    public string $search   = '';
    public int    $perPage  = 15;
    public string $sortBy   = 'created_at';
    public string $sortDir  = 'desc';

    public string $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search'  => ['except' => ''],
        'perPage' => ['except' => 15],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy  = $column;
            $this->sortDir = 'asc';
        }
        $this->resetPage();
    }

    public function render()
    {
        $customers = Customer::withCount('orders')
            ->withSum('orders', 'total')
            ->when($this->search, fn($q) =>
                $q->where(function ($q) {
                    $q->where('first_name', 'LIKE', "%{$this->search}%")
                      ->orWhere('last_name',  'LIKE', "%{$this->search}%")
                      ->orWhere('phone',      'LIKE', "%{$this->search}%")
                      ->orWhere('email',      'LIKE', "%{$this->search}%");
                })
            )
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

        return view('livewire.admin.customers.customer-list', compact('customers'));
    }
}
