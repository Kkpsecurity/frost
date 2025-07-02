<?php
namespace App\Http\Controllers\Admin\Orders;

use App\Classes\SwiftCrud;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Traits\PageMetaDataTrait;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    use PageMetaDataTrait;

    /**
     * @desc: Fields to exclude from the table
     * @return array
     */
    protected function getTableExcludes(): array
    {
        return [
            'id',
            'updated_at',
        ];
    }

    /**
     * @desc: Fields for each view 
     * @return array
     */
    protected function getFieldViews(): array
    {
        return [
            'table' => [
                'id',                // It's useful to have the Order ID for reference.
                'user_id',           // Representing the user; could be replaced with 'student_name' via join.
                'course_id',         // Could be replaced with 'course_name' if you join with the Courses table.
                'payment_type_id',   // Consider representing as 'payment_type' using a join.
                'course_price',      // Directly from the Order model.
                'discount_code_id',  // Optionally show the code itself via a join.
                'total_price',       // Directly from the Order model.
                'completed_at',      // Useful for seeing when an order was finalized.
            ],
            'create' => [
                'user_id',           // Necessary for linking the order to a user.
                'course_id',         // Necessary for selecting which course is being ordered.
                'payment_type_id',   // To choose how the order is being paid for.
                'course_price',      // Could be pre-filled and then adjusted as needed.
                'discount_code_id',  // To apply a discount at the time of order creation.
                'total_price',       // Calculated based on the course price and discount.
            ],
            'edit' => [
                'course_price',      // Allow editing the price in case of adjustments.
                'discount_code_id',  // Allow changing the discount code post-creation.
                'total_price',       // Might need to be adjusted if other elements change.
                'completed_at',      // Allow marking an order as completed if it wasn't before.
                'refunded_at',       // Marking an order as refunded.
            ],
        ];
    }
    


    public function dashboard(Request $request)
    {
        // Compile all page-related content in one go
        $content = [
            'OrdersTable' => $this->getOrders($request),
        ];

        // Merge page meta data into content
        $content = array_merge($content, self::renderPageMeta('orders'));

        return view('admin.orders.dashboard', compact('content'));
    }



    public function getOrders($request)
    {
        /**
         * Instantiate SwiftCrud
         */
        $crud = new SwiftCrud(new Order());

        /**
         * Prepare the query builder
         */
        $query = Order::query();
        $query = $crud->prepareCrudFilters($query, $request);

        /**
         * manage the sort column and direction
         */
        $sortColumn = request()->get('sort_column', 'course_price');
        $sortDirection = request()->get('sort_direction', 'desc');

        /**
         * Get the data
         */
        $data = $query->select($this->getFieldViews()['table'])
            ->whereNotNull('completed_at')->orderBy($sortColumn, $sortDirection)
            ->paginate(10);

        /**
         * Generate the View
         */
        $content = $crud->generateSwiftCrud([
            'data' => $data,
            'columns' => $this->getFieldViews(),
            'exclude' => $this->getTableExcludes(),
            'parent_route' => 'admin.orders.dashboard',
        ]);
        return $content;
    }
}
