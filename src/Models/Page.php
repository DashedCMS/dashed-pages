<?php

namespace Qubiqx\QcommercePages\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Qubiqx\QcommerceCore\Classes\Sites;
use Qubiqx\QcommerceCore\Models\Concerns\HasMetadata;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

class Page extends Model
{
    use SoftDeletes;
    use HasTranslations;
    use LogsActivity;
    use HasMetadata;

    protected static $logFillable = true;

    protected $table = 'qcommerce__pages';
    protected $fillable = [
        'name',
        'slug',
        'is_home',
        'parent_page_id',
        'content',
        'site_id',
        'meta_title',
        'meta_description',
        'meta_image',
        'start_date',
        'end_date',
    ];

    public $translatable = [
        'name',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'meta_image',
    ];

    protected $dates = [
        'start_date',
        'end_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public $casts = [
        'content' => 'array',
    ];

    public $with = [
        'parentPage',
    ];

    protected static function booted()
    {
        static::created(function ($page) {
            Cache::tags(['pages', "page-$page->id"])->flush();
        });

        static::updated(function ($page) {
            Cache::tags(['pages', "page-$page->id"])->flush();
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function parentPage(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    public function scopeThisSite($query, $siteId = null)
    {
        if (!$siteId) {
            $siteId = Sites::getActive();
        }

        $query->where('site_id', $siteId);
    }

    public function scopePublicShowable($query)
    {
        $query->thisSite()
            ->where(function ($query) {
                $query->where('start_date', null)
                    ->orWhere('start_date', '<=', now()->format('Y-m-d H:i:s'));
            })->where(function ($query) {
                $query->where('end_date', null)
                    ->orWhere('end_date', '>=', now()->format('Y-m-d H:i:s'));
            });;
    }

    public function scopeSearch($query)
    {
        if (request()->get('search')) {
            $search = strtolower(request()->get('search'));
            $query->where('name', 'LIKE', "%$search%")
                ->orWhere('slug', 'LIKE', "%$search%")
                ->orWhere('content', 'LIKE', "%$search%")
                ->orWhere('start_date', 'LIKE', "%$search%")
                ->orWhere('end_date', 'LIKE', "%$search%")
                ->orWhere('meta_title', 'LIKE', "%$search%")
                ->orWhere('meta_description', 'LIKE', "%$search%")
                ->orWhere('site_id', 'LIKE', "%$search%");
        }
    }

    public function getUrl()
    {
        if ($this->is_home) {
            $url = '/';
        } elseif ($this->parentPage) {
            $url = "{$this->parentPage->getUrl()}/{$this->slug}";
        } else {
            $url = $this->slug;
        }

        return LaravelLocalization::localizeUrl($url);
    }

    public function site()
    {
        foreach (Sites::getSites() as $site) {
            if ($site['id'] == $this->site_id) {
                return $site;
            }
        }
    }

    public function getStatusAttribute()
    {
        if (!$this->start_date && !$this->end_date) {
            return 'active';
        } else {
            if ($this->start_date && $this->end_date) {
                if ($this->start_date <= Carbon::now() && $this->end_date >= Carbon::now()) {
                    return 'active';
                } else {
                    return 'inactive';
                }
            } else {
                if ($this->start_date) {
                    if ($this->start_date <= Carbon::now()) {
                        return 'active';
                    } else {
                        return 'inactive';
                    }
                } else {
                    if ($this->end_date >= Carbon::now()) {
                        return 'active';
                    } else {
                        return 'inactive';
                    }
                }
            }
        }
    }

    public function breadcrumbs()
    {
        $page = $this;

        $homePage = Page::where('is_home', 1)->publicShowable()->first();
        if ($homePage) {
            $breadcrumbs[] = [
                'name' => $homePage->name,
                'url' => $homePage->getUrl(),
            ];
        }

        while ($page->parentPage) {
            if (!$page->parentPage->is_home) {
                $breadcrumbs[] = [
                    'name' => $page->parentPage->name,
                    'url' => $page->parentPage->getUrl(),
                ];
            }
            $page = $page->parentPage;
        }

        $breadcrumbs = [
            [
                'name' => $this->name,
                'url' => $this->getUrl(),
            ],
        ];

        return $breadcrumbs;
    }
}
