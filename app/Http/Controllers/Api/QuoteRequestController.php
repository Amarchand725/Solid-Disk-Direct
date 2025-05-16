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
        $formName = $request->form;

        if ($formName === 'full_form') {
             $validator = Validator::make($request->all(), [
                'full_name' => 'required|string|max:255',
                'mpn' => 'required|string|max:20',
                'email' => 'required|email',
                'phone' => 'required|string|max:20',
                'how_soon_need' => 'required|string|max:50',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'email' => 'required|email',
                'phone' => 'required|string|max:20',
                'quantity' => 'required|numeric|min:1',
            ]);
        }

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }
        
        DB::beginTransaction();

        try{
            $model = new QuoteRequest();
            if($formName === 'full_form'){
                $model->full_name = $request->full_name;
                $model->company = $request->company;
            }else{
                $model->setFullName($request->first_name, $request->last_name);
                $model->quantity = $request->quantity;
            }
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
                $message = 'We have received your quote request.! We will contact you soon!';
                return $this->success(null, $message, 200);
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
