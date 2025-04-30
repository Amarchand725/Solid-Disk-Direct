<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\City;
use App\Models\Menu;
use App\Models\State;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard(){
        $title = 'Dashboard';
        if(!Auth::check()){
            return redirect()->route('admin.login');
        }
        return view('admin.dashboards.dashboard', get_defined_vars());
    }
    public function loginForm()
    {
        $title = 'Login';
        if(Auth::check()){
            return redirect()->route('dashboard');
        }
        return view('admin.auth.login', compact('title'));
    }
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => ['required','email'],
            'password' => ['required'],
        ]);

        $credentials = $request->only('email', 'password');
        try{
            if (Auth::attempt($credentials, $request->remember)) {
                $user = Auth::user();
                if ($user->status == 1) {
                    return response()->json([
                        'success' => true, 
                        'message' =>'You are logged successfully.', 
                        'route' => route('dashboard')
                    ]);
                } else {
                    Auth::logout(); // Log out the user if they are not active
                    return response()->json(['error' => 'Your account is not active.']);
                }
            } else {
                return response()->json(['error' => 'Invalid credentials']);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
    public function logOut()
    {
        auth()->logout();
        return redirect()->route('admin.login');
    }

    public function getStates(Request $request){
        $country = Country::where('name', $request->country)->first();
        return State::where('country_id', $country->id)->get();
    }
    
    public function getCities(Request $request){
        $state = State::where('name', $request->state)->first();
        return City::where('state_id', $state->id)->get();
    }

    public function apiDocs(){
        $this->authorize('api_docs-list');
        $title = 'SDD-APIs';
        return view('apiDocs', get_defined_vars());
    }
}