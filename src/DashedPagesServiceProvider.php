<?php

namespace Dashed\DashedPages;

use Dashed\DashedPages\Models\Page;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class DashedPagesServiceProvider extends PackageServiceProvider
{
    public static string $name = 'dashed-pages';

    public function bootingPackage(): void
    {
        Gate::policy(\Dashed\DashedPages\Models\Page::class, \Dashed\DashedPages\Policies\PagePolicy::class);

        cms()->registerRolePermissions('Pagina\'s', [
            'view_page' => 'Pagina\'s bekijken',
            'edit_page' => 'Pagina\'s bewerken',
            'delete_page' => 'Pagina\'s verwijderen',
        ]);

        cms()->registerResourceDocs(
            resource: \Dashed\DashedPages\Filament\Resources\PageResource::class,
            title: 'Pagina\'s',
            intro: 'Dit is de page builder van het CMS waarmee je alle pagina\'s van de website opbouwt. Je zet pagina\'s in elkaar met flexibele content blokken, regelt de SEO instellingen en bepaalt wanneer een pagina live gaat.',
            sections: [
                [
                    'heading' => 'Wat kun je hier doen?',
                    'body' => <<<MARKDOWN
- Nieuwe pagina\'s aanmaken voor bijvoorbeeld een over ons, landingspagina of contact.
- Bestaande pagina\'s bewerken en hun volgorde of status aanpassen.
- Per taal een eigen versie van de inhoud bijhouden.
- Kiezen welke pagina dient als de homepage van de website.
MARKDOWN,
                ],
                [
                    'heading' => 'Content blokken gebruiken',
                    'body' => 'Elke pagina bouw je op met content blokken die je naar wens onder elkaar zet. Je kunt blokken slepen om ze in een andere volgorde te zetten, dupliceren en verwijderen. Per blok vul je de inhoud in zoals tekst, afbeeldingen of een call to action. Zo bouw je zonder code een pagina op die precies doet wat je wilt.',
                ],
                [
                    'heading' => 'SEO en publicatie',
                    'body' => 'Onder het tabblad SEO geef je per pagina een pagina titel en omschrijving op zoals ze in zoekmachines getoond worden. Je kunt ook een publicatiedatum instellen als een pagina pas op een later moment online mag komen, of juist een einddatum voor tijdelijke pagina\'s. Heb je meerdere talen ingesteld? Dan vul je per taal een eigen titel, inhoud en SEO gegevens in.',
                ],
            ],
            tips: [
                'Geef elke pagina een korte en duidelijke URL, dat werkt beter voor bezoekers en zoekmachines.',
                'Vul altijd de SEO titel en omschrijving in, dat maakt een groot verschil in zoekresultaten.',
                'Gebruik de publicatiedatum om een pagina alvast voor te bereiden en automatisch live te zetten.',
                'Controleer een nieuwe pagina via de voorbeeldweergave voor je hem publiceert.',
            ],
        );
    }

    public function configurePackage(Package $package): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->publishes([
            __DIR__ . '/../resources/templates' => resource_path('views/' . config('dashed-core.site_theme', 'dashed')),
        ], 'dashed-templates');

        cms()->registerRouteModel(Page::class, 'Pagina', 'Pagina\'s');

        $package
            ->name('dashed-pages');

        cms()->builder('plugins', [
            new DashedPagesPlugin(),
        ]);
    }
}
