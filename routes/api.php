<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DropDownController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\SummaryController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\ProfileController;




use App\Http\Controllers\ChapterReviewController;

/*
|--------------------------------------------------------------------------
| API Routes
|-------------------------------------------------------- ------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(AuthController::class)->group(function(){
    Route::post('register' , 'register')
          ->name('user.register');

    Route::post('signin' , 'signin')
    ->name('user.signin');

    Route::post('userForgotPassword' , 'userForgotPassword')
    ->name('user.password.email');

Route::post('userCheckCode' , 'userCheckCode')
->name('user.password.code.check');

Route::post('userResetPassword/{code}' , 'userResetPassword')
->name('user.password.reset');

Route::middleware('auth:sanctum')->get('logout', [AuthController::class, 'logout'])->name('user.logout');
Route::post('authGoogle', [AuthController::class, 'googleSignIn']);
Route::post('setPassword/{email}', [AuthController::class, 'setPassword']);
});

Route::get('getNationalities', [DropDownController::class, 'getNationalities']);

Route::get('getClassifications', [HomeController::class, 'getClassifications']);
Route::middleware('auth:sanctum')->get('getContinueReading', [HomeController::class, 'getContinueReading']);
Route::get('getFeatures', [HomeController::class, 'getFeatures']);
Route::middleware('auth:sanctum')->get('getHome', [HomeController::class, 'getHome']);
Route::middleware('auth:sanctum')->get('openContinueReading', [HomeController::class, 'openContinueReading']);
Route::middleware('auth:sanctum')->post('updateProgress/{contentId}', [HomeController::class, 'updateProgress']);

Route::middleware('auth:sanctum')->get('getClassificationDetails/{classificationId}', [BookController::class, 'getClassificationDetails']);
Route::middleware('auth:sanctum')->get('getBookDetails/{bookId}', [BookController::class, 'getBookDetails']);
Route::middleware('auth:sanctum')->get('getChapterDetails/{chapterId}', [BookController::class, 'getChapterDetails']);

Route::middleware('auth:sanctum')->get('addChapterToReviewList/{chapterId}', [ChapterReviewController::class, 'addChapterToReviewList']);
Route::middleware('auth:sanctum')->get('removeChapterFromReviewList/{chapterId}', [ChapterReviewController::class, 'removeChapterFromReviewList']);
Route::middleware('auth:sanctum')->get('getReviewList', [ChapterReviewController::class, 'getReviewList']);

Route::middleware('auth:sanctum')->get('startQuiz/{chapterId}', [QuizController::class, 'startQuiz']);
Route::middleware('auth:sanctum')->get('submitAnswer/{sessionId}/{questionId}/{choiceId}', [QuizController::class, 'submitAnswer']);
Route::middleware('auth:sanctum')->get('endQuiz/{sessionId}', [QuizController::class, 'endQuiz']);
Route::middleware('auth:sanctum')->get('quizResult/{chapterId}', [QuizController::class, 'quizResult']);
Route::middleware('auth:sanctum')->post('getOpenQuestion/{chapterId}', [QuizController::class, 'getOpenQuestion']);
// Route::middleware('auth:sanctum')->get('getAnswer/{questionId}', [QuizController::class, 'getAnswer']);

Route::middleware('auth:sanctum')->post('addSummary/{chapterId}', [SummaryController::class, 'addSummary']);
Route::middleware('auth:sanctum')->post('uploadSummary/{chapterId}', [SummaryController::class, 'uploadSummary']);
Route::middleware('auth:sanctum')->post('editSummary/{summaryId}', [SummaryController::class, 'editSummary']);
Route::middleware('auth:sanctum')->get('summaryDetails/{summaryId}', [SummaryController::class, 'summaryDetails']);
Route::middleware('auth:sanctum')->get('deleteSummary/{summaryId}', [SummaryController::class, 'deleteSummary']);
Route::middleware('auth:sanctum')->get('allCreatedSummary', [SummaryController::class, 'allCreatedSummary']);
Route::middleware('auth:sanctum')->get('allUploadedSummary', [SummaryController::class, 'allUploadedSummary']);

Route::middleware('auth:sanctum')->post('/meetings', [MeetingController::class, 'create_meet']);






Route::middleware('auth:sanctum')->get('/getStudentStatistics', [ProfileController::class, 'getStudentStatistics']);
Route::middleware('auth:sanctum')->get('/getAllLibraryBooks', [ProfileController::class, 'getAllLibraryBooks']);
Route::middleware('auth:sanctum')->post('/addBookToCart/{id}', [ProfileController::class, 'addBookToCart']);
Route::middleware('auth:sanctum')->get('/getMyPoints', [ProfileController::class, 'getMyPoints']);
Route::middleware('auth:sanctum')->get('/getCartItems', [ProfileController::class, 'getCartItems']);
Route::middleware('auth:sanctum')->post('/removeBookFromCart/{id}', [ProfileController::class, 'removeBookFromCart']);
Route::middleware('auth:sanctum')->post('/confirmBookRedemption', [ProfileController::class, 'confirmBookRedemption']);






