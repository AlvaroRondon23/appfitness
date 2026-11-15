<?php

namespace Tests\Unit;

use App\Models\PhysicalProfile;
use App\Services\PerfilCalculos;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PerfilCalculosTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo('2026-11-15 12:00:00');
    }

    private function perfil(array $datos = []): PhysicalProfile
    {
        return new PhysicalProfile(array_merge([
            'weight' => 70,
            'height' => 175,
            'birth_date' => '1996-06-15', // 30 años en la fecha fijada
            'gender' => 'Masculino',
            'activity_level' => 'Moderado',
        ], $datos));
    }

    public function test_calcula_todo_para_un_hombre_moderado(): void
    {
        $r = (new PerfilCalculos())->para($this->perfil());

        $this->assertSame(30, $r['edad']);
        $this->assertSame(22.9, $r['imc']);
        $this->assertSame('Normal', $r['categoria_imc']);
        $this->assertSame(1649, $r['tmb']);
        $this->assertSame(2556, $r['tdee']);
        $this->assertSame(256, $r['carbs']);
        $this->assertSame(192, $r['proteinas']);
        $this->assertSame(85, $r['grasas']);
    }

    public function test_mujer_sedentaria(): void
    {
        $r = (new PerfilCalculos())->para($this->perfil([
            'weight' => 60,
            'height' => 165,
            'gender' => 'Femenino',
            'activity_level' => 'Sedentario',
        ]));

        $this->assertSame(1320, $r['tmb']);
        $this->assertSame(1584, $r['tdee']);
    }

    public function test_genero_otro_usa_el_punto_medio(): void
    {
        $r = (new PerfilCalculos())->para($this->perfil([
            'weight' => 60,
            'height' => 165,
            'gender' => 'Otro',
        ]));

        $this->assertSame(1403, $r['tmb']);
    }

    public function test_nivel_de_actividad_desconocido_usa_factor_sedentario(): void
    {
        $r = (new PerfilCalculos())->para($this->perfil(['activity_level' => 'Desconocido']));

        $this->assertSame((int) round($r['tmb'] * 1.2), $r['tdee']);
    }

    #[DataProvider('categoriasImc')]
    public function test_categoria_del_imc(float $peso, string $categoria): void
    {
        $r = (new PerfilCalculos())->para($this->perfil(['weight' => $peso, 'height' => 170]));

        $this->assertSame($categoria, $r['categoria_imc']);
    }

    public static function categoriasImc(): array
    {
        return [
            'bajo peso' => [50, 'Bajo Peso'],
            'normal' => [70, 'Normal'],
            'sobrepeso' => [80, 'Sobrepeso'],
            'obesidad' => [95, 'Obesidad'],
        ];
    }
}