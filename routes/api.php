<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DosenController;
use App\Http\Controllers\API\MahasiswaController;
use App\Http\Controllers\API\KelasController;
use App\Http\Controllers\API\MagangController;
use App\Http\Controllers\API\PerusahaanController;
use App\Http\Controllers\dataMhsController;
use App\Http\Controllers\API\LowonganController;
use App\Http\Controllers\API\PeriodeController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\WilayahController;
use App\Http\Controllers\API\EvaluasiController;
use App\Http\Controllers\API\PlottingController;
use App\Http\Controllers\API\Dosen\DosenMahasiswaController as DosenMaha;
use App\Http\Controllers\API\Dosen\dashboardController as DosenDash;
use App\Http\Controllers\API\Dosen\DosenProfileController;
use App\Http\Controllers\API\Dosen\ProfileController as DosenProfile;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\EvaluasiMagangController;
use App\Http\Controllers\API\Admin\AdminMahasiswaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// =========================================================
// 1. PUBLIC ROUTES - No Authentication Required
// =========================================================
Route::get('/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
})->middleware('web');

Route::get('/wilayah', [WilayahController::class, 'index']);
Route::get('/dosen/with-perusahaan', [DosenController::class, 'withPerusahaan']);
Route::get('/dosen/with-details', [DosenController::class, 'withDetails']);
Route::get('/wilayah', [WilayahController::class, 'index']);

// =========================================================
// 2. AUTHENTICATION ROUTES
// =========================================================
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
});

// =========================================================
// 3. MAHASISWA ROUTES - For Mahasiswa Role (DIPINDAHKAN KE DEPAN)
// =========================================================
Route::middleware(['web', 'auth', 'role:mahasiswa'])->prefix('mahasiswa')->group(function () {
    // Lowongan routes
    Route::get('/lowongan', [App\Http\Controllers\API\Mahasiswa\MahasiswaLowonganController::class, 'index']);
     Route::get('/lowongan/active-period', [App\Http\Controllers\API\Mahasiswa\MahasiswaLowonganController::class, 'getActivePeriod']);
    Route::get('/lowongan/{id}', [App\Http\Controllers\API\Mahasiswa\MahasiswaLowonganController::class, 'show']);
    Route::get('/active-internship', [App\Http\Controllers\API\Mahasiswa\MahasiswaLowonganController::class, 'checkActiveInternship']);
    Route::post('/apply/{lowongan_id}', [App\Http\Controllers\API\Mahasiswa\MahasiswaLowonganController::class, 'applyLowongan']);
    Route::post('/apply-with-documents', [App\Http\Controllers\API\Mahasiswa\MahasiswaLowonganController::class, 'applyWithDocuments']);
     Route::get('lowongan/{lowongan_id}/application-status', [App\Http\Controllers\API\Mahasiswa\MahasiswaLowonganController::class, 'checkApplicationStatus']);
      Route::get('/applications/user', [App\Http\Controllers\API\Mahasiswa\MahasiswaLowonganController::class, 'getUserApplications']);
      Route::get('/{id_mahasiswa}/logbook', [App\Http\Controllers\API\Mahasiswa\LogbookController::class, 'getByMahasiswa']);


    // Profile management routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [App\Http\Controllers\API\MahasiswaController::class, 'getProfile']);
        Route::put('/', [App\Http\Controllers\API\MahasiswaController::class, 'updateProfile']);
        // ✅ FIXED: Skills routes untuk mahasiswa
        Route::get('/skills', [App\Http\Controllers\API\Mahasiswa\ProfileController::class, 'getSkills']);
        Route::post('/skills', [App\Http\Controllers\API\Mahasiswa\ProfileController::class, 'updateSkills']);

        // ✅ FIXED: Minat routes untuk mahasiswa
        Route::get('/minat', [App\Http\Controllers\API\Mahasiswa\ProfileController::class, 'getMinat']);
        Route::post('/minat', [App\Http\Controllers\API\Mahasiswa\ProfileController::class, 'updateMinat']);

        // ✅ FIXED: Profile update route
        Route::post('/update', [App\Http\Controllers\API\Mahasiswa\ProfileController::class, 'update']);
        Route::post('/avatar', [App\Http\Controllers\API\Mahasiswa\ProfileController::class, 'updateAvatar']);
        Route::post('/password', [App\Http\Controllers\API\Mahasiswa\ProfileController::class, 'updatePassword']);
        Route::post('/cv', [App\Http\Controllers\API\Mahasiswa\ProfileController::class, 'uploadCv']);
        Route::delete('/cv', [App\Http\Controllers\API\Mahasiswa\ProfileController::class, 'deleteCv']);

        Route::get('/documents', [App\Http\Controllers\API\Mahasiswa\MahasiswaLowonganController::class, 'getAvailableDocuments']);

    });

    // Notifications routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [App\Http\Controllers\API\NotificationController::class, 'index']);
        Route::get('/count', [App\Http\Controllers\API\NotificationController::class, 'getUnreadCount']);
        Route::get('/{id}', [App\Http\Controllers\API\NotificationController::class, 'show']);
        Route::post('/{id}/read', [App\Http\Controllers\API\NotificationController::class, 'markAsRead']);
        Route::post('/mark-all-read', [App\Http\Controllers\API\NotificationController::class, 'markAllAsRead']);
        Route::delete('/{id}', [App\Http\Controllers\API\NotificationController::class, 'destroy']);
        Route::delete('/', [App\Http\Controllers\API\NotificationController::class, 'clearAll']);
        Route::delete('/read', [App\Http\Controllers\API\NotificationController::class, 'clearRead']);
        Route::delete('/expired', [App\Http\Controllers\API\NotificationController::class, 'clearExpired']);
    });

    // ✅ CLEANED: Lamaran routes (menggabungkan duplikasi)
    Route::prefix('lamaran')->group(function () {
        Route::get('/data', [\App\Http\Controllers\API\Mahasiswa\MahasiswaLamaranController::class, 'getLamaranMahasiswa']);
        Route::get('/reload', [\App\Http\Controllers\API\Mahasiswa\MahasiswaLamaranController::class, 'reloadLamaranData']);
        Route::post('/submit', [\App\Http\Controllers\API\Mahasiswa\MahasiswaLamaranController::class, 'submitLamaran']);
        Route::delete('/{id}', [\App\Http\Controllers\API\Mahasiswa\MahasiswaLamaranController::class, 'cancelLamaran']);
        Route::get('/{id}/detail', [\App\Http\Controllers\API\Mahasiswa\MahasiswaLamaranController::class, 'getDetailLamaran']);
        Route::get('check/{id}', [\App\Http\Controllers\API\Mahasiswa\MahasiswaLamaranController::class, 'checkStatus']);

    });

    // ✅ FIXED: Logbook routes menggunakan controller yang benar
    Route::prefix('logbook')->group(function () {
        Route::get('/', [App\Http\Controllers\API\Mahasiswa\LogbookController::class, 'index']);
        Route::post('/', [App\Http\Controllers\API\Mahasiswa\LogbookController::class, 'store']);
        Route::delete('/{id}', [App\Http\Controllers\API\Mahasiswa\LogbookController::class, 'destroy']);
    });

    Route::prefix('recommendations')->group(function () {
        Route::get('/test', [App\Http\Controllers\API\Mahasiswa\RecommendationController::class, 'test']);
        // Basic endpoints
        Route::get('/', [App\Http\Controllers\API\Mahasiswa\RecommendationController::class, 'getRecommendations']);
        Route::delete('/cache', [App\Http\Controllers\API\Mahasiswa\RecommendationController::class, 'clearCache']);
        Route::get('/debug', [App\Http\Controllers\API\Mahasiswa\RecommendationController::class, 'debug']);
        Route::get('/debug-files', [App\Http\Controllers\API\Mahasiswa\RecommendationController::class, 'debugFiles']);

        // ✅ TAMBAH: Missing SPK routes
        Route::get('/saw', [App\Http\Controllers\API\Mahasiswa\RecommendationController::class, 'getSAWRecommendations']);
        Route::get('/stats', [App\Http\Controllers\API\Mahasiswa\RecommendationController::class, 'getStats']);
        Route::get('/analysis/{lowonganId}', [App\Http\Controllers\API\Mahasiswa\RecommendationController::class, 'getDetailedAnalysis']);

        // ✅ TAMBAH: Direct calculation endpoints
        Route::get('/edas/{mahasiswaId}', [App\Http\Controllers\API\Mahasiswa\RecommendationController::class, 'getEDASRecommendation']);
        Route::get('/saw/{mahasiswaId}', [App\Http\Controllers\API\Mahasiswa\RecommendationController::class, 'getSAWRecommendation']);
    });


     Route::prefix('lamaran')->group(function () {
        Route::delete('/{id}/cancel', [App\Http\Controllers\API\Mahasiswa\MahasiswaLamaranController::class, 'cancelLamaran']);
    });

    // Evaluasi routes
    Route::prefix('evaluasi')->group(function () {
        Route::get('/', [App\Http\Controllers\API\Mahasiswa\EvaluasiController::class, 'index']);
        Route::get('/filter-options', [App\Http\Controllers\API\Mahasiswa\EvaluasiController::class, 'getFilterOptions']);
    });

    // Magang completion routes
    Route::get('/magang/check-completion', [MahasiswaController::class, 'checkMagangCompletion']);
    Route::post('/magang/submit-final-evaluation', [MahasiswaController::class, 'submitFinalEvaluation']);
    // ✅ TAMBAH: Evaluasi Magang Routes
    Route::prefix('evaluasi-magang')->group(function () {
        Route::get('/check/{idMagang}', [App\Http\Controllers\EvaluasiMagangController::class, 'checkNeedEvaluation']);
        Route::post('/submit', [App\Http\Controllers\EvaluasiMagangController::class, 'submitEvaluasi']);
        Route::get('/status/{idMagang}', [App\Http\Controllers\EvaluasiMagangController::class, 'getEvaluasiStatus']);
    });

    // ✅ TAMBAH: Batch status check route
    Route::post('/lamaran/batch-status', [\App\Http\Controllers\API\Mahasiswa\MahasiswaLamaranController::class, 'batchCheckStatus']);
});

// =========================================================
// 4. ADMIN & SUPERADMIN ROUTES - Dashboard & Main Functionality
// =========================================================
Route::middleware(['api', 'web', 'auth:sanctum', 'role:admin,superadmin'])->group(function () {
    // Dashboard
    Route::get('/dashboard/active-period', [DashboardController::class, 'getActivePeriod']);
    Route::get('/dashboard/summary', [DashboardController::class, 'getSummary']);
    Route::get('/dashboard/latest-applications', [DashboardController::class, 'getLatestApplications']);

    // Admin Mahasiswa API Routes (for testing)
    Route::prefix('admin')->group(function () {
        Route::get('/mahasiswa', [AdminMahasiswaController::class, 'index']);
        Route::get('/mahasiswa/search', [AdminMahasiswaController::class, 'search']); // BEFORE {id}
        Route::get('/mahasiswa/filter/kelas', [AdminMahasiswaController::class, 'filterByKelas']); // BEFORE {id}
        Route::get('/mahasiswa/{id}', [AdminMahasiswaController::class, 'show']);
        Route::post('/mahasiswa', [AdminMahasiswaController::class, 'store']);
        Route::put('/mahasiswa/{id}', [AdminMahasiswaController::class, 'update']);
        Route::delete('/mahasiswa/{id}', [AdminMahasiswaController::class, 'destroy']);

        // --- Perusahaan CRUD routes for admin ---
        Route::get('/perusahaan', [PerusahaanController::class, 'getPerusahaanData']);
        Route::get('/perusahaan/{id}', [PerusahaanController::class, 'getDetailPerusahaan']);
        Route::post('/perusahaan', [PerusahaanController::class, 'store']);
        Route::put('/perusahaan/{id}', [PerusahaanController::class, 'update']);
        Route::delete('/perusahaan/{id}', [PerusahaanController::class, 'destroy']);
        Route::post('/tambah-perusahaan', [PerusahaanController::class, 'tambahPerusahaan']);

        // --- Lowongan CRUD routes for admin ---
        Route::get('/lowongan', [LowonganController::class, 'index']);
        Route::post('/lowongan', [LowonganController::class, 'store']);
        Route::get('/lowongan/{id}', [LowonganController::class, 'show']);
        Route::put('/lowongan/{id}', [LowonganController::class, 'update']);
        Route::delete('/lowongan/{id}', [LowonganController::class, 'destroy']);
    });

    // Mahasiswa Management (existing)
    Route::get('/export/pdf', [MahasiswaController::class, 'exportPDF']);
    Route::post('/import', [MahasiswaController::class, 'importCSV']);
    Route::get('/template', [MahasiswaController::class, 'downloadTemplate']);
    Route::get('/mahasiswa', [MahasiswaController::class, 'index']);
    Route::post('/mahasiswa', [MahasiswaController::class, 'store']);
    Route::get('/mahasiswa/{id}', [MahasiswaController::class, 'show']);
    Route::put('/mahasiswa/{id}', [MahasiswaController::class, 'update']);
    Route::delete('/mahasiswa/{id}', [MahasiswaController::class, 'destroy']);
    Route::get('/kelas-options', [MahasiswaController::class, 'getKelasOptions']);


    // Magang Management
    Route::get('/magang/available', [MagangController::class, 'getAvailable']);
    Route::get('/magang', [MagangController::class, 'index']);
    Route::get('/magang/{id}', [MagangController::class, 'show']);
    Route::post('/magang/{id}/accept', [MagangController::class, 'accept']);
    Route::post('/magang/{id}/reject', [MagangController::class, 'reject']);
    Route::post('/magang/assign-dosen/{id}', [MagangController::class, 'assignDosen']);
    Route::get('/magang/{id}/check-dosen', [MagangController::class, 'checkDosen']);
    Route::put('/magang/{id}/reject', [MagangController::class, 'reject']); // ✅ NEW: PUT method untuk reject
    Route::put('/magang/{id}/reactivate', [MagangController::class, 'reactivate']); // ✅ NEW: Reactivate route

    // Perusahaan Management
    Route::get('/perusahaan', [PerusahaanController::class, 'getPerusahaanData']);
    Route::get('/perusahaan/{id}', [PerusahaanController::class, 'getDetailPerusahaan']);
    Route::post('/perusahaan', [PerusahaanController::class, 'store']);
    Route::put('/perusahaan/{id}', [PerusahaanController::class, 'update']);
    Route::delete('/perusahaan/{id}', [PerusahaanController::class, 'destroy']);
    Route::post('/perusahaan/import', [PerusahaanController::class, 'import']);
    Route::get('/perusahaan/export/pdf', [PerusahaanController::class, 'exportPDF']);
    Route::post('/tambah-perusahaan', [PerusahaanController::class, 'tambahPerusahaan']);

    // Lowongan Management
    Route::get('/lowongan', [LowonganController::class, 'index']);
    Route::post('/lowongan', [LowonganController::class, 'store']);
    Route::get('/lowongan/{id}', [LowonganController::class, 'show']);
    Route::put('/lowongan/{id}', [LowonganController::class, 'update']);
    Route::delete('/lowongan/{id}', [LowonganController::class, 'destroy']);
    Route::get('/lowongan/{id}/capacity', [LowonganController::class, 'getAvailableCapacity']);
    Route::post('/lowongan/{id}/sync-capacity', [LowonganController::class, 'syncCapacity']);

    // Dosen Management
    Route::get('/dosen', [DosenController::class, 'index']);
    Route::post('/dosen', [DosenController::class, 'store']);
    Route::get('/dosen/{id}', [DosenController::class, 'show']);
    Route::put('/dosen/{id}', [DosenController::class, 'update']);
    Route::delete('/dosen/{id}', [DosenController::class, 'destroy']);
    Route::post('/dosen/import', [DosenController::class, 'import']);
    Route::get('/dosen/export/pdf', [DosenController::class, 'exportPDF']);
    Route::post('/dosen/{id}/remove-assignments', [DosenController::class, 'removeAssignments']);
    Route::post('/dosen/{id}/assign-mahasiswa', [DosenController::class, 'assignMahasiswa']);


    // Kelas Management
    Route::get('/kelas', [dataMhsController::class, 'getKelas']);
    Route::get('/kelas', [KelasController::class, 'index']);
    Route::post('/kelas', [KelasController::class, 'store']);
    Route::get('/kelas/{id}', [KelasController::class, 'show']);
    Route::put('/kelas/{id}', [KelasController::class, 'update']);
    Route::delete('/kelas/{id}', [KelasController::class, 'destroy']);

    // Periode Management
    Route::get('/periode', [PeriodeController::class, 'index']);
    Route::post('/periode', [PeriodeController::class, 'store']);
    Route::get('/periode/{id}', [PeriodeController::class, 'show']);
    Route::put('/periode/{id}', [PeriodeController::class, 'update']);
    Route::delete('/periode/{id}', [PeriodeController::class, 'destroy']);
    Route::post('/periode/set-active/{id}', [PeriodeController::class, 'setActive']);

    // Skill Management
    Route::get('/skill', [LowonganController::class, 'getSkill']);
    Route::get('/skills', [App\Http\Controllers\SkillController::class, 'getSkills']);
    Route::post('/skill', [App\Http\Controllers\SkillController::class, 'store']);
    Route::put('/skill/{id}', [App\Http\Controllers\SkillController::class, 'update']);
    Route::delete('/skill/{id}', [App\Http\Controllers\SkillController::class, 'destroy']);

    // Minat Management
    Route::get('/minat', [App\Http\Controllers\MinatController::class, 'getMinat']);
    Route::post('/minat', [App\Http\Controllers\MinatController::class, 'store']);
    Route::get('/minat/{id}', [App\Http\Controllers\MinatController::class, 'show']); // ✅ TAMBAH INI
    Route::put('/minat/{id}', [App\Http\Controllers\MinatController::class, 'update']);
    Route::delete('/minat/{id}', [App\Http\Controllers\MinatController::class, 'destroy']);
    // Evaluasi
    Route::get('/evaluasi', [EvaluasiController::class, 'index']);

    // Plotting
    Route::post('/plotting/auto', [PlottingController::class, 'autoPlot']);
    Route::get('/plotting/matrix', [PlottingController::class, 'getPlottingMatrixDetails']);
    Route::get('/plotting/matrix-decision', [PlottingController::class, 'getMatrix']);

    // Misc Options
    Route::get('/jenis', [LowonganController::class, 'getJenis']);
    Route::get('/prodi', [KelasController::class, 'getProdi']);
});

// =========================================================
// 5. SUPERADMIN-ONLY ROUTES
// =========================================================
Route::middleware(['api', 'web', 'auth:sanctum', 'role:superadmin'])->prefix('superadmin')->group(function () {
    // Admin Management - only accessible by superadmin
    Route::get('/admin', [AdminController::class, 'index']);
    Route::post('/admin', [AdminController::class, 'store']);
    Route::get('/admin/{id}', [AdminController::class, 'show']);
    Route::put('/admin/{id}', [AdminController::class, 'update']);
    Route::delete('/admin/{id}', [AdminController::class, 'destroy']);
});

// Route tambahan untuk mendapatkan mahasiswa bimbingan dosen
// =========================================================
// 6. Dosen-ONLY ROUTES
// =========================================================


// Dosen Dashboard Routes
Route::middleware(['api', 'web', 'auth:sanctum', 'role:dosen'])->group(function () {
    Route::get('/dosen/current', [DosenProfileController::class, 'getCurrentDosen']);
    Route::get('/dosen/dashboard/stats/{id_dosen}', [DosenDash::class, 'getStats']);
    Route::get('/dosen/dashboard/mahasiswa/{id_dosen}', [DosenDash::class, 'getMahasiswaBimbingan']);
    Route::get('/dosen/{id_dosen}/mahasiswa-bimbingan', [DosenMaha::class, 'getMahasiswaBimbingan']);
    Route::get('/perusahaan-list', [DosenMaha::class, 'getPerusahaanList']);
    Route::get('dosen/profile/data', [DosenProfile::class, 'getProfileData']);
    Route::get('dosen/profile/minat', [DosenProfile::class, 'getMinat']);
    Route::post('dosen/profile/minat', [DosenProfile::class, 'updateMinat']);
    Route::get('dosen/profile/skills', [DosenProfile::class, 'getSkills']);
    Route::post('dosen/profile/skills', [DosenProfile::class, 'updateSkills']);
    Route::post('/dosen/profile/update', [DosenProfile::class, 'update'])->name('dosen.profile.update');
    Route::get('/periode-list', [DosenMaha::class, 'getPeriodeList']);
    Route::get('/dosen/magang/{magangId}/evaluation-status', [DosenMaha::class, 'checkEvaluationStatus']);
    Route::get('/mahasiswa/{id}/info', [DosenMaha::class, 'getMahasiswaInfo']);
    Route::get('/mahasiswa/{id}/logbook', [DosenMaha::class, 'getMahasiswaLogbook']);
    Route::get('/mahasiswa/{id}/logbook/{id_log}', [DosenMaha::class, 'getMahasiswaLogbook']); // Add this new route
    Route::get('/mahasiswa/{id}/evaluasi', [DosenMaha::class, 'getMahasiswaEvaluasi']);
    Route::post('/mahasiswa/{id}/evaluasi', [DosenMaha::class, 'storeMahasiswaEvaluasi']);
});

// ✅ TAMBAH: Routes di section umum (bukan di prefix mahasiswa)
Route::middleware(['web', 'auth'])->group(function () {
    // ✅ EVALUASI MAGANG: Routes global (tidak dalam prefix mahasiswa)
    Route::prefix('evaluasi-magang')->group(function () {
        Route::get('/check/{idMagang}', [\App\Http\Controllers\EvaluasiMagangController::class, 'checkNeedEvaluation']);
        Route::post('/submit', [\App\Http\Controllers\EvaluasiMagangController::class, 'submitEvaluasi']);
        Route::get('/status/{idMagang}', [\App\Http\Controllers\EvaluasiMagangController::class, 'getEvaluasiStatus']);
    });
});
