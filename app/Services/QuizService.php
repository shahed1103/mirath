<?php


namespace App\Services;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Models\User;
use App\Models\Exam;
use App\Models\Chapter;
use App\Models\Question;
use App\Models\OpenQuestion;
use App\Models\QuestionChoice;
use App\Models\UserChapterProgress;
use App\Models\UserQusetionHistory;
use App\Http\Resources\QuizResultResource;
use App\Http\Resources\QuizQuestionResource;
use Exception;
use Throwable;
use DB;


class QuizService {
    private const QUESTIONS_PER_EXAM = 5;

    public function startQuiz($chapterId): array{
        $userId = auth()->id();

        $examQuery = Exam::where('user_id', $userId)
                        ->where('chapter_id', $chapterId);

        if ($examQuery->clone()->where('success', true)->exists()) {
            throw new Exception('You have already passed this chapter', 409);
        }

        if ($examQuery->clone()->where('status', 'active')->exists()) {
            throw new Exception('You already have an active quiz', 409);
        }

        $currentLevel = UserChapterProgress::where('user_id', $userId)
        ->where('chapter_id', $chapterId)
        ->value('current_level_score') ?? 400;

        $estimatedDuration = $this->calculateEstimatedDuration($currentLevel) + 10;

        $session = Exam::create([
            'user_id' => $userId,
            'chapter_id' => $chapterId,
            'questions_answered' => 0,
            'correct_answers' => 0,
            'estimated_duration' => $estimatedDuration,
            'current_level_score' => $currentLevel,
            'started_at' => now(),
            'status' => 'active'
        ]);

        $question = $this->generateQuiz($userId , $chapterId , $currentLevel);

        $data = [
            'session_id' => $session->id,
            'estimated_duration' => $estimatedDuration,
            'total_questions' => self::QUESTIONS_PER_EXAM,
            'question' => new QuizQuestionResource($question),
        ];

        return [
            'quiz' => $data,
            'message' => 'quiz generte'
        ];
    }

    private function generateQuiz($userId , $chapterId , $currentLevel) {
        $userQuestionsHistory = UserQusetionHistory::where('user_id', $userId)
                                        ->whereHas('question', function ($query) use ($chapterId) {
                                            $query->where('chapter_id', $chapterId);
                                        })
                                        ->pluck('question_id')
                                        ->all();

        $ranges = [
            50,
            100,
            200,
            300,
            500,
        ];

        $question = null;

        foreach ($ranges as $range) {

            $question = Question::with('choices')->where('chapter_id', $chapterId)
                ->whereNotIn('id', $userQuestionsHistory)
                ->whereBetween('difficulty_score', [
                        max(100, $currentLevel - $range),
                        min(900, $currentLevel + $range)
                    ])
                ->inRandomOrder()
                ->first();

            if ($question) {
                break;
            }
        }

        if (!$question) {
            $question = Question::with('choices')->where('chapter_id', $chapterId)
                            ->whereNotIn('id',$userQuestionsHistory)
                            ->orderByRaw('ABS(difficulty_score - ?)',[$currentLevel])
                            ->first();
        }

        if (!$question) {
            throw new Exception('No questions available for this chapter' , 404);
        }
        return $question;

    }

    private function calculateEstimatedDuration(int $currentLevel): int {
        if ($currentLevel <= 250) {
            $timePerQuestion = 20;
        }
        elseif ($currentLevel <= 400) {
            $timePerQuestion = 30;
        }
        elseif ($currentLevel <= 600) {
            $timePerQuestion = 45;
        }
        elseif ($currentLevel <= 750) {
            $timePerQuestion = 60;
        }
        else {
            $timePerQuestion = 90;
        }

        return $timePerQuestion * self::QUESTIONS_PER_EXAM;
    }

    public function submitAnswer($sessionId , $questionId , $choiceId): array {

        return DB::transaction(function () use ($sessionId, $questionId, $choiceId) {

            $userId = auth()->id();
            $session = Exam::findOrFail($sessionId);

            if ($result = $this->checkQuizTime($session)) {
                    return $result;
            }

            if ($session->status !== 'active') {
                throw new Exception('Quiz finished' , 409);
            }

            $question = Question::findOrFail($questionId);

            $correctChoice = QuestionChoice::where('question_id', $questionId)
                                        ->where('is_correct', true)
                                        ->first();

            $alreadyAnswered = UserQusetionHistory::where('user_id', $userId)
                ->where('question_id', $questionId)
                ->exists();

            if ($alreadyAnswered) {
                throw new Exception('Question already answered' , 409);
            }

            $isCorrect = $correctChoice->id == $choiceId;

            UserQusetionHistory::create([
                'user_id' => $userId,
                'question_id' => $questionId,
            ]);

            $currentLevel = $session->current_level_score;

            $difference = abs($question->difficulty_score - $currentLevel);

            $change = max(10,round($difference * 0.1));

            if ($isCorrect) {
                $currentLevel += $change;
                $session->correct_answers++;
            } else {
                $currentLevel -= $change;
            }

            $currentLevel = max(100,min(900, $currentLevel));

            $session->questions_answered++;
            $session->current_level_score = $currentLevel;
            $session->save();

            UserChapterProgress::updateOrCreate(
                [
                    'user_id' => $userId,
                    'chapter_id' => $session->chapter_id
                ],
                [
                    'current_level_score' => $currentLevel
                ]
            );

            if ($session->questions_answered >= self::QUESTIONS_PER_EXAM) {
                $result = $this->endQuiz($sessionId);
                    return $result;
            }

            $nextQuestion = $this->generateQuiz($userId , $session->chapter_id , $currentLevel);

            $data = [
                'is_correct' => $isCorrect,
                'questions_answered' => $session->questions_answered,
                'remaining_questions' => self::QUESTIONS_PER_EXAM - $session->questions_answered,
                'next_question' => new QuizQuestionResource($nextQuestion),

            ];
            if($isCorrect){
                $data['choice_id'] = $correctChoice->id;
            } else {
                    $data['choice_id'] = $choiceId;
                    $data['correct_choice_id'] = $correctChoice->id;
                    $data['explanation'] = $question->explanation;
            }
            return [
                'quiz' => $data,
                'message' => 'answered successfully'
            ];
        });
    }

    public function endQuiz($sessionId): array{

        $user = auth()->user();
        $session = Exam::findOrFail($sessionId);
        $session->update([
            'status' => 'finished',
            'finished_at' => now(),
            'success' => false,
        ]);

        $correctPercentage  = ($session->correct_answers *100)/self::QUESTIONS_PER_EXAM ;
        $isSuccess = $correctPercentage >= 60;
        $session->success = $isSuccess;

        $pointsEarned = 0;

        $currentChapter = Chapter::find($session->chapter_id);
        $nextChapter = Chapter::where('order_number', $currentChapter->order_number + 1)->first();

            if($isSuccess && $nextChapter){
                UserChapterProgress::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'chapter_id' => $nextChapter->id
                    ],
                    [
                        'is_open' => true
                    ]
                );

                $tryCount = Exam::where('user_id' , $user->id)->where('chapter_id' , $session->chapter_id)->count();

                if($tryCount == 1 && $correctPercentage >=90){
                    $pointsEarned = 3;
                    $user->increment('points', 3);
                }
            }
            $session->points = $pointsEarned;
            $session->save();

            $total_questions = self::QUESTIONS_PER_EXAM;

            $data = (new QuizResultResource($session))->additional([
                'total_questions' => $total_questions
            ]);

            return ['quiz' => $data , 'message' => 'quiz end'];
    }

    private function checkQuizTime(Exam $session) {
        $elapsed = now()->diffInSeconds($session->started_at, false);

        if ($elapsed >= $session->estimated_duration) {
            $result =  $this->endQuiz($session->id);

        return $result;
        }
    }

    public function quizResult($chapterId): array{
        $exam = Chapter::with('exams')->findOrFail($chapterId);

        $user = auth()->user();
        $passedExam = $exam->exams()
            ->where('user_id', $user->id)
            ->where('success', true)
            ->latest()
            ->first();

        $data =  new QuizResultResource($passedExam);
        return ['quiz' => $data , 'message' => 'quiz result'];
    }

    
    public function getOpenQuestion($chapterId, int $index = 0): array {
        $questionsCount = OpenQuestion::where('chapter_id', $chapterId)->count();

        if ($questionsCount === 0) {
            throw new Exception('No questions found', 404);
        }

        if ($index < 0 || $index >= $questionsCount) {
            throw new Exception('Question index out of range', 404);
        }

        $question = OpenQuestion::select('id', 'question_text')
            ->where('chapter_id', $chapterId)
            ->orderBy('order_number')
            ->offset($index)
            ->firstOrFail();

        $data = [
            'question' => [
                'id' => $question->id,
                'question_text' => $question->question_text,
            ],
            'navigation' => [
                'current_index' => $index,
                'total_questions' => $questionsCount,
                'has_previous' => $index > 0,
                'has_next' => $index < ($questionsCount - 1),
            ]
        ];
        return ['question' => $data , 'message' => 'question retrieved successfully'];
    }

    public function getAnswer($questionId): array {
        $question = OpenQuestion::select(
                'id',
                'question_text',
                'answer'
            )
            ->findOrFail($questionId);


        return [
            'answer' => $question->answer,
            'message' => 'answer retrieved successfully'
        ];
    }

}
