<?php
declare(strict_types=1);

namespace App\Imports;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;

class UsersImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return Model|User|null
     */
    public function model(array $row): Model|User|null
    {
        return new User([
            'name' => $row[0],
            'email' => $row[1],
            'sex' => $row[2],
            'password' => Hash::make($row[3]),
        ]);
    }
}
