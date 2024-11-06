<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Visitor;
use Illuminate\Support\Str;

class VisitorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $visitors = [
            [
                'nombres' => 'Carlos',
                'apellidos' => 'Perez',
                'identificacion' => Str::random(10),
                'motivo_visita' => 'Reunión con el decano'
            ],
            [
                'nombres' => 'Ana',
                'apellidos' => 'Martínez',
                'identificacion' => Str::random(10),
                'motivo_visita' => 'Entrevista de trabajo'
            ],
            [
                'nombres' => 'Luis',
                'apellidos' => 'García',
                'identificacion' => Str::random(10),
                'motivo_visita' => 'Asunto personal'
            ],
            [
                'nombres' => 'Maria',
                'apellidos' => 'Lopez',
                'identificacion' => Str::random(10),
                'motivo_visita' => 'Entrega de documentos'
            ],
            [
                'nombres' => 'Jorge',
                'apellidos' => 'Rodriguez',
                'identificacion' => Str::random(10),
                'motivo_visita' => 'Visita guiada'
            ],
        ];

        foreach ($visitors as $visitor) {
            Visitor::create($visitor);
        }
    }
}
