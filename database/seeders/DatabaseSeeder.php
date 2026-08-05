<?php

/**
 * Documentacion de archivo:
 * Seeder de datos iniciales; inserta registros base para poder usar el sistema al levantar la base.
 *
 * Mantiene la documentacion dentro del codigo para facilitar el estudio y mantenimiento del proyecto.
 */

namespace Database\Seeders;

use App\Models\User;
use App\Models\TipoTratamiento;
use App\Models\Producto;
use App\Models\DoctorExterno;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Documentacion de clase:
 * Seeder de datos iniciales; inserta registros base para poder usar el sistema al levantar la base.
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    /**
     * Documentacion: Ejecuta el seeder.
     * Como lo hace: Inserta datos base o llama otros seeders para preparar el sistema.
     */
    public function run(): void
    {
        // Crear usuarios
        $this->crearUsuarios();

        // Crear doctores externos
        $this->crearDoctoresExternos();

        // Crear catálogo de tratamientos
        $this->crearTratamientos();

        // Crear productos
        $this->crearProductos();
    }

    /**
     * Documentacion: Ejecuta la operacion crear usuarios.
     * Como lo hace: Seeder de datos iniciales; inserta registros base para poder usar el sistema al levantar la base.
     */
    private function crearUsuarios(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@alpadent.com'],
            [
                'nombre' => 'Administrador',
                'apellido' => 'Sistema',
                'password' => Hash::make('Alpadent2026*'),
                'rol' => 'administrador',
                'activo' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'secretaria@alpadent.com'],
            [
                'nombre' => 'Secretaria',
                'apellido' => 'Oficial',
                'password' => Hash::make('Alpadent2024*'),
                'rol' => 'secretaria',
                'activo' => true,
            ]
        );
    }

    /**
     * Documentacion: Ejecuta la operacion crear doctores externos.
     * Como lo hace: Seeder de datos iniciales; inserta registros base para poder usar el sistema al levantar la base.
     */
    private function crearDoctoresExternos(): void
    {
        DoctorExterno::firstOrCreate(
            ['especialidad' => 'Cirugía de terceros molares y frenillos'],
            [
                'nombre' => 'Dr. Especialista',
                'apellido' => 'Cirujano',
                'especialidad' => 'Cirugía de terceros molares y frenillos',
                'activo' => true,
            ]
        );

        DoctorExterno::firstOrCreate(
            ['especialidad' => 'Endodoncia'],
            [
                'nombre' => 'Dr. Especialista',
                'apellido' => 'Endodoncista',
                'especialidad' => 'Endodoncia',
                'activo' => true,
            ]
        );

        DoctorExterno::firstOrCreate(
            ['especialidad' => 'Implantología y rehabilitación oral'],
            [
                'nombre' => 'Dr. Especialista',
                'apellido' => 'Implantólogo',
                'especialidad' => 'Implantología y rehabilitación oral',
                'activo' => true,
            ]
        );
    }

    /**
     * Documentacion: Ejecuta la operacion crear tratamientos.
     * Como lo hace: Seeder de datos iniciales; inserta registros base para poder usar el sistema al levantar la base.
     */
    private function crearTratamientos(): void
    {
        $tratamientos = [
            // OPERATORIA
            ['nombre' => 'Restauración resina 1 superficie', 'categoria' => 'operatoria', 'precio' => 35.00, 'descripcion' => 'Curación de resina compuesta en una superficie'],
            ['nombre' => 'Restauración resina 2 superficies', 'categoria' => 'operatoria', 'precio' => 45.00, 'descripcion' => 'Curación de resina compuesta en dos superficies'],
            ['nombre' => 'Restauración resina 3 superficies', 'categoria' => 'operatoria', 'precio' => 55.00, 'descripcion' => 'Curación de resina compuesta en tres superficies'],
            ['nombre' => 'Restauración amalgama', 'categoria' => 'operatoria', 'precio' => 30.00, 'descripcion' => 'Curación de amalgama dental'],
            ['nombre' => 'Reconstrucción dental', 'categoria' => 'operatoria', 'precio' => 65.00, 'descripcion' => 'Reconstrucción total de la corona dental'],
            
            // LIMPIEZA
            ['nombre' => 'Profilaxis dental (limpieza)', 'categoria' => 'limpieza', 'precio' => 40.00, 'descripcion' => 'Limpieza dental completa con ultrasonido y pulido'],
            ['nombre' => 'Detartraje supragingival', 'categoria' => 'limpieza', 'precio' => 45.00, 'descripcion' => 'Eliminación de sarro por encima de la encía'],
            ['nombre' => 'Detartraje subgingival', 'categoria' => 'limpieza', 'precio' => 60.00, 'descripcion' => 'Eliminación de sarro por debajo de la encía'],
            ['nombre' => 'Aplicación de flúor', 'categoria' => 'limpieza', 'precio' => 15.00, 'descripcion' => 'Aplicación tópica de flúor'],
            
            // PERIODONCIA
            ['nombre' => 'Raspado y alisado radicular por cuadrante', 'categoria' => 'periodoncia', 'precio' => 70.00, 'descripcion' => 'Tratamiento periodontal por cuadrante'],
            ['nombre' => 'Cirugía periodontal', 'categoria' => 'periodoncia', 'precio' => 150.00, 'descripcion' => 'Intervención quirúrgica de encías'],
            
            // ENDODONCIA
            ['nombre' => 'Endodoncia diente anterior (1 conducto)', 'categoria' => 'endodoncia', 'precio' => 120.00, 'descripcion' => 'Tratamiento de conducto diente anterior'],
            ['nombre' => 'Endodoncia diente premolar (2 conductos)', 'categoria' => 'endodoncia', 'precio' => 150.00, 'descripcion' => 'Tratamiento de conducto premolar'],
            ['nombre' => 'Endodoncia diente molar (3 conductos)', 'categoria' => 'endodoncia', 'precio' => 200.00, 'descripcion' => 'Tratamiento de conducto molar'],
            ['nombre' => 'Endodoncia diente molar (4 conductos)', 'categoria' => 'endodoncia', 'precio' => 230.00, 'descripcion' => 'Tratamiento de conducto molar complejo'],
            
            // EXODONCIA
            ['nombre' => 'Exodoncia simple', 'categoria' => 'exodoncia', 'precio' => 35.00, 'descripcion' => 'Extracción dental simple'],
            ['nombre' => 'Exodoncia compleja', 'categoria' => 'exodoncia', 'precio' => 60.00, 'descripcion' => 'Extracción dental con complicaciones'],
            
            // CIRUGÍA
            ['nombre' => 'Exodoncia de terceros molares', 'categoria' => 'cirugia', 'precio' => 150.00, 'descripcion' => 'Extracción de muela del juicio'],
            ['nombre' => 'Frenectomía labial', 'categoria' => 'cirugia', 'precio' => 120.00, 'descripcion' => 'Cirugía de frenillo labial'],
            ['nombre' => 'Implante dental (colocación)', 'categoria' => 'cirugia', 'precio' => 800.00, 'descripcion' => 'Colocación quirúrgica del implante'],
            
            // PRÓTESIS REMOVIBLE
            ['nombre' => 'Prótesis total superior', 'categoria' => 'protesis_removible', 'precio' => 350.00, 'descripcion' => 'Dentadura completa superior removible'],
            ['nombre' => 'Prótesis total inferior', 'categoria' => 'protesis_removible', 'precio' => 350.00, 'descripcion' => 'Dentadura completa inferior removible'],
            ['nombre' => 'Prótesis parcial removible acrílica', 'categoria' => 'protesis_removible', 'precio' => 200.00, 'descripcion' => 'Puente removible de acrílico'],
            ['nombre' => 'Prótesis parcial removible metálica', 'categoria' => 'protesis_removible', 'precio' => 280.00, 'descripcion' => 'Prótesis parcial con estructura metálica'],
            
            // PRÓTESIS FIJA
            ['nombre' => 'Corona de porcelana', 'categoria' => 'protesis_fija', 'precio' => 280.00, 'descripcion' => 'Corona dental de porcelana'],
            ['nombre' => 'Corona metal-porcelana', 'categoria' => 'protesis_fija', 'precio' => 230.00, 'descripcion' => 'Corona de metal con recubrimiento de porcelana'],
            ['nombre' => 'Corona de zirconia', 'categoria' => 'protesis_fija', 'precio' => 350.00, 'descripcion' => 'Corona de alta estética en zirconia'],
            ['nombre' => 'Puente de 3 piezas porcelana', 'categoria' => 'protesis_fija', 'precio' => 750.00, 'descripcion' => 'Puente fijo de tres unidades en porcelana'],
            ['nombre' => 'Carilla de porcelana', 'categoria' => 'protesis_fija', 'precio' => 300.00, 'descripcion' => 'Carilla estética de porcelana'],
            ['nombre' => 'Carilla de resina', 'categoria' => 'protesis_fija', 'precio' => 120.00, 'descripcion' => 'Carilla estética de resina compuesta'],
            ['nombre' => 'Corona sobre implante', 'categoria' => 'protesis_fija', 'precio' => 350.00, 'descripcion' => 'Rehabilitación protésica sobre implante'],
            
            // ORTODONCIA
            ['nombre' => 'Ortodoncia metálica (consulta inicial)', 'categoria' => 'ortodoncia', 'precio' => 30.00, 'descripcion' => 'Consulta inicial y plan de tratamiento'],
            ['nombre' => 'Ortodoncia metálica (mensualidad)', 'categoria' => 'ortodoncia', 'precio' => 60.00, 'descripcion' => 'Control mensual de ortodoncia metálica'],
            ['nombre' => 'Ortodoncia estética (mensualidad)', 'categoria' => 'ortodoncia', 'precio' => 80.00, 'descripcion' => 'Control mensual de ortodoncia estética'],
            ['nombre' => 'Retenedor removible', 'categoria' => 'ortodoncia', 'precio' => 80.00, 'descripcion' => 'Retenedor postortodoncia removible'],
            ['nombre' => 'Retenedor fijo', 'categoria' => 'ortodoncia', 'precio' => 60.00, 'descripcion' => 'Retenedor postortodoncia fijo'],
            
            // RAYOS X
            ['nombre' => 'Radiografía periapical', 'categoria' => 'rayos_x', 'precio' => 8.00, 'descripcion' => 'Radiografía de un diente y su raíz'],
            ['nombre' => 'Radiografía bite-wing', 'categoria' => 'rayos_x', 'precio' => 10.00, 'descripcion' => 'Radiografía de aleta de mordida'],
            ['nombre' => 'Radiografía panorámica', 'categoria' => 'rayos_x', 'precio' => 35.00, 'descripcion' => 'Radiografía panorámica de toda la boca'],
            
            // OTROS
            ['nombre' => 'Consulta / Revisión general', 'categoria' => 'otros', 'precio' => 15.00, 'descripcion' => 'Revisión general y diagnóstico'],
            ['nombre' => 'Blanqueamiento dental (consultorio)', 'categoria' => 'otros', 'precio' => 120.00, 'descripcion' => 'Blanqueamiento profesional en consultorio'],
            ['nombre' => 'Blanqueamiento dental (casa)', 'categoria' => 'otros', 'precio' => 80.00, 'descripcion' => 'Kit de blanqueamiento para uso en casa'],
            ['nombre' => 'Sellantes de fosas y fisuras', 'categoria' => 'otros', 'precio' => 20.00, 'descripcion' => 'Sellantes preventivos por diente'],
            ['nombre' => 'Tratamiento fluorización', 'categoria' => 'otros', 'precio' => 25.00, 'descripcion' => 'Aplicación profesional de fluorización'],
        ];

        foreach ($tratamientos as $tratamiento) {
            TipoTratamiento::firstOrCreate(
                ['nombre' => $tratamiento['nombre']],
                $tratamiento
            );
        }
    }

    /**
     * Documentacion: Ejecuta la operacion crear productos.
     * Como lo hace: Seeder de datos iniciales; inserta registros base para poder usar el sistema al levantar la base.
     */
    private function crearProductos(): void
    {
        $productos = [
            // Cepillos
            ['nombre' => 'Cepillo dental adulto suave', 'marca' => 'Oral-B', 'descripcion' => 'Cepillo manual cerdas suaves para adulto', 'precio_venta' => 3.50, 'stock_actual' => 20, 'stock_minimo' => 5],
            ['nombre' => 'Cepillo dental adulto medio', 'marca' => 'Oral-B', 'descripcion' => 'Cepillo manual cerdas medias para adulto', 'precio_venta' => 3.50, 'stock_actual' => 20, 'stock_minimo' => 5],
            ['nombre' => 'Cepillo dental infantil', 'marca' => 'Colgate', 'descripcion' => 'Cepillo manual para niños con mango antideslizante', 'precio_venta' => 3.00, 'stock_actual' => 15, 'stock_minimo' => 5],
            ['nombre' => 'Cepillo dental ortodoncia', 'marca' => 'GUM', 'descripcion' => 'Cepillo especial para brackets de ortodoncia', 'precio_venta' => 5.00, 'stock_actual' => 10, 'stock_minimo' => 3],
            ['nombre' => 'Cepillo interproximal', 'marca' => 'GUM', 'descripcion' => 'Cepillo para limpiar entre dientes y brackets', 'precio_venta' => 4.50, 'stock_actual' => 10, 'stock_minimo' => 3],
            
            // Pastas dentales
            ['nombre' => 'Pasta dental blanqueadora', 'marca' => 'Colgate', 'descripcion' => 'Crema dental para blanqueamiento diario 150ml', 'precio_venta' => 4.00, 'stock_actual' => 15, 'stock_minimo' => 5],
            ['nombre' => 'Pasta dental flúor total', 'marca' => 'Oral-B', 'descripcion' => 'Crema dental protección completa 150ml', 'precio_venta' => 3.80, 'stock_actual' => 15, 'stock_minimo' => 5],
            ['nombre' => 'Pasta dental infantil', 'marca' => 'Colgate', 'descripcion' => 'Crema dental para niños sin flúor 75ml', 'precio_venta' => 3.50, 'stock_actual' => 10, 'stock_minimo' => 3],
            ['nombre' => 'Pasta dental sensibilidad', 'marca' => 'Sensodyne', 'descripcion' => 'Crema dental para dientes sensibles 100ml', 'precio_venta' => 5.50, 'stock_actual' => 10, 'stock_minimo' => 3],
            ['nombre' => 'Pasta dental post-ortodoncia', 'marca' => 'GUM', 'descripcion' => 'Crema dental especial post tratamiento 75ml', 'precio_venta' => 6.00, 'stock_actual' => 8, 'stock_minimo' => 3],
            
            // Hilos dentales
            ['nombre' => 'Hilo dental clásico', 'marca' => 'Oral-B', 'descripcion' => 'Hilo dental encerado sabor menta 50m', 'precio_venta' => 2.50, 'stock_actual' => 20, 'stock_minimo' => 5],
            ['nombre' => 'Hilo dental superfloss', 'marca' => 'Oral-B', 'descripcion' => 'Hilo dental para puentes e implantes', 'precio_venta' => 5.00, 'stock_actual' => 10, 'stock_minimo' => 3],
            ['nombre' => 'Palillos con hilo dental', 'marca' => 'GUM', 'descripcion' => 'Palillos con hilo incorporado x30', 'precio_venta' => 3.00, 'stock_actual' => 15, 'stock_minimo' => 5],
            
            // Enjuagues bucales
            ['nombre' => 'Enjuague bucal antiséptico', 'marca' => 'Listerine', 'descripcion' => 'Enjuague antibacterial 500ml', 'precio_venta' => 6.50, 'stock_actual' => 12, 'stock_minimo' => 4],
            ['nombre' => 'Enjuague bucal flúor', 'marca' => 'Colgate', 'descripcion' => 'Enjuague con flúor anticaries 500ml', 'precio_venta' => 5.50, 'stock_actual' => 12, 'stock_minimo' => 4],
            ['nombre' => 'Enjuague bucal sin alcohol', 'marca' => 'Listerine', 'descripcion' => 'Enjuague suave sin alcohol 500ml', 'precio_venta' => 6.50, 'stock_actual' => 8, 'stock_minimo' => 3],
            
            // Accesorios
            ['nombre' => 'Irrigador oral portátil', 'marca' => 'Waterpik', 'descripcion' => 'Irrigador de agua para higiene dental', 'precio_venta' => 45.00, 'stock_actual' => 5, 'stock_minimo' => 2],
            ['nombre' => 'Limpiador lingual', 'marca' => 'Genérico', 'descripcion' => 'Raspador de lengua de acero inoxidable', 'precio_venta' => 2.00, 'stock_actual' => 15, 'stock_minimo' => 5],
            ['nombre' => 'Protector bucal deportivo', 'marca' => 'Genérico', 'descripcion' => 'Protector bucal termoformable para deporte', 'precio_venta' => 8.00, 'stock_actual' => 8, 'stock_minimo' => 3],
        ];

        foreach ($productos as $producto) {
            Producto::firstOrCreate(
                ['nombre' => $producto['nombre']],
                $producto
            );
        }
    }
}
