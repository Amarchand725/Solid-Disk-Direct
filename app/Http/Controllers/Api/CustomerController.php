<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CustomerResource;
use Exception;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Notifications\SiteEventNotification;
use App\Traits\ApiResponse;

class CustomerController extends Controller
{
    use ApiResponse;
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:customers,email',
            'password' => 'required|string|min:6|confirmed',
        ]);
    
        if ($validator->fails()) {
            return $this->error(
                $validator->errors(),
                    422
                    );
        }
    
        DB::beginTransaction();
    
        try {
            $model = new Customer();
            $model->name = $request->name;
            $model->phone = $request->phone;
            $model->email = $request->email;
            $model->password = Hash::make($request->password);
    
            if ($model->save()) {
                DB::commit();
    
                // Optional: Notify admin
                if (function_exists('getActiveAdminUser')) {
                    $admin = getActiveAdminUser();
                    if (!empty($admin)) {
                        $url = route('customers.index');
                        $admin->notify(new SiteEventNotification(
                            'profile.png',
                            'New Registration',
                            "{$model->name} just registered.",
                            $url
                        ));
                    }
                }

                return $this->success([
                    'token' => $model->createToken('API Token')->plainTextToken,
                ], 'You got registration successfully.!');
            } else {
                DB::rollBack();

                return $this->error(
                'Failed to register customer',
                    500,
                    []
                    );
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error(
                'Server error: ' . $e->getMessage(),
                    500,
                    []
                    );
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $customer = Customer::where('email', $request->email)->first();

        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return response()->json([
                'message' => 'Invalid credentials.'
            ], 401);
        }

        $token = $customer->createToken('customer-token')->plainTextToken;

        return response()->json([
            'message' => 'Logged in successfully',
            'token' => $token,
            'customer' => new CustomerResource($customer),
        ]);
    }

    public function show(Request $request){
        $user = $request->user();
    
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        return $this->success([
            new CustomerResource($user),
        ], 'Customer Data');
    }

    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
    
        try {
            $customer = $request->user();
            $customer->name = $request->name;
            $customer->phone = $request->phone;
            if ($request->hasFile('image')) {
                $uploadPath = 'customers';
                $customer->image = $request->file('image')->store('uploads/'.$uploadPath, 'public');
            }
            $customer->save();
    
            if ($customer) {
                return $this->success([
                    'data' => new CustomerResource($request->user()),
                ], 'You have updated data successfully.!');
            } else {
                return $this->error(
                'Failed to update data',
                    500,
                    []
                    );
            }
        } catch (Exception $e) {
            return $this->error(
                'Server error: ' . $e->getMessage(),
                    500,
                    []
                    );
        }
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}