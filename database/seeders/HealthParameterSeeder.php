<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HealthParameter;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class HealthParameterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'health-parameter-list',
            'health-parameter-add',
            'health-parameter-edit',
            'health-parameter-delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign permissions to Super Admin role if exists
        $superAdmin = Role::where('name', 'Super Admin')->where('guard_name', 'web')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
        }

        $parameters = [
            [
                'health_parameter_name' => 'Average Sleep',
                'health_parameter_question' => 'How many hours do you sleep daily?',
                'health_parameter_show_type' => 'dropdown',
                'health_parameter_option1' => '6 - 7 hours',
                'health_parameter_option2' => '7 - 8 hours',
                'health_parameter_option3' => 'Less than 6 hours',
                'health_parameter_option4' => 'More than 8 hours',
                'health_parameter_order' => 1,
                'health_parameter_status' => 1,
            ],
            [
                'health_parameter_name' => 'Physical Activity',
                'health_parameter_question' => 'How often do you exercise or be active?',
                'health_parameter_show_type' => 'dropdown',
                'health_parameter_option1' => '3 - 4 days / week',
                'health_parameter_option2' => 'Daily',
                'health_parameter_option3' => '1 - 2 days / week',
                'health_parameter_option4' => 'Rarely',
                'health_parameter_order' => 2,
                'health_parameter_status' => 1,
            ],
            [
                'health_parameter_name' => 'Smoking',
                'health_parameter_question' => 'Do you smoke?',
                'health_parameter_show_type' => 'radio',
                'health_parameter_option1' => 'Never',
                'health_parameter_option2' => 'Former',
                'health_parameter_option3' => 'Yes, regularly',
                'health_parameter_option4' => null,
                'health_parameter_order' => 3,
                'health_parameter_status' => 1,
            ],
            [
                'health_parameter_name' => 'Water Intake',
                'health_parameter_question' => 'How much water do you drink daily?',
                'health_parameter_show_type' => 'dropdown',
                'health_parameter_option1' => '1.5 - 2 L',
                'health_parameter_option2' => 'Less than 1.5 L',
                'health_parameter_option3' => '2 - 3 L',
                'health_parameter_option4' => 'More than 3 L',
                'health_parameter_order' => 4,
                'health_parameter_status' => 1,
            ],
        ];

        foreach ($parameters as $parameter) {
            HealthParameter::firstOrCreate(
                ['health_parameter_name' => $parameter['health_parameter_name']],
                $parameter
            );
        }
    }
}
