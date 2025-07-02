<?php namespace App\Support\Enum;

use App\Models\ProductCategory;

class CategoryList
{


    /**
     * @return array
     */
    public static function options()
    {
        $cats = ProductCategory::whereNull('category_id')->get();

        $categories = [];
        if ($cats->count() > 0) {
            foreach ($cats as $cat) {
                $categories[$cat->id] = $cat->name;
            }
        }

        return $categories;
    }
}
