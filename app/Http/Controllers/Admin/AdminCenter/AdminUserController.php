<?php

namespace App\Http\Controllers\Admin\AdminCenter;

use App\Http\Controllers\Controller;
use App\Models\CourseAuth;
use App\Models\User;
use App\Forms\UserForm;
use App\Traits\PageMetaDataTrait;
use Illuminate\Http\Request;
use App\Classes\SwiftCrud;
use RCache;


class AdminUserController extends Controller
{
    use PageMetaDataTrait;

    /**
     * @desc: Fields to exclude from the table
     * @return array
     */
    protected function getTableExcludes() : array
    {
        return [
            'password',
            'remember_token',
            'updated_at',
            'use_gravatar',
            'zoom_last_validated',
            'zoom_payload',
            'created_at',
        ];
    }

    /**
     * @desc: Fields for each view
     * @return array
     */
    protected function getFieldViews() : array
    {
        $crudViews = [
            'table' => [
                'id',
                'avatar',
                'lname',
                'fname',
                'email',
                'is_active',
                'role_id',
            ],
            'create' => [
                'id',
                'lname',
                'fname',
                'is_active',
                'role_id',
                'email',
                'password',
            ],
            'edit' => [
                'id',
                'lname',
                'fname',
                'is_active',
                'role_id',
                'email',
                'password',
                'avatar',
                'use_gravatar',
            ],
        ];

        return $crudViews;
    }

    /**
     * @desc: Admin User Dashboard Using SwiftCrud
     * @param Request $request
     * @param null $view
     * @param null $id
     */
    public function dashboard(Request $request, $view = null, $id = null)
    {
        /**
         * Instantiate SwiftCrud
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
        $sortColumn = request()->get('sort_column', 'lname');
        $sortDirection = request()->get('sort_direction', 'desc');

        /**
         * Get the data
         */
        $data = $query->select($this->getFieldViews()['table'])
            # ROLES_UPDATE
            #->whereIn('role_id', [1, 2, 3])
            ->whereNot( 'role_id', RCache::RoleID( 'Student' ) )
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

                'parent_route' => 'admin.center.adminusers',
            ]),
        ], self::renderPageMeta('admin_user_accounts'));

        return view('admin.center.list', compact('content'));
    }
}
