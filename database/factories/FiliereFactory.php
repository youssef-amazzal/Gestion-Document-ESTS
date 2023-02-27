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

        $promotion = range(2015, 2022);

        // generate a cartesian product of the two arrays
        $filiere_promotion = Arr::crossJoin($filieres, $promotion);

        // pick a random element from the cartesian product
        $filiere_promotion = $this->faker->unique()->randomElement($filiere_promotion);

        // get the filiere name and abbreviation
        $filiere_v = $filiere_promotion[0];
        $filiere_k = array_search($filiere_v, $filieres);

        // get the promotion
        $promotion = $filiere_promotion[1];

        return [
            'name' => $filiere_v,
            'abbreviation' => $filiere_k,
            'promotion' => $promotion
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function ($filiere) {
            $filiere->elements()->attach(Element::query()->inRandomOrder()->limit(5)->get());
        });
    }
}
