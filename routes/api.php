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
use App\Http\Controllers\StudyPlanController;
use App\Http\Controllers\LibraryAdminController;

use App\Http\Controllers\ContentAdminController;
use App\Http\Controllers\QuestionAdminController;
use App\Http\Controllers\UserAdminController;

use App\Http\Controllers\ChatController;
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
Route::get('getLevels', [DropDownController::class, 'getLevels']);
Route::middleware('auth:sanctum')->get('getBooks/{classificationId}', [DropDownController::class, 'getBooks'])->middleware('can:getBooks');
Route::middleware('auth:sanctum')->get('getChapters/{bookId}', [DropDownController::class, 'getChapters'])->middleware('can:getChapters');


Route::get('getClassifications', [HomeController::class, 'getClassifications']);
Route::middleware('auth:sanctum')->get('getContinueReading', [HomeController::class, 'getContinueReading'])->middleware('can:getContinueReading');
Route::middleware('auth:sanctum')->get('getFeatures', [HomeController::class, 'getFeatures'])->middleware('can:getFeatures');
Route::middleware('auth:sanctum')->get('getHome', [HomeController::class, 'getHome'])->middleware('can:getHome');
Route::middleware('auth:sanctum')->post('updateProgress/{contentId}', [HomeController::class, 'updateProgress'])->middleware('can:updateProgress');
Route::middleware('auth:sanctum')->post('addFeedback', [HomeController::class, 'addFeedback'])->middleware('can:addFeedback');

Route::middleware('auth:sanctum')->get('getClassificationDetails/{classificationId}', [BookController::class, 'getClassificationDetails'])->middleware('can:getClassificationDetails');
Route::middleware('auth:sanctum')->get('getBookDetails/{bookId}', [BookController::class, 'getBookDetails'])->middleware('can:getBookDetails');
Route::middleware('auth:sanctum')->get('getChapterDetails/{chapterId}', [BookController::class, 'getChapterDetails'])->middleware('can:getChapterDetails');

Route::middleware('auth:sanctum')->get('addChapterToReviewList/{chapterId}', [ChapterReviewController::class, 'addChapterToReviewList'])->middleware('can:addChapterToReviewList');
Route::middleware('auth:sanctum')->get('removeChapterFromReviewList/{chapterId}', [ChapterReviewController::class, 'removeChapterFromReviewList'])->middleware('can:removeChapterFromReviewList');
Route::middleware('auth:sanctum')->get('getReviewList', [ChapterReviewController::class, 'getReviewList'])->middleware('can:getReviewList');

Route::middleware('auth:sanctum')->get('startQuiz/{chapterId}', [QuizController::class, 'startQuiz'])->middleware('can:startQuiz');
Route::middleware('auth:sanctum')->get('submitAnswer/{sessionId}/{questionId}/{choiceId}', [QuizController::class, 'submitAnswer'])->middleware('can:submitAnswer');
Route::middleware('auth:sanctum')->get('endQuiz/{sessionId}', [QuizController::class, 'endQuiz'])->middleware('can:endQuiz');
Route::middleware('auth:sanctum')->get('quizResult/{chapterId}', [QuizController::class, 'quizResult'])->middleware('can:quizResult');
Route::middleware('auth:sanctum')->get('getOpenQuestion/{chapterId}', [QuizController::class, 'getOpenQuestion'])->middleware('can:getOpenQuestion');
// Route::middleware('auth:sanctum')->get('getAnswer/{questionId}', [QuizController::class, 'getAnswer'])->middleware('can:getAnswer');

Route::middleware('auth:sanctum')->post('addSummary/{chapterId}', [SummaryController::class, 'addSummary'])->middleware('can:addSummary');
Route::middleware('auth:sanctum')->post('uploadSummary/{chapterId}', [SummaryController::class, 'uploadSummary'])->middleware('can:uploadSummary');
Route::middleware('auth:sanctum')->post('editSummary/{summaryId}', [SummaryController::class, 'editSummary'])->middleware('can:editSummary');
Route::middleware('auth:sanctum')->get('summaryDetails/{summaryId}', [SummaryController::class, 'summaryDetails'])->middleware('can:summaryDetails');
Route::middleware('auth:sanctum')->get('deleteSummary/{summaryId}', [SummaryController::class, 'deleteSummary'])->middleware('can:deleteSummary');
Route::middleware('auth:sanctum')->get('allCreatedSummary', [SummaryController::class, 'allCreatedSummary'])->middleware('can:allCreatedSummary');
Route::middleware('auth:sanctum')->get('allUploadedSummary', [SummaryController::class, 'allUploadedSummary'])->middleware('can:allUploadedSummary');

Route::middleware('auth:sanctum')->get('getAllChats', [ChatController::class, 'getAllChats'])->middleware('can:getAllChats');
Route::middleware('auth:sanctum')->get('getChatMessages/{chatId}', [ChatController::class, 'getChatMessages'])->middleware('can:getChatMessages');
Route::middleware('auth:sanctum')->post('chat', [ChatController::class, 'chat'])->middleware('can:chat');

Route::middleware('auth:sanctum')->get('getAllUsers', [UserAdminController::class, 'getAllUsers'])->middleware('can:getAllUsers');
Route::middleware('auth:sanctum')->get('getAllFeedbacks', [UserAdminController::class, 'getAllFeedbacks'])->middleware('can:getAllFeedbacks');
Route::middleware('auth:sanctum')->get('userStatisticsOverview', [UserAdminController::class, 'userStatisticsOverview'])->middleware('can:userStatisticsOverview');

Route::middleware('auth:sanctum')->get('getBookDetailsAdmin/{bookId}', [ContentAdminController::class, 'getBookDetailsAdmin'])->middleware('can:getBookDetailsAdmin');
Route::middleware('auth:sanctum')->get('getChapterDetailsAdmin/{chapterId}', [ContentAdminController::class, 'getChapterDetailsAdmin'])->middleware('can:getChapterDetailsAdmin');
Route::middleware('auth:sanctum')->post('addNewClassification', [ContentAdminController::class, 'addNewClassification'])->middleware('can:addNewClassification');
Route::middleware('auth:sanctum')->post('addNewBook/{classificationId}', [ContentAdminController::class, 'addNewBook'])->middleware('can:addNewBook');
Route::middleware('auth:sanctum')->post('addNewChapter/{bookId}', [ContentAdminController::class, 'addNewChapter'])->middleware('can:addNewChapter');
Route::middleware('auth:sanctum')->post('editClassification/{classificationId}', [ContentAdminController::class, 'editClassification'])->middleware('can:editClassification');
Route::middleware('auth:sanctum')->post('editBook/{bookId}', [ContentAdminController::class, 'editBook'])->middleware('can:editBook');
Route::middleware('auth:sanctum')->post('editChapter/{chapterId}', [ContentAdminController::class, 'editChapter'])->middleware('can:editChapter');
Route::middleware('auth:sanctum')->post('editChapterContent/{contentId}', [ContentAdminController::class, 'editChapterContent'])->middleware('can:editChapterContent');
Route::middleware('auth:sanctum')->get('deleteClassification/{classificationId}', [ContentAdminController::class, 'deleteClassification'])->middleware('can:deleteClassification');
Route::middleware('auth:sanctum')->get('deleteBook/{bookId}', [ContentAdminController::class, 'deleteBook'])->middleware('can:deleteBook');
Route::middleware('auth:sanctum')->get('deleteChapter/{chapterId}', [ContentAdminController::class, 'deleteChapter'])->middleware('can:deleteChapter');

Route::middleware('auth:sanctum')->get('allChapterQuestionsWithAnswers/{chapterId}', [QuestionAdminController::class, 'allChapterQuestionsWithAnswers'])->middleware('can:allChapterQuestionsWithAnswers');
Route::middleware('auth:sanctum')->get('allChapterOpenQuestionsWithAnswers/{chapterId}', [QuestionAdminController::class, 'allChapterOpenQuestionsWithAnswers'])->middleware('can:allChapterOpenQuestionsWithAnswers');
Route::middleware('auth:sanctum')->post('addQuestionToChapter/{chapterId}', [QuestionAdminController::class, 'addQuestionToChapter'])->middleware('can:addQuestionToChapter');
Route::middleware('auth:sanctum')->post('addOpenQuestionToChapter/{chapterId}', [QuestionAdminController::class, 'addOpenQuestionToChapter'])->middleware('can:addOpenQuestionToChapter');
Route::middleware('auth:sanctum')->post('editQuestion/{questionId}', [QuestionAdminController::class, 'editQuestion'])->middleware('can:editQuestion');
Route::middleware('auth:sanctum')->post('editChoice/{choiceId}', [QuestionAdminController::class, 'editChoice'])->middleware('can:editChoice');
Route::middleware('auth:sanctum')->post('editOpenQuestion/{openQuestionId}', [QuestionAdminController::class, 'editOpenQuestion'])->middleware('can:editOpenQuestion');
Route::middleware('auth:sanctum')->get('deleteQuestion/{questionId}', [QuestionAdminController::class, 'deleteQuestion'])->middleware('can:deleteQuestion');
Route::middleware('auth:sanctum')->get('deleteChoice/{choiceId}', [QuestionAdminController::class, 'deleteChoice'])->middleware('can:deleteChoice');
Route::middleware('auth:sanctum')->get('deleteOpenQuestion/{openQuestionId}', [QuestionAdminController::class, 'deleteOpenQuestion'])->middleware('can:deleteOpenQuestion');


Route::middleware('auth:sanctum')->post('/meetings', [MeetingController::class, 'create_meet']);

Route::middleware('auth:sanctum')->get('/getStudentStatistics', [ProfileController::class, 'getStudentStatistics']);
Route::middleware('auth:sanctum')->get('/getAllLibraryBooks', [ProfileController::class, 'getAllLibraryBooks']);
Route::middleware('auth:sanctum')->post('/addBookToCart/{id}', [ProfileController::class, 'addBookToCart']);
Route::middleware('auth:sanctum')->get('/getMyPoints', [ProfileController::class, 'getMyPoints']);
Route::middleware('auth:sanctum')->get('/getCartItems', [ProfileController::class, 'getCartItems']);
Route::middleware('auth:sanctum')->post('/removeBookFromCart/{id}', [ProfileController::class, 'removeBookFromCart']);
Route::middleware('auth:sanctum')->post('/requestBookRedemption', [ProfileController::class, 'requestBookRedemption']);
Route::middleware('auth:sanctum')->get('/getLastUserExams', [ProfileController::class, 'getLastUserExams']);
Route::middleware('auth:sanctum')->get('/getAllUserExams', [ProfileController::class, 'getAllUserExams']);

Route::middleware('auth:sanctum')->post('/calculatePlan', [StudyPlanController::class, 'calculatePlan']);
Route::middleware('auth:sanctum')->post('/createPlan', [StudyPlanController::class, 'createPlan']);
Route::middleware('auth:sanctum')->post('/calculate', [StudyPlanController::class, 'calculate']);


Route::middleware('auth:sanctum')->post('/storeLibraryBook', [LibraryAdminController::class, 'storeLibraryBook']);
Route::middleware('auth:sanctum')->get('/getAllBookRedemptions', [LibraryAdminController::class, 'getAllBookRedemptions']);
Route::middleware('auth:sanctum')->get('/getMostRedeemedBooks', [LibraryAdminController::class, 'getMostRedeemedBooks']);
Route::middleware('auth:sanctum')->get('/getMonthlyRedeemedPoints', [LibraryAdminController::class, 'getMonthlyRedeemedPoints']);
Route::middleware('auth:sanctum')->get('/getBookRedemptionStatistics', [LibraryAdminController::class, 'getBookRedemptionStatistics']);
Route::middleware('auth:sanctum')->post('/confirmBookRedemption/{redemptionId}', [LibraryAdminController::class, 'confirmBookRedemption']);






