<?php

namespace Dashed\DashedPages\Models;

use Dashed\DashedCore\Classes\Sites;
use Dashed\DashedCore\Models\Concerns\HasCustomBlocks;
use Dashed\DashedCore\Models\Concerns\IsVisitable;
use Dashed\Seo\Jobs\ScanSpecificResult;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class Page extends Model
{
    use SoftDeletes;
    use IsVisitable;
    use HasCustomBlocks;

    protected $table = 'dashed__pages';

    public $translatable = [
        'name',
        'slug',
        'content',
    ];

    public $casts = [
        'content' => 'array',
        'site_ids' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public $with = [
        'parent',
    ];

    protected static function booted()
    {
        static::created(function ($page) {
            Cache::tags(['pages', "page-$page->id"])->flush();
        });

        static::updated(function ($page) {
            Cache::tags(['pages', "page-$page->id"])->flush();
        });

        static::deleted(function ($page) {
            Cache::tags(['pages', "page-$page->id"])->flush();
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    public function scopeIsHome($query)
    {
        $query->where('is_home', 1);
    }

    public function scopeIsNotHome($query)
    {
        $query->where('is_home', 0);
    }

    public static function resolveRoute($parameters = [])
    {
        $slug = $parameters['slug'] ?? '';
        if ($slug) {
            $slugParts = explode('/', $slug);
            $parentPageId = null;
            foreach ($slugParts as $slugPart) {
                $page = Page::publicShowable()->isNotHome()->where('slug->' . app()->getLocale(), $slugPart)->where('parent_id', $parentPageId)->first();
                $parentPageId = $page?->id;
                if (! $page) {
                    return;
                }
            }
        } else {
            $page = Page::publicShowable()->isHome()->first();
        }

        if ($page) {
            if (View::exists('dashed.pages.show')) {
                seo()->metaData('metaTitle', $page->metadata && $page->metadata->title ? $page->metadata->title : $page->name);
                seo()->metaData('metaDescription', $page->metadata->description ?? '');
                if ($page->metadata && $page->metadata->image) {
                    seo()->metaData('metaImage', $page->metadata->image);
                }

                $correctLocale = app()->getLocale();
                $alternateUrls = [];
                foreach (Sites::getLocales() as $locale) {
                    if ($locale['id'] != $correctLocale) {
                        LaravelLocalization::setLocale($locale['id']);
                        app()->setLocale($locale['id']);
                        $alternateUrls[$locale['id']] = $page->getUrl();
                    }
                }
                LaravelLocalization::setLocale($correctLocale);
                app()->setLocale($correctLocale);
                seo()->metaData('alternateUrls', $alternateUrls);

                View::share('page', $page);
                View::share('breadcrumbs', $page->breadcrumbs());

                return view('dashed.pages.show');
            } else {
                return 'pageNotFound';
            }
        }
    }
}
