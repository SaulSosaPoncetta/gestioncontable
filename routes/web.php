<?php

use App\Http\Controllers\Admin\PagoController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\SuscriptorController;
use App\Http\Controllers\AsientoController;
use App\Http\Controllers\ComprobanteController;
use App\Http\Controllers\ConfigContableController;
use App\Http\Controllers\CuentaController;
use App\Http\Controllers\TerceroController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EjercicioController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\LibroController;
use App\Http\Controllers\LibroIvaController;
use App\Http\Controllers\SuscripcionController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

/*
|--------------------------------------------------------------------------
| Sistema (clientes)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'empresa', 'suscripcion'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Mi suscripción (siempre accesible, aun bloqueada)
    Route::get('/mi-suscripcion', [SuscripcionController::class, 'show'])->name('suscripcion.show');

    // Mi empresa
    Route::get('/mi-empresa', [EmpresaController::class, 'edit'])->name('empresa.edit');
    Route::put('/mi-empresa', [EmpresaController::class, 'update'])->name('empresa.update');

    // Usuarios de la empresa
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('/usuarios/crear', [UsuarioController::class, 'create'])->name('usuarios.create');
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::get('/usuarios/{usuario}/editar', [UsuarioController::class, 'edit'])->name('usuarios.edit');
    Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');

    // Plan de cuentas
    Route::get('/cuentas', [CuentaController::class, 'index'])->name('cuentas.index');
    Route::get('/cuentas/crear', [CuentaController::class, 'create'])->name('cuentas.create');
    Route::post('/cuentas', [CuentaController::class, 'store'])->name('cuentas.store');
    Route::get('/cuentas/exportar', [CuentaController::class, 'exportar'])->name('cuentas.exportar');
    Route::get('/cuentas/importar', [CuentaController::class, 'importarForm'])->name('cuentas.importar.form');
    Route::post('/cuentas/importar', [CuentaController::class, 'importar'])->name('cuentas.importar');
    Route::get('/cuentas/plantilla', [CuentaController::class, 'plantilla'])->name('cuentas.plantilla');
    Route::post('/cuentas/cargar-modelo', [CuentaController::class, 'cargarModelo'])->name('cuentas.modelo');
    Route::get('/cuentas/{cuenta}/editar', [CuentaController::class, 'edit'])->name('cuentas.edit');
    Route::put('/cuentas/{cuenta}', [CuentaController::class, 'update'])->name('cuentas.update');
    Route::delete('/cuentas/{cuenta}', [CuentaController::class, 'destroy'])->name('cuentas.destroy');
    Route::post('/cuentas/{cuenta}/estado', [CuentaController::class, 'cambiarEstado'])->name('cuentas.estado');

    // Asientos
    Route::get('/asientos', [AsientoController::class, 'index'])->name('asientos.index');
    Route::get('/asientos/crear', [AsientoController::class, 'create'])->name('asientos.create');
    Route::post('/asientos', [AsientoController::class, 'store'])->name('asientos.store');
    Route::post('/asientos/renumerar', [AsientoController::class, 'renumerar'])->name('asientos.renumerar');
    Route::get('/asientos/{asiento}', [AsientoController::class, 'show'])->name('asientos.show');
    Route::get('/asientos/{asiento}/editar', [AsientoController::class, 'edit'])->name('asientos.edit');
    Route::put('/asientos/{asiento}', [AsientoController::class, 'update'])->name('asientos.update');
    Route::delete('/asientos/{asiento}', [AsientoController::class, 'destroy'])->name('asientos.destroy');
    Route::post('/asientos/{asiento}/anular', [AsientoController::class, 'anular'])->name('asientos.anular');

    // Libros contables
    Route::get('/libros/diario', [LibroController::class, 'diario'])->name('libros.diario');
    Route::get('/libros/mayor', [LibroController::class, 'mayor'])->name('libros.mayor');
    Route::get('/libros/sumas-y-saldos', [LibroController::class, 'sumasSaldos'])->name('libros.sumas');

    // Clientes y proveedores
    Route::get('/terceros', [TerceroController::class, 'index'])->name('terceros.index');
    Route::get('/terceros/crear', [TerceroController::class, 'create'])->name('terceros.create');
    Route::post('/terceros', [TerceroController::class, 'store'])->name('terceros.store');
    Route::get('/terceros/{tercero}/editar', [TerceroController::class, 'edit'])->name('terceros.edit');
    Route::put('/terceros/{tercero}', [TerceroController::class, 'update'])->name('terceros.update');
    Route::delete('/terceros/{tercero}', [TerceroController::class, 'destroy'])->name('terceros.destroy');

    // Comprobantes de venta y compra (módulo IVA)
    Route::middleware('modulo:iva')
        ->prefix('comprobantes/{circuito}')
        ->whereIn('circuito', ['venta', 'compra'])
        ->name('comprobantes.')
        ->group(function () {
            Route::get('/', [ComprobanteController::class, 'index'])->name('index');
            Route::get('/crear', [ComprobanteController::class, 'create'])->name('create');
            Route::post('/', [ComprobanteController::class, 'store'])->name('store');
            Route::get('/{comprobante}', [ComprobanteController::class, 'show'])->name('show');
            Route::get('/{comprobante}/editar', [ComprobanteController::class, 'edit'])->name('edit');
            Route::put('/{comprobante}', [ComprobanteController::class, 'update'])->name('update');
            Route::post('/{comprobante}/anular', [ComprobanteController::class, 'anular'])->name('anular');
        });

    // Libros IVA y posición mensual (módulo IVA)
    Route::middleware('modulo:iva')->prefix('iva')->name('iva.')->group(function () {
        Route::get('/libro/{circuito}', [LibroIvaController::class, 'libro'])->whereIn('circuito', ['venta', 'compra'])->name('libro');
        Route::get('/posicion', [LibroIvaController::class, 'posicion'])->name('posicion');
        Route::post('/posicion/cerrar', [LibroIvaController::class, 'cerrar'])->name('cerrar');
        Route::post('/posicion/{posicion}/reabrir', [LibroIvaController::class, 'reabrir'])->name('reabrir');
    });

    // Configuración de cuentas automáticas
    Route::get('/configuracion/cuentas', [ConfigContableController::class, 'edit'])->name('config.cuentas');
    Route::put('/configuracion/cuentas', [ConfigContableController::class, 'update'])->name('config.cuentas.update');

    // Estados contables
    Route::get('/estados/situacion-patrimonial', [EstadoController::class, 'situacion'])->name('estados.situacion');
    Route::get('/estados/resultados', [EstadoController::class, 'resultados'])->name('estados.resultados');

    // Ejercicios
    Route::get('/ejercicios',[EjercicioController::class, 'index'])->name('ejercicios.index');
    Route::get('/ejercicios/crear', [EjercicioController::class, 'create'])->name('ejercicios.create');
    Route::post('/ejercicios', [EjercicioController::class, 'store'])->name('ejercicios.store');
    Route::get('/ejercicios/{ejercicio}/editar', [EjercicioController::class, 'edit'])->name('ejercicios.edit');
    Route::put('/ejercicios/{ejercicio}', [EjercicioController::class, 'update'])->name('ejercicios.update');
    Route::delete('/ejercicios/{ejercicio}', [EjercicioController::class, 'destroy'])->name('ejercicios.destroy');
    Route::post('/ejercicios/{ejercicio}/estado', [EjercicioController::class, 'cambiarEstado'])->name('ejercicios.estado');
    Route::post('/ejercicios/{ejercicio}/seleccionar', [EjercicioController::class, 'seleccionar'])->name('ejercicios.seleccionar');
});

/*
|--------------------------------------------------------------------------
| Panel de administración SaaS (solo superadmin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'empresa', 'superadmin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/suscriptores', [SuscriptorController::class, 'index'])->name('suscriptores.index');
    Route::get('/suscriptores/{empresa}', [SuscriptorController::class, 'edit'])->name('suscriptores.edit');
    Route::put('/suscriptores/{empresa}', [SuscriptorController::class, 'update'])->name('suscriptores.update');
    Route::post('/suscriptores/{empresa}/ingresar', [SuscriptorController::class, 'ingresar'])->name('suscriptores.ingresar');
    Route::post('/salir-de-empresa', [SuscriptorController::class, 'salir'])->name('suscriptores.salir');

    Route::post('/suscriptores/{empresa}/pagos', [PagoController::class, 'store'])->name('pagos.store');
    Route::delete('/pagos/{pago}', [PagoController::class, 'destroy'])->name('pagos.destroy');

    Route::get('/planes', [PlanController::class, 'index'])->name('planes.index');
    Route::get('/planes/crear', [PlanController::class, 'create'])->name('planes.create');
    Route::post('/planes', [PlanController::class, 'store'])->name('planes.store');
    Route::get('/planes/{plan}/editar', [PlanController::class, 'edit'])->name('planes.edit');
    Route::put('/planes/{plan}', [PlanController::class, 'update'])->name('planes.update');
});

require __DIR__.'/auth.php';
