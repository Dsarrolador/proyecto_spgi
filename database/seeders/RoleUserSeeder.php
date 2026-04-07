<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoleUser;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = ['Administracion', 'Encargado', 'Desarrollador', 'Soporte'];

        foreach ($roles as $role) {
            RoleUser::updateOrCreate(['nombre' => $role]);
        }
    }
}
