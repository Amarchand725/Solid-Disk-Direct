<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Traits\ApiResponse;
use App\Models\QuoteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Notifications\SiteEventNotification;

class QuoteRequestController extends Controller
{
    use ApiResponse;

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'quantity' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.'
            ], 422);
        } 
        
        DB::beginTransaction();

        try{
            $model = new QuoteRequest();
            $model->full_name = $request->full_name;
            $model->company = $request->company;
            $model->mpn = $request->mpn;
            $model->email = $request->email;
            $model->phone_number = $request->phone;
            $model->how_soon_need = $request->how_soon_need;
            $model->message = $request->message;
            $model->status = 0; //default pending request
            $model->save();

            if($model){
                DB::commit();

                $admin = getActiveAdminUser();
                if(!empty($admin)){
                    $url = route('quote_requests.index');
                    $admin->notify(new SiteEventNotification('quote-request.png', 'New quote of ', "{$request->full_name} has received.", $url));
                }
                return $this->success('We have received your quote request.! We will contact you soon!', 200);
            }else{
                DB::rollBack();

                return $this->error(
                    'Failed to send your quote request try again',
                    500,
                );
            }
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
