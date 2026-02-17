<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get("/", function () {
    return view("welcome");
});

Route::get("/dashboard", function () {
    return view("dashboard");
})
    ->middleware(["auth", "verified"])
    ->name("dashboard");

Route::middleware("auth")->group(function () {
    Route::get("/profile", [ProfileController::class, "edit"])->name(
        "profile.edit",
    );
    Route::patch("/profile", [ProfileController::class, "update"])->name(
        "profile.update",
    );
    Route::delete("/profile", [ProfileController::class, "destroy"])->name(
        "profile.destroy",
    );
});

// Design system visual reference — no auth, remove or protect before production
Route::get("/design", function () {
    return view("design");
})->name("design");

Route::get("/test-components", function () {
    return view("test-components");
})->name("test-components");

Route::get("/test-s3", function () {
    $results = [];
    try {
        Storage::disk("coas")->put(
            "test-coa.txt",
            "Test CoA - Pacific Edge Labs",
        );
        $results["coa_upload"] = Storage::disk("coas")->exists("test-coa.txt")
            ? "✅ Success"
            : "❌ Failed";
        $results["coa_signed_url"] = Storage::disk("coas")->temporaryUrl(
            "test-coa.txt",
            now()->addMinutes(5),
        );

        Storage::disk("products")->put(
            "test-product.txt",
            "Test Product - Pacific Edge Labs",
        );
        $results["product_upload"] = Storage::disk("products")->exists(
            "test-product.txt",
        )
            ? "✅ Success"
            : "❌ Failed";
        $results["product_public_url"] = Storage::disk("products")->url(
            "test-product.txt",
        );
    } catch (\Exception $e) {
        $results["error"] = $e->getMessage();
    }
    return response()->json($results, 200, [], JSON_PRETTY_PRINT);
});

// Payment test route (REMOVE IN PRODUCTION)
Route::get('/test-payment', function (PaymentService $paymentService) {
    $user = auth()->user() ?? \App\Models\User::first();

    if (!$user) {
        return 'No users found. Create a user first.';
    }

    // Test successful payment
    $successTransaction = $paymentService->processPayment(
        user: $user,
        amount: 99.99,
        paymentDetails: [
            'card_number' => '4111111111111111', // Test Visa
            'cvv' => '123',
            'expiry' => '12/25',
            'name' => $user->name,
        ],
        metadata: ['test' => true, 'description' => 'Test payment']
    );

    // Test failed payment
    $failedTransaction = $paymentService->processPayment(
        user: $user,
        amount: 49.99,
        paymentDetails: [
            'card_number' => '4111111111110000', // Ends in 0000 = fails
            'cvv' => '123',
            'expiry' => '12/25',
            'name' => $user->name,
        ],
        metadata: ['test' => true, 'description' => 'Test failed payment']
    );

    return response()->json([
        'gateway_info' => $paymentService->getGatewayInfo(),
        'success_transaction' => [
            'id' => $successTransaction->id,
            'status' => $successTransaction->status,
            'amount' => $successTransaction->amount,
            'transaction_id' => $successTransaction->transaction_id,
            'payment_method' => $successTransaction->payment_method,
        ],
        'failed_transaction' => [
            'id' => $failedTransaction->id,
            'status' => $failedTransaction->status,
            'amount' => $failedTransaction->amount,
            'error' => $failedTransaction->error_message,
        ],
    ], 200, [], JSON_PRETTY_PRINT);
})->name('test-payment');

// Dev utility: clear age-gate cookie so you can re-test the modal — REMOVE BEFORE PRODUCTION
Route::get('/dev/clear-age-gate', function () {
    return redirect('/design')->withCookie(cookie()->forget('age_verified'));
})->name('dev.clear-age-gate');

// Phase 2: Product Catalog
Route::get('/search', [ProductController::class, 'search'])
    ->name('products.search');
Route::get('/products', [ProductController::class, 'index'])
    ->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])
    ->name('products.show');
Route::get('/categories/{slug}', [CategoryController::class, 'show'])
    ->name('categories.show');

require __DIR__ . "/auth.php";
