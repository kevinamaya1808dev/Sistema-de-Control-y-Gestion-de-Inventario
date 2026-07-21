<?php

namespace App\Http\Controllers;

use App\Models\CajaMovimiento;
use App\Models\InventoryMovement;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CajaController extends Controller
{
    /**
     * Muestra la vista operativa del módulo de caja (Apertura / Corte del usuario actual).
     */
    public function index()
    {
        $user = auth()->user();

        // Buscar si el usuario actual tiene una caja abierta
        $cajaActiva = CajaMovimiento::where('user_id', $user->id)
            ->where('estado', 'abierta')
            ->first();

        $totalVentasEfectivo = 0;
        $totalGastos = 0;
        $ventasTurno = collect();
        $gastosTurno = collect();

        // Si la caja está abierta, obtenemos los movimientos ligados a esta caja
        if ($cajaActiva) {
            // Ventas directas registradas en este turno de caja
            $ventasTurno = InventoryMovement::with(['product', 'user'])
                ->where('caja_id', $cajaActiva->id)
                ->where('type', 'salida')
                ->where('reason', 'Venta directa')
                ->latest()
                ->get();

            // Suma del total de las ventas registradas
            $totalVentasEfectivo = $ventasTurno->sum('total');

            // Gastos o salidas adicionales de inventario en este turno (opcional)
            $gastosTurno = InventoryMovement::with(['product', 'user'])
                ->where('caja_id', $cajaActiva->id)
                ->where('type', 'salida')
                ->where('reason', '!=', 'Venta directa')
                ->latest()
                ->get();
        }

        return view('caja.index', compact(
            'cajaActiva',
            'ventasTurno',
            'totalVentasEfectivo',
            'gastosTurno',
            'totalGastos'
        ));
    }

    /**
     * Muestra el historial general de caja (Exclusivo para Administradores / Supervisores).
     */
    public function historial()
    {
        $historial = CajaMovimiento::with('user')->latest()->paginate(10);

        return view('caja.historial', compact('historial'));
    }

    /**
     * Procesa la apertura de la caja.
     */
    public function abrir(Request $request)
    {
        $user = auth()->user();

        $tieneCajaAbierta = CajaMovimiento::where('user_id', $user->id)
            ->where('estado', 'abierta')
            ->exists();

        if ($tieneCajaAbierta) {
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Ya tienes una sesión de caja activa.'], 422);
            }

            return redirect()->back()->with('error', 'Ya tienes una sesión de caja activa.');
        }

        $request->validate([
            'monto_apertura' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $caja = CajaMovimiento::create([
            'user_id' => $user->id,
            'monto_apertura' => $request->monto_apertura,
            'fecha_apertura' => Carbon::now(),
            'estado' => 'abierta',
            'observaciones' => $request->observaciones,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => '¡Caja abierta exitosamente!',
                'caja' => $caja,
            ]);
        }

        return redirect()->route('caja.index')->with('success', '¡Caja abierta exitosamente! Ya puedes realizar ventas.');
    }

    /**
     * Procesa el cierre o corte de la caja.
     */
    public function cerrar(Request $request)
    {
        $user = auth()->user();

        $cajaActiva = CajaMovimiento::where('user_id', $user->id)
            ->where('estado', 'abierta')
            ->first();

        if (! $cajaActiva) {
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'No se encontró ninguna caja abierta para cerrar.'], 404);
            }

            return redirect()->back()->with('error', 'No se encontró ninguna caja abierta para cerrar.');
        }

        $request->validate([
            'monto_cierre' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $nuevasObservaciones = $cajaActiva->observaciones;
        if ($request->filled('observaciones')) {
            $nuevasObservaciones .= ($nuevasObservaciones ? ' | ' : '').'Cierre: '.$request->observaciones;
        }

        $cajaActiva->update([
            'monto_cierre' => $request->monto_cierre,
            'fecha_cierre' => Carbon::now(),
            'estado' => 'cerrada',
            'observaciones' => $nuevasObservaciones,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Caja cerrada correctamente. Turno finalizado.',
                'caja' => $cajaActiva,
            ]);
        }

        return redirect()->route('caja.index')->with('success', 'Caja cerrada correctamente. Turno finalizado.');
    }
}
