<?php

namespace Qubiqx\QcommercePages\Classes;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Qubiqx\QcommercePages\Models\Page;
use Artesaos\SEOTools\Facades\SEOTools;

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
                SEOTools::setTitle($page->meta_title ?: $page->name);
                SEOTools::setDescription($page->meta_description);
                SEOTools::opengraph()->setUrl(url()->current());
                if ($page->meta_image) {
                    SEOTools::addImages($page->meta_image);
                }

                View::share('page', $page);

                return view('qcommerce.pages.show');
            } else {
                return 'pageNotFound';
            }
        }
    }
}
