<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('health_parameters', function (Blueprint $table) {
            $table->string('health_parameter_emoji')->nullable()->after('health_parameter_show_type');
        });

        Schema::table('symptom', function (Blueprint $table) {
            $table->string('symptom_emoji')->nullable()->after('symptom_desc');
        });

        // Seed emojis for health parameters
        $healthEmojis = [
            'Average Sleep' => '😴',
            'Physical Activity' => '🏃',
            'Smoking' => '🚭',
            'Alcohol' => '🍷',
            'Water Intake' => '💧',
            'Diet' => '🥗',
            'Stress Level' => '🧠',
            'Fasting' => '⏱️',
            'Work Type' => '💼',
            'Menstrual Cycle' => '📅',
        ];

        foreach ($healthEmojis as $name => $emoji) {
            DB::table('health_parameters')
                ->where('health_parameter_name', $name)
                ->update(['health_parameter_emoji' => $emoji]);
        }

        // Seed emojis for symptoms
        $symptomEmojis = [
            'Fatigue' => '😩',
            'Dizziness' => '😵',
            'Headache' => '🤕',
            'Excessive Urination' => '💧',
            'Weight Gain' => '⚖️',
            'Hair Fall' => '💇',
            'Poor Sleep' => '🌙',
            'Bloating' => '🤢',
            'Palpitations' => '💓',
            'Mood Changes' => '😔',
            'Blurred Vision' => '👁️',
            'Joint or Muscle Pain' => '🦴',
        ];

        foreach ($symptomEmojis as $name => $emoji) {
            DB::table('symptom')
                ->where('symptom_name', $name)
                ->update(['symptom_emoji' => $emoji]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('health_parameters', function (Blueprint $table) {
            $table->dropColumn('health_parameter_emoji');
        });

        Schema::table('symptom', function (Blueprint $table) {
            $table->dropColumn('symptom_emoji');
        });
    }
};
