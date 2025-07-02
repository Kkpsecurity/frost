<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Artesaos\SEOTools\Facades\SEOMeta;

trait PageMetaDataTrait
{
    /**
     * Render page meta data.
     *
     * @param string $method
     * @param string|null $content
     * @return array
     */
    public static function renderPageMeta(string $method, ?string $content = null): array
    {
        $pageTitle = ($method === 'index' ? __('Dashboard') : ucfirst(humanize($method)));

        $title = Str::title($pageTitle);
        $keywords = self::generateKeywords($content, $pageTitle);
        $description = self::generateDescription($content, $pageTitle);

        SEOMeta::setTitle($title);
        SEOMeta::setKeywords($keywords);
        SEOMeta::setDescription($description);

        return [
            'title' => $title,
            'keywords' => $keywords,
            'description' => $description,
        ];
    }

    /**
     * Generate keywords.
     *
     * @param string|null $content
     * @param string $pageTitle
     * @return string
     */
    private static function generateKeywords(?string $content, string $pageTitle): string
    {
        $keywords = Config::get('app.keywords', []);
        $keywords[] = $pageTitle;

        if ($content !== null) {
            $keywords[] = $content;
        }

        return implode(', ', $keywords);
    }

    /**
     * Generate description.
     *
     * @param string|null $content
     * @param string $pageTitle
     * @return string
     */
    private static function generateDescription(?string $content, string $pageTitle): string
    {
        $description = Config::get('app.description', []);
        $description[] = $pageTitle;

        if ($content !== null) {
            $description[] = $content;
        }

        return implode(', ', $description);
    }
}
