<?php


namespace App\Services;
use App\Models\Exam;
use App\Models\CartItem;
use App\Models\LibraryBook;
//use App\Models\BookRedemption;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\LibraryBookResource;

class ProfileService {

public function getStudentStatistics(): array
{

    $points = User::where('id', auth()->id())->value('points') ?? 0;
    $successfulExams = Exam::where('user_id', auth()->id())
        ->where('success', true);

    $successfulExamsCount = (clone $successfulExams)->count();
    $averageCorrectAnswers = (clone $successfulExams)->avg('correct_answers');

    $averagePercentage = $averageCorrectAnswers
        ? round(($averageCorrectAnswers / 25) * 100, 2)
        : 0;

    return [
        'statistics' => [
            'successful_exams_count' => $successfulExamsCount,
            'average_percentage' => $averagePercentage,
            'hours_study' => 20,
            'tasks_completed' => 2,
            'all_tasks' => 10,
            'points' => $points
        ],
        'message' => 'Student statistics retrieved successfully'
    ];
}

public function getMyPoints(): array
{
    $points = User::where('id', auth()->id())->get('points');
    return [
        'points' => $points,
        'message' => 'Student points retrieved successfully'
    ];
}


public function getAllLibraryBooks(): array
{
    $books = LibraryBook::all();

    return [
        'books' => LibraryBookResource::collection($books),
        'message' => 'Library books retrieved successfully'
    ];
}



public function addBookToCart($bookId): array
{
    $userId = auth()->id();
    LibraryBook::findOrFail($bookId);
    $exists = CartItem::where('user_id', $userId)
        ->where('library_book_id', $bookId)
        ->exists();
    if ($exists) {
        throw new \Exception('Book already exists in cart');
    }
    CartItem::create([
        'user_id' => $userId,
        'library_book_id' => $bookId,
    ]);

    return [
        'message' => 'Book added to cart successfully'
    ];
}


public function getCartItems(): array
{
    $cartItems = CartItem::with('book')
        ->where('user_id', auth()->id())
        ->get();

    $totalPoints = $cartItems->sum(function ($item) {
        return $item->book->price;
    });

    return [
        'cart_items' => $cartItems,
        'total_points' => $totalPoints,
        'message' => 'Cart retrieved successfully'
    ];
}


public function removeBookFromCart($bookId): array
{
    $cartItem = CartItem::where('user_id', auth()->id())
        ->where('library_book_id', $bookId)
        ->firstOrFail();

    $cartItem->delete();

    return [
        'message' => 'Book removed from cart successfully'
    ];
}



public function confirmBookRedemption(array $bookIds): array
{
    return DB::transaction(function () use ($bookIds) {

        $user = User::findOrFail(auth()->id());

        $cartItems = CartItem::where('user_id', $user->id)
            ->whereIn('library_book_id', $bookIds)
            ->with('book')
            ->get();

        if ($cartItems->isEmpty()) {
            throw new \Exception('No selected books found in your cart.');
        }

        $totalPoints = $cartItems->sum(function ($item) {
            return $item->book->price;
        });

        if ($user->points < $totalPoints) {
            throw new \Exception('Not enough points.');
        }

        $user->decrement('points', $totalPoints);
        CartItem::where('user_id', $user->id)
            ->whereIn('library_book_id', $bookIds)
            ->delete();

        return [
            // 'total_points_spent' => $totalPoints,
            // 'remaining_points' => $user->fresh()->points,
            'message' => 'Books redeemed successfully.'
        ];
    });
}

public function getLastUserExams(int $limit = 3): array
    {
       $exams = Exam::where('user_id', auth()->id())
            ->latest('started_at')
            ->take($limit)
            ->get()
            ->map(function ($exam) {
                $percentage = ($exam->questions_answered > 0)
                    ? ($exam->correct_answers / $exam->questions_answered) * 100
                    : 0;
                return [
                    'score_percentage' => round($percentage, 2) . '%',
                    'date' => $exam->started_at,
                    'book_name' => $exam-> chapter->book-> title,
                ];
            })
            ->toArray();

        return [
            'data' => $exams,
            'message' => 'Last exams retrieved successfully'
        ];
    }




    public function getAllUserExams(): array
    {
       $exams = Exam::where('user_id', auth()->id())
            ->get()
            ->map(function ($exam) {
                $percentage = ($exam->questions_answered > 0)
                    ? ($exam->correct_answers / $exam->questions_answered) * 100
                    : 0;
                return [
                    'score_percentage' => round($percentage, 2) . '%',
                    'date' => $exam->started_at
                ];
            })
            ->toArray();

        return [
            'data' => $exams,
            'message' => 'Last exams retrieved successfully'
        ];
    }
}



