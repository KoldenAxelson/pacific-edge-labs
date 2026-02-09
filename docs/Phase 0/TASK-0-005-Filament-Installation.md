# [TASK-0-005] Filament Installation & Basic Configuration

## Overview
Install Filament 3 (Laravel admin panel builder) and configure it to work with the existing authentication system. Filament will be the primary admin interface for managing products, orders, customers, and CoAs.

## Prerequisites
- [x] TASK-0-002 completed (Breeze authentication installed)
- [x] TASK-0-003 completed (Livewire installed)
- [x] Spatie Laravel Permission installed

## Goals
- Install Filament 3 Panel Builder
- Configure Filament to work with existing Breeze authentication
- Set up role-based access control for admin panel
- Create first admin user
- Customize Filament branding for Pacific Edge Labs
- Install Filament Shield for advanced permissions

## Step-by-Step Instructions

### 1. Install Filament Panel Builder

```bash
sail composer require filament/filament:"^3.2" -W
```

### 2. Install Filament Panel

```bash
sail artisan filament:install --panels
```

**When prompted:**
- Would you like to create a new panel? → **yes**
- What is the ID of the panel? → **admin**
- What is the path for the panel? → **/admin** (press Enter for default)

### 3. Run Filament Migrations

```bash
sail artisan migrate
```

This creates the `personal_access_tokens` table if not already created.

### 4. Create Admin User

```bash
sail artisan make:filament-user
```

**Enter:**
- Name: **Admin User**
- Email: **admin@pacificedgelabs.test**
- Password: **password** (or your preferred password)

### 5. Assign Super Admin Role to Admin User

```bash
sail artisan tinker
```

In Tinker:
```php
$admin = \App\Models\User::where('email', 'admin@pacificedgelabs.test')->first();
$admin->assignRole('super-admin');
exit
```

### 6. Test Filament Access

Visit: http://localhost/admin

Login with:
- Email: admin@pacificedgelabs.test
- Password: password

You should see the Filament dashboard.

### 7. Install Filament Shield (Role/Permission Management)

```bash
sail composer require bezhansalleh/filament-shield
```

### 8. Publish Shield Configuration

```bash
sail artisan vendor:publish --tag=filament-shield-config
```

### 9. Setup Shield

```bash
sail artisan shield:install --fresh
```

This will:
- Create super-admin role if it doesn't exist
- Generate permissions for all existing resources
- Setup shield configuration

**When prompted:**
- Would you like to generate permissions for existing resources? → **yes**

### 10. Configure Filament Panel for Authentication

Edit `app/Providers/Filament/AdminPanelProvider.php`:

```php
<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->brandName('Pacific Edge Labs')
            ->favicon(asset('favicon.ico'))
            ->viteTheme('resources/css/filament/admin/theme.css');
    }
}
```

### 11. Create Custom Filament Theme

```bash
sail artisan make:filament-theme admin
```

This creates `resources/css/filament/admin/theme.css`.

Edit `resources/css/filament/admin/theme.css`:

```css
@import '/vendor/filament/filament/resources/css/theme.css';

@config 'tailwind.config.js';

/* Pacific Edge Labs custom Filament styles */
:root {
    --primary: 59 130 246; /* pel-blue-500 */
    --success: 34 197 94; /* green-500 */
    --warning: 251 191 36; /* amber-400 */
    --danger: 239 68 68; /* red-500 */
}

/* Custom admin panel styling */
.fi-sidebar {
    /* Darker sidebar for premium look */
    background: linear-gradient(180deg, #1e3a8a 0%, #1e40af 100%);
}

.fi-sidebar-nav-item-label {
    font-weight: 500;
}
```

### 12. Register Theme in Vite

Edit `vite.config.js`:

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/filament/admin/theme.css',
            ],
            refresh: [
                'app/Livewire/**',
                'app/Filament/**',
                'resources/views/**',
            ],
        }),
    ],
    server: {
        hmr: {
            host: 'localhost',
        },
    },
});
```

### 13. Compile Filament Theme

```bash
sail npm run build
```

Then restart dev server:
```bash
sail npm run dev
```

### 14. Configure Shield Access Policy

Create `app/Policies/ShieldPolicy.php`:

```bash
sail artisan make:policy ShieldPolicy --model=User
```

Edit `app/Policies/ShieldPolicy.php`:

```php
<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ShieldPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['super-admin', 'admin']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user): bool
    {
        return $user->hasRole(['super-admin', 'admin']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('super-admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return $user->hasRole('super-admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return $user->hasRole('super-admin');
    }
}
```

### 15. Create First Test Resource

Let's create a simple "Users" resource to verify Filament is working:

```bash
sail artisan make:filament-resource User --generate
```

This auto-generates:
- `app/Filament/Resources/UserResource.php`
- `app/Filament/Resources/UserResource/Pages/ListUsers.php`
- `app/Filament/Resources/UserResource/Pages/CreateUser.php`
- `app/Filament/Resources/UserResource/Pages/EditUser.php`

### 16. Customize User Resource

Edit `app/Filament/Resources/UserResource.php`:

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\DateTimePicker::make('email_verified_at'),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->maxLength(255),
                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->colors([
                        'danger' => 'super-admin',
                        'warning' => 'admin',
                        'success' => 'customer',
                    ]),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole(['super-admin', 'admin']);
    }
}
```

### 17. Test User Resource

Visit: http://localhost/admin/users

You should see:
- List of all users
- Ability to create new users
- Ability to edit/delete users
- Role badges displayed

### 18. Configure Navigation Groups

Edit `app/Providers/Filament/AdminPanelProvider.php` and add:

```php
->navigationGroups([
    'User Management',
    'Products & Inventory',
    'Orders & Sales',
    'Compliance',
    'Settings',
])
```

### 19. Create Custom Dashboard Widget

```bash
sail artisan make:filament-widget StatsOverview --stats-overview
```

Edit `app/Filament/Widgets/StatsOverview.php`:

```php
<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->description('All registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
            Stat::make('Total Products', 0)
                ->description('Coming in Phase 2')
                ->descriptionIcon('heroicon-m-cube')
                ->color('warning'),
            Stat::make('Total Orders', 0)
                ->description('Coming in Phase 4')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('info'),
        ];
    }
}
```

Register widget in `app/Providers/Filament/AdminPanelProvider.php`:

```php
->widgets([
    \App\Filament\Widgets\StatsOverview::class,
    Widgets\AccountWidget::class,
])
```

### 20. Test Dashboard

Visit: http://localhost/admin

You should see:
- Stats overview widget with user count
- Account widget
- Navigation sidebar with groups

### 21. Commit Changes

```bash
git add .
git commit -m "Install and configure Filament 3 admin panel with Shield"
git push
```

## Validation Checklist

- [ ] Can access http://localhost/admin
- [ ] Can login with admin@pacificedgelabs.test
- [ ] Dashboard displays stats overview widget
- [ ] User resource shows all users
- [ ] Can create new user through Filament
- [ ] Can edit existing user
- [ ] Can assign roles to users
- [ ] Role badges display correctly
- [ ] Navigation groups appear in sidebar
- [ ] Custom theme is applied (blue branding)
- [ ] Shield installed and permissions working
- [ ] Super-admin can access all features
- [ ] Regular users cannot access admin panel

## Common Issues & Solutions

### Issue: "Class 'Filament\Panel' not found"
**Solution:**
```bash
sail composer dump-autoload
sail artisan config:clear
```

### Issue: Admin user cannot login
**Solution:**
Verify user has proper role:
```bash
sail artisan tinker
$user = User::where('email', 'admin@pacificedgelabs.test')->first();
$user->assignRole('super-admin');
exit
```

### Issue: Custom theme not loading
**Solution:**
Rebuild assets:
```bash
sail npm run build
sail npm run dev
```

### Issue: "Target class [AdminPanelProvider] does not exist"
**Solution:**
Register in `config/app.php`:
```php
'providers' => [
    // ...
    App\Providers\Filament\AdminPanelProvider::class,
],
```

### Issue: Shield permissions not working
**Solution:**
Re-run setup:
```bash
sail artisan shield:install --fresh
```

## Filament Best Practices

### Resource Organization
- Group related resources using `$navigationGroup`
- Use icons from [Heroicons](https://heroicons.com/)
- Set `$navigationSort` for custom ordering

### Form Building
- Use `->required()` for required fields
- Use `->unique()` for fields that must be unique
- Use `->relationship()` for foreign keys
- Use `->preload()` for small datasets
- Use `->searchable()` for large datasets

### Table Optimization
- Use `->toggleable()` for less important columns
- Use `->searchable()` and `->sortable()` appropriately
- Add filters for common queries
- Use badges for status columns

### Security
- Always implement `canViewAny()`, `canCreate()`, etc.
- Use Spatie Permissions for role-based access
- Never expose sensitive data in tables without proper gates

## Next Steps

Once all validation items pass:
- ✅ Mark TASK-0-005 as complete
- ➡️ Proceed to TASK-0-006 (Email Abstraction Layer)

## Time Estimate
**45-60 minutes**

## Success Criteria
- Filament 3 installed and configured
- Admin panel accessible at /admin
- User resource fully functional
- Role-based access control working via Shield
- Custom branding applied
- Dashboard widgets displaying
- All changes committed to GitHub

---
**Phase:** 0 (Environment & Foundation)  
**Dependencies:** TASK-0-002, TASK-0-003  
**Blocks:** TASK-0-009 (seeders will create Filament resources)  
**Priority:** High
