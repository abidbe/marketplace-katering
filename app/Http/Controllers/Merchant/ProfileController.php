<?php

namespace App\Http\Controllers\Merchant;

use App\Helpers\AutoFill;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function form(Request $request)
    {
        $model = $request->user();

        return view('merchant.pages.profile.form', get_defined_vars());
    }

    public function save(Request $request)
    {
        $model = $request->user();
        $params = $request->all();

        $model->validator($params, $model->profileRules(), [], $model->labels())->validate();

        AutoFill::fill($model, params: $params);
        $model->saveOrFail();

        return redirect()->back()->with('success', 'Profil berhasil disimpan');
    }
}
