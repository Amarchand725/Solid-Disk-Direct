<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\DataTableTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;

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
                    ->latest()
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
        $order = $this->model->findOrFail($id);
        return view('admin.orders.invoice', compact('order'));
    }

    public function downloadInvoice($orderId) {
        $order = Order::findOrFail($orderId);
        $pdf = Pdf::loadView('invoice', [
            'invoiceNumber' => $order->invoice_number,
            'date' => $order->created_at->format('d-m-Y h:i:s a'),
            // Add other variables here...
        ]);

        return $pdf->download('invoice_'.$order->invoice_number.'.pdf');
    }
}
