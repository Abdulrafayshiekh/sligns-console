<?php

namespace App\Http\Controllers\Deployer;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        return view('deployer.home', [
            'companies' => Company::listing($request->query('search'))->paginate(20)->withQueryString(),
        ]);
    }
}
