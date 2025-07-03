<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Vendor;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\DataTableTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Mail\OrderDeliveredReviewMail;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    use DataTableTrait;

    protected $model;
    protected $routePrefix;
    protected $pathInitialize;
    protected $singularLabel;
    protected $pluralLabel;

    public function __construct(Order $model)
    {
        parent::__construct();

        $this->model = $model; 
        $this->routePrefix = Str::before(Route::currentRouteName(), '.');
        $this->pathInitialize = 'admin.'.$this->routePrefix;
        $this->singularLabel = Str::ucfirst(Str::singular($this->routePrefix));
        $this->pluralLabel = 'All '.Str::ucfirst($this->routePrefix);

        // Initialize the permissions array
        $this->permissions = [
            'index'  => $this->routePrefix . '-list',
            'create' => $this->routePrefix . '-create',
            'edit'   => $this->routePrefix . '-edit',
            'show'   => $this->routePrefix . '-show',
            'invoice'   => $this->routePrefix . '-invoice',
            'destroy' => $this->routePrefix . '-delete',
        ];
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $title = $this->pluralLabel;
        $singularLabel = $this->singularLabel;
        $routeInitialize = $this->routePrefix;
        $bladePath = $this->pathInitialize;

        $models = $this->model
                    ->orderBy('id', 'desc')
                    ->select(['id', 'order_number', 'subtotal', 'shipping_cost', 'tax', 'total', 'payment_method', 'payment_status', 'order_status', 'created_at']);

        // Define the columns dynamically
        $columns = [
            'order_number' => fn($model) => '<strong>' . $model->order_number . '</strong>',
            'subtotal' => fn($model) => currency() . number_format($model->subtotal, 2),
            'shipping_cost' => fn($model) => currency() . number_format($model->shipping_cost, 2),
            'tax' => fn($model) => currency() . number_format($model->tax, 2),
            'total' => fn($model) => '<b>' . currency() . number_format($model->total, 2) . '</b>',

            // ✅ Payment Method Badge
            'payment_method' => function ($model) {
                $badgeClass = match ($model->payment_method) {
                    'paypal' => 'badge bg-info',
                    'payarc' => 'badge bg-primary',
                    default => 'badge bg-secondary',
                };
                return '<span class="' . $badgeClass . '">' . ucfirst($model->payment_method) . '</span>';
            },

            // ✅ Payment Status Badge
            'payment_status' => function ($model) {
                $badgeClass = match ($model->payment_status) {
                    'paid' => 'badge bg-success',
                    'unpaid' => 'badge bg-danger',
                    'pending' => 'badge bg-warning text-dark',
                    default => 'badge bg-secondary',
                };
                return '<span class="' . $badgeClass . '">' . ucfirst($model->payment_status) . '</span>';
            },

            'order_status' => function ($model) {
                $statuses = orderStatus();
                $status = $model->order_status;
                $label = $statuses[$status]['label'] ?? ucfirst($status);
                $badgeClass = $statuses[$status]['badge'] ?? 'bg-secondary';

                return '<span class="badge ' . $badgeClass . '">' . $label . '</span>';
            },

            'created_at' => fn($model) => \Carbon\Carbon::parse($model->created_at)->format('d M, Y'),

            'action' => function ($model) use ($bladePath, $singularLabel, $routeInitialize) {
                return view($bladePath.'.action', [
                    'model' => $model,
                    'singularLabel' => $singularLabel,
                    'routeInitialize' => $routeInitialize,
                ])->render();
            }
        ];
        
        if ($request->ajax() && $request->loaddata == "yes") {
            return $this->getDataTable($request, $models, $columns);
        }

        $columnsConfig = collect($columns)->map(function ($callback, $key) {
            return [
                'data' => $key,
                'name' => $key,
                'orderable' => !in_array($key, ['action']), // Set orderable=false for 'action'
                'searchable' => !in_array($key, ['action']) // Set searchable=false for 'action'
            ];
        })->values()->toArray();
        
        return view($bladePath.'.index', get_defined_vars());
    }

    public function invoice($id)
    {
        $order = $this->model->with('orderShippingMethod', 'shipping')->findOrFail($id);
        return view('admin.orders.invoice', compact('order'));
    }

    public function show($id){
        $order = $this->model->with('orderShippingMethod', 'shipping')->findOrFail($id);
        return (string) view('admin.orders.order_content', compact('order'));
    }

    public function changeStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => ['required', Rule::in(array_keys(orderStatus()))],
            'tracking_id' => ['nullable', 'string', 'max:255'],
        ]);

        $order->update([
            'status' => $request->status,
            'tracking_id' => $request->tracking_id,
        ]);

        return redirect()->back()->with('success', 'Order updated successfully.');
    }

    public function edit(string $id)
    {
        $bladePath = $this->pathInitialize;
        $title = $this->singularLabel;
        $model = $this->model->where('id', $id)->first();
        return view($bladePath.'.edit_content', get_defined_vars());
    }

    public function update(Request $request, $modelId)
    {   
        $validator = Validator::make($request->all(), [
            'order_status' => 'required|string',
            'tracking_number' => 'string|nullable',
            'shipping_method' => 'string|nullable',
            'custom_shipping_method' => 'string|nullable',
        ]);

        if (!in_array($request->order_status, ['cancelled', 'returned'])) {
            $validator->after(function ($validator) use ($request) {
                if (empty($request->tracking_number)) {
                    $validator->errors()->add('tracking_number', 'Tracking number is required.');
                }

                if (empty($request->shipping_method) && empty($request->custom_shipping_method)) {
                    $validator->errors()->add('shipping_method', 'Either shipping method or custom shipping method is required.');
                }
            });
        }

        $validator->validate();

        $model = $this->model->where('id', $modelId)->first();
        $singularLabel = $this->singularLabel;

        try{
            // Determine which shipping method to use
            $finalShippingMethod = $request->shipping_method ?: $request->custom_shipping_method;
            if(!empty($model)){
                $model->order_status = $request->order_status;
                $model->tracking_number = $request->tracking_number;
                $model->shipping_method = $finalShippingMethod;
                $model->save(); 
                
                if(!empty($model) && !empty($request->order_status) && $request->order_status=='delivered'){
                    $customerName = '';
                    $customerEmail = '';
                    if(isset($model->shipping) && !empty($model->shipping->first_name)){
                        $customerName = $model->shipping->first_name. ' '. $model->shipping->last_name;
                        $customerEmail = $model->shipping->email;
                    }
                    $reviewLink = config('system.trust_pilot_url');
                    $storeName = appName();

                    if(!empty($customerEmail)){
                        Mail::to($customerEmail)->send(new OrderDeliveredReviewMail($customerName, $reviewLink, $storeName));
                    }
                }

                return response()->json(['success' => true, 'message' =>'You have updated '.$singularLabel.' successfully.']);
            }else{
                return response()->json(['error' => 'Something went wrong.']);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function destroy($modelId)
    {
        $singularLabel = $this->singularLabel;
        if($this->model->where('id', $modelId)->delete()) {
            return response()->json([
                'status' => true,
                'message' => $singularLabel.' Deleted Successfully'
            ]);
        } else{
            return response()->json([
                'status' => true,
                'error' => $singularLabel.' not deleted try again.'
            ]);
        }
    }   

    public function trashed(Request $request)
    {
        $singularLabel = $this->singularLabel;
        $routeInitialize = $this->routePrefix;
        $bladePath = $this->pathInitialize;
        $title = 'All Trashed '.Str::plural($singularLabel);

        $models = $this->model
                    ->onlyTrashed()->latest()
                    ->select(['id', 'order_number', 'subtotal', 'shipping_cost', 'tax', 'total', 'payment_method', 'payment_status', 'order_status', 'created_at']);

        // Define the columns dynamically
        $columns = [
            'order_number' => fn($model) => '<strong>' . $model->order_number . '</strong>',
            'subtotal' => fn($model) => number_format($model->subtotal, 2),
            'shipping_cost' => fn($model) => number_format($model->shipping_cost, 2),
            'tax' => fn($model) => number_format($model->tax, 2),
            'total' => fn($model) => '<b>' . number_format($model->total, 2) . '</b>',

            // ✅ Payment Method Badge
            'payment_method' => function ($model) {
                $badgeClass = match ($model->payment_method) {
                    'paypal' => 'badge bg-info',
                    'payarc' => 'badge bg-primary',
                    default => 'badge bg-secondary',
                };
                return '<span class="' . $badgeClass . '">' . ucfirst($model->payment_method) . '</span>';
            },

            // ✅ Payment Status Badge
            'payment_status' => function ($model) {
                $badgeClass = match ($model->payment_status) {
                    'paid' => 'badge bg-success',
                    'unpaid' => 'badge bg-danger',
                    'pending' => 'badge bg-warning text-dark',
                    default => 'badge bg-secondary',
                };
                return '<span class="' . $badgeClass . '">' . ucfirst($model->payment_status) . '</span>';
            },

            // ✅ Order Status Badge
            'order_status' => function ($model) {
                $badgeClass = match ($model->order_status) {
                    'pending' => 'badge bg-warning text-dark',
                    'processing' => 'badge bg-primary',
                    'shipped' => 'badge bg-info',
                    'delivered' => 'badge bg-success',
                    'cancelled' => 'badge bg-danger',
                    default => 'badge bg-secondary',
                };
                return '<span class="' . $badgeClass . '">' . ucfirst($model->order_status) . '</span>';
            },

            'created_at' => fn($model) => \Carbon\Carbon::parse($model->created_at)->format('d M, Y'),

            'action' => fn($model) =>
                '<a href="' . route($routeInitialize . '.restore', $model->id) . '" class="btn btn-icon btn-label-info waves-effect me-1">' .
                    '<span><i class="ti ti-refresh ti-sm"></i></span>' .
                '</a>'
        ];
        
        if ($request->ajax() && $request->loaddata == "yes") {
            return $this->getDataTable($request, $models, $columns);
        }

        $columnsConfig = collect($columns)->map(function ($callback, $key) {
            return [
                'data' => $key,
                'name' => $key,
                'orderable' => !in_array($key, ['action']), // Set orderable=false for 'action'
                'searchable' => !in_array($key, ['action']) // Set searchable=false for 'action'
            ];
        })->values()->toArray();
        
        return view($bladePath.'.index', get_defined_vars());
    }
    public function restore($id)
    {
       $find = $this->model->onlyTrashed()->where('id', $id)->first();
        if(isset($find) && !empty($find)) {
            $restore = $find->restore();
            if(!empty($restore)) {
                return redirect()->back()->with('message', 'Record Restored Successfully.');
            }
        } else {
            return false;
        }
    }

    public function downloadInvoice($orderId)
    {
        $order = $this->model->with('orderShippingMethod', 'shipping')->findOrFail($orderId);
        $pdf = Pdf::loadView('admin.orders.download-invoice', compact('order'))
                ->setPaper('a4');

        return $pdf->download('invoice_'.$order->order_number.'.pdf');
    }

    public function getOrder($orderNumber){
        $vendors = Vendor::where('status', 1)->get();
        $order = Order::where('order_number', $orderNumber )->first();
        $customerName = null;
        if(isset($order->shipping) && !empty($order->shipping)){
            $customerName = $order->shipping->first_name.' '.$order->shipping->last_name;
        }
        
        return (string) view('admin.orders.get_order_content', get_defined_vars());
    }
}
