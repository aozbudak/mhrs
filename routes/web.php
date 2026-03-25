<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\AdminDoctorController;
use App\Http\Controllers\Admin\AdminPanelController;
use App\Http\Controllers\Admin\AdminRandevuController;
use App\Http\Controllers\Musteri\MusteriPanelController;
use App\Models\Department;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing', [
        'departments' => Department::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(),
    ]);
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth:web')
    ->name('logout');

Route::post('/admin/logout', [LoginController::class, 'destroyAdmin'])
    ->middleware('auth:admin')
    ->name('admin.logout');

Route::middleware(['auth:web', 'patient'])->prefix('musteri')->name('musteri.')->group(function () {
    Route::get('/', [MusteriPanelController::class, 'index'])->name('panel');
    Route::get('/doktorlar', [MusteriPanelController::class, 'doktorlar'])->name('doktorlar');
    Route::get('/profil', [MusteriPanelController::class, 'profil'])->name('profil');
    Route::put('/profil', [MusteriPanelController::class, 'profilGuncelle'])->name('profil.guncelle');
    Route::get('/randevular/gecmis', [MusteriPanelController::class, 'gecmisRandevular'])->name('randevu.gecmis');
    Route::get('/randevu-al', [MusteriPanelController::class, 'randevuAlForm'])->name('randevu.al');
    Route::post('/randevu', [MusteriPanelController::class, 'randevuKaydet'])->name('randevu.kaydet');
    Route::delete('/randevular/{randevu}', [MusteriPanelController::class, 'randevuIptal'])->name('randevu.iptal');
});

Route::middleware(['auth:admin', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminPanelController::class, 'index'])->name('panel');
    Route::get('/randevular', [AdminRandevuController::class, 'index'])->name('randevular.index');
    Route::post('/randevular/{randevu}/tamamla', [AdminRandevuController::class, 'tamamla'])->name('randevular.tamamla');
    Route::delete('/randevular/{randevu}', [AdminRandevuController::class, 'destroy'])->name('randevular.destroy');
    Route::get('/doktorlar', [AdminDoctorController::class, 'index'])->name('doktorlar.index');
    Route::get('/doktorlar/yeni', [AdminDoctorController::class, 'create'])->name('doktorlar.create');
    Route::post('/doktorlar', [AdminDoctorController::class, 'store'])->name('doktorlar.store');
    Route::get('/doktorlar/{doktor}/duzenle', [AdminDoctorController::class, 'edit'])->name('doktorlar.edit');
    Route::put('/doktorlar/{doktor}', [AdminDoctorController::class, 'update'])->name('doktorlar.update');
    Route::delete('/doktorlar/{doktor}', [AdminDoctorController::class, 'destroy'])->name('doktorlar.destroy');
});
