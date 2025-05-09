<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Notifications\SiteEventNotification;
use App\Traits\ApiResponse;

class SubscriberController extends Controller
{
    use ApiResponse;
    
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:subscribers,email',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or already subscribed.'
            ], 422);
        } 
        
        DB::beginTransaction();

        try{
            $model = new Subscriber();
            $model->email = $request->email;
            $model->save();

            if($model){
                DB::commit();

                $admin = getActiveAdminUser();
                if(!empty($admin)){
                    $url = route('subscribers.index');
                    $admin->notify(new SiteEventNotification('subscribe.png', 'New Email Subscription', "{$model->email} has subscribed.", $url));
                }
                return $this->success('Thank you for subscribing!', 200);
            }else{
                DB::rollBack();

                return $this->error(
                    'Failed to subscribing try again',
                    500,
                );
            }
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
