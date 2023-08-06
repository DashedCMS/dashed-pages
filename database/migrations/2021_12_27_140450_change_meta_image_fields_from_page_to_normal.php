<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeMetaImageFieldsFromPageToNormal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dashed__pages', function (Blueprint $table) {
            $table->json('meta_image')->after('meta_description')->nullable();
        });

        foreach (\Dashed\DashedPages\Models\Page::get() as $page) {
            foreach (\Dashed\DashedCore\Classes\Locales::getLocales() as $locale) {
                $media = \Illuminate\Support\Facades\DB::table('media')
                    ->where('model_type', 'Dashed\Dashed\Models\Page')
                    ->where('model_id', $page->id)
                    ->where('collection_name', 'meta-image-' . $locale['id'])
                    ->first();

                if ($media) {
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists("/dashed/uploads/$media->id/$media->file_name")) {
                        try {
                            \Illuminate\Support\Facades\Storage::disk('public')->copy("/dashed/uploads/$media->id/$media->file_name", "/dashed/pages/meta-images/$media->file_name");
                        } catch (Exception $exception) {
                        }
                        $page->setTranslation('meta_image', $locale['id'], "/dashed/pages/meta-images/$media->file_name");
                        $page->save();
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('page_to_normal', function (Blueprint $table) {
            //
        });
    }
}
