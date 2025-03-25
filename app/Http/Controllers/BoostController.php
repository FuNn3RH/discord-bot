<?php
namespace App\Http\Controllers;

use App\Models\Run;

class BoostController extends Controller {

    public function index() {

        $runs = Run::with('user')
            ->selectRaw('* , DATE(created_at) as run_date')
            ->get()
            ->groupBy('run_date');

        $payments = [
            'pending' => [
                'K' => 0,
                'T' => 0,
            ],
            'paid' => [
                'K' => 0,
                'T' => 0,
            ],
            'total' => [
                'K' => 0,
                'T' => 0,
            ],
        ];

        foreach ($runs as $run) {

            $payments['pending']['K'] += $run->whereIn('unit', ['K', 'k'])
                ->where('paid', 0)
                ->sum('pot');

            $payments['pending']['T'] += $run->whereIn('unit', ['T', 't'])
                ->where('paid', 0)
                ->sum('pot');

            $payments['paid']['K'] += $run->whereIn('unit', ['K', 'k'])
                ->where('paid', 1)
                ->sum('pot');

            $payments['paid']['T'] += $run->whereIn('unit', ['T', 't'])
                ->where('paid', 1)
                ->sum('pot');
        }

        $payments['total']['K'] = $payments['pending']['K'] + $payments['paid']['K'];
        $payments['total']['T'] = $payments['pending']['T'] + $payments['paid']['T'];

        return view('index', compact('runs', 'payments'));
    }
}
