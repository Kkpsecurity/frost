<?php
namespace App\Classes;

use Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Support\LTEBootstrap;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use App\Models\CourseUnit;
use App\Models\Course;

use Illuminate\Support\Collection;
use Collective\Html\FormFacade as Form;
use App\Support\FormBuilder;

/**
 * Class SwiftCrud
 * @version 1.0.1
 * @author: Richard Clark
 * @package App\Classes
 * 
 * @desc: This class is used to generate the main crud system:
 */

class SwiftCrud
{
    protected $model;

    /**
     * SwiftCrud constructor.
     * @param $modelOrTable string|Model
     * @throws \InvalidArgumentException
     */
    public function __construct($modelOrTable)
    {
        if (is_string($modelOrTable)) {
            $this->model = $modelOrTable;
        } elseif ($modelOrTable instanceof Model) {
            $this->model = $modelOrTable;
        } else {
            throw new \InvalidArgumentException('Invalid argument type: ' . gettype($modelOrTable));
        }
    }

    /**
     * @desc: Get all records
     * @param array $fields
     * @return Collection
     */
    public function getAll($fields = null): Collection
    {
        if ($fields) {
            return $this->model->get($fields);
        } else {
            return $this->model->all();
        }
    }



    /**
     * @desc: Get all records
     * @param int $id
     * @return Model
     */
    public function getById($id): Model
    {
        return $this->model->find($id);
    }

    /**
     * @desc: Create a new record
     * @param array $data
     * @return Model
     */
    public function store($data): Model
    {
        $data = $this->prepareData($data);

        return $this->model->create($data);
    }

    /**
     * @desc: Update a record
     * @param int $id
     * @param array $data
     * @return Model
     */
    public function update($id, $data): Model
    {
        $data = $this->prepareData($data);
        $model = $this->model->find($id);

        $model->fill($data)->save();
        return $model;
    }

    protected function prepareData($data)
    {

        foreach ($data as $key => $value) {

            if (isset($data['is_active']) && $data['is_active'] == '1') {
                $data['is_active'] = 1;
            } else {
                $data['is_active'] = 0;
            }


            if (isset($data['use_gravatar']) && $data['use_gravatar'] == '1') {
                $data['use_gravatar'] = 1;
            } else {
                $data['use_gravatar'] = 0;
            }

            // check for file uploads and deletions
            if (request()->hasFile($key)) {
                $uploadedFile = $data[$key];
                $fileUrl = $uploadedFile->store('uploads', 'public');
                $data[$key] = asset('storage/' . $fileUrl);
            } elseif (isset($data['delete_avatar'])) {
                $path = storage_path('app/public/' . str_replace('/storage/', '', $data['avatar']));
                if (\Storage::disk('public')->delete($path)) {
                    flash('File Deleted')->success();
                    $data['avatar'] = null;
                    unset($data['delete_avatar']);
                } else {
                    flash('File Not Deleted')->error();
                }
            }


            if (empty($data['password'])) {
                unset($data['password']);
            } else {
                $data['password'] = bcrypt($data['password']);
            }

        }

        return $data;
    }


    /**
     * @desc: Delete a record
     * @param int $id
     * @return bool
     */
    public function delete($id): bool
    {
        $roleId = Auth::user()->role_id;
        if ($roleId !== 1) {
            return false;
        }

        $model = $this->model->findOrFail($id);
        if (!$model->delete()) {
            return false;
        }

        return true;
    }



    /**
     * @desc: Generate the table
     * @param $options
     */
    public function generateSwiftCrud($options)
    {
        /**
         * Determine the current view and ID
         */
        $pathSegments = explode('/', request()->path());
        $parentSegment = count(explode('.', $options['parent_route']));
        $view = $pathSegments[$parentSegment] ?? 'index';
        $id = $pathSegments[$parentSegment + 1] ?? null;

           
        /**
         * Generate the CRUD system based on the current view
         */
        if (
            ($view == 'create' && !empty($options['columns']['create'])) ||
            ($view == 'edit' && !empty($options['columns']['edit']))
        ) {
            $form = new FormBuilder();
            $options = $this->prepareFormOptions($form, $options, $view);
            return $this->generateForm($form, $options, $view, $id);
        } elseif ($view == 'store') {
            $id = $this->store(request()->all());
            flash('Record Created')->success();
            return redirect()->route($options['parent_route'], ['edit', $id]);
        } elseif ($view == 'update') {
            $this->update($id, request()->all());
            flash('Record Updated')->success();
            return redirect()->route($options['parent_route'], ['edit', $id]);
        } elseif ($view == 'delete') {
            flash('Record Deleted')->success();
            return $this->delete($id);
        } elseif ($view == 'view' && isset($options['columns']['view'])) { 
            return view($options['view'], ['id' => $id]); // Use the view from options and pass the id to it
        } else {
            return $this->generateTable($options);
        }
    }


    /**
     * @desc: Prepares the Form Options 
     * and determines the form type
     * @param array $options
     * @return array
     */
    protected function prepareFormOptions($form, $options, $view): array
    {
        // Generate fields based on the specified columns
        $fields = $this->generateFields($options['columns'][$view], $form);

        // Set default buttons (can be customized further later)
        $buttons = $this->getDefaultButtons();

        // Prepare the form options structure
        $formOptions = [
            $view => [
                'fields' => $fields,
                'buttons' => $buttons
            ]
        ];

        // Merge the form options with the provided options
        $mergedOptions = $this->mergeFormOptions($options, $formOptions);

        return $mergedOptions;
    }

    protected function generateFields($columns, $form)
    {
        return collect($columns)->map(function ($column) use ($form) {
            return [
                'name' => $column,
                'label' => ucwords(str_replace('_', ' ', $column)),
                'type' => $form->getFormType($column),
                'attributes' => ['class' => 'form-control'],
            ];
        })->toArray();
    }

    protected function getDefaultButtons()
    {
        return [
            ['label' => 'Save', 'attributes' => ['class' => 'btn btn-primary']]
        ];
    }

    protected function mergeFormOptions($originalOptions, $formOptions)
    {
        if (isset($originalOptions['columns'])) {
            // If 'columns' exists in the original options, we will merge but avoid data duplication
            $originalOptions['columns'] = array_replace_recursive($originalOptions['columns'], $formOptions);
        } else {
            $originalOptions['columns'] = $formOptions;
        }

        return $originalOptions;
    }



    /**
     * @desc: Generate the Create Form
     * @param array $options
     */
    protected function generateForm($form, $options, $view, $id = null)
    {
        if ($view == 'edit') {
            if ($id !== null) {
                $data = $this->getById($id);
            } else {
                $data = $this->model;
            }
        }

        return view('admin.center.filters.form_view', compact('form', 'options', 'view', 'id', 'data'));
    }



    /**
     * @desc: Prepares search queries for the crud system
     * @param $request
     * @param $query
     * 
     */
    public function prepareCrudFilters($query, $request)
    {
        // Check if the model is User  if so add search for users
        if (get_class($query->getModel()) === 'App\Models\User') {
            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('fname', 'ilike', '%' . $request->search . '%');
                    $q->orWhere('lname', 'ilike', '%' . $request->search . '%');
                    $q->orWhere('email', 'ilike', '%' . $request->search . '%');
                });
            }
        } else {
            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('title', 'ilike', '%' . $request->search . '%');
                    $q->orWhere('description', 'ilike', '%' . $request->search . '%');
                });
            }
        }

        if ($request->filled('daterange')) {
            $daterange = explode(' - ', $request->daterange);
            $query->whereBetween('created_at', $daterange);
        }

        return $query;
    }


    /**
     * @desc: Generate the Table
     * @param array $options
     * @return string
     */
    protected function generateTable($options): string
    {
        /**
         * Get records Check if the model is a LengthAwarePaginator or a Collection
         */
        $data = $options['data'];
        if ($data instanceof LengthAwarePaginator) {
            $records = $data->items();
        } elseif ($data instanceof Illuminate\Database\Eloquent\Collection) {
            $records = $data->all();
        } else {
            $records = $data;
        }

        foreach ($records as &$record) {
            if ($record instanceof User && method_exists($record, 'getAvatar')) {
                $record->avatar = $record->getAvatar('thumb');
            }
        }

        /**
         * Get table Columns
         */
        $columns = $options['columns']['table'];

        /**
         * Preapre headers to support sorting
         */
        $sortColumn = request()->get('sort_column', '');
        $sortDirection = request()->get('sort_direction', '');

        /**
         * Format table headers
         */
        $headerColumns = array_map(function ($column) use ($sortColumn, $sortDirection, $options) {
            $sortDir = $sortColumn === $column ? ($sortDirection === 'asc' ? 'desc' : 'asc') : 'asc';
            $url = route($options['parent_route'], array_merge(request()->all(), ['view' => 'table', 'sort_column' => $column, 'sort_direction' => $sortDir]));
            return "<div class=\"d-flex justify-content-between\">" . $this->formatFieldTitle($column) . ' <a href="' . $url . '" class="sort-icons"><i class="fas fa-sort' . ($sortColumn === $column ? '-' . $sortDirection : '') . '"></i></a></div>';
        }, $columns);

        /**
         * Exclude fields from table view
         */
        if (isset($options['exclude'])) {
            $excludeColumns = $options['exclude'];
            $columns = array_diff($columns, $excludeColumns);
            $headerColumns = array_diff($headerColumns, array_map([$this, 'formatFieldTitle'], $excludeColumns));
        }

        /**
         * Generate table headers and rows
         */
        $rows = $this->getTableRows($records, $columns, $options);
        $tableRows = $this->generateTableRows($rows, $options);
        $tableHeaders = array_merge($headerColumns, ['Action']);

        /**
         * Generate table
         */
        $tableOptions = array_filter($options, 'is_string');
        $table = LTEBootstrap::Table($tableHeaders, $tableRows, $tableOptions);

        /**
         * Generate table filters and pagination links
         */
        $filters = $this->displayTableFilters($options, $records);
        $pagination = $this->paginationLinks($records);

        /**
         * Generate output
         */
        return $filters . $table . $pagination;
    }

    /**
     * @desc: Generates the Table Headers
     * @param array $headerColumns
     * @param array $options
     * @return string
     */
    protected function generateTableHeaders($headerColumns, $options = [])
    {
        $sortColumn = request()->get('sort_column', '');
        $sortDirection = request()->get('sort_direction', '');

        $headers = array_map(function ($column) use ($sortColumn, $sortDirection, $options) {
            $sortDir = $sortColumn === $column ? ($sortDirection === 'asc' ? 'desc' : 'asc') : 'asc';
            $url = route($options['parent_route'], array_merge(request()->all(), ['view' => 'table', 'sort_column' => $column, 'sort_direction' => $sortDir]));
            return "<th><div class=\"d-flex justify-content-between\">" . $this->formatHeader($column) . ' <a href="' . $url . '" class="sort-icons"><i class="fas fa-sort' . ($sortColumn === $column ? '-' . $sortDirection : '') . '"></i></a></div></th>';
        }, $headerColumns);

        // Add the action column
        $headers[] = '<th>Action</th>';

        return '<tr>' . implode('', $headers) . '</tr>';
    }

    /**
     * @desc: Display Table Filters
     * @param array $options
     * @param int $maxColumnSize
     * @return string
     */
    protected function displayTableFilters($options = [], $records, $maxColumnSize = 4): string
    {
        $filters = view('admin.center.filters.search', compact('options'));

        $customFilters = [
            'status' => 'admin.center.filters.status',
            'created_at' => 'admin.center.filters.daterange',
        ];

        $numFilters = 0;
        foreach ($customFilters as $columnName => $viewPath) {
            if (view()->exists($viewPath)) {
                $numFilters++;
            }
        }

        // Move column size calculation after counting filters
        $colSize = min($maxColumnSize, max(1, intval(12 / max($numFilters, 1))));

        /**
         * Generate the User Filter
         */
        $roleFilters = "";
        $userFilterViewPath = 'admin.center.filters';
        foreach ($records->items() as &$record) {
            if ($record instanceof User) {
                $roleFilters = '<div class="col-' . $colSize . ' d-flex align-items-center justify-content-center">' .
                    view($userFilterViewPath . '.user_role', compact('options', 'record'))->render() .
                    '</div>';
            }
        }

        if ($roleFilters) {
            $filters .= $roleFilters;
        }

        foreach ($customFilters as $columnName => $viewPath) {
            if (view()->exists($viewPath)) {
                // Encapsulate the view inside a div with bootstrap column size
                $filters .= '<div class="col-' . $colSize . ' d-flex align-items-center justify-content-center">' .
                    view($viewPath, compact('options'))->render() .
                    '</div>';
            }
        }

        return '<div class="row">' . $filters . '</div>';
    }



    /**
     * @desc: Generates table rows for the given records and columns
     * @param Collection $records
     * @param array $columns
     * @param array $options
     * @return array
     */
    protected function getTableRows($records, $columns, $options = []): array
    {
        $roleId = auth()->user()->role_id;
        $rows = [];

        foreach ($records as $record) {
            $row = [];
            foreach ($columns as $column) {
                $row[] = $this->formatData($record, $column, $options);
            }
            $row[] = $this->generateTableActions($record, $roleId, $options);
            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * @desc: Format the data for the given column
     * @param Model $record
     * @param string $column
     * @param array $options
     */
    protected function formatData($record, $column, $options = [])
    {
        if ($column === 'avatar') {
            $row = '<img src="' . $record['avatar'] . '" class="img-thumbnail" width="40" height="40" alt="Avatar" />';
        } elseif ($column === 'is_active') {
            $row = $record['is_active'] ? '<i class="fas fa-dot-circle text-green mr-2"></i> Active' : '<i class="fas fa-dot-circle text-red mr-2"></i> Inactive';
        } elseif ($column === 'created_at' || $column === 'updated_at' || $column === 'completed_at') {
            try {
                $date = Carbon::parse($record[$column]);
                $row = $date->format('d/m/Y : H:i:s');
            } catch (\Exception $e) {
                // Handle the case where $record[$column] is not a valid date
                $row = $record[$column];
            }
        } elseif ($column === 'role_id') {
            $row = $record['role']->name;
        } elseif ($column === 'course_date_id') {
            // use the "CourseDate" relation to get the CourseUnit and then the Course title
            if ($record->CourseDate) {
                $courseUnit = $record->CourseDate->GetCourseUnit();
                if ($courseUnit && $courseUnit->course) {
                    $row = $courseUnit->course->title_long . ' - ' . $courseUnit->title;
                } else {
                    $row = 'Course or Course Unit not found';
                }
            } else {
                $row = 'Course Date not found';
            }
        } else {
            $value = $record[$column] ?? '';
            $row = $value;
        }

        return $row;
    }



    /**
     * @desc: Format the header name
     * @param $column
     * @return string
     */
    protected function formatHeader($column): string
    {
        if (strpos($column, '_id') !== false) {
            $column = str_replace('_id', '', $column);
        }

        if ($column == 'fname') {
            return 'First Name';
        }
        if ($column == 'lname') {
            return 'Last Name';
        }

        return ucwords(humanize($column));
    }

    /**
     * @desc: Generates the Table Rows
     * @param array $rows
     * @param array $options
     * @return array
     */
    protected function generateTableRows($rows, $options = []): array
    {
        $tableRows = array_map(function ($row) {
            if (is_array($row) || is_object($row)) {
                return implode('', array_map(function ($cell) {
                    return '<td>' . $cell . '</td>';
                }, $row));
            } else {
                return '<td>' . $row . '</td>';
            }
        }, $rows);

        return $tableRows;
    }

    /**
     * @desc: Format Column to To Title
     */
    public function formatFieldTitle($column): string
    {
        if (strpos($column, '_id') !== false) {
            $column = str_replace('_id', '', $column);
        }

        if ($column == 'fname') {
            return 'First Name';
        }
        if ($column == 'lname') {
            return 'Last Name';
        }

        return ucwords(humanize($column));
    }

    /**
     * @desc: Generate the pagination links
     * @param $records
     * @return string
     */
    protected function paginationLinks($records): string
    {
        return $records->appends(request()->except('page'))->links('pagination::bootstrap-4');
    }

    /**
     * @desc: Generate the actions column
     * @param $record
     * @param $roleId
     * @param $options
     */
    protected function generateTableActions($record, $roleId, $options)
    {
        $actions = '<div class="d-flex justify-content-end">';

        // Existing edit link code...
        if (!empty($options['columns']['edit'])) {
            $actions .= '<a class="btn btn-primary btn-sm m-2" href="' . route($options['parent_route'], ['edit', $record->id]) . '"><i class="fa fa-edit"></i></a>';
        }

        // Existing view link code...
        if (!empty($options['columns']['view'])) {
            $actions .= '<a class="btn btn-success btn-sm m-2" href="' . route($options['parent_route'], [$options['columns']['view'], $record->id]) . '/' . $record->id . '"><i class="fa fa-eye"></i></a>';
        }

        // New impersonate button code
        // check model if use
        if ($record instanceof User) {
            if ($roleId == 1) {
                $actions .= '<a class="btn btn-warning btn-sm m-2" href="' . route('admin.impersonate.account.user', $record->id) . '"><i class="fa fa-user-secret"></i></a>';
            }
        }

        // Existing delete button code...
        if ($roleId == 1) {
            $actions .= '<button 
            type="button" 
            class="btn btn-danger btn-sm delete-btn m-2" 
            data-modal-id="delete-record-modal"
            data-record-id="' . $record->id . '"
            data-toggle="modal" data-target="#delete-record-modal"
            data-modal-type="delete-record-modal"
        ><i class="fa fa-trash"></i></button>';
        }

        $actions .= '</div>';

        return $actions;
    }



}