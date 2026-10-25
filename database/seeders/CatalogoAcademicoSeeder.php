<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Carrera;
use Illuminate\Database\Seeder;

class CatalogoAcademicoSeeder extends Seeder
{
    public function run(): void
    {
        $catalogo = [
            'SAL' => [
                'nombre' => 'Área Salud',
                'carreras' => [
                    ['codigo' => 'BQF', 'nombre' => 'Bioquímica Farmacia'],
                    ['codigo' => 'ENF', 'nombre' => 'Enfermería'],
                    ['codigo' => 'LAB', 'nombre' => 'Laboratorio Clínico'],
                    ['codigo' => 'ODO', 'nombre' => 'Odontología'],
                    ['codigo' => 'MED', 'nombre' => 'Medicina'],
                    ['codigo' => 'RAD', 'nombre' => 'Radioterapia'],
                    ['codigo' => 'MNU', 'nombre' => 'Medicina Nuclear'],
                    ['codigo' => 'FIS', 'nombre' => 'Fisioterapia'],
                ],
            ],
            'TEC' => [
                'nombre' => 'Área Tecnología',
                'carreras' => [
                    ['codigo' => 'AGR', 'nombre' => 'Agronomía'],
                    ['codigo' => 'MIN', 'nombre' => 'Minas y Topografía'],
                    ['codigo' => 'MAU', 'nombre' => 'Mecánica Automotriz'],
                    ['codigo' => 'INF', 'nombre' => 'Informática'],
                    ['codigo' => 'ELE', 'nombre' => 'Electromecánica'],
                    ['codigo' => 'CIV', 'nombre' => 'Civil'],
                    ['codigo' => 'REL', 'nombre' => 'Recursos Evaporíticos de Litio'],
                    ['codigo' => 'BIO', 'nombre' => 'Biomédica'],
                ],
            ],
            'SOC' => [
                'nombre' => 'Área Sociales',
                'carreras' => [
                    ['codigo' => 'COM', 'nombre' => 'Comunicación Social'],
                    ['codigo' => 'EDU', 'nombre' => 'Ciencias de la Educación'],
                    ['codigo' => 'DER', 'nombre' => 'Derecho'],
                    ['codigo' => 'CON', 'nombre' => 'Contaduría Pública'],
                ],
            ],
        ];

        foreach ($catalogo as $codigoArea => $datos) {
            $area = Area::updateOrCreate(
                ['codigo' => $codigoArea],
                ['nombre' => $datos['nombre'], 'activo' => true]
            );

            foreach ($datos['carreras'] as $carrera) {
                Carrera::updateOrCreate(
                    ['codigo' => $carrera['codigo']],
                    [
                        'area_id' => $area->id,
                        'nombre' => $carrera['nombre'],
                        'activo' => true,
                    ]
                );
            }
        }
    }
}
