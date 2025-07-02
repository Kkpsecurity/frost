<?php  namespace App\Services;

use File;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;


class SiteSearchService
{

    /**
     * Text wrapping Buffer
     */
    const BUFFER = 10;


    /**
     * Initiate
     */
    protected function __construct() {
        $this->excluded = [];
    }


    /**
     * A helper function to generate the model namespace
     * @return string
     */
    private static function getModelNamespace(): string
    {
        return app()->getNamespace() . 'Models\\';
    }

    /**
     * @param Request $request
     */
    public static function FullSiteSearch(Request $request)
    {

        $result = [];

        $excluded = [];

        $keyword = $request->search;
        $files = File::fIles(app()->basePath() . '/app/Models');

        $result = collect($files)
            ->map(function (\SplFileInfo $file) {

                $filename = $file->getRelativePathname();
                if (!str_ends_with($filename, '.php')) {
                    return null;
                }

                return substr($filename, 0, -4);
            })->filter(function (?string $classname) use ($excluded)  {

                if ($classname === null)  return false;

                try {
                    $reflection = new \ReflectionClass(self::getModelNamespace() . $classname);
                } catch (\ReflectionException $e) {
                    return false;
                }

                /**
                 * Check if model extends Eloquent
                 */
                $isModel = $reflection->isSubclassOf(Model::class);

                /**
                 * Is The Model Searchable
                 */
                $searchable = $reflection->hasMethod('search');

                return ($isModel && $searchable && !in_array($reflection->getName(), $excluded, true));
        })->map(function ($classname) use ($keyword) {

                $classPath   = self::getModelNamespace() . $classname;
                $model          = app($classPath);
                $fields            = array_filter($model::SEARCHABLE_FIELDS, fn($field) => $field !== 'id');

                return $model::search($keyword)->get()->map(function($modelRecord) use($fields, $keyword, $classname) {

                    /**
                     * Compare the keyword
                     */
                    $fieldsData = $modelRecord->only($fields);
                    $serializedValues = collect($fieldsData)->join(' ');
                    $searchPos = strpos(strtolower($serializedValues), strtolower($keyword));

                    /**
                     * Format Output
                     */
                    if($searchPos !== false) {
                        $start = $searchPos  - self::BUFFER;
                        $start = $start < 0 ? 0 : $start;
                        $length = strlen($keyword) + 2 * self::BUFFER;

                        $sliced = substr($serializedValues, $start, $length);

                        $shouldAddPrefix = $start > 0;
                        $shouldAddPostfix = ($start + $length) < strlen($serializedValues);

                        $sliced = $shouldAddPrefix ? '...' : $sliced;
                        $sliced = $shouldAddPostfix ? $sliced .  '...' : $sliced;
                    }

                    $modelRecord->setAttribute('match', $sliced ?? substr($serializedValues, 0, 2 * self::BUFFER) . '...');
                    $modelRecord->setAttribute('model', $classname);
                    $modelRecord->setAttribute('view_link', self::resolveModelViewLink($modelRecord));

                    return $modelRecord;
                });
        })->flatten(1);

      return $result;
    }

    /**
     * @param Model $model
     * @return mixed
     */
    protected static function resolveModelViewLink(Model $model): mixed
    {

        $mapping = [\App\Models\User::class => 'account' ];

        $modelClass = get_class($model);

        /**
         * Mapping Custom URL Patterns
         */
        if(\Arr::has($mapping, $modelClass)) {
            return \URL::to(str_replace('{id}', $model->id, $mapping[$modelClass]));
        }

        $modelName = \Str::pluar(\Arr::last(explode('\\', $modelClass)));
        $modelName = \Str::kebab(\Str::camel($modelClass));

        return \URL::to('/' . $modelName . '/' . $model->id) ?? '';
    }
}
