<?php

namespace Qubiqx\QcommercePages\Classes;

use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Qubiqx\QcommercePages\Models\Page;

class PageRouteHandler
{
    public static function handle($parameters = [])
    {
        $slug = $parameters['slug'] ?? '';
        if ($slug) {
            $slugParts = explode('/', $slug);
            $parentPageId = null;
            foreach ($slugParts as $slugPart) {
                $page = Page::publicShowable()->where('slug->' . App::getLocale(), $slugPart)->where('parent_page_id', $parentPageId)->where('is_home', 0)->first();
                $parentPageId = $page?->id;
            }
        } else {
            $page = Page::publicShowable()->where('is_home', 1)->first();
        }

        if ($page) {
            if (View::exists('qcommerce.pages.show')) {
                seo()->metaData('metaTitle', $page->meta_title ?: $page->name);
                seo()->metaData('metaDescription', $page->meta_description);
                if ($page->meta_image) {
                    seo()->metaData('metaImage', $page->meta_image);
                }

                View::share('page', $page);

                return view('qcommerce.pages.show');
            } else {
                return 'pageNotFound';
            }
        }
    }
}
