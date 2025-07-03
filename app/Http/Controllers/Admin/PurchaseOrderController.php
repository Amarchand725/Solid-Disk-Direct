<?php

namespace App\Http\Controllers\Admin;

use App\Models\PurchaseOrderItem;
use Exception;
use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Traits\DataTableTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Mail\OrderDeliveredReviewMail;
use App\Models\Vendor;
use Illuminate\Support\Facades\Validator;

class PurchaseOrderController extends Controller
{
    use DataTableTrait;

    protected $model;
    protected $purchaseOrderItemModel;
    protected $routePrefix;
    protected $pathInitialize;
    protected $singularLabel;
    protected $pluralLabel;

    public function __construct(PurchaseOrder $model)
    {
        parent::__construct();

        $this->model = $model; 
        $this->purchaseOrderItemModel = new PurchaseOrderItem(); 
        $this->routePrefix = Str::before(Route::currentRouteName(), '.');
        $this->pathInitialize = 'admin.'.$this->routePrefix;
        $this->singularLabel = Str::title(str_replace('_', ' ', Str::singular($this->routePrefix)));
        $this->pluralLabel = 'All ' . Str::title(str_replace('_', ' ', $this->routePrefix));

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
                    ->select(['id', 'po_number', 'vendor', 'order_date', 'notes', 'sub_total','tax_rate', 'tax', 'shipping_charges', 'total_amount', 'payment_method', 'warranty_info', 'order_status', 'created_at']);

        // Define the columns dynamically
        $columns = [
            'po_number' => fn($model) => '<strong>' . $model->po_number . '</strong>',
            'subtotal' => fn($model) => currency() . number_format($model->sub_total, 2),
            'shipping' => fn($model) => currency() . number_format($model->shipping_charges, 2),
            'tax_rate' => fn($model) => '%' . number_format($model->tax_rate, 2),
            'tax_amount' => fn($model) => currency() . number_format($model->tax, 2),
            'total' => fn($model) => '<b>' . currency() . number_format($model->total_amount, 2) . '</b>',

            // ✅ Payment Method Badge
            'payment_method' => function ($model) {
                $badgeClass = match ($model->payment_method) {
                    'paypal' => 'badge bg-info',
                    'payarc' => 'badge bg-primary',
                    default => 'badge bg-secondary',
                };
                return '<span class="' . $badgeClass . '">' . ucfirst($model->payment_method) . '</span>';
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

    public function create(Request $request){
        $bladePath = $this->pathInitialize;
        $orders = Order::whereNotIn('order_status',  ['cancelled', 'returned', 'delivered'])->get();
        return (string) view($bladePath.'.create_content', get_defined_vars());
    }

    public function store(Request $request){
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'shipping_charges' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string',
            'vendor' => 'required|exists:vendors,id',
        ]); 

        $po_number = null;
        do {
            $po_number = 'PO-' . strtoupper(substr(bin2hex(random_bytes(2)), 0, 6));
        } while ($this->model->where('po_number', $po_number)->exists());
        
        DB::beginTransaction();
        try {
            $taxAmount = ($request->sub_total_value / 100) * $request->tax_rate;

            $pOrder = new $this->model;
            $pOrder->vendor = $request->vendor;
            $pOrder->order_number  = $request->order_number;
            $pOrder->po_number  = $po_number;
            $pOrder->order_date  = date('Y/m/d');
            $pOrder->sub_total  = $request->sub_total_value;
            $pOrder->tax_rate  = $request->tax_rate;
            $pOrder->tax  = $taxAmount;
            $pOrder->shipping_charges  = $request->shipping_charges;
            $pOrder->total_amount  = $request->grand_total_value;
            $pOrder->payment_status  = 'confirmed';
            $pOrder->order_status  = 'confirmed';
            $pOrder->payment_method  = $request->payment_method;
            $pOrder->warranty_info  = $request->warranty_info;
            $pOrder->notes  = $request->notes;
            $pOrder->save();

            Log::info('Purchase Order Created Successfully: '.json_encode($pOrder));

            if($pOrder){
                foreach($request->items as $productKey=>$item){
                    $pOrderItem = new $this->purchaseOrderItemModel;
                    $pOrderItem->purchase_order_id = $pOrder->id;
                    $pOrderItem->product_id = $productKey;
                    $pOrderItem->unit_price = $item['unit_price'];
                    $pOrderItem->quantity = $item['quantity'];
                    $pOrderItem->sub_total = $item['unit_price']*$item['quantity'];
                    $pOrderItem->product_condition = $item['condition'];
                    $pOrderItem->save();
                }

                Log::info('Purchase Order Item Added Successfully: '.json_encode($pOrderItem));
            }

            if($pOrderItem){
                DB::commit();
                Log::info('Final Purchase Order created successfully.');

                return response()->json([
                    'success' => true,
                    'message' => 'You have placed purchase order successfully.',
                ], 200);
            }else{
                DB::rollback();
                $errorMessage = 'Credentials not match';
                Log::error('Purchase order creating failed: ' . $errorMessage);
                return response()->json([
                    'success' => false,
                    'message' => 'Purchase order not placed: ' . $errorMessage,
                ], 500);
            }
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Purchase order creating error: ' . $e->getMessage(),
            ], 500);
        }
    }

    protected function getFieldsAndColumns()
    {
        // Dynamic fields fetched from the database
        $dynamicFields = $this->generateDynamicFieldArray($this->model);

        // Common fields that should always be included
        $commonFields = $this->getCommonFields($this->model);
    
        // Merging common fields with dynamic fields
        $mergedFields = array_merge($dynamicFields, $commonFields);
        
        return $mergedFields;
    }

    public function generateDynamicFieldArray($model) {
        $table = $model->getTable();

        // Get column names and types from the database schema
        // $columns = Schema::getColumnListing($table);
        $columns = DB::connection()->getDoctrineSchemaManager()->listTableColumns($table);
        $fieldArray = [];
    
        foreach ($columns as $columnName => $column) {
            // Skip common fields
            if (in_array($columnName, ['id', 'status', 'order_status', 'order_number', 'po_number', 'order_date', 'payment_status', 'transaction_id', 'created_at', 'created_by', 'action', 'deleted_at', 'updated_at'])) {
                continue;
            }
    
            // Get the type of each column (e.g., string, integer, etc.)
            // $type = Schema::getColumnType($table, $column);
            $type = $column->getType()->getName();
            
            // Build the dynamic field configuration
            $fieldArray[$columnName] = [
                'type' => $type == 'text' ? 'text' : ($type == 'boolean' ? 'select' : 'text'), // Default 'text' or 'select' for boolean
                'label' => ucfirst(str_replace('_', ' ', $columnName)), // Use column name as label (capitalize words and replace underscores)
                'placeholder' => "Enter $columnName", // Placeholder text
                'required' => in_array($columnName, ['title', 'status']), // Example: Mark some fields as required
                'value' => fn($model) => $model->{$columnName} ?? '', // Get the value from the model
                'index' => fn($model) => $model->{$columnName} ?? '-', // Index view value
                'index_visible' => true, // You can dynamically set visibility rules
                'create_visible' => true,
                'edit_visible' => true,
                'show_visible' => true,
            ];

            // Specifically handle the 'description' field
            if ($columnName == 'fields') {
                $fieldArray[$columnName]['index_visible'] = false; // Hide description in index view
            }
        }
    
        return $fieldArray;
    }
    public function getCommonFields($model) {
        // Common fields data (status, created_at, created_by, action)
        return [
            'status' => [
                'type' => 'select',
                'label' => 'Status',
                'options' => [
                    1 => 'Active',
                    0 => 'De-Active'
                ],
                'index' => fn($model) => $model->status == 1
                    ? '<span class="badge bg-label-success me-1">Active</span>'
                    : '<span class="badge bg-label-danger me-1">De-Active</span>',
                'required' => true,
                'index_visible' => true,
                'create_visible' => true,
                'edit_visible' => true,
                'show_visible' => true,
            ],
            'created_at' => [
                'type' => 'datetime',
                'label' => 'Created At',
                'required' => false,
                'value' => fn($model) => Carbon::parse($model->created_at)->format('d, M Y | H:i A') ?? '',
                'index' => fn($model) => Carbon::parse($model->created_at)->format('d, M Y'),
                'index_visible' => true,
                'create_visible' => false,  // Hide in create form
                'edit_visible' => false,    // Hide in edit form
                'show_visible' => true,
            ],
            'created_by' => [
                'type' => 'text',
                'label' => 'Created By',
                'required' => false,
                'value' => fn($model) => isset($model->createdBy) && !empty($model->createdBy) ? $model->createdBy->name : '-',
                'index' => fn($model) => isset($model->createdBy) && !empty($model->createdBy) ? $model->createdBy->name : '-',
                'index_visible' => true,
                'create_visible' => false,  // Hide in create form
                'edit_visible' => false,    // Hide in edit form
                'show_visible' => true,
            ],
            'action' => [
                'index' => fn($model) => view($this->pathInitialize . '.action', [
                    'model' => $model,
                    'singularLabel' => $this->singularLabel,
                    'routeInitialize' => $this->routePrefix
                ])->render(),
                'index_visible' => true,
                'create_visible' => false,  // Hide in create form
                'edit_visible' => false,    // Hide in edit form
                'show_visible' => false,
            ]
        ];
    }

    public function downloadPOInvoice($id)
    {
        $order = $this->model->with('items', 'getVendor', 'getOrder', 'getOrder.shipping')->findOrFail($id);
        $pdf = Pdf::loadView('admin.purchase_orders.invoice_content', compact('order'))
                ->setPaper('a4');

        return $pdf->download('purchase_order_invoice_'.$order->po_number.'.pdf');
    }

    public function show($id){
        $order = $this->model->with('items', 'getVendor', 'getOrder', 'getOrder.shipping')->findOrFail($id);
        return (string) view('admin.purchase_orders.order_content', compact('order'));
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
        $vendors = Vendor::where('status', 1)->get();
        $order = $this->model->where('id', $id)->first();
        $customerName = null;
        if(isset($order->getOrder->shipping) && !empty($order->getOrder->shipping)){
            $customerName = $order->getOrder->shipping->first_name.' '.$order->getOrder->shipping->last_name;
        }
        return view($bladePath.'.edit_content', get_defined_vars());
    }

    public function update(Request $request, $modelId)
    {   
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'shipping_charges' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string',
            'vendor' => 'required|exists:vendors,id',
        ]); 
        
        DB::beginTransaction();
        try {
            $taxAmount = ($request->sub_total_value / 100) * $request->tax_rate;

            $pOrder = $this->model->where('id', $modelId)->first();
            $pOrder->vendor = $request->vendor;
            $pOrder->order_date  = date('Y/m/d');
            $pOrder->sub_total  = $request->sub_total_value;
            $pOrder->tax_rate  = $request->tax_rate;
            $pOrder->tax  = $taxAmount;
            $pOrder->shipping_charges  = $request->shipping_charges;
            $pOrder->total_amount  = $request->grand_total_value;
            $pOrder->payment_status  = 'confirmed';
            $pOrder->order_status  = 'confirmed';
            $pOrder->payment_method  = $request->payment_method;
            $pOrder->warranty_info  = $request->warranty_info;
            $pOrder->notes  = $request->notes;
            $pOrder->save();
            
            Log::info('Purchase Order Created Successfully: '.json_encode($pOrder));

            if($pOrder){
                $this->purchaseOrderItemModel->where('purchase_order_id', $pOrder->id)->delete();
                
                foreach($request->items as $productKey=>$item){
                    $pOrderItem = new $this->purchaseOrderItemModel;
                    $pOrderItem->purchase_order_id = $pOrder->id;
                    $pOrderItem->product_id = $productKey;
                    $pOrderItem->unit_price = $item['unit_price'];
                    $pOrderItem->quantity = $item['quantity'];
                    $pOrderItem->sub_total = $item['unit_price']*$item['quantity'];
                    $pOrderItem->product_condition = $item['condition'];
                    $pOrderItem->save();
                }

                Log::info('Purchase Order Item Updated Successfully: '.json_encode($pOrderItem));
            }

            if($pOrderItem){
                DB::commit();
                Log::info('Final Purchase Order Updated successfully.');

                return response()->json([
                    'success' => true,
                    'message' => 'You have updated purchase order successfully.',
                ], 200);
            }else{
                DB::rollback();
                $errorMessage = 'Credentials not match';
                Log::error('Purchase order updating failed: ' . $errorMessage);
                return response()->json([
                    'success' => false,
                    'message' => 'Purchase order not updated: ' . $errorMessage,
                ], 500);
            }
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Purchase order updating error: ' . $e->getMessage(),
            ], 500);
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
                    ->select(['id', 'po_number', 'vendor', 'order_date', 'notes', 'sub_total','tax_rate', 'tax', 'shipping_charges', 'total_amount', 'payment_method', 'warranty_info', 'order_status', 'created_at']);

        // Define the columns dynamically
        $columns = [
            'po_number' => fn($model) => '<strong>' . $model->po_number . '</strong>',
            'subtotal' => fn($model) => currency() . number_format($model->sub_total, 2),
            'shipping' => fn($model) => currency() . number_format($model->shipping_charges, 2),
            'tax_rate' => fn($model) => '%' . number_format($model->tax_rate, 2),
            'tax_amount' => fn($model) => currency() . number_format($model->tax, 2),
            'total' => fn($model) => '<b>' . currency() . number_format($model->total_amount, 2) . '</b>',

            // ✅ Payment Method Badge
            'payment_method' => function ($model) {
                $badgeClass = match ($model->payment_method) {
                    'paypal' => 'badge bg-info',
                    'payarc' => 'badge bg-primary',
                    default => 'badge bg-secondary',
                };
                return '<span class="' . $badgeClass . '">' . ucfirst($model->payment_method) . '</span>';
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
}
