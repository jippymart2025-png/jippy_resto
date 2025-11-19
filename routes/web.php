<?php



use Illuminate\Support\Facades\Route;



/*

|--------------------------------------------------------------------------

| Web Routes

|--------------------------------------------------------------------------

|

| Here is where you can register web routes for your application. These

| routes are loaded by the RouteServiceProvider within a group which

| contains the "web" middleware group. Now create something great!

|

*/

Route::get('lang/change', [App\Http\Controllers\LangController::class, 'change'])->name('changeLang');

// System health monitoring routes
Route::get('/health', [App\Http\Controllers\SystemHealthController::class, 'checkHealth'])->name('health.check');
Route::post('/health/cleanup', [App\Http\Controllers\SystemHealthController::class, 'emergencyCleanup'])->name('health.cleanup');

// Impersonation API routes
Route::prefix('api')->group(function () {
    // Check if there's an active impersonation session
    Route::get('/check-impersonation', [App\Http\Controllers\ImpersonationController::class, 'checkImpersonation']);

    // Process the impersonation and log in the user
    Route::post('/process-impersonation', [App\Http\Controllers\ImpersonationController::class, 'processImpersonation']);

    // End impersonation session
    Route::post('/end-impersonation', [App\Http\Controllers\ImpersonationController::class, 'endImpersonation']);

    // Get current impersonation status
    Route::get('/impersonation-status', [App\Http\Controllers\ImpersonationController::class, 'getImpersonationStatus']);

    // Debug endpoint to store test impersonation data
    Route::post('/debug-store-impersonation', [App\Http\Controllers\ImpersonationController::class, 'debugStoreImpersonationData']);
});

Route::post('setToken', [App\Http\Controllers\Auth\AjaxController::class, 'setToken'])->name('setToken');
Route::post('setSubcriptionFlag', [App\Http\Controllers\Auth\AjaxController::class, 'setSubcriptionFlag'])->name('setSubcriptionFlag');

Route::get('register', function () {

    return view('auth.register');

})->name('register');

Route::get('signup', function () {

    return view('auth.signup');

})->name('signup');

Route::get('register/phone', function () {

    return view('auth.phone_register');

})->name('register.phone');

Route::get('subscription-plan', [App\Http\Controllers\SubscriptionController::class, 'show'])->name('subscription-plan.show');

Route::get('subscription-plan/checkout/{id}', [App\Http\Controllers\SubscriptionController::class, 'checkout'])->name('subscription-plans.checkout');

Route::post('payment-proccessing', [App\Http\Controllers\SubscriptionController::class, 'orderProccessing'])->name('payment-proccessing')->middleware('throttle:10,1');

Route::get('pay-subscription', [App\Http\Controllers\SubscriptionController::class, 'proccesstopay'])->name('pay-subscription');

Route::post('order-complete', [App\Http\Controllers\SubscriptionController::class, 'orderComplete'])->name('order-complete');

Route::post('process-stripe', [App\Http\Controllers\SubscriptionController::class, 'processStripePayment'])->name('process-stripe')->middleware('throttle:10,1');

Route::post('process-paypal', [App\Http\Controllers\SubscriptionController::class, 'processPaypalPayment'])->name('process-paypal');

Route::post('razorpaypayment', [App\Http\Controllers\SubscriptionController::class, 'razorpaypayment'])->name('razorpaypayment');

Route::post('process-mercadopago', [App\Http\Controllers\SubscriptionController::class, 'processMercadoPagoPayment'])->name('process-mercadopago')->middleware('throttle:10,1');

Route::get('success', [App\Http\Controllers\SubscriptionController::class, 'success'])->name('success');

Route::get('failed', [App\Http\Controllers\SubscriptionController::class, 'failed'])->name('failed');

Route::get('notify', [App\Http\Controllers\SubscriptionController::class, 'notify'])->name('notify');

// Secure Impersonation API Routes
Route::prefix('api/impersonation')->middleware(['throttle:60,1'])->group(function () {
    Route::post('/generate', [App\Http\Controllers\ImpersonationSecurityController::class, 'generateSecureImpersonationToken'])->name('impersonation.generate');
    Route::post('/validate', [App\Http\Controllers\ImpersonationSecurityController::class, 'validateSecureImpersonationToken'])->name('impersonation.validate');
});

// Monitoring and Health Check Routes
Route::prefix('api/monitoring')->middleware(['throttle:120,1'])->group(function () {
    Route::get('/health', [App\Http\Controllers\ImpersonationMonitoringController::class, 'getSystemHealth'])->name('monitoring.health');
    Route::get('/performance', [App\Http\Controllers\ImpersonationMonitoringController::class, 'getPerformanceMetrics'])->name('monitoring.performance');
    Route::get('/security', [App\Http\Controllers\ImpersonationMonitoringController::class, 'getSecurityStatistics'])->name('monitoring.security');
    Route::get('/test', [App\Http\Controllers\ImpersonationMonitoringController::class, 'testImpersonationSystem'])->name('monitoring.test');
    Route::post('/cleanup', [App\Http\Controllers\ImpersonationMonitoringController::class, 'cleanupOldData'])->name('monitoring.cleanup');
});

// Security Audit Routes
Route::prefix('api/security')->middleware(['throttle:60,1'])->group(function () {
    Route::get('/audit-logs', [App\Http\Controllers\ImpersonationMonitoringController::class, 'getSecurityAuditLogs'])->name('security.audit-logs');
    Route::get('/audit-logs/{id}', [App\Http\Controllers\ImpersonationMonitoringController::class, 'getSecurityEvent'])->name('security.audit-event');
});

Auth::routes();

// Enhanced authentication routes with rate limiting
Route::get('forgot-password', [App\Http\Controllers\Auth\LoginController::class, 'forgotPassword'])->name('forgot-password');
Route::post('password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->middleware('throttle.login:3,1')
    ->name('password.email');
Route::get('password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])
    ->middleware('throttle.login:3,1')
    ->name('password.update');

// Social login routes
Route::get('auth/{provider}', [App\Http\Controllers\Auth\SocialLoginController::class, 'redirectToProvider'])
    ->where('provider', 'google|facebook|github')
    ->name('social.login');
Route::get('auth/{provider}/callback', [App\Http\Controllers\Auth\SocialLoginController::class, 'handleProviderCallback'])
    ->where('provider', 'google|facebook|github')
    ->name('social.callback');

// Two-Factor Authentication routes
Route::middleware(['auth'])->group(function () {
    Route::get('2fa/setup', [App\Http\Controllers\Auth\TwoFactorController::class, 'showSetupForm'])->name('2fa.setup');
    Route::post('2fa/enable', [App\Http\Controllers\Auth\TwoFactorController::class, 'enable'])->name('2fa.enable');
    Route::post('2fa/disable', [App\Http\Controllers\Auth\TwoFactorController::class, 'disable'])->name('2fa.disable');
    Route::post('2fa/regenerate-codes', [App\Http\Controllers\Auth\TwoFactorController::class, 'regenerateBackupCodes'])->name('2fa.regenerate-codes');
});

Route::middleware(['auth', '2fa'])->group(function () {
    Route::get('2fa/verify', [App\Http\Controllers\Auth\TwoFactorController::class, 'showVerificationForm'])->name('2fa.verify');
    Route::post('2fa/verify', [App\Http\Controllers\Auth\TwoFactorController::class, 'verify'])->name('2fa.verify.post');
});

// Passwordless authentication routes
Route::get('passwordless/login', [App\Http\Controllers\Auth\PasswordlessController::class, 'showLoginForm'])->name('passwordless.login');
Route::post('passwordless/send', [App\Http\Controllers\Auth\PasswordlessController::class, 'sendMagicLink'])
    ->middleware('throttle.login:3,1')
    ->name('passwordless.send');
Route::get('passwordless/verify/{token}', [App\Http\Controllers\Auth\PasswordlessController::class, 'verifyMagicLink'])
    ->name('passwordless.verify');

Route::get('passwordless/register', [App\Http\Controllers\Auth\PasswordlessController::class, 'showRegisterForm'])->name('passwordless.register');
Route::post('passwordless/register/send', [App\Http\Controllers\Auth\PasswordlessController::class, 'sendRegistrationLink'])
    ->middleware('throttle.login:3,1')
    ->name('passwordless.register.send');
Route::get('passwordless/register/verify/{token}', [App\Http\Controllers\Auth\PasswordlessController::class, 'verifyRegistrationLink'])
    ->name('passwordless.register.verify');

// Impersonation management routes
Route::post('impersonation/end', function() {
    // Clear impersonation data from localStorage via JavaScript
    return response()->json(['success' => true, 'message' => 'Impersonation session ended']);
})->name('impersonation.end');

// Debug route for impersonation testing (remove in production)
Route::get('test-impersonation', function() {
    return view('test-impersonation');
})->name('test.impersonation');
Route::post('store-firebase-service', [App\Http\Controllers\HomeController::class, 'storeFirebaseService'])->name('store-firebase-service');

Route::middleware(['check.subscription'])->group(function () {

    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::get('my-subscription/show/{id}', [App\Http\Controllers\MySubscriptionsController::class, 'show'])->name('my-subscription.show');

    Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');

    Route::get('my-subscriptions', [App\Http\Controllers\MySubscriptionsController::class, 'index'])->name('my-subscriptions');

    Route::get('/users/profile', [App\Http\Controllers\UserController::class, 'profile'])->name('user.profile');

    Route::get('/restaurant', [App\Http\Controllers\UserController::class, 'restaurant'])->name('restaurant');

    Route::get('/foods', [App\Http\Controllers\FoodController::class, 'index'])->name('foods');

    Route::get('/foods/edit/{id}', [App\Http\Controllers\FoodController::class, 'edit'])->name('foods.edit');

    Route::get('/foods/create', [App\Http\Controllers\FoodController::class, 'create'])->name('foods.create');

    Route::get('/orders', [App\Http\Controllers\OrderController::class, 'index'])->name('orders');

    Route::get('/orders/edit/{id}', [App\Http\Controllers\OrderController::class, 'edit'])->name('orders.edit');

    Route::get('/placedOrders', [App\Http\Controllers\OrderController::class, 'placedOrders'])->name('placedOrders');

    Route::get('/acceptedOrders', [App\Http\Controllers\OrderController::class, 'acceptedOrders'])->name('acceptedOrders');

    Route::get('/rejectedOrders', [App\Http\Controllers\OrderController::class, 'rejectedOrders'])->name('rejectedOrders');

    Route::get('/payments', [App\Http\Controllers\PayoutsController::class, 'index'])->name('payments');

    Route::get('/payments/create', [App\Http\Controllers\PayoutsController::class, 'create'])->name('payments.create');

    // Route::get('/payments/edit/{id}', [App\Http\Controllers\PaymentController::class, 'edit'])->name('payments.edit');

    // Route::get('/earnings', [App\Http\Controllers\EarningController::class, 'index'])->name('earnings');

    // Route::get('/earnings/edit/{id}', [App\Http\Controllers\EarningController::class, 'edit'])->name('earnings.edit');

    Route::get('/coupons', [App\Http\Controllers\CouponController::class, 'index'])->name('coupons');

    Route::get('/coupons/edit/{id}', [App\Http\Controllers\CouponController::class, 'edit'])->name('coupons.edit');

    Route::get('/coupons/create', [App\Http\Controllers\CouponController::class, 'create'])->name('coupons.create');

    Route::post('order-status-notification', [App\Http\Controllers\OrderController::class, 'sendNotification'])->name('order-status-notification');

    Route::post('/sendnotification', [App\Http\Controllers\BookTableController::class, 'sendnotification'])->name('sendnotification');

    Route::get('/booktable', [App\Http\Controllers\BookTableController::class, 'index'])->name('booktable');

    Route::get('/booktable/edit/{id}', [App\Http\Controllers\BookTableController::class, 'edit'])->name('booktable.edit');

    Route::get('/orders/print/{id}', [App\Http\Controllers\OrderController::class, 'orderprint'])->name('vendors.orderprint');

    Route::get('/wallettransaction', [App\Http\Controllers\TransactionController::class, 'index'])->name('wallettransaction.index');

    Route::post('send-email', [App\Http\Controllers\SendEmailController::class, 'sendMail'])->name('sendMail');

    Route::get('document-list', [App\Http\Controllers\DocumentController::class, 'DocumentList'])->name('vendors.document');

    Route::get('document/upload/{id}', [App\Http\Controllers\DocumentController::class, 'DocumentUpload'])->name('document.upload');

    Route::get('withdraw-method', [App\Http\Controllers\WithdrawMethodController::class, 'index'])->name('withdraw-method');

    Route::get('withdraw-method/add', [App\Http\Controllers\WithdrawMethodController::class, 'create'])->name('withdraw-method.create');

    Route::patch('/foods/inline-update/{id}', [App\Http\Controllers\FoodController::class, 'inlineUpdate'])->name('foods.inlineUpdate');

    Route::get('/foods/download-template', [App\Http\Controllers\FoodController::class, 'downloadTemplate'])->name('foods.download-template');
    Route::post('/foods/import', [App\Http\Controllers\FoodController::class, 'import'])->name('foods.import')->middleware('throttle:5,1'); // 5 requests per minute
});

// Admin Impersonation Routes (for admin panel integration)
Route::prefix('admin')->group(function () {
    Route::post('/impersonate/generate-token', [App\Http\Controllers\AdminImpersonationController::class, 'generateImpersonationToken'])->name('admin.impersonate.generate');
    Route::post('/impersonate/validate-token', [App\Http\Controllers\AdminImpersonationController::class, 'validateImpersonationToken'])->name('admin.impersonate.validate');
    Route::get('/impersonate/stats', [App\Http\Controllers\AdminImpersonationController::class, 'getImpersonationStats'])->name('admin.impersonate.stats');
});


