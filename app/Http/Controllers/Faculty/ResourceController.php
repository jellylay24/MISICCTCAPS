<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\Borrow;

class ResourceController extends Controller
{
    public function index()
    {
        $resources = Resource::withSum(['borrows as borrowed_qty' => function($q) {
            $q->whereIn('status', ['approved', 'borrowed']);
        }], 'quantity')->get();

        $totalQuantity = $resources->sum('quantity');
        $inUse = Borrow::whereIn('status', ['approved', 'borrowed'])->sum('quantity');
        $available = max(0, $totalQuantity - $inUse);
        $unavailable = $totalQuantity - $available;

        return view('faculty.resources.index', compact(
            'resources', 'totalQuantity', 'available', 'inUse', 'unavailable'
        ));
    }
}
