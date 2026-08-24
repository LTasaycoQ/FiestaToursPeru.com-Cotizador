<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chain;
use Illuminate\Http\Request;

class ChainController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $chains = Chain::withCount('suppliers')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('admin.suppliers.chains.index', compact('chains', 'search'));
    }

    public function show(Chain $chain)
    {
        $search = request()->input('search');
        $sort = request()->input('sort', 'newest');

        $suppliers = $chain->suppliers()
            ->with(['category', 'country', 'city'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('supplier_name', 'like', "%{$search}%")
                        ->orWhere('tax_code', 'like', "%{$search}%")
                        ->orWhere('general_email', 'like', "%{$search}%");
                });
            })
            ->when($sort, function ($query, $sort) {
                switch ($sort) {
                    case 'newest':
                        $query->orderBy('created_at', 'desc');
                        break;
                    case 'oldest':
                        $query->orderBy('created_at', 'asc');
                        break;
                    case 'az':
                        $query->orderBy('supplier_name', 'asc');
                        break;
                    case 'za':
                        $query->orderBy('supplier_name', 'desc');
                        break;
                    default:
                        $query->orderBy('created_at', 'desc');
                }
            })
            ->paginate(10)
            ->withQueryString();

        return view('admin.suppliers.chains.show', compact('chain', 'suppliers', 'search', 'sort'));
    }

    public function create()
    {
        return view('admin.chains.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:chain,name',
            'description' => 'nullable|string|max:1000',
        ]);

        Chain::create($request->only('name', 'description'));

        return redirect()->route('admin.chains.index')
            ->with('success', 'Cadena creada exitosamente.');
    }

    public function edit(Chain $chain)
    {
        return view('admin.chains.edit', compact('chain'));
    }

    public function update(Request $request, Chain $chain)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:chain,name,' . $chain->id_chain . ',id_chain',
            'description' => 'nullable|string|max:1000',
        ]);

        $chain->update($request->only('name', 'description'));

        return redirect()->route('admin.chains.index')
            ->with('success', 'Cadena actualizada exitosamente.');
    }

    public function destroy(Chain $chain)
    {
        if ($chain->suppliers()->count() > 0) {
            return redirect()->route('admin.chains.index')
                ->with('error', 'No se puede eliminar la cadena porque tiene proveedores asociados.');
        }

        $chain->delete();

        return redirect()->route('admin.chains.index')
            ->with('success', 'Cadena eliminada exitosamente.');
    }
}
