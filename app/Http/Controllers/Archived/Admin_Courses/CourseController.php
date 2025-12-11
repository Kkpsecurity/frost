<?php namespace App\Http\Controllers\Admin\Courses;


use App\Classes\SwiftCrud;
use App\Models\CourseDate;
use App\Models\InstUnit;
use Illuminate\Http\Request;
use App\Traits\PageMetaDataTrait;
use App\Http\Controllers\Controller;

class CourseController extends Controller {
    use PageMetaDataTrait;

    protected function getTableExcludes() : array {
        return [];
    }

    /**
     * List of fields to be displayed in the CRUD
     * table, create, edit are the db fields
     * view is the blade view to be used
     */
    protected function getFieldViews() : array {
        $crudViews = [
            'table' => [
                'id',
                'course_date_id',
                'created_at',
                'completed_at'
            ],
            'create' => [],
            'edit' => [],
            'view' => 'details',
        ];  
        return $crudViews;
    }
   
    
    public function dashboard(Request $request) {

        /**
         * Instantiate SwiftCrud
         */
        $crud = new SwiftCrud(new CourseDate());
       
        $data = [];

        /**
         * Prepare the query builder
         */
        $query = InstUnit::query();
        $query = $crud->prepareCrudFilters($query, $request);
        
        /**
         * manage the sort column and direction
         */
        $sortColumn = request()->get('sort_column', 'course_date_id');
        $sortDirection = request()->get('sort_direction', 'desc'); 
       
        /**
         * If System Admin or Admin, show all records
         */
        if (auth()->user()->role->id == 1 || auth()->user()->role->id == 2) {
            $data = $query->select($this->getFieldViews()['table'])
                ->orderBy($sortColumn, $sortDirection)
                ->latest('created_at')
                ->paginate(10);
        } else {
            $data = $query->select($this->getFieldViews()['table'])
                ->where('created_by', auth()->id())
                ->orderBy($sortColumn, $sortDirection)
                ->latest('created_at')
                ->paginate(10);
        }
        
        /**
         * Generate the View
         */
        $content = array_merge([
            'SwiftCrud' => $crud->generateSwiftCrud([
                'data' => $data,
                'columns' => $this->getFieldViews(),
                'exclude' => $this->getTableExcludes(),

                'parent_route' => 'admin.courses.dashboard',
            ]),
        ], self::renderPageMeta('admin_user_accounts'));
        
        return view('admin.courses.dashboard', compact('content'));
    }
    
}
