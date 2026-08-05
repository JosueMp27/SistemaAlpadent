{{--
    Documentacion de archivo:
    Vista Blade del modulo reportes; pinta la interfaz, llama la API y actualiza tablas, formularios o modales.
    Esta explicacion queda dentro de la vista para estudiar que pinta y que logica JavaScript ejecuta.
--}}

@extends('layouts.app')

@section('content')

<style>
    .reports-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .reports-title {
        font-weight: 800;
        color: #172033;
        margin-bottom: .25rem;
    }

    .reports-subtitle {
        color: #6c757d;
        margin: 0;
    }

    .reports-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }

    .report-card {
        border: 1px solid #e7edf5;
        border-radius: 8px;
        min-height: 190px;
        padding: 1.35rem;
        text-decoration: none;
        color: #172033;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 .35rem 1.1rem rgba(22, 34, 51, .05);
        transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
    }

    .report-card:hover {
        color: #172033;
        transform: translateY(-3px);
        border-color: #b8d5f5;
        box-shadow: 0 .75rem 1.5rem rgba(22, 34, 51, .09);
    }

    .report-card.patients {
        background: #eefaf3;
    }

    .report-card.appointments {
        background: #eef6ff;
    }

    .report-card.payments {
        background: #fff7e6;
    }

    .report-card.movements {
        background: #f4f0ff;
    }

    .report-icon {
        width: 54px;
        height: 54px;
        border-radius: 8px;
        background: rgba(255, 255, 255, .78);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        margin-bottom: 1rem;
        box-shadow: 0 .35rem 1rem rgba(22, 34, 51, .06);
    }

    .report-card.patients .report-icon {
        color: #198754;
    }

    .report-card.appointments .report-icon {
        color: #0d6efd;
    }

    .report-card.payments .report-icon {
        color: #b7791f;
    }

    .report-card.movements .report-icon {
        color: #6f42c1;
    }

    .report-name {
        font-size: 1.25rem;
        font-weight: 800;
        margin-bottom: .45rem;
    }

    .report-detail {
        color: #5d6878;
        font-size: .93rem;
        line-height: 1.45;
        margin-bottom: 1rem;
    }

    .report-action {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        color: #334155;
        font-weight: 700;
        font-size: .9rem;
    }

    @media (max-width: 991px) {
        .reports-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="reports-header">
    <div>
        <h3 class="reports-title">Reportes</h3>
        <p class="reports-subtitle">Seleccione el reporte que desea visualizar, imprimir o descargar.</p>
    </div>
</div>

<div class="reports-grid">
    <a class="report-card patients" href="/reportes/pacientes">
        <div>
            <span class="report-icon"><i class="bi bi-people"></i></span>
            <h4 class="report-name">Reporte de pacientes</h4>
            <p class="report-detail">Visualice todos los pacientes registrados, sus datos de contacto, estado e historia clinica.</p>
        </div>
        <span class="report-action">Abrir reporte <i class="bi bi-arrow-right"></i></span>
    </a>

    <a class="report-card appointments" href="/reportes/citas">
        <div>
            <span class="report-icon"><i class="bi bi-calendar2-check"></i></span>
            <h4 class="report-name">Reporte de citas</h4>
            <p class="report-detail">Consulte la agenda completa con paciente, tratamiento, profesional asignado y estado de atencion.</p>
        </div>
        <span class="report-action">Abrir reporte <i class="bi bi-arrow-right"></i></span>
    </a>

    <a class="report-card payments" href="/reportes/pagos">
        <div>
            <span class="report-icon"><i class="bi bi-cash-coin"></i></span>
            <h4 class="report-name">Reporte de pagos</h4>
            <p class="report-detail">Revise pagos de citas o ventas, montos cancelados, saldos pendientes y abonos registrados.</p>
        </div>
        <span class="report-action">Abrir reporte <i class="bi bi-arrow-right"></i></span>
    </a>

    <a class="report-card movements" href="/reportes/movimientos-pagos">
        <div>
            <span class="report-icon"><i class="bi bi-receipt-cutoff"></i></span>
            <h4 class="report-name">Movimientos de pagos</h4>
            <p class="report-detail">Visualice cada abono de citas o ventas, con fecha, monto, metodo, referencia y usuario receptor.</p>
        </div>
        <span class="report-action">Abrir reporte <i class="bi bi-arrow-right"></i></span>
    </a>
</div>

@endsection
