<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\BlockedDateController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\BlogTaxonomyController;
use App\Http\Controllers\Admin\BookingCalendarController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\BookingSettingsController;
use App\Http\Controllers\Admin\BusinessSettingsController;
use App\Http\Controllers\Admin\ContactEnquiryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EditorUploadController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Admin\GalleryItemController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\SystemSettingsController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SitemapController;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/about-us', [PageController::class, 'about'])->name('about');
Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms-and-conditions', [PageController::class, 'terms'])->name('terms');

Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store')->middleware('throttle:5,1');

Route::get('/book-now', [BookingController::class, 'index'])->name('booking.index');
Route::post('/book-now', [BookingController::class, 'store'])->name('booking.store')->middleware('throttle:10,1');
Route::post('/book-now/coupon', [BookingController::class, 'validateCoupon'])->name('booking.coupon')->middleware('throttle:30,1');
Route::get('/book-now/confirmation/{reference}', [BookingController::class, 'confirmation'])->name('booking.confirmation');
Route::post('/book-now/payment/verify', [BookingController::class, 'verifyPayment'])->name('booking.payment.verify')->middleware('throttle:20,1');
Route::get('/api/booking/slots', [BookingController::class, 'slots'])->name('booking.slots')->middleware('throttle:60,1');

Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');

Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');

Route::get('/sitemap.xml', [SitemapController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [LoginController::class, 'create'])->name('login');
        Route::post('login', [LoginController::class, 'store'])->name('login.store');
    });

    Route::middleware(['auth', EnsureAdmin::class])->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

        Route::get('settings/business', [BusinessSettingsController::class, 'edit'])->name('settings.business');
        Route::put('settings/business', [BusinessSettingsController::class, 'update'])->name('settings.business.update');

        Route::get('settings/booking', [BookingSettingsController::class, 'edit'])->name('settings.booking');
        Route::put('settings/booking', [BookingSettingsController::class, 'update'])->name('settings.booking.update');

        Route::get('bookings/today', [AdminBookingController::class, 'today'])->name('bookings.today');
        Route::get('bookings/calendar', BookingCalendarController::class)->name('bookings.calendar');
        Route::get('bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
        Route::get('bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
        Route::patch('bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.status');

        Route::post('editor/uploads', [EditorUploadController::class, 'store'])->name('editor.uploads.store');

        Route::resource('services', AdminServiceController::class)->except(['show']);
        Route::resource('service-categories', ServiceCategoryController::class)->except(['show']);
        Route::resource('locations', LocationController::class)->except(['show']);
        Route::resource('blocked-dates', BlockedDateController::class)->except(['show']);

        Route::resource('faqs', AdminFaqController::class)->except(['show']);
        Route::resource('pages', AdminPageController::class)->except(['show']);
        Route::resource('blog-posts', BlogPostController::class)->except(['show']);
        Route::get('blog-taxonomies', [BlogTaxonomyController::class, 'index'])->name('blog-taxonomies.index');
        Route::post('blog-categories', [BlogTaxonomyController::class, 'storeCategory'])->name('blog-categories.store');
        Route::delete('blog-categories/{blogCategory}', [BlogTaxonomyController::class, 'destroyCategory'])->name('blog-categories.destroy');
        Route::post('blog-tags', [BlogTaxonomyController::class, 'storeTag'])->name('blog-tags.store');
        Route::delete('blog-tags/{blogTag}', [BlogTaxonomyController::class, 'destroyTag'])->name('blog-tags.destroy');
        Route::resource('gallery-items', GalleryItemController::class)->except(['show']);
        Route::resource('testimonials', TestimonialController::class)->except(['show']);

        Route::get('enquiries', [ContactEnquiryController::class, 'index'])->name('enquiries.index');
        Route::get('enquiries/{enquiry}', [ContactEnquiryController::class, 'show'])->name('enquiries.show');
        Route::patch('enquiries/{enquiry}/status', [ContactEnquiryController::class, 'updateStatus'])->name('enquiries.status');

        Route::resource('coupons', CouponController::class)->except(['show']);

        Route::get('seo', SeoController::class)->name('seo.index');

        Route::get('reports/bookings', [ReportController::class, 'bookings'])->name('reports.bookings');
        Route::get('reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');

        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');

        Route::get('system/settings', [SystemSettingsController::class, 'edit'])->name('system.settings');
        Route::put('system/settings', [SystemSettingsController::class, 'update'])->name('system.settings.update');
        Route::post('system/cache', [SystemSettingsController::class, 'clearCache'])->name('system.cache.clear');
    });
});
