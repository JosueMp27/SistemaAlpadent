<?php

/**
 * Documentacion de archivo:
 * Archivo de configuracion de Laravel o Alpadent; centraliza valores reutilizables y variables de entorno.
 *
 * Mantiene la documentacion dentro del codigo para facilitar el estudio y mantenimiento del proyecto.
 */

/**
 * Configuración de la API Alpadent
 * Constantes y configuraciones globales para el proyecto
 */

return [
    'app_name' => 'Alpadent',
    'app_version' => '1.0.0',
    'api_version' => 'v1',

    'roles' => [
        'administrador' => 'Administrador (Odontólogo)',
        'secretaria' => 'Secretaria',
    ],

    'estado_cita' => [
        'programada' => 'Programada',
        'en_curso' => 'En Curso',
        'completada' => 'Completada',
        'cancelada' => 'Cancelada',
        'no_asistio' => 'No Asistió',
    ],

    'estado_pago' => [
        'pendiente' => 'Pendiente',
        'parcial' => 'Pago Parcial',
        'pagado' => 'Pagado',
    ],

    'metodo_pago' => [
        'efectivo' => 'Efectivo',
        'transferencia' => 'Transferencia',
        'tarjeta' => 'Tarjeta',
    ],

    'condicion_diente' => [
        'sano' => 'Sano',
        'cariado' => 'Cariado',
        'obturado' => 'Obturado',
        'perdido' => 'Perdido',
        'fractura' => 'Fractura',
        'corona' => 'Corona',
        'implante' => 'Implante',
        'extraccion_indicada' => 'Extracción Indicada',
        'otro' => 'Otro',
    ],

    'tipo_movimiento' => [
        'entrada' => 'Entrada',
        'salida' => 'Salida',
        'ajuste' => 'Ajuste',
    ],

    'categorias_tratamiento' => [
        'operatoria' => 'Operatoria',
        'periodoncia' => 'Periodoncia',
        'protesis_removible' => 'Prótesis Removible',
        'protesis_fija' => 'Prótesis Fija',
        'exodoncia' => 'Exodoncia',
        'ortodoncia' => 'Ortodoncia',
        'endodoncia' => 'Endodoncia',
        'rayos_x' => 'Rayos X',
        'cirugia' => 'Cirugía',
        'limpieza' => 'Limpieza',
        'otros' => 'Otros',
    ],

    'horarios' => [
        'inicio' => '09:00',
        'fin' => '18:00',
        'duracion_minima_cita' => 30, // minutos
    ],

    'tokens' => [
        'expiration' => 60 * 24 * 365, // 1 año en minutos
    ],

    'pagination' => [
        'per_page' => 15,
    ],

    'validaciones' => [
        'nombre_min' => 2,
        'nombre_max' => 100,
        'apellido_min' => 2,
        'apellido_max' => 100,
        'email_max' => 150,
        'telefono_min' => 7,
        'telefono_max' => 20,
        'descripcion_min' => 5,
        'motivo_consulta_min' => 3,
        'motivo_consulta_max' => 255,
        'nombre_producto_min' => 2,
        'nombre_producto_max' => 150,
        'indice_cpo_max' => 32,
    ],
];
