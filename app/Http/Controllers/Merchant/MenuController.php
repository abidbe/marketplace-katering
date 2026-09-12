<?php

namespace App\Http\Controllers\Merchant;

use App\Helpers\AutoFill;
use App\Helpers\QuerySearch;
use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Traits\CrudTrait;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    use CrudTrait;

    public function index(Request $request)
    {
        $models = QuerySearch::apply(
            query: $this->ownQuery(),
            request: $request,
            searchableColumns: ['name', 'category', 'description'],
            filterableColumns: ['category', 'is_active'],
            perPage: 10,
            defaultSort: ['created_at' => 'desc']
        );

        $categories = Menu::distinctCategories();

        return view('merchant.pages.menus.index', get_defined_vars());
    }

    public function form($id = null)
    {
        $model = $id ? $this->findModel(['id' => $id]) : new Menu;

        return view('merchant.pages.menus.form', data: get_defined_vars());
    }

    public function save(Request $request, $id = null)
    {
        $model = $id ? $this->findModel(['id' => $id]) : new Menu;
        $params = $request->all();
        $params['is_active'] = $request->boolean('is_active');
        $params['user_id'] = $request->user()->id;

        $model->validator($params, $model->rules(), [], $model->labels())->validate();
        if ($request->ajax()) {
            return;
        }
        AutoFill::fill($model, params: $params);
        $model->saveOrFail();

        return redirect()->back()->with('success', 'Simpan Berhasil');
    }

    private function ownQuery()
    {
        return Menu::where('user_id', auth()->id());
    }

    private function findModel(array $params)
    {
        return $this->ownQuery()->where($params)->firstOrFail();
    }
}
