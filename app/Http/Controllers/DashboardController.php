<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Contact;
use App\Models\Supplier;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return $this->adminDashboard($user);
        }

        return $this->userDashboard($user);
    }

    private function adminDashboard($user)
    {
        $now = now();

        $totalUsers    = User::count();
        $totalUsuarios = User::where('role', 'usuario')->count();
        $totalAdmins   = User::where('role', 'admin')->count();

        // Nuevos hoy vs ayer
        $newToday     = User::whereDate('created_at', $now->toDateString())->count();
        $newYesterday = User::whereDate('created_at', $now->copy()->subDay()->toDateString())->count();
        $todayVsYesterday = $newToday - $newYesterday;

        // Crecimiento total vs mes pasado
        $usersBeforeThisMonth = User::where('created_at', '<', $now->copy()->startOfMonth())->count();
        $growthPct = $usersBeforeThisMonth > 0
            ? round((($totalUsers - $usersBeforeThisMonth) / $usersBeforeThisMonth) * 100)
            : ($totalUsers > 0 ? 100 : 0);

        // Nuevos este mes / esta semana (para deltas y barras de distribución)
        $newThisMonth = User::whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->count();

        $newThisWeek = User::whereBetween('created_at', [
            $now->copy()->startOfWeek(),
            $now->copy()->endOfWeek(),
        ])->count();

        // Actividad de registros - últimos 6 meses (real, no inventado)
        $monthlyRegistrations = [];
        $monthLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $monthLabels[] = ucfirst($month->isoFormat('MMM'));
            $monthlyRegistrations[] = User::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        }
        $maxRegistrations = max($monthlyRegistrations) ?: 1;
        $currentMonthIndex = count($monthlyRegistrations) - 1;

        $recentUsers = User::latest()->take(5)->get();

        return view('dashboard', compact(
            'user',
            'totalUsers',
            'totalUsuarios',
            'totalAdmins',
            'newToday',
            'todayVsYesterday',
            'growthPct',
            'newThisMonth',
            'newThisWeek',
            'monthlyRegistrations',
            'monthLabels',
            'maxRegistrations',
            'currentMonthIndex',
            'recentUsers'
        ));
    }

    private function userDashboard($user)
    {
        $totalClients   = Client::count();
        $totalContacts  = Contact::count();
        $totalSuppliers = Supplier::count();

        $clientsWithoutContact = Client::doesntHave('contacts')->count();

        $recentClients = Client::withCount('contacts')
            ->latest()->take(5)->get();

        $recentSuppliers = Supplier::with(['category'])
            ->latest()->take(4)->get();

        return view('dashboard', compact(
            'user',
            'totalClients',
            'totalContacts',
            'totalSuppliers',
            'clientsWithoutContact',
            'recentClients',
            'recentSuppliers'
        ));
    }
}