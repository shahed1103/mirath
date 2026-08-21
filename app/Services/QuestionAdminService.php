<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\Chapter;
use App\Models\Question;
use App\Models\QuestionChoice;
use App\Models\OpenQuestion;
use Exception;
use Throwable;

class QuestionAdminService {

    public function allChapterQuestionsWithAnswers($chapterId): array{
        $chapter = Chapter::with('questions.choices')->findOrFail($chapterId);

        $questionsWithAnswers = $chapter->questions->map(function ($question) {
            return [
                'id' => $question->id,
                'question_text' => $question->question_text,
                'explanation' => $question->explanation,
                'answers' => $question->choices->map(function ($choice) {
                    return [
                        'id' => $choice->id,
                        'answer_text' => $choice->choice_text,
                        'is_correct' => $choice->is_correct,
                    ];
                }),
            ];
        });

        return [
            'data' => $questionsWithAnswers,
            'message' => 'All questions with answers retrieved successfully.',
        ];
    }

    public function allChapterOpenQuestionsWithAnswers($chapterId): array{
        $chapter = Chapter::with('openQuestions')->findOrFail($chapterId);

        $openQuestionsWithAnswers = $chapter->openQuestions->map(function ($question) {
            return [
                'id' => $question->id,
                'question_text' => $question->question_text,
                'answer' => $question->answer,
            ];
        });

        return [
            'data' => $openQuestionsWithAnswers,
            'message' => 'All open questions with answers retrieved successfully.',
        ];
    }

    public function addQuestionToChapter($request , $chapterId): array{
        $chapter = Chapter::findOrFail($chapterId);

        $question = DB::transaction(function () use ($chapter, $request) {
            $question = Question::create([
                'chapter_id' => $chapter->id,
                'question_text' => $request->question_text,
                'explanation' => $request->explanation,
                'difficulty_score' => $request->difficulty_score,
            ]);

            foreach ($request->answers as $answer) {
                QuestionChoice::create([
                    'question_id' => $question->id,
                    'choice_text' => $answer['answer_text'],
                    'is_correct' => $answer['is_correct'],
                ]);
            }

            return $question;
        });

        return [
            'message' => 'Question and answers added successfully.',
            'data' => [
                'question' => $question,
                'answers' => $question->choices,
            ],
        ];
    }

    public function addOpenQuestionToChapter($request , $chapterId): array{
        $chapter = Chapter::findOrFail($chapterId);

        $nextOrderNumber = OpenQuestion::where('chapter_id', $chapter->id)
            ->max('order_number');

        $nextOrderNumber = $nextOrderNumber ? $nextOrderNumber + 1 : 1;

        $openQuestion = OpenQuestion::create([
            'chapter_id' => $chapter->id,
            'question_text' => $request->question_text,
            'answer' => $request->answer,
            'order_number' => $nextOrderNumber,
        ]);

        return [
            'message' => 'Open question added successfully.',
            'data' => $openQuestion,
        ];
    }

    public function editQuestion($request , $questionId): array{
        $question = Question::findOrFail($questionId);

        $question->update([
                'question_text' => $request->question_text ?? $question->question_text,
                'explanation' => $request->explanation ?? $question->explanation,
                'difficulty_score' => $request->difficulty_score ?? $question->difficulty_score,
            ]);

        $question->refresh();

        return [
            'message' => 'Question updated successfully.',
            'data' => [
                'question' => $question,
            ],
        ];
    }

    public function editChoice($request, $choiceId): array{
        $choice = QuestionChoice::findOrFail($choiceId);

        if ($request->has('is_correct') && $request->is_correct) {

            QuestionChoice::where('question_id', $choice->question_id)
                ->update([
                    'is_correct' => false
                ]);

            $choice->is_correct = true;
        }

        if (
            $choice->is_correct &&
            $request->has('is_correct') &&
            !$request->is_correct
        ) {
            throw new Exception('You cannot remove the correct answer. Select another choice as correct instead.', 422);
        }

        if ($request->filled('choice_text')) {
            $choice->choice_text = $request->choice_text;
        }

        $choice->save();

        return [
            'message' => 'Choice updated successfully.',
            'data' => $choice,
        ];
    }

    public function editOpenQuestion($request , $openQuestionId): array{
        $openQuestion = OpenQuestion::findOrFail($openQuestionId);

        $openQuestion->update([
            'question_text' => $request->question_text ?? $openQuestion->question_text,
            'answer' => $request->answer ?? $openQuestion->answer,
        ]);

        return [
            'message' => 'Open question updated successfully.',
            'data' => $openQuestion,
        ];
    }

    public function deleteQuestion($questionId): array{
            $question = Question::findOrFail($questionId);
            $question->delete();

        return [
            'data' => $question,
            'message' => 'Question and its choices deleted successfully.',
        ];
    }

    public function deleteChoice($choiceId): array {
        $choice = QuestionChoice::findOrFail($choiceId);

        $choicesCount = QuestionChoice::where('question_id', $choice->question_id)->count();

        if ($choicesCount <= 2) {
            throw new Exception('A question must have at least two choices.', 422);
        }

        if ($choice->is_correct) {
            throw new \Exception('The correct choice cannot be deleted. Assign another correct choice first.' , 422);
        }

        $choice->delete();

        return [
            'message' => 'Choice deleted successfully.',
            'data' => $choice,
        ];
    }  

    public function deleteOpenQuestion($openQuestionId): array{
        $openQuestion = OpenQuestion::findOrFail($openQuestionId);
        $openQuestion->delete();

        return [
            'data' => $openQuestion,
            'message' => 'Open question deleted successfully.',
        ];
    }
}