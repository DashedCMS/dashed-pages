<?php

namespace Dashed\DashedPages\Models;

use Dashed\DashedCore\Classes\Sites;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dashed\DashedCore\Models\Concerns\IsVisitable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Dashed\DashedCore\Models\Concerns\HasCustomBlocks;
use Dashed\LaravelLocalization\Facades\LaravelLocalization;

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

    /**
     * Let op: deze kan extra memory kosten, maar is vaak nuttig voor breadcrumbs.
     * Als je ooit écht moet knijpen, kun je deze eventueel uitzetten en parent
     * handmatig eager loaden in specifieke queries.
     */
    public $with = [
        'parent',
    ];

    /**
     * Runtime cache voor resolveRoute resultaten per request.
     *
     * [
     *   'siteId|locale|slug' => Page|null|'pageNotFound'
     * ]
     */
    protected static array $resolvedRouteCache = [];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    public function scopeIsHome($query)
    {
        return $query->where('is_home', 1);
    }

    public function scopeIsNotHome($query)
    {
        return $query->where('is_home', 0);
    }

    /**
     * Lost een frontend route op naar een Page, of geeft:
     * - view
     * - 'pageNotFound'
     * - null
     */
    public static function resolveRoute($parameters = [])
    {
        $slug = $parameters['slug'] ?? '';
        $siteId = Sites::getActive();
        $locale = app()->getLocale();

        $cacheKey = static::routeCacheKey($siteId, $locale, $slug);

        // Runtime cache per request – geen dubbele queries/work als resolveRoute 2x wordt aangeroepen.
        if (array_key_exists($cacheKey, static::$resolvedRouteCache)) {
            $page = static::$resolvedRouteCache[$cacheKey];

            if (! $page instanceof self) {
                // pageNotFound of null – geef het direct terug
                return $page;
            }

            return static::buildPageResponse($page);
        }

        // 1) Pagina ophalen
        if ($slug) {
            $page = static::findBySlugPath($slug);
        } else {
            $page = static::publicShowable()->isHome()->first();
        }

        // Geen page? Cache en klaar.
        if (! $page) {
            static::$resolvedRouteCache[$cacheKey] = null;

            return null;
        }

        static::$resolvedRouteCache[$cacheKey] = $page;

        // 2) Bouw SEO/meta + view response
        return static::buildPageResponse($page);
    }

    /**
     * Vind een page op basis van slug-pad (bijv. a/b/c),
     * door per niveau met parent_id te zoeken.
     */
    protected static function findBySlugPath(string $slug): ?self
    {
        $slugParts = explode('/', trim($slug, '/'));
        $parentPageId = null;
        $page = null;

        foreach ($slugParts as $slugPart) {
            $page = static::publicShowable()
                ->isNotHome()
                ->slug($slugPart)
                ->where('parent_id', $parentPageId)
                ->first();

            if (! $page) {
                return null;
            }

            $parentPageId = $page->id;
        }

        return $page;
    }

    /**
     * Bouwt de volledige response voor een gevonden page:
     * - SEO meta title/description/image
     * - alternate URLs
     * - shared view-data
     * - of 'pageNotFound'
     */
    protected static function buildPageResponse(self $page)
    {
        if (! View::exists(config('dashed-core.site_theme', 'dashed') . '.pages.show')) {
            // Hou gedrag gelijk aan je originele implementatie
            return 'pageNotFound';
        }

        // SEO-basics
        $metaTitle = $page->metadata && $page->metadata->title
            ? $page->metadata->title
            : $page->name;

        seo()->metaData('metaTitle', $metaTitle);
        seo()->metaData('metaDescription', $page->metadata->description ?? '');

        if ($page->metadata && $page->metadata->image) {
            seo()->metaData('metaImage', $page->metadata->image);
        }

        // Alternate URLs – geef dit eventueel een cache als het zwaar voelt.
        $alternateUrls = static::buildAlternateUrls($page);
        seo()->metaData('alternateUrls', $alternateUrls);

        // Shared view data
        View::share('page', $page);
        View::share('model', $page);
        View::share('breadcrumbs', $page->breadcrumbs());

        return view(config('dashed-core.site_theme', 'dashed') . '.pages.show');
    }

    /**
     * Bouwt alternate URLs voor alle locales.
     *
     * Dit kun je, als nodig, ook nog in Cache::remember() wikkelen op basis van page-id + site-id.
     */
    protected static function buildAlternateUrls(self $page): array
    {
        $correctLocale = app()->getLocale();
        $alternateUrls = [];

        // Als deze call duur is, kun je hier een Cache::remember omheen zetten:
        // Cache::remember("page_alt_urls_{$page->id}_{$siteId}", 3600, fn () => ...);
        foreach (Sites::getLocales() as $locale) {
            $localeId = $locale['id'] ?? null;

            if (! $localeId || $localeId === $correctLocale) {
                continue;
            }

            // LaravelLocalization heeft meestal een helper om de juiste URL voor een andere locale te krijgen
            // zonder global state te moeten switchen, maar omdat jouw code dat nu al doet,
            // houden we dat gedrag in stand, alleen netjes ingepakt.
            LaravelLocalization::setLocale($localeId);
            app()->setLocale($localeId);

            $alternateUrls[$localeId] = $page->getUrl();
        }

        // Locale terugzetten
        LaravelLocalization::setLocale($correctLocale);
        app()->setLocale($correctLocale);

        return $alternateUrls;
    }

    protected static function routeCacheKey(string $siteId, string $locale, string $slug): string
    {
        return $siteId . '|' . $locale . '|' . $slug;
    }
}
