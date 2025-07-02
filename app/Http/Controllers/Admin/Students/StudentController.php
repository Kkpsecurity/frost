<?php
namespace App\Http\Controllers\Admin\Students;

use App\Traits\PageMetaDataTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Forms\UserForm;
use App\Classes\SwiftCrud;
use RCache;


class StudentController extends Controller
{
    use PageMetaDataTrait;

    /**
     * @desc: Fields to exclude from the table
     * @return array
     */
    protected function getTableExcludes(): array
    {
        return [
            'password',
            'remember_token',
            'updated_at',
            'use_gravatar',
            'zoom_last_validated',
            'zoom_payload',
            'role_id',
        ];
    }

    /**
     * @desc: Fields for each view
     * @return array
     */
    protected function getFieldViews(): array
    {
        $crudViews = [
            'table' => [
                'id',
                'avatar',
                'lname',
                'fname',
                'email',
                'created_at',
                'is_active',
            ],
            'create' => [
                'lname',
                'fname',
                'is_active',
                'email',
            ],
            'edit' => [
                'id',
                'lname',
                'fname',
                'is_active',
                'email',
                'avatar',
                'use_gravatar',
            ],
        ];

        return $crudViews;
    }

    /**
     * @desc: Admin User Dashboard Using SwiftCrud
     * @param Request $request
     * @param $view
     * @param $id
     */
    public function dashboard(Request $request, $view = null, $id = null)
    {
        /**
         * @MUST: Set Root Model
         */
        $crud = new SwiftCrud(new User());

        /**
         * Prepare the query builder
         */
        $query = User::query();
        $query = $crud->prepareCrudFilters($query, $request);


        /**
         * manage the sort column and direction
         */
        if ($request->has('sort_column') && $request->has('sort_direction')) {
            $sortColumn = $request->get('sort_column', 'lname');
            $sortDirection = $request->get('sort_direction', 'desc');
        } else {
            $sortColumn = 'lname';
            $sortDirection = 'desc';
        }

        /**
         * Get the data
         */
        $data = $query->select($this->getFieldViews()['table'])
            # ROLES_UPDATE
            #->whereIn('role_id', [4])
            ->where( 'role_id', \App\RCache::RoleID( 'Student' ) )
            ->orderBy($sortColumn, $sortDirection)
            ->latest('created_at')
            ->paginate(10);

        /**
         * Generate the View
         */
        $content = array_merge([
            'SwiftCrud' => $crud->generateSwiftCrud([
                'data' => $data,
                'columns' => $this->getFieldViews(),
                'exclude' => $this->getTableExcludes(),

                'parent_route' => 'admin.students',
            ]),
        ], self::renderPageMeta('student_accounts'));

        return view('admin.students.dashboard', compact('content'));
    }
}