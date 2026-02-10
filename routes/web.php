<?php

use App\Http\Controllers\ProfileController;
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

require __DIR__ . "/auth.php";
