<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

/**
 * Class UsersExport
 * @package App\Exports
 */
class UsersExport implements FromCollection
{

    public const FILE_NAME = 'users.xlsx';

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return User::all();
    }
}
