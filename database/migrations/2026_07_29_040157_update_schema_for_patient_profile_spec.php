<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add fields to patient table
        Schema::table('patient', function (Blueprint $table) {
            $table->string('preferred_language')->nullable()->after('patient_city');
            $table->integer('height_cm')->nullable()->after('preferred_language');
            $table->integer('weight_kg')->nullable()->after('height_cm');
            $table->string('profile_step')->default('basic')->after('weight_kg');
            $table->boolean('is_profile_complete')->default(false)->after('profile_step');
        });

        // 2. Add health_parameter_option5 to health_parameters table
        Schema::table('health_parameters', function (Blueprint $table) {
            $table->string('health_parameter_option5')->nullable()->after('health_parameter_option4');
        });

        // 3. Seed/Update the 10 health parameters
        $parameters = [
            [
                'health_parameter_name' => 'Average Sleep',
                'health_parameter_question' => 'How many hours do you sleep daily?',
                'health_parameter_show_type' => 'dropdown',
                'health_parameter_option1' => 'Less than 5 hours',
                'health_parameter_option2' => '5 - 6 hours',
                'health_parameter_option3' => '6 - 7 hours',
                'health_parameter_option4' => '7 - 8 hours',
                'health_parameter_option5' => 'More than 8 hours',
                'health_parameter_order' => 1,
                'health_parameter_status' => 1,
            ],
            [
                'health_parameter_name' => 'Physical Activity',
                'health_parameter_question' => 'How often do you exercise or be active?',
                'health_parameter_show_type' => 'dropdown',
                'health_parameter_option1' => 'Rarely',
                'health_parameter_option2' => '1 - 2 days / week',
                'health_parameter_option3' => '3 - 4 days / week',
                'health_parameter_option4' => '5 - 6 days / week',
                'health_parameter_option5' => 'Daily',
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
                'health_parameter_option5' => null,
                'health_parameter_order' => 3,
                'health_parameter_status' => 1,
            ],
            [
                'health_parameter_name' => 'Alcohol',
                'health_parameter_question' => 'Do you drink alcohol?',
                'health_parameter_show_type' => 'radio',
                'health_parameter_option1' => 'Never',
                'health_parameter_option2' => 'Occasionally',
                'health_parameter_option3' => 'Regularly',
                'health_parameter_option4' => null,
                'health_parameter_option5' => null,
                'health_parameter_order' => 4,
                'health_parameter_status' => 1,
            ],
            [
                'health_parameter_name' => 'Water Intake',
                'health_parameter_question' => 'How much water do you drink daily?',
                'health_parameter_show_type' => 'dropdown',
                'health_parameter_option1' => 'Less than 1 L',
                'health_parameter_option2' => '1 - 1.5 L',
                'health_parameter_option3' => '1.5 - 2 L',
                'health_parameter_option4' => '2 - 3 L',
                'health_parameter_option5' => 'More than 3 L',
                'health_parameter_order' => 5,
                'health_parameter_status' => 1,
            ],
            [
                'health_parameter_name' => 'Diet',
                'health_parameter_question' => 'What is your diet type?',
                'health_parameter_show_type' => 'dropdown',
                'health_parameter_option1' => 'Vegetarian',
                'health_parameter_option2' => 'Non-Vegetarian',
                'health_parameter_option3' => 'Vegan',
                'health_parameter_option4' => 'Eggetarian',
                'health_parameter_option5' => 'Mixed',
                'health_parameter_order' => 6,
                'health_parameter_status' => 1,
            ],
            [
                'health_parameter_name' => 'Stress Level',
                'health_parameter_question' => 'What is your stress level?',
                'health_parameter_show_type' => 'dropdown',
                'health_parameter_option1' => 'Low',
                'health_parameter_option2' => 'Moderate',
                'health_parameter_option3' => 'High',
                'health_parameter_option4' => 'Very High',
                'health_parameter_option5' => null,
                'health_parameter_order' => 7,
                'health_parameter_status' => 1,
            ],
            [
                'health_parameter_name' => 'Fasting',
                'health_parameter_question' => 'Do you practice fasting?',
                'health_parameter_show_type' => 'radio',
                'health_parameter_option1' => 'Never',
                'health_parameter_option2' => 'Occasionally',
                'health_parameter_option3' => 'Regularly',
                'health_parameter_option4' => null,
                'health_parameter_option5' => null,
                'health_parameter_order' => 8,
                'health_parameter_status' => 1,
            ],
            [
                'health_parameter_name' => 'Work Type',
                'health_parameter_question' => 'What is your work type?',
                'health_parameter_show_type' => 'dropdown',
                'health_parameter_option1' => 'Regular (Day)',
                'health_parameter_option2' => 'Night Shift',
                'health_parameter_option3' => 'Rotational',
                'health_parameter_option4' => 'Flexible',
                'health_parameter_option5' => 'Not Working',
                'health_parameter_order' => 9,
                'health_parameter_status' => 1,
            ],
            [
                'health_parameter_name' => 'Menstrual Cycle',
                'health_parameter_question' => 'What is your menstrual cycle status?',
                'health_parameter_show_type' => 'dropdown',
                'health_parameter_option1' => 'Regular',
                'health_parameter_option2' => 'Irregular',
                'health_parameter_option3' => 'Not Applicable',
                'health_parameter_option4' => 'Prefer not to say',
                'health_parameter_option5' => null,
                'health_parameter_order' => 10,
                'health_parameter_status' => 1,
            ],
        ];

        // Clean existing health_parameters first or update/insert
        \App\Models\HealthParameter::query()->delete();
        foreach ($parameters as $parameter) {
            \App\Models\HealthParameter::create($parameter);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient', function (Blueprint $table) {
            $table->dropColumn(['preferred_language', 'height_cm', 'weight_kg', 'profile_step', 'is_profile_complete']);
        });

        Schema::table('health_parameters', function (Blueprint $table) {
            $table->dropColumn('health_parameter_option5');
        });
    }
};
