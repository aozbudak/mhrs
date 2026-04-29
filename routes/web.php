<?php

use App\Http\Controllers\Admin\AdminDoctorController;
use App\Http\Controllers\Admin\AdminHospitalController;
use App\Http\Controllers\Admin\AdminKurumGeocodeController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\AdminPanelController;
use App\Http\Controllers\Admin\AdminRandevuController;
use App\Http\Controllers\Admin\AdminSaglikMerkeziController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Hospital\HospitalPanelController;
use App\Http\Controllers\Hospital\HospitalRandevuController;
use App\Http\Controllers\Hospital\HospitalDepartmentHeadController;
use App\Http\Controllers\Hospital\SaglikMerkeziPanelController;
use App\Http\Controllers\Hospital\SaglikMerkeziRandevuController;
use App\Http\Controllers\Musteri\MusteriPanelController;
use App\Models\Department;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing', [
        'departments' => Department::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(),
    ]);
});

Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store']);

Route::middleware('guest:patient')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
    Route::middleware('throttle:45,1')->group(function () {
        Route::get('/register/ilceler', [RegisterController::class, 'districtsForCity'])->name('register.ilceler');
        Route::get('/register/aile-hekimleri', [RegisterController::class, 'aileHekimleriJson'])->name('register.aile-hekimleri');
    });
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth:patient')
    ->name('logout');

Route::post('/admin/logout', [LoginController::class, 'destroyAdmin'])
    ->middleware('auth:admin')
    ->name('admin.logout');

Route::post('/hastane/logout', [LoginController::class, 'destroyHospital'])
    ->middleware('auth:hospital')
    ->name('hastane.logout');

Route::post('/saglik-merkezi/logout', [LoginController::class, 'destroyHospital'])
    ->middleware('auth:hospital')
    ->name('saglik-merkezi.logout');

Route::middleware(['auth:patient', 'patient'])->prefix('musteri')->name('musteri.')->group(function () {
    Route::get('/', [MusteriPanelController::class, 'index'])->name('panel');
    Route::get('/favoriler', [MusteriPanelController::class, 'favoriler'])->name('favoriler');
    Route::post('/favoriler/hastane', [MusteriPanelController::class, 'favoriHastaneToggle'])->name('favori.hastane.toggle');
    Route::post('/favoriler/poliklinik', [MusteriPanelController::class, 'favoriPoliklinikToggle'])->name('favori.poliklinik.toggle');
    Route::get('/aile-hekimi', [MusteriPanelController::class, 'aileHekimi'])->name('aile-hekimi');
    Route::post('/aile-hekimi', [MusteriPanelController::class, 'aileHekimiKaydet'])->name('aile-hekimi.kaydet');
    Route::post('/aile-hekimi/kaldir', [MusteriPanelController::class, 'aileHekimiKaldir'])->name('aile-hekimi.kaldir');
    Route::middleware('throttle:45,1')->group(function () {
        Route::get('/aile-hekimi/ilceler', [MusteriPanelController::class, 'aileHekimiIlceler'])->name('aile-hekimi.ilceler');
        Route::get('/aile-hekimi/oneri', [MusteriPanelController::class, 'aileHekimiOneriJson'])->name('aile-hekimi.oneri');
    });
    Route::get('/yetkili-olduklarim', [MusteriPanelController::class, 'yetkiliOlduklarim'])->name('yetkili-olduklarim');
    Route::post('/yetkili-olduklarim', [MusteriPanelController::class, 'yetkiliHastaEkle'])->name('yetkili.hasta.ekle');
    Route::post('/yetkili-olduklarim/kaldir', [MusteriPanelController::class, 'yetkiliHastaKaldir'])->name('yetkili.hasta.kaldir');
    Route::get('/profil', [MusteriPanelController::class, 'profil'])->name('profil');
    Route::put('/profil', [MusteriPanelController::class, 'profilGuncelle'])->name('profil.guncelle');
    Route::get('/randevular/gecmis', [MusteriPanelController::class, 'gecmisRandevular'])->name('randevu.gecmis');
    Route::get('/bildirimler', [MusteriPanelController::class, 'bildirimler'])->name('bildirimler.index');
    Route::get('/randevu-al', [MusteriPanelController::class, 'randevuAlForm'])->name('randevu.al');
    Route::get('/bildirimler/json', [MusteriPanelController::class, 'notificationsJson'])->name('bildirimler.json');
    Route::post('/randevu', [MusteriPanelController::class, 'randevuKaydet'])->name('randevu.kaydet');
    Route::post('/randevu/katilim-bildirimi', [MusteriPanelController::class, 'randevuKatilimBildirimiKaydet'])->name('randevu.katilim-bildirimi');
    Route::delete('/randevular/{randevu}', [MusteriPanelController::class, 'randevuIptal'])->name('randevu.iptal');
});

Route::middleware(['auth:admin', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::middleware('throttle:24,1')->get('/kurum-konum-ara', AdminKurumGeocodeController::class)->name('kurum-konum-ara');
    Route::get('/', [AdminPanelController::class, 'index'])->name('panel');
    Route::get('/bildirimler', [AdminNotificationController::class, 'index'])->name('bildirimler.index');
    Route::post('/bildirimler', [AdminNotificationController::class, 'update'])->name('bildirimler.update');
    Route::get('/randevular', [AdminRandevuController::class, 'index'])->name('randevular.index');
    Route::post('/randevular/{randevu}/tamamla', [AdminRandevuController::class, 'tamamla'])->name('randevular.tamamla');
    Route::delete('/randevular/{randevu}', [AdminRandevuController::class, 'destroy'])->name('randevular.destroy');
    Route::get('/doktorlar', [AdminDoctorController::class, 'index'])->name('doktorlar.index');
    Route::get('/doktorlar/{doktor}/duzenle', [AdminDoctorController::class, 'edit'])->name('doktorlar.edit');
    Route::put('/doktorlar/{doktor}', [AdminDoctorController::class, 'update'])->name('doktorlar.update');
    Route::delete('/doktorlar/{doktor}', [AdminDoctorController::class, 'destroy'])->name('doktorlar.destroy');
    Route::get('/hastaneler', [AdminHospitalController::class, 'index'])->name('hastaneler.index');
    Route::get('/hastaneler/yeni', [AdminHospitalController::class, 'create'])->name('hastaneler.create');
    Route::post('/hastaneler', [AdminHospitalController::class, 'store'])->name('hastaneler.store');
    Route::get('/hastaneler/{hastane}/duzenle', [AdminHospitalController::class, 'edit'])->name('hastaneler.edit');
    Route::post('/hastaneler/{hastane}/doktorlar', [AdminHospitalController::class, 'storeDoctor'])->name('hastaneler.doktorlar.store');
    Route::post('/hastaneler/{hastane}/kurum-yoneticisi', [AdminHospitalController::class, 'storeHospitalAdmin'])->name('hastaneler.kurum-yoneticisi.store');
    Route::put('/hastaneler/{hastane}/kurum-yoneticileri/{kurumYoneticisi}', [AdminHospitalController::class, 'updateHospitalAdmin'])->name('hastaneler.kurum-yoneticisi.update');
    Route::delete('/hastaneler/{hastane}/kurum-yoneticileri/{kurumYoneticisi}', [AdminHospitalController::class, 'destroyHospitalAdmin'])->name('hastaneler.kurum-yoneticisi.destroy');
    Route::put('/hastaneler/{hastane}', [AdminHospitalController::class, 'update'])->name('hastaneler.update');
    Route::delete('/hastaneler/{hastane}', [AdminHospitalController::class, 'destroy'])->name('hastaneler.destroy');
    Route::get('/saglik-merkezleri', [AdminSaglikMerkeziController::class, 'index'])->name('saglik-merkezleri.index');
    Route::get('/saglik-merkezleri/yeni', [AdminSaglikMerkeziController::class, 'create'])->name('saglik-merkezleri.create');
    Route::post('/saglik-merkezleri', [AdminSaglikMerkeziController::class, 'store'])->name('saglik-merkezleri.store');
    Route::get('/saglik-merkezleri/{hastane}/duzenle', [AdminSaglikMerkeziController::class, 'edit'])->name('saglik-merkezleri.edit');
    Route::post('/saglik-merkezleri/{hastane}/kurum-yoneticisi', [AdminHospitalController::class, 'storeHospitalAdmin'])->name('saglik-merkezleri.kurum-yoneticisi.store');
    Route::put('/saglik-merkezleri/{hastane}/kurum-yoneticileri/{kurumYoneticisi}', [AdminHospitalController::class, 'updateHospitalAdmin'])->name('saglik-merkezleri.kurum-yoneticisi.update');
    Route::delete('/saglik-merkezleri/{hastane}/kurum-yoneticileri/{kurumYoneticisi}', [AdminHospitalController::class, 'destroyHospitalAdmin'])->name('saglik-merkezleri.kurum-yoneticisi.destroy');
    Route::post('/saglik-merkezleri/{hastane}/doktorlar', [AdminSaglikMerkeziController::class, 'storeDoctor'])->name('saglik-merkezleri.doktorlar.store');
    Route::put('/saglik-merkezleri/{hastane}', [AdminSaglikMerkeziController::class, 'update'])->name('saglik-merkezleri.update');
    Route::delete('/saglik-merkezleri/{hastane}', [AdminSaglikMerkeziController::class, 'destroy'])->name('saglik-merkezleri.destroy');
});

Route::middleware(['auth:hospital', 'hospital', 'managed_kind:hospital'])->prefix('hastane')->name('hastane.')->group(function () {
    Route::get('/', [HospitalPanelController::class, 'index'])->name('panel');
    Route::get('/doktorlar', [HospitalPanelController::class, 'doktorlar'])->name('doktorlar');
    Route::get('/profil', [HospitalPanelController::class, 'profil'])->name('profil');
    Route::put('/profil', [HospitalPanelController::class, 'profilGuncelle'])->name('profil.guncelle');
    Route::get('/ayarlar', [HospitalPanelController::class, 'ayarlar'])->name('ayarlar');
    Route::put('/ayarlar', [HospitalPanelController::class, 'update'])->name('ayarlar.guncelle');
    Route::post('/bolum-baskani', [HospitalPanelController::class, 'storeDepartmentHead'])->name('bolum-baskani.store');
    Route::delete('/bolum-baskanlari/{bolumBaskani}', [HospitalPanelController::class, 'destroyDepartmentHead'])->name('bolum-baskani.destroy');
    Route::get('/randevular', [HospitalRandevuController::class, 'index'])->name('randevular.index');
    Route::post('/randevular/{randevu}/tamamla', [HospitalRandevuController::class, 'tamamla'])->name('randevular.tamamla');
    Route::delete('/randevular/{randevu}', [HospitalRandevuController::class, 'destroy'])->name('randevular.destroy');
});

Route::middleware(['auth:hospital', 'hospital'])->prefix('bolum-baskanligi')->name('bolum-baskanligi.')->group(function () {
    Route::get('/', [HospitalDepartmentHeadController::class, 'index'])->name('panel');
    Route::get('/doktorlar', [HospitalDepartmentHeadController::class, 'doctors'])->name('doktorlar.index');
    Route::put('/doktorlar/secimden/randevu-erteleme', [HospitalDepartmentHeadController::class, 'setDoctorRandevuErtelemeGunBySelection'])->name('doktor.secim.randevu-erteleme');
    Route::put('/doktorlar/secimden/izin', [HospitalDepartmentHeadController::class, 'setDoctorLeaveBySelection'])->name('doktor.secim.izin');
    Route::delete('/doktorlar/secimden/izin', [HospitalDepartmentHeadController::class, 'removeDoctorLeaveBySelection'])->name('doktor.secim.izin.sil');
    Route::put('/ayarlar', [HospitalDepartmentHeadController::class, 'updateSettings'])->name('ayarlar.guncelle');
    Route::put('/doktorlar/{doctor}/izin', [HospitalDepartmentHeadController::class, 'setDoctorLeave'])->name('doktor.izin');
    Route::delete('/doktorlar/{doctor}/izin', [HospitalDepartmentHeadController::class, 'removeDoctorLeave'])->name('doktor.izin.sil');
});

Route::middleware(['auth:hospital', 'hospital', 'managed_kind:saglik_merkezi'])->prefix('saglik-merkezi')->name('saglik-merkezi.')->group(function () {
    Route::get('/', [SaglikMerkeziPanelController::class, 'index'])->name('panel');
    Route::get('/doktorlar', [SaglikMerkeziPanelController::class, 'doktorlar'])->name('doktorlar');
    Route::get('/profil', [SaglikMerkeziPanelController::class, 'profil'])->name('profil');
    Route::put('/profil', [SaglikMerkeziPanelController::class, 'profilGuncelle'])->name('profil.guncelle');
    Route::get('/ayarlar', [SaglikMerkeziPanelController::class, 'ayarlar'])->name('ayarlar');
    Route::put('/ayarlar', [SaglikMerkeziPanelController::class, 'update'])->name('ayarlar.guncelle');
    Route::get('/randevular', [SaglikMerkeziRandevuController::class, 'index'])->name('randevular.index');
    Route::post('/randevular/{randevu}/tamamla', [SaglikMerkeziRandevuController::class, 'tamamla'])->name('randevular.tamamla');
    Route::delete('/randevular/{randevu}', [SaglikMerkeziRandevuController::class, 'destroy'])->name('randevular.destroy');
});
