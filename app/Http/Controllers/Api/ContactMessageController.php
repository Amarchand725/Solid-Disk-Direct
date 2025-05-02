<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Traits\ApiResponse;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Notifications\SiteEventNotification;

class ContactMessageController extends Controller
{
    use ApiResponse;

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'name' => 'required',
            'subject' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.'
            ], 422);
        } 
        
        DB::beginTransaction();

        try{
            $model = new ContactMessage();
            $model->name = $request->name;
            $model->email = $request->email;
            $model->phone = $request->phone;
            $model->subject = $request->subject;
            $model->message = $request->message;
            $model->save();

            if($model){
                DB::commit();

                $admin = getActiveAdminUser();
                if(!empty($admin)){
                    $url = route('contact_messages.index');
                    $admin->notify(new SiteEventNotification('contact-us.png', 'New message of ', "{$request->name} has received.", $url));
                }
                return $this->success('We have received your message.! We will contact you soon!', 200);
            }else{
                DB::rollBack();

                return $this->error(
                    'Failed to send your message try again',
                    500,
                );
            }
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
