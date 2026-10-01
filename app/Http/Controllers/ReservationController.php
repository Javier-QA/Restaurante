<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function index()
    {
        // Traemos reservas futuras y las de hoy (incluso si pasaron hace unas horas)
        $reservations = Reservation::where('reservation_time', '>=', Carbon::now()->startOfDay()) 
            ->orderBy('reservation_time', 'asc')
            ->with('table')
            ->get();
            
        $tables = Table::where('status', 'available')->get();

        // Indicadores para el panel de reservas
        $todayReservations = $reservations->filter(function ($reservation) {
            return $reservation->reservation_time->isToday();
        });

        $stats = [
            'today' => $todayReservations->count(),
            'pending' => $reservations->where('status', 'pending')->count(),
            'confirmed' => $reservations->where('status', 'confirmed')->count(),
            'people_today' => $todayReservations
                ->where('status', '!=', 'cancelled')
                ->sum('people'),
        ];

        return view('reservations.index', compact('reservations', 'tables', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'reservation_date' => 'required|date',
            'reservation_hour' => 'required|date_format:H:i',
            'reservation_period' => 'required|in:AM,PM',
            'people' => 'required|integer|min:1',
            'table_id' => 'nullable|exists:tables,id',
            'note' => 'nullable|string|max:500',
        ]);

        $data['reservation_time'] = Carbon::createFromFormat(
            'Y-m-d h:i A',
            $data['reservation_date'] . ' ' . $data['reservation_hour'] . ' ' . $data['reservation_period']
        );

        unset($data['reservation_date'], $data['reservation_hour'], $data['reservation_period']);

        Reservation::create($data);

        return redirect()
            ->route('reservations.index')
            ->with('success', 'Reserva agendada correctamente.');
    }

    public function update(Request $request, Reservation $reservation)
    {
        $data = $request->validate([
            'client_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'reservation_date' => 'required|date',
            'reservation_hour' => 'required|date_format:H:i',
            'reservation_period' => 'required|in:AM,PM',
            'people' => 'required|integer|min:1',
            'table_id' => 'nullable|exists:tables,id',
            'note' => 'nullable|string|max:500',
        ]);

        $data['reservation_time'] = Carbon::createFromFormat(
            'Y-m-d h:i A',
            $data['reservation_date'] . ' ' . $data['reservation_hour'] . ' ' . $data['reservation_period']
        );

        unset($data['reservation_date'], $data['reservation_hour'], $data['reservation_period']);

        $reservation->update($data);

        return redirect()
            ->route('reservations.index')
            ->with('success', 'Reserva actualizada correctamente.');
    }
    public function updateStatus(Request $request, Reservation $reservation)
    {
        $request->validate(['status' => 'required|in:confirmed,cancelled']);
        $reservation->update(['status' => $request->status]);
        
        $msg = $request->status == 'confirmed' ? 'Reserva confirmada.' : 'Reserva cancelada.';
        return redirect()->back()->with('success', $msg);
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();
        return redirect()->back()->with('success', 'Reserva eliminada.');
    }
}