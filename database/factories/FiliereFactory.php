<?php

namespace Database\Factories;

use App\Models\Element;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Filiere>
 */
class FiliereFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $filieres = [
            'GL'    => 'Genie Logiciel',
            'GE'    => 'Genie Electrique',
            'GM'    => 'Genie Mecanique',
            'GC'    => 'Genie Civil',
            'GEII'  => 'Genie Electrique et Informatique Industrielle',
            'GIM'   => 'Genie Industriel et Maintenance',
            'ARI'   => 'Administration de Reseaux Informatiques'
            ];

        // pick a random element from the cartesian product
        $filiere_v = $this->faker->unique()->randomElement($filieres);

        //abbreviation
        $filiere_k = array_search($filiere_v, $filieres);

        return [
            'name' => $filiere_v,
            'abbreviation' => $filiere_k,
        ];
    }
}
