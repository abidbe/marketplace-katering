<?php

namespace App\Http\Controllers\Customer;

use App\Helpers\QuerySearch;
use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Http\Request;

class CateringController extends Controller
{

    public function index(Request $request)
    {
        $query = User::query()
            ->where('role', 'merchant')
            ->where('is_active', true)
            ->whereNotNull('company_name')
            ->with(['menus' => fn ($q) => $q->where('is_active', true)->orderBy('price')]);

        if ($request->filled('category')) {
            $query->whereHas('menus', fn ($q) => $q
                ->where('is_active', true)
                ->where('category', $request->string('category')));
        }

        $models = QuerySearch::apply(
            query: $query,
            request: $request,
            searchableColumns: ['company_name', 'description', 'city'],
            filterableColumns: ['city'],
            perPage: 9,
            defaultSort: ['company_name' => 'asc']
        );

        $cities = User::query()
            ->where('role', 'merchant')
            ->where('is_active', true)
            ->whereNotNull('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city')
            ->all();

        $categories = Menu::distinctCategories();

        return view('customer.pages.caterings.index', get_defined_vars());
    }
}
