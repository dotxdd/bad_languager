<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClickupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrelloController;
use App\Http\Middleware\TenantMiddleware;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::middleware([TenantMiddleware::class])->group(function () {
    // Wszystkie routy, które wymagają wielodzierżawności
});
Route::get('trello-redirect', [TrelloController::class, 'showRedirectPage'])->name('trello.redirect');
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', AdminMiddleware::class])->group(function () {
    Route::get('/admin/users', [AdminController::class, 'index'])->name('admin.users');
    Route::post('/admin/users/{user}/login', [AdminController::class, 'loginAsUser'])->name('admin.users.login');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/connect-trello', function () {
        return view('trello-connection');
    })->name('trello.connection');
    Route::get('login/trello', [TrelloController::class, 'redirectToTrello'])->name('login.trello');
    Route::get('/trello-auth', [TrelloController::class, 'handleTrelloAuth'])->name('trello.auth');
    Route::get('/trello-redirect', [TrelloController::class, 'showRedirectPage'])->name('trello.redirect');
    Route::get('/trello/all-cards', [TrelloController::class, 'getAllCards']);
    Route::delete('/trello/disconnect', [TrelloController::class, 'deleteTrelloConnection'])->name('trello.disconnect');

    Route::get('/connect-clickup', function () {
        return view('clickup-connection');
    })->name('clickup.connection');
    Route::get('/clickup/authorize', [ClickUpController::class, 'redirectToClickUp'])->name('clickup.authorize');

// Route to handle ClickUp callback after authorization
    Route::get('/clickup/callback', [ClickUpController::class, 'handleCallback'])->name('clickup.callback');
    Route::delete('/clickup/disconnect', [ClickupController::class, 'deleteClickupConnection'])->name('clickup.disconnect');

    Route::prefix('clickup-data')->group(function () {

        Route::get('/whole/users', [\App\Http\Controllers\ClickupDataController::class, 'getWholeToxicUsers'])->name('clickup.toxic.whole.users');
        Route::get('/whole/users-monthly', [\App\Http\Controllers\ClickupDataController::class, 'getMonthlyWholeToxicUsers'])->name('clickup.toxic.whole.monthly');
        Route::get('/whole/tasks', [\App\Http\Controllers\ClickupDataController::class, 'getWholeTasksData'])->name('clickup.toxic.whole.tasks');
        Route::get('/whole/tasks-monthly', [\App\Http\Controllers\ClickupDataController::class, 'getWholeTasksDataMonth'])->name('clickup.toxic.whole.tasks.monthly');
        Route::get('/whole/comments', [\App\Http\Controllers\ClickupDataController::class, 'getWholeCommentsData'])->name('clickup.toxic.whole.comments');
        Route::get('/whole/comments-monthly', [\App\Http\Controllers\ClickupDataController::class, 'getWholeCommentsMonth'])->name('clickup.toxic.whole.comments.monthly');
    });
    Route::prefix('trello-data')->group(function () {

        Route::get('/whole-report', function () {
            return view('trello_whole_report');
        })->name('trello.whole.report');

        Route::get('/monthly-report', function () {
            return view('trello_monthly_report');
        })->name('trello.monthly.report');

        Route::get('/whole/users', [\App\Http\Controllers\TrelloDataController::class, 'getWholeToxicUsers'])->name('trello.toxic.whole.users');
        Route::get('/whole/users-monthly', [\App\Http\Controllers\TrelloDataController::class, 'getMonthlyWholeToxicUsers'])->name('trello.toxic.whole.monthly');
        Route::get('/whole/tasks', [\App\Http\Controllers\TrelloDataController::class, 'getWholeTasksData'])->name('trello.toxic.whole.tasks');
        Route::get('/whole/tasks-monthly', [\App\Http\Controllers\TrelloDataController::class, 'getWholeTasksDataMonth'])->name('trello.toxic.whole.tasks.monthly');
        Route::get('/whole/comments', [\App\Http\Controllers\TrelloDataController::class, 'getWholeCommentsData'])->name('trello.toxic.whole.comments');
        Route::get('/whole/comments-monthly', [\App\Http\Controllers\TrelloDataController::class, 'getWholeCommentsMonth'])->name('trello.toxic.whole.comments.monthly');
    });


    // Route for handling the callback after authentication
//    Route::get('login/trello/callback', [TrelloController::class, 'handleTrelloCallback'])->name('login.trello.callback');
});

require __DIR__.'/auth.php';
