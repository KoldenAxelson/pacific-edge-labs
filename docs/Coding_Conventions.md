# Coding Conventions - Pacific Edge Labs

> **Principles-based coding standards to maintain consistency and avoid common AI pitfalls**

This document establishes coding conventions for the Pacific Edge Labs e-commerce platform. These standards are designed to produce maintainable, secure, and performant code while avoiding common mistakes that AI coding assistants tend to make.

## 🎯 Core Principles

### 1. Simplicity Over Cleverness
**DO:** Write straightforward, readable code
**DON'T:** Over-engineer solutions or prematurely optimize

```php
// ✅ GOOD - Clear and simple
public function getActiveProducts()
{
    return Product::where('active', true)->get();
}

// ❌ BAD - Over-engineered
public function getActiveProducts()
{
    return $this->productRepository
        ->withCriteria(new ActiveCriteria())
        ->withScope(new PublishedScope())
        ->get();
}
```

### 2. Convention Over Configuration
**DO:** Follow Laravel conventions
**DON'T:** Fight the framework or create unnecessary abstractions

```php
// ✅ GOOD - Laravel convention
class OrderController extends Controller
{
    public function store(Request $request)
    {
        // Store order
    }
}

// ❌ BAD - Fighting conventions
class OrderStorer extends AbstractStorer implements StorerInterface
{
    public function execute(array $data): array
    {
        // Over-abstracted
    }
}
```

### 3. Explicit Over Implicit
**DO:** Be clear about what code does
**DON'T:** Rely on magic or hidden behavior

```php
// ✅ GOOD - Explicit relationship
public function batches()
{
    return $this->hasMany(Batch::class);
}

// ❌ BAD - Magic methods
public function __call($method, $args)
{
    // Hidden dynamic behavior
}
```

## 📁 File Organization

### Directory Structure
```
app/
├── Contracts/           # Interfaces only (PaymentGatewayInterface)
├── Filament/           # Filament admin resources
├── Http/
│   ├── Controllers/    # Keep thin, delegate to services
│   ├── Middleware/     # Custom middleware
│   └── Requests/       # Form request validation
├── Livewire/           # Livewire components
├── Models/             # Eloquent models
├── Services/           # Business logic (PaymentService, BatchService)
└── View/Components/    # Blade components
```

### Naming Conventions

**Models:** Singular, PascalCase
```php
Product.php, Batch.php, Order.php, ComplianceLog.php
```

**Controllers:** Singular resource name + "Controller"
```php
ProductController.php, OrderController.php, CheckoutController.php
```

**Livewire Components:** Descriptive, PascalCase
```php
ShoppingCart.php, ProductFilter.php, AgeVerificationGate.php
```

**Services:** Descriptive + "Service"
```php
PaymentService.php, BatchAllocationService.php, ComplianceLogger.php
```

**Blade Views:** kebab-case
```
products/index.blade.php
checkout/shipping-info.blade.php
components/product-card.blade.php
```

## 🏗 Laravel-Specific Conventions

### Models

**✅ DO:**
- Use Eloquent relationships instead of manual joins
- Define `$fillable` or `$guarded`
- Cast attributes appropriately
- Keep models focused on data and relationships
- Add helpful scopes for common queries

**❌ DON'T:**
- Put business logic in models (use services)
- Use `$guarded = []` (too permissive)
- Forget to define casts for dates, JSON, booleans

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    // ✅ Explicit fillable fields
    protected $fillable = [
        'sku',
        'name',
        'description',
        'price',
        'category_id',
        'active',
    ];

    // ✅ Proper casting
    protected $casts = [
        'price' => 'decimal:2',
        'active' => 'boolean',
    ];

    // ✅ Clear relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }

    // ✅ Helpful scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    // ✅ Accessor for computed properties
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->price, 2);
    }
}
```

### Controllers

**✅ DO:**
- Keep controllers thin (delegate to services)
- Use form requests for validation
- Return views or JSON, not mixed responses
- Follow RESTful conventions

**❌ DON'T:**
- Put business logic in controllers
- Validate in controller methods (use FormRequests)
- Return different response types from same method

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

    // ✅ GOOD - Thin controller
    public function store(StoreOrderRequest $request)
    {
        $order = $this->orderService->createOrder(
            $request->user(),
            $request->validated()
        );

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Order placed successfully!');
    }

    // ❌ BAD - Fat controller
    public function storeBad(Request $request)
    {
        // Validation in controller
        $validated = $request->validate([...]);
        
        // Business logic in controller
        DB::transaction(function () use ($validated) {
            $order = Order::create([...]);
            
            foreach ($validated['items'] as $item) {
                $order->items()->create([...]);
                
                $batch = Batch::find($item['batch_id']);
                $batch->decrement('quantity', $item['quantity']);
            }
            
            // Email sending in controller
            Mail::to($order->user)->send(new OrderConfirmation($order));
        });
        
        return redirect()->route('orders.show', $order);
    }
}
```

### Services

**✅ DO:**
- Use services for business logic
- Inject dependencies via constructor
- Use database transactions for multi-step operations
- Throw exceptions for error cases
- Return domain objects (models, collections)

**❌ DON'T:**
- Return arrays when you should return models
- Suppress exceptions silently
- Mix concerns (keep single responsibility)

```php
<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;
use App\Exceptions\InsufficientInventoryException;

class OrderService
{
    public function __construct(
        private BatchAllocationService $batchService,
        private ComplianceLogger $complianceLogger
    ) {}

    public function createOrder(User $user, array $data): Order
    {
        return DB::transaction(function () use ($user, $data) {
            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'total' => $data['total'],
                'status' => 'pending',
            ]);

            // Process cart items
            foreach ($data['items'] as $item) {
                // Allocate batch inventory
                $allocation = $this->batchService->allocate(
                    $item['batch_id'],
                    $item['quantity']
                );

                if (!$allocation) {
                    throw new InsufficientInventoryException(
                        "Batch {$item['batch_id']} has insufficient inventory"
                    );
                }

                // Create order item
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'batch_id' => $item['batch_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            // Log compliance attestations
            $this->complianceLogger->logOrderCompliance($order, $data['attestations']);

            return $order;
        });
    }
}
```

### Migrations

**✅ DO:**
- Use descriptive migration names
- Add indexes for foreign keys and frequently queried columns
- Use appropriate column types
- Add comments for complex fields
- Use `up()` and `down()` methods properly

**❌ DON'T:**
- Use `$table->timestamps()` without thinking (some tables don't need it)
- Forget to add indexes
- Use `string()` when `text()` is more appropriate

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('product_id')
                ->constrained()
                ->onDelete('cascade');
            
            // Batch identification
            $table->string('batch_number', 50)->unique();
            
            // Inventory
            $table->integer('quantity_available')->default(0);
            $table->integer('quantity_allocated')->default(0);
            
            // Quality data
            $table->decimal('purity_percentage', 5, 2);
            $table->date('test_date');
            $table->date('expiration_date')->nullable();
            
            // CoA storage
            $table->string('coa_path')->comment('S3 path to PDF');
            
            // Status
            $table->boolean('active')->default(true);
            
            $table->timestamps();
            
            // Indexes for queries
            $table->index(['product_id', 'active']);
            $table->index('expiration_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
```

## 🎨 Frontend Conventions (TALL Stack)

### Blade Templates

**✅ DO:**
- Use Blade components for reusable UI
- Use `@props()` for component props
- Use slots for flexible content
- Keep templates focused and simple

**❌ DON'T:**
- Put logic in templates (use controllers/Livewire)
- Create deeply nested component hierarchies
- Mix inline styles (use Tailwind classes)

```blade
{{-- ✅ GOOD - Clean component --}}
@props([
    'product',
    'showBadge' => true
])

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
    
    <div class="p-4">
        <h3 class="text-lg font-semibold text-gray-900">
            {{ $product->name }}
        </h3>
        
        @if($showBadge && $product->hasLowStock())
            <x-badge color="yellow">Low Stock</x-badge>
        @endif
        
        <p class="mt-2 text-xl font-bold text-pel-blue-600">
            {{ $product->formatted_price }}
        </p>
        
        {{ $slot }}
    </div>
</div>

{{-- ❌ BAD - Logic in template --}}
<div>
    @php
        $price = $product->price;
        $discount = $product->discount_percentage;
        $finalPrice = $price - ($price * $discount / 100);
        $formattedPrice = '$' . number_format($finalPrice, 2);
    @endphp
    
    <p>{{ $formattedPrice }}</p>
</div>
```

### Livewire Components

**✅ DO:**
- Use Livewire for interactive components with server state
- Use `wire:model.live` for real-time updates
- Use `wire:loading` for better UX
- Keep components focused (single responsibility)

**❌ DON'T:**
- Use Livewire for static content (use Blade)
- Overuse Livewire when Alpine.js would suffice
- Create giant "god components"

```php
<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cart;

class ShoppingCart extends Component
{
    public $cart;
    
    public function mount()
    {
        $this->cart = auth()->user()?->cart ?? Cart::forSession();
    }

    public function updateQuantity($itemId, $quantity)
    {
        $this->cart->updateItem($itemId, $quantity);
        $this->dispatch('cart-updated');
    }

    public function removeItem($itemId)
    {
        $this->cart->removeItem($itemId);
        $this->dispatch('cart-updated');
    }

    public function render()
    {
        return view('livewire.shopping-cart');
    }
}
```

```blade
<div>
    @foreach($cart->items as $item)
        <div class="flex items-center justify-between p-4 border-b">
            <div>
                <h4 class="font-semibold">{{ $item->product->name }}</h4>
                <p class="text-sm text-gray-600">Batch: {{ $item->batch->batch_number }}</p>
            </div>
            
            <div class="flex items-center gap-4">
                <input 
                    type="number" 
                    wire:model.live="quantity.{{ $item->id }}"
                    wire:change="updateQuantity({{ $item->id }}, $event.target.value)"
                    min="1"
                    max="{{ $item->batch->quantity_available }}"
                    class="w-20 px-2 py-1 border rounded"
                >
                
                <button 
                    wire:click="removeItem({{ $item->id }})"
                    wire:loading.attr="disabled"
                    class="text-red-600 hover:text-red-800"
                >
                    <span wire:loading.remove>Remove</span>
                    <span wire:loading>Removing...</span>
                </button>
            </div>
        </div>
    @endforeach
</div>
```

### Alpine.js

**✅ DO:**
- Use Alpine for client-side interactions (no server state)
- Keep Alpine components simple
- Use `x-data` at the component root

**❌ DON'T:**
- Use Alpine when server state is needed (use Livewire)
- Create complex Alpine logic (move to Livewire if complex)

```blade
{{-- ✅ GOOD - Simple toggle --}}
<div x-data="{ open: false }">
    <button @click="open = !open" class="btn-primary">
        Show Details
    </button>
    
    <div x-show="open" x-transition class="mt-4">
        <p>Product details here...</p>
    </div>
</div>

{{-- ❌ BAD - Complex Alpine logic --}}
<div x-data="{
    items: [],
    total: 0,
    addItem(item) {
        this.items.push(item);
        this.calculateTotal();
    },
    calculateTotal() {
        // Complex calculations
    }
}">
    {{-- This should be Livewire --}}
</div>
```

### Tailwind CSS

**✅ DO:**
- Use Tailwind utility classes
- Extract common patterns to Blade components
- Use `@apply` sparingly (only for custom utilities)
- Follow mobile-first responsive design

**❌ DON'T:**
- Write custom CSS unless absolutely necessary
- Use inline styles
- Over-use `@apply` (defeats the purpose of Tailwind)

```blade
{{-- ✅ GOOD - Utility classes --}}
<button class="px-4 py-2 bg-pel-blue-500 text-white rounded-lg hover:bg-pel-blue-600 transition duration-150">
    Add to Cart
</button>

{{-- ✅ GOOD - Component for reuse --}}
<x-button color="primary">Add to Cart</x-button>

{{-- ❌ BAD - Inline styles --}}
<button style="background-color: #3b82f6; color: white; padding: 8px 16px;">
    Add to Cart
</button>
```

## 🔒 Security Conventions

### Input Validation

**✅ DO:**
- Always validate user input
- Use Form Requests for complex validation
- Whitelist allowed values (don't blacklist)
- Validate on both client and server

**❌ DON'T:**
- Trust user input
- Only validate on the client side
- Use weak validation rules

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasVerifiedAge();
    }

    public function rules(): array
    {
        return [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.batch_id' => 'required|exists:batches,id',
            'items.*.quantity' => 'required|integer|min:1|max:100',
            
            'shipping_address' => 'required|string|max:255',
            'shipping_city' => 'required|string|max:100',
            'shipping_state' => 'required|string|size:2',
            'shipping_zip' => 'required|regex:/^\d{5}(-\d{4})?$/',
            'shipping_country' => 'required|string|in:US,CA',
            
            'attestations.age_verified' => 'required|accepted',
            'attestations.researcher' => 'required|accepted',
            'attestations.research_only' => 'required|accepted',
        ];
    }

    public function messages(): array
    {
        return [
            'attestations.*.accepted' => 'You must confirm all compliance requirements to proceed.',
        ];
    }
}
```

### SQL Injection Prevention

**✅ DO:**
- Use Eloquent ORM
- Use query builder with parameter binding
- Use `whereIn()` with arrays, not string concatenation

**❌ DON'T:**
- Use raw queries with user input
- Concatenate SQL strings

```php
// ✅ GOOD - Eloquent with parameter binding
$products = Product::where('category_id', $categoryId)
    ->where('active', true)
    ->get();

// ✅ GOOD - Query builder with binding
$products = DB::table('products')
    ->where('category_id', '=', $categoryId)
    ->get();

// ❌ BAD - Raw query with concatenation (SQL injection risk)
$products = DB::select("SELECT * FROM products WHERE category_id = $categoryId");
```

### XSS Prevention

**✅ DO:**
- Let Blade escape output automatically (`{{ }}`)
- Use `{!! !!}` only for trusted HTML (rare)
- Sanitize rich text with HTMLPurifier if needed

**❌ DON'T:**
- Use `{!! !!}` for user input
- Disable Blade escaping

```blade
{{-- ✅ GOOD - Automatic escaping --}}
<h1>{{ $product->name }}</h1>

{{-- ⚠️ DANGEROUS - Only for trusted HTML --}}
<div>{!! $trustedAdminContent !!}</div>

{{-- ❌ BAD - Unescaped user input --}}
<div>{!! $userComment !!}</div>
```

### CSRF Protection

**✅ DO:**
- Use `@csrf` in all forms
- Keep CSRF protection enabled (Laravel default)

**❌ DON'T:**
- Disable CSRF protection
- Forget `@csrf` in forms

```blade
{{-- ✅ GOOD --}}
<form method="POST" action="{{ route('orders.store') }}">
    @csrf
    <!-- form fields -->
</form>

{{-- ❌ BAD - Missing CSRF --}}
<form method="POST" action="{{ route('orders.store') }}">
    <!-- form fields -->
</form>
```

## 🧪 Testing Conventions

### Test Structure

**✅ DO:**
- Write tests as you build features
- Use descriptive test names
- Follow Arrange-Act-Assert pattern
- One assertion per test (when possible)

**❌ DON'T:**
- Write all tests at the end
- Use vague test names
- Test multiple things in one test

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShoppingCartTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_add_product_to_cart(): void
    {
        // Arrange
        $user = User::factory()->create();
        $product = Product::factory()->create();
        
        // Act
        $response = $this->actingAs($user)
            ->post(route('cart.add'), [
                'product_id' => $product->id,
                'quantity' => 2,
            ]);
        
        // Assert
        $response->assertRedirect();
        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    /** @test */
    public function guest_cannot_add_product_to_cart(): void
    {
        // Arrange
        $product = Product::factory()->create();
        
        // Act
        $response = $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
        
        // Assert
        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('cart_items', 0);
    }
}
```

### Factory Usage

**✅ DO:**
- Use factories for test data
- Create realistic test data
- Use states for variations

**❌ DON'T:**
- Hardcode test data in tests
- Create incomplete factories

```php
<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'sku' => 'PEL-' . strtoupper($this->faker->bothify('???-##')),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 29.99, 299.99),
            'category_id' => Category::factory(),
            'active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }

    public function lowStock(): static
    {
        return $this->afterCreating(function (Product $product) {
            $product->batches()->create([
                'batch_number' => 'LOW-001',
                'quantity_available' => 2,
                'purity_percentage' => 99.0,
            ]);
        });
    }
}
```

## 🚫 Common AI Pitfalls to Avoid

### 1. Over-Abstraction
**Problem:** Creating unnecessary layers of abstraction
**Solution:** Start simple, refactor when you have 3+ similar implementations

```php
// ❌ BAD - Premature abstraction
interface Repository {
    public function find($id);
    public function all();
}

class EloquentProductRepository implements Repository {
    public function find($id) {
        return Product::find($id);
    }
}

// ✅ GOOD - Use Eloquent directly until you need abstraction
$product = Product::find($id);
```

### 2. Verbose Naming
**Problem:** Names that are too long or redundant
**Solution:** Use clear, concise names

```php
// ❌ BAD
public function getUserShoppingCartItemsFromDatabase(): Collection

// ✅ GOOD
public function getCartItems(): Collection
```

### 3. Unnecessary Comments
**Problem:** Commenting obvious code
**Solution:** Write self-documenting code, comment only complex logic

```php
// ❌ BAD - Obvious comment
// Get the user
$user = auth()->user();

// Create a new order
$order = new Order();

// ✅ GOOD - Comment explains WHY
// Use FIFO allocation to prevent batch expiration
$batch = $this->batchService->allocateOldestFirst($product);
```

### 4. Not Using Laravel Helpers
**Problem:** Reinventing Laravel features
**Solution:** Learn and use Laravel's built-in helpers

```php
// ❌ BAD
$value = isset($array['key']) ? $array['key'] : 'default';

// ✅ GOOD
$value = data_get($array, 'key', 'default');

// ❌ BAD
if ($user !== null && $user->isAdmin() === true) {

// ✅ GOOD
if ($user?->isAdmin()) {
```

### 5. Ignoring N+1 Queries
**Problem:** Not eager loading relationships
**Solution:** Always eager load in loops

```php
// ❌ BAD - N+1 query problem
$products = Product::all();
foreach ($products as $product) {
    echo $product->category->name; // Queries DB for each product
}

// ✅ GOOD - Eager loading
$products = Product::with('category')->get();
foreach ($products as $product) {
    echo $product->category->name; // Uses eager loaded data
}
```

### 6. Not Using Database Transactions
**Problem:** Partial updates on failure
**Solution:** Wrap multi-step operations in transactions

```php
// ❌ BAD - No transaction
public function createOrder($data)
{
    $order = Order::create($data);
    $order->items()->createMany($data['items']);
    // If this fails, we have an order with no items
    $this->allocateInventory($order);
}

// ✅ GOOD - Transaction
public function createOrder($data)
{
    return DB::transaction(function () use ($data) {
        $order = Order::create($data);
        $order->items()->createMany($data['items']);
        $this->allocateInventory($order);
        return $order;
    });
}
```

### 7. Creating "Service" for Everything
**Problem:** Every piece of logic becomes a service
**Solution:** Services are for complex business logic, not simple operations

```php
// ❌ BAD - Service for simple operation
class ProductNameFormatter {
    public function format(string $name): string {
        return ucfirst($name);
    }
}

// ✅ GOOD - Simple method in model or helper
class Product extends Model {
    public function getFormattedNameAttribute(): string {
        return ucfirst($this->name);
    }
}
```

## 📊 Performance Conventions

### Database Queries

**✅ DO:**
- Use `select()` to limit columns when fetching large datasets
- Add indexes for frequently queried columns
- Use `chunk()` for processing large datasets
- Use `exists()` instead of `count() > 0`

**❌ DON'T:**
- Fetch all columns when you only need a few
- Query in loops (use eager loading)
- Load entire tables into memory

```php
// ✅ GOOD - Select only needed columns
$products = Product::select('id', 'name', 'price')
    ->active()
    ->get();

// ✅ GOOD - Efficient existence check
if (Product::where('sku', $sku)->exists()) {
    // Do something
}

// ✅ GOOD - Process large datasets
Product::chunk(100, function ($products) {
    foreach ($products as $product) {
        // Process product
    }
});

// ❌ BAD - Fetch everything
$products = Product::all();

// ❌ BAD - Inefficient count
if (Product::where('sku', $sku)->count() > 0) {
```

### Caching

**✅ DO:**
- Cache expensive queries
- Use appropriate cache durations
- Clear cache when data changes

**❌ DON'T:**
- Cache everything
- Use infinite cache durations
- Forget to invalidate cache

```php
// ✅ GOOD - Cache with appropriate TTL
$categories = Cache::remember('categories', 3600, function () {
    return Category::with('products')->get();
});

// ✅ GOOD - Clear cache when data changes
public function update(Request $request, Category $category)
{
    $category->update($request->validated());
    Cache::forget('categories');
    return redirect()->back();
}
```

## 🔄 Git Conventions

### Commit Messages

**Format:** `type: description`

**Types:**
- `feat:` New feature
- `fix:` Bug fix
- `refactor:` Code restructuring
- `docs:` Documentation changes
- `style:` Formatting changes
- `test:` Adding tests
- `chore:` Maintenance tasks

```bash
# ✅ GOOD
feat: add batch allocation service for FIFO inventory
fix: prevent negative inventory in cart
refactor: extract payment logic to service
docs: update README with deployment instructions

# ❌ BAD
"updated stuff"
"fixes"
"WIP"
```

### Branch Strategy

**For Solo Development:**
- `main` - Production-ready code
- Feature branches as needed for complex work

```bash
# Create feature branch
git checkout -b feature/batch-coa-system

# Work on feature, commit regularly
git commit -m "feat: create batch model and migration"
git commit -m "feat: add CoA upload to Filament"

# Merge to main when complete
git checkout main
git merge feature/batch-coa-system
git push
```

## 📝 Documentation Requirements

See `Documentation_Guide.md` for comprehensive documentation conventions.

**Quick reference:**
- Document complex logic with inline comments
- Use DocBlocks for all public methods
- Keep README.md updated with each phase
- Document architectural decisions
- Create completion reports for each task

---

**Remember:** These conventions exist to maintain consistency and quality. When in doubt, prioritize readability and simplicity over cleverness.
