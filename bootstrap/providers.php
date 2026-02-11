<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    ...app()->environment("local")
        ? [App\Providers\TelescopeServiceProvider::class]
        : [],
];
