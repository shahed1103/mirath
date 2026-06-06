<?php


namespace App\Services;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Models\User;
use App\Models\Exam;
use App\Models\Chapter;
use App\Models\Question;
use App\Models\QuestionChoice;
use App\Models\UserChapterProgress;
use App\Models\UserQusetionHistory;
use Exception;
use Throwable;
use DB;


class QuizService {
    private const QUESTIONS_PER_EXAM = 5;
    
    public function startQuiz($chapterId): array{
        $userId = auth()->id();

        $activeExam = Exam::where('user_id', $userId)
        ->where('chapter_id', $chapterId)
        ->where('status', 'active')
        ->first();

        if ($activeExam) {
            throw new Exception('You already have an active quiz' , 409);
        }

        $currentLevel = UserChapterProgress::where('user_id',$userId)
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
            'question' => $question ,
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

            if($isCorrect){
                    $data = [
                    'is_correct' => $isCorrect,
                    'choice_id' => $correctChoice->id,
                    'next_question' => $nextQuestion,
                    'questions_answered' => $session->questions_answered,
                    'remaining_questions' => self::QUESTIONS_PER_EXAM - $session->questions_answered,
                    ];
                return ['quiz' => $data , 'message' => 'answered successfully'];
            } else {
                $data = [
                    'is_correct' => $isCorrect,
                    'choice_id' => $choiceId,
                    'correct_choice_id' => $correctChoice->id,
                    'explanation' => $question->explanation,
                    'next_question' => $nextQuestion,
                    'questions_answered' => $session->questions_answered,
                    'remaining_questions' => self::QUESTIONS_PER_EXAM - $session->questions_answered,
                ];
                return ['quiz' => $data , 'message' => 'answered successfully'];
            }
        });
    }

    public function endQuiz($sessionId): array{
        $point = 0;
        $userId = auth()->id();
        $user = auth()->user();
        $session = Exam::findOrFail($sessionId);
        $session->status = 'finished';
        $session->finished_at = now();
        $session->success = false;
        $session->save();

        if ($session->status === 'finished') {
            throw new Exception('Quiz already finished', 409);
        }

        $tryCount = Exam::where('user_id' , $userId)->where('chapter_id' , $session->chapter_id)->count();
        
        $user_points = $user->points;

        $correct_answers_pricent = ($session->correct_answers *100)/self::QUESTIONS_PER_EXAM ;
            if($correct_answers_pricent >= 60){
                $session->success = true;
                $session->save();

                $order_number = Chapter::where('id' , $session->chapter_id)->value('order_number');
                $new_chapters_open = Chapter::where('order_number' , $order_number+1)->firstOrFail();

                UserChapterProgress::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'chapter_id' => $new_chapters_open->id
                    ],
                    [
                        'is_open' => true
                    ]
                );  
                if($tryCount == 1){
                    $point = 3;
                    $user_points = $user_points + $point;  
                    $user->points =  $user_points;
                    $user->save();
                }
            }
            $data = [
                    'success' => $session->success,
                    'correct_answers' => $session->correct_answers,
                    'correct_answers_pricent' => $correct_answers_pricent,
                    'new_points' => $point ,
                    'all_user_points' => $user_points
            ];
            return ['quiz' => $data , 'message' => 'quiz end'];
    }

    private function checkQuizTime(Exam $session) {
        $elapsed = now()->diffInSeconds($session->started_at, false);

        if ($elapsed >= $session->estimated_duration) {
            $result =  $this->endQuiz($session->id);

        return $result;
        }
    }

}