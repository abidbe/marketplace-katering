<?php

namespace App\Http\Controllers;

use App\Helpers\AutoFill;
use App\Helpers\QuerySearch;
use App\Models\User;
use App\Traits\CrudTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use CrudTrait;

    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $models = QuerySearch::apply(
            query: User::query(),
            request: $request,
            searchableColumns: ['name', 'email', 'phone', 'company_name'],
            filterableColumns: ['role' => 'role', 'status' => 'is_active'],
            perPage: 10
        );

        return view('merchant/pages/users/index', get_defined_vars());
    }

    public function form($id = null)
    {
        $model = $id ? $this->findModel(['id' => $id]) : new User;
        $user = $this->user;

        return view('merchant/pages/users/form', data: get_defined_vars());
    }

    public function save(Request $request, $id = null)
    {
        $model = $id ? $this->findModel(['id' => $id]) : new User;
        $params = $request->all();
        $params['is_active'] = $request->boolean('is_active');

        // password kosong saat edit = jangan ganti password
        if (empty($params['password'])) {
            unset($params['password'], $params['password_confirmation']);
        }

        $model->validator($params, $model->rules($model->exists ? 'edit' : null), [], $model->labels())->validate();
        if ($request->ajax()) {
            return;
        }
        AutoFill::fill($model, params: $params);
        $model->saveOrFail();

        return redirect()->back()->with('success', 'Simpan Berhasil');
    }

    private function findModel(array $params)
    {
        return User::where($params)->firstOrFail();
    }
}
