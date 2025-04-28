<?php

namespace App\Http\Controllers\frontend;

use Exception;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Notifications\SiteEventNotification;

class CustomerController extends Controller
{
    public function loginForm(){
        $title = "Login Customer";
        return view('frontend.customer.login', get_defined_vars());
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $credentials = $request->only('email', 'password');

        try {
            // Attempt to log in with the customer guard
            if (Auth::guard('customer')->attempt($credentials, $request->remember)) {
                $user = Auth::guard('customer')->user();

                // Check if user is active
                if ($user->status == 1) {
                    
                    // Check if checkout data exists in session
                    if (session()->has('checkout_data')) {
                        return redirect()->route('cart.checkout');  // Redirect to checkout if data exists
                    }

                    // No checkout data, proceed to dashboard
                    return redirect()->route('shop-now')->with(['success' => true, 'message' => 'You are logged in successfully.']);
                } else {
                    // Log out the user if account is not active
                    Auth::guard('customer')->logout();
                    return redirect()->back()->with(['error' => false, 'message' => 'Your account is not active.']);
                }
            } else {
                return redirect()->back()->with(['error' => false, 'message' => 'Invalid credentials.']);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function create(){
        $title = "Register Customer";
        return view('frontend.customer.register', get_defined_vars());
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'email' => ['required', 'email', 'unique:customers,email'],
            'password' => ['required', 'confirmed'],  // assuming you're using password confirmation
        ]);

        try {
            // Create the user
            $user = new Customer();
            $user->phone = $request->phone;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->status = 1; // Assuming status is active after registration
            $user->save();

            if($user){
                $admin = getActiveAdminUser();
                if(!empty($admin)){
                    $url = route('customers.index');
                    $admin->notify(new SiteEventNotification('profile.png','New Registration', "{$user->name} just registered.", $url));
                }
            }

            // Automatically log the user in after registration
            Auth::guard('customer')->login($user);

            // If checkout data exists in the session, redirect to checkout
            if (session()->has('checkout_data')) {
                return redirect()->route('cart.checkout');
            }

            // No checkout data, proceed to dashboard
            return redirect()->route('shop-now')->with(['success' => true, 'message' => 'You are logged in successfully.']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
    public function logout(Request $request)
    {
        // Log out the customer
        Auth::guard('customer')->logout();

        // Optionally, invalidate the session
        $request->session()->invalidate();
        
        // Regenerate the session ID to prevent session fixation attacks
        $request->session()->regenerateToken();

        // Redirect to the home page or login page after logout
        return redirect()->route('home')->with('message', 'You have successfully logged out.');
    }

}
