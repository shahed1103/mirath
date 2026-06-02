<?php


namespace App\Services;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Models\Book;
use App\Models\User;
use App\Models\Chapter;
use App\Models\Classification;
use App\Http\Resources\BookResource;
use App\Http\Resources\ChapterResource;
use App\Http\Resources\BookDetailsResource;
use Exception;
use Throwable;

class ExamService {
    // public function generateQuiz($chapterId): array{
    // }

    public function generateQuiz($chapter) {
        $userId = auth()->id();
        return DB::transaction(function () use ($userId, $chapter) {

            $progress = $this->getOrCreateProgress($userId, $chapter->id);

            $questions = $this->selectAdaptiveQuestions($userId, $chapter, $progress);

            $totalTime = $this->calculateExamTime($questions);

            $exam = Exam::create([
                'user_id' => $userId,
                'chapter_id' => $chapter->id,
                'started_at' => Carbon::now(),
                'status' => 'in_progress',
            ]);

            foreach ($questions as $index => $q) {
                $exam->questions()->create([
                    'question_id' => $q->id,
                    'order_number' => $index + 1,
                ]);
            }

            return [
                'exam' => $exam,
                'total_time' => $totalTime,
                'questions_count' => count($questions),
            ];
        });
    }

    public function selectAdaptiveQuestions($userId, $chapter, $progress) {
        $level = $progress->current_level_score ?? 500;

        $usedQuestions = UserQuestionHistory::where('user_id', $userId)
            ->where('chapter_id', $chapter->id)
            ->pluck('question_id');

        return Question::where('chapter_id', $chapter->id)
            ->whereNotIn('id', $usedQuestions)
            ->orderByRaw('ABS(difficulty_score - ?) ASC', [$level])
            ->limit(25)
            ->get();
    }

    public function calculateExamTime($questions) {
        return $questions->sum('estimated_time');
    }

    public function submitAnswer($exam, $questionId, $choiceId) {
        $examQuestion = $exam->questions()
            ->where('question_id', $questionId)
            ->first();

        $question = $examQuestion->question;

        $isCorrect = $question->choices()
            ->where('id', $choiceId)
            ->where('is_correct', true)
            ->exists();

        $examQuestion->update([
            'selected_choice_id' => $choiceId,
            'is_correct' => $isCorrect,
            'answered' => true,
            'answered_at' => Carbon::now(),
            'earned_score' => $isCorrect ? 1 : 0
        ]);

        return [
            'correct' => $isCorrect,
            'correct_answer' => $isCorrect
                ? null
                : $question->choices()->where('is_correct', true)->first(),
            'explanation' => $question->explanation
        ];
    }

    public function finishExam($exam) {
        $exam->update([
            'finished_at' => Carbon::now(),
            'status' => 'completed',
        ]);

        $this->calculateResult($exam);
    }

    public function calculateResult($exam) {
        $total = $exam->questions()->count();

        $correct = $exam->questions()
            ->where('is_correct', true)
            ->count();

        $score = $total > 0
            ? ($correct / $total) * 100
            : 0;

        $exam->update([
            'total_score' => $score,
            'passed' => $score >= 60
        ]);

        if ($exam->passed) {
            $this->unlockNextChapter($exam);
        }

        $this->updateStudentLevel($exam);

        $this->storeQuestionHistory($exam);
    }

    public function updateStudentLevel($exam) {
        $progress = UserChapterProgress::where([
            'user_id' => $exam->user_id,
            'chapter_id' => $exam->chapter_id,
        ])->first();

        $correctQuestions = $exam->questions()
            ->where('is_correct', true)
            ->get();

        if ($correctQuestions->count() == 0) {
            return;
        }

        $avgDifficulty = $correctQuestions
            ->avg(fn($q) => $q->question->difficulty_score);

        $oldLevel = $progress->current_level_score ?? 500;

        $performance = $exam->total_score / 100;

        $newLevel = $oldLevel +
            (($avgDifficulty - $oldLevel) * $performance * 0.3);

        $progress->update([
            'current_level_score' => $newLevel
        ]);
    }

    public function unlockNextChapter($exam) {
        $currentChapter = $exam->chapter;

        $nextChapter = $currentChapter->book
            ->chapters()
            ->where('order_number', '>', $currentChapter->order_number)
            ->orderBy('order_number')
            ->first();

        if ($nextChapter) {
            UserChapterProgress::updateOrCreate([
                'user_id' => $exam->user_id,
                'chapter_id' => $nextChapter->id,
            ], [
                'is_unlocked' => true
            ]);
        }
    }

    public function storeQuestionHistory($exam) {
        foreach ($exam->questions as $q) {
            UserQuestionHistory::updateOrCreate([
                'user_id' => $exam->user_id,
                'chapter_id' => $exam->chapter_id,
                'question_id' => $q->question_id,
            ], [
                'appeared_count' => DB::raw('appeared_count + 1'),
                'last_seen_at' => Carbon::now(),
                'answered_correctly' => $q->is_correct,
            ]);
        }
    }

    public function leaveExam($exam) {
        $exam->update([
            'status' => 'left',
            'finished_at' => Carbon::now(),
        ]);

        $this->calculateResult($exam);
    }

    public function expireExam($exam) {
        $exam->update([
            'status' => 'expired',
            'finished_at' => Carbon::now(),
        ]);

        $this->calculateResult($exam);
    }

    public function getOrCreateProgress($userId, $chapterId) {
        return UserChapterProgress::firstOrCreate([
            'user_id' => $userId,
            'chapter_id' => $chapterId,
        ], [
            'is_unlocked' => false,
            'is_completed' => false,
            'current_level_score' => 500,
            'attempts_count' => 0,
        ]);
    }
}