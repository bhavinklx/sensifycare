<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\User\RoleController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Pages\PagesController;
use App\Http\Controllers\Bcategory\BcategoryController;
use App\Http\Controllers\Blog\BlogController;

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    return 'Application cache has been cleared';
});

Route::get('/admin', function () {
    if (\Auth::guest()) {
        return redirect(url('/admin/login'));
    } else {
        return redirect(url('/admin/dashboard'));
    }
});

Route::get('/admin/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/admin/login', [LoginController::class, 'login'])->name('login');

Route::group(['middleware' => ['auth']], function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name("dashboard");
    Route::get('/admin/logout', [LoginController::class, 'logout'])->name('logout');

    //For Administrator
    Route::controller(UserController::class)->group(function (){
        Route::middleware('can:user-add')->group(function () {
            Route::get('/admin/user-add', 'create')->name('user-add');
            Route::post('/admin/user-insert', 'insert')->name('user-insert');
        });
        Route::middleware('can:user-edit')->group(function () {
            Route::get('/admin/user-edit/{id}', 'edit')->name('user-edit');
            Route::post('/admin/user-update', 'update')->name('user-update');
        });
        Route::middleware('can:user-list')->group(function () {
            Route::get("/admin/user-list", "view")->name("user-list");
            Route::get("/admin/user-load-table", "load_table")->name("user-load-table");
        });
        Route::middleware('can:user-delete')->group(function () {
            Route::post("/admin/user-delete", "delete")->name("user-delete");
            Route::get("/admin/user-changepassword/{id}", "changepassword")->name("user-changepassword");
            Route::post("/admin/user-changepassword-update", "changepassword_update")->name("user-changepassword-update");
        });
    });

    //For Role
    Route::controller(RoleController::class)->group(function (){
        Route::middleware('can:role-add')->group(function () {
            Route::get("/admin/role-add", "create")->name("role-add");
            Route::post("/admin/role-insert", "insert")->name("role-insert");
        });
        Route::middleware('can:role-edit')->group(function () {
            Route::get("/admin/role-edit/{id}", "edit")->name("role-edit");
            Route::post("/admin/role-update", "update")->name("role-update");
        });
        Route::middleware('can:role-list')->group(function () {
            Route::get("/admin/role-list", "view")->name("role-list");
            Route::get("/admin/role-load-table", "load_table")->name("role-load-table");
        });
        Route::middleware('can:role-delete')->group(function () {
            Route::post("/admin/role-delete", "delete")->name("role-delete");
        });
    });

    //For Pages
    Route::controller(PagesController::class)->group(function (){
        Route::middleware('can:pages-add')->group(function () {
            Route::get("/admin/pages-add", "create")->name("pages-add");
            Route::post("/admin/pages-insert", "insert")->name("pages-insert");
        });
        Route::middleware('can:pages-edit')->group(function () {
            Route::get("/admin/pages-edit/{id}", "edit")->name("pages-edit");
            Route::post("/admin/pages-update", "update")->name("pages-update");
        });
        Route::middleware('can:pages-list')->group(function () {
            Route::get("/admin/pages-list", "view")->name("pages-list");
        });
        Route::middleware('can:pages-delete')->group(function () {
            Route::post("/admin/pages-delete", "delete")->name("pages-delete");
        });
        Route::get("/admin/pages-create-slug", "createSlug")->name("pages-create-slug");
        Route::post("/admin/pages-change-status", "change_status")->name("pages-change-status");
        Route::post("/admin/pages-change-header-status", "change_header_status")->name("pages-change-header-status");
        Route::post("/admin/pages-change-footer-status", "change_footer_status")->name("pages-change-footer-status");
        Route::post("/admin/pages-update-order", "update_order")->name("pages-update-order");
        Route::post('/admin/pages-image-upload', 'uploadImage')->name('pages-image-upload');
    });

    //For Blog Category
    Route::controller(BcategoryController::class)->group(function (){
        Route::middleware('can:bcategory-add')->group(function () {
            Route::get("/admin/bcategory-add", "create")->name("bcategory-add");
            Route::post("/admin/bcategory-insert", "insert")->name("bcategory-insert");
        });
        Route::middleware('can:bcategory-edit')->group(function () {
            Route::get("/admin/bcategory-edit/{id}", "edit")->name("bcategory-edit");
            Route::post("/admin/bcategory-update", "update")->name("bcategory-update");
        });
        Route::middleware('can:bcategory-list')->group(function () {
            Route::get("/admin/bcategory-list", "view")->name("bcategory-list");
            Route::get("/admin/bcategory-load-table", "load_table")->name("bcategory-load-table");
        });
        Route::middleware('can:bcategory-delete')->group(function () {
            Route::post("/admin/bcategory-delete", "delete")->name("bcategory-delete");
        });
        Route::get("/admin/bcategory-create-slug", "createSlug")->name("bcategory-create-slug");
        Route::post("/admin/bcategory-change-status", "change_status")->name("bcategory-change-status");
        Route::post("/admin/bcategory-update-order", "update_order")->name("bcategory-update-order");
    });

    //For Blog
    Route::controller(BlogController::class)->group(function (){
        Route::middleware('can:blog-add')->group(function () {
            Route::get("/admin/blog-add", "create")->name("blog-add");
            Route::post("/admin/blog-insert", "insert")->name("blog-insert");
        });
        Route::middleware('can:blog-edit')->group(function () {
            Route::get("/admin/blog-edit/{id}", "edit")->name("blog-edit");
            Route::post("/admin/blog-update", "update")->name("blog-update");
        });
        Route::middleware('can:blog-list')->group(function () {
            Route::get("/admin/blog-list", "view")->name("blog-list");
            Route::get("/admin/blog-load-table", "load_table")->name("blog-load-table");
        });
        Route::middleware('can:blog-delete')->group(function () {
            Route::post("/admin/blog-delete", "delete")->name("blog-delete");
        });
        Route::get("/admin/blog-create-slug", "createSlug")->name("blog-create-slug");
        Route::post("/admin/blog-change-status", "change_status")->name("blog-change-status");
        Route::post("/admin/blog-change-header-status", "change_header_status")->name("blog-change-header-status");
        Route::post("/admin/blog-change-footer-status", "change_footer_status")->name("blog-change-footer-status");
        Route::post("/admin/blog-update-order", "update_order")->name("blog-update-order");
        Route::post('/admin/blog-image-upload', 'uploadImage')->name('blog-image-upload');
    });
});

Route::get('/', function () {
    return view('welcome');
});

/*Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});*/


require __DIR__.'/auth.php';
