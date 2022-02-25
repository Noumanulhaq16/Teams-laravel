<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Agent;
use App\Models\Salesman;
use App\Models\SuperAdmin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $SuperAdmin = new SuperAdmin();
        $SuperAdmin->firstname = 'superadmin';
        $SuperAdmin->lastname = 'team';
        $SuperAdmin->email = 'superadmin@teams.com';
        $SuperAdmin->password = Hash::make('12345678');
        $SuperAdmin->role = 'superadmin';
        $SuperAdmin->save();

        $Admin = new Admin();
        $Admin->firstname = 'admin';
        $Admin->lastname = 'team';
        $Admin->email = 'admin@teams.com';
        $Admin->password = Hash::make('12345678');
        $Admin->role = 'admin';
        $Admin->save();

        $Agents = new Agent();
        $Agents->firstname = 'Agents';
        $Agents->lastname = 'team';
        $Agents->email = 'agent@teams.com';
        $Agents->password = Hash::make('12345678');
        $Agents->role = 'agent';
        $Agents->save();

        $Salesman = new Salesman();
        $Salesman->firstname = 'salesman';
        $Salesman->lastname = 'team';
        $Salesman->email = 'salesman@teams.com';
        $Salesman->password = Hash::make('12345678');
        $Salesman->role = 'salesman';
        $Salesman->save();
    }
}
