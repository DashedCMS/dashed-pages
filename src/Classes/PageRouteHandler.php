<?php

namespace Qubiqx\QcommercePages\Classes;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Qubiqx\QcommerceCore\Classes\Locales;
use Qubiqx\QcommerceCore\Classes\Sites;
use Qubiqx\QcommercePages\Models\Page;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class PageRouteHandler
{
    public static function handle($parameters = [])
    {
        $slug = $parameters['slug'] ?? '';
        if ($slug) {
            $slugParts = explode('/', $slug);
            $parentPageId = null;
            foreach ($slugParts as $slugPart) {
                $page = Page::publicShowable()->isNotHome()->where('slug->' . App::getLocale(), $slugPart)->where('parent_page_id', $parentPageId)->first();
                $parentPageId = $page?->id;
                if (! $page) {
                    return;
                }
            }
        } else {
            $page = Page::publicShowable()->isHome()->first();
        }

        if ($page) {
            if (View::exists('qcommerce.pages.show')) {
                seo()->metaData('metaTitle', $page->metadata && $page->metadata->title ? $page->metadata->title : $page->name);
                seo()->metaData('metaDescription', $page->metadata->description ?? '');
                if ($page->metadata && $page->metadata->image) {
                    seo()->metaData('metaImage', $page->metadata->image);
                }

                $correctLocale = App::getLocale();
                $alternateUrls = [];
                foreach (Sites::getLocales() as $locale) {
                    if ($locale['id'] != $correctLocale) {
                        LaravelLocalization::setLocale($locale['id']);
                        App::setLocale($locale['id']);
                        $alternateUrls[$locale['id']] = $page->getUrl();
                    }
                }
                LaravelLocalization::setLocale($correctLocale);
                App::setLocale($correctLocale);
                seo()->metaData('alternateUrls', $alternateUrls);

                View::share('page', $page);
                View::share('breadcrumbs', $page->breadcrumbs());

                return view('qcommerce.pages.show');
            } else {
                return 'pageNotFound';
            }
        }
    }

    public static function getSitemapUrls(Sitemap $sitemap): Sitemap
    {
        foreach (Page::publicShowable()->get() as $page) {
            foreach (Locales::getLocales() as $locale) {
                if (in_array($locale['id'], Sites::get()['locales'])) {
                    Locales::setLocale($locale['id']);
                    $sitemap
                        ->add(Url::create($page->getUrl()));
                }
            }
        }

        return $sitemap;
    }
}
