<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\User\RoleController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Pages\PagesController;
use App\Http\Controllers\Bcategory\BcategoryController;
use App\Http\Controllers\Blog\BlogController;
use App\Http\Controllers\Banner\BannerController;
use App\Http\Controllers\Patient\PatientController;
use App\Http\Controllers\Doctor\DoctorController;
use App\Http\Controllers\Setting\SettingController;
use App\Http\Controllers\Symptom\SymptomController;
use App\Http\Controllers\QuestionAnswer\QuestionAnswerController;
use App\Http\Controllers\HealthParameter\HealthParameterController;

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
    Route::get('/admin/ai-assistant', [DashboardController::class, 'aiAssistant'])->name("ai-assistant");

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
        Route::post("/admin/blog-update-order", "update_order")->name("blog-update-order");
        Route::post('/admin/blog-image-upload', 'uploadImage')->name('blog-image-upload');
    });

    //For Banner
    Route::controller(BannerController::class)->group(function (){
        Route::middleware('can:banner-add')->group(function () {
            Route::get("/admin/banner-add", "create")->name("banner-add");
            Route::post("/admin/banner-insert", "insert")->name("banner-insert");
        });
        Route::middleware('can:banner-edit')->group(function () {
            Route::get("/admin/banner-edit/{id}", "edit")->name("banner-edit");
            Route::post("/admin/banner-update", "update")->name("banner-update");
        });
        Route::middleware('can:banner-list')->group(function () {
            Route::get("/admin/banner-list", "view")->name("banner-list");
            Route::get("/admin/banner-load-table", "load_table")->name("banner-load-table");
        });
        Route::middleware('can:banner-delete')->group(function () {
            Route::post("/admin/banner-delete", "delete")->name("banner-delete");
        });
        Route::post("/admin/banner-change-status", "change_status")->name("banner-change-status");
        Route::post("/admin/banner-update-order", "update_order")->name("banner-update-order");
        Route::post('/admin/banner-image-upload', 'uploadImage')->name('banner-image-upload');
    });

    //For Patient
    Route::controller(PatientController::class)->group(function (){
        Route::middleware('can:patient-add')->group(function () {
            Route::get("/admin/patient-add", "create")->name("patient-add");
            Route::post("/admin/patient-insert", "insert")->name("patient-insert");
        });
        Route::middleware('can:patient-edit')->group(function () {
            Route::get("/admin/patient-edit/{id}", "edit")->name("patient-edit");
            Route::post("/admin/patient-update", "update")->name("patient-update");
        });
        Route::middleware('can:patient-list')->group(function () {
            Route::get("/admin/patient-list", "view")->name("patient-list");
            Route::get("/admin/patient-load-table", "load_table")->name("patient-load-table");
        });
        Route::middleware('can:patient-delete')->group(function () {
            Route::post("/admin/patient-delete", "delete")->name("patient-delete");
        });
        Route::get("/admin/patient-create-slug", "createSlug")->name("patient-create-slug");
        Route::post("/admin/patient-change-status", "change_status")->name("patient-change-status");
        Route::post("/admin/patient-update-order", "update_order")->name("patient-update-order");
        Route::post('/admin/patient-image-upload', 'uploadImage')->name('patient-image-upload');
    });

    //For Doctor
    Route::controller(DoctorController::class)->group(function (){
        Route::middleware('can:doctor-add')->group(function () {
            Route::get("/admin/doctor-add", "create")->name("doctor-add");
            Route::post("/admin/doctor-insert", "insert")->name("doctor-insert");
        });
        Route::middleware('can:doctor-edit')->group(function () {
            Route::get("/admin/doctor-edit/{id}", "edit")->name("doctor-edit");
            Route::post("/admin/doctor-update", "update")->name("doctor-update");
        });
        Route::middleware('can:doctor-list')->group(function () {
            Route::get("/admin/doctor-list", "view")->name("doctor-list");
            Route::get("/admin/doctor-load-table", "load_table")->name("doctor-load-table");
        });
        Route::middleware('can:doctor-delete')->group(function () {
            Route::post("/admin/doctor-delete", "delete")->name("doctor-delete");
        });
        Route::get("/admin/doctor-create-slug", "createSlug")->name("doctor-create-slug");
        Route::post("/admin/doctor-change-status", "change_status")->name("doctor-change-status");
        Route::post("/admin/doctor-update-order", "update_order")->name("doctor-update-order");
        Route::post('/admin/doctor-image-upload', 'uploadImage')->name('doctor-image-upload');
    });

    //For Setting
    Route::controller(SettingController::class)->group(function (){
        Route::middleware('can:setting-edit')->group(function () {
            Route::get("/admin/setting", "edit")->name("setting");
            Route::post("/admin/setting-update", "update")->name("setting-update");
        });
    });

    //For Symptom
    Route::controller(SymptomController::class)->group(function (){
        Route::middleware('can:symptom-add')->group(function () {
            Route::get("/admin/symptom-add", "create")->name("symptom-add");
            Route::post("/admin/symptom-insert", "insert")->name("symptom-insert");
        });
        Route::middleware('can:symptom-edit')->group(function () {
            Route::get("/admin/symptom-edit/{id}", "edit")->name("symptom-edit");
            Route::post("/admin/symptom-update", "update")->name("symptom-update");
        });
        Route::middleware('can:symptom-list')->group(function () {
            Route::get("/admin/symptom-list", "view")->name("symptom-list");
            Route::get("/admin/symptom-load-table", "load_table")->name("symptom-load-table");
        });
        Route::middleware('can:symptom-delete')->group(function () {
            Route::post("/admin/symptom-delete", "delete")->name("symptom-delete");
        });
        Route::post("/admin/symptom-change-status", "change_status")->name("symptom-change-status");
        Route::post("/admin/symptom-update-order", "update_order")->name("symptom-update-order");
        Route::post('/admin/symptom-image-upload', 'uploadImage')->name('symptom-image-upload');
    });

    //For Question Answer
    Route::controller(QuestionAnswerController::class)->group(function (){
        // Route::middleware('can:qa-add')->group(function () {
            Route::get("/admin/qa-add", "create")->name("qa-add");
            Route::post("/admin/qa-insert", "insert")->name("qa-insert");
        // });
        // Route::middleware('can:qa-edit')->group(function () {
            Route::get("/admin/qa-edit/{id}", "edit")->name("qa-edit");
            Route::post("/admin/qa-update", "update")->name("qa-update");
        // });
        // Route::middleware('can:qa-list')->group(function () {
            Route::get("/admin/qa-list", "view")->name("qa-list");
            Route::get("/admin/qa-load-table", "load_table")->name("qa-load-table");
        // });
        // Route::middleware('can:qa-delete')->group(function () {
            Route::post("/admin/qa-delete", "delete")->name("qa-delete");
        // });
    });

    //For Health Parameter
    Route::controller(HealthParameterController::class)->group(function (){
        Route::middleware('can:health-parameter-add')->group(function () {
            Route::get("/admin/health-parameter-add", "create")->name("health-parameter-add");
            Route::post("/admin/health-parameter-insert", "insert")->name("health-parameter-insert");
        });
        Route::middleware('can:health-parameter-edit')->group(function () {
            Route::get("/admin/health-parameter-edit/{id}", "edit")->name("health-parameter-edit");
            Route::post("/admin/health-parameter-update", "update")->name("health-parameter-update");
        });
        Route::middleware('can:health-parameter-list')->group(function () {
            Route::get("/admin/health-parameter-list", "view")->name("health-parameter-list");
            Route::get("/admin/health-parameter-load-table", "load_table")->name("health-parameter-load-table");
        });
        Route::middleware('can:health-parameter-delete')->group(function () {
            Route::post("/admin/health-parameter-delete", "delete")->name("health-parameter-delete");
        });
        Route::post("/admin/health-parameter-change-status", "change_status")->name("health-parameter-change-status");
        Route::post("/admin/health-parameter-update-order", "update_order")->name("health-parameter-update-order");
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
