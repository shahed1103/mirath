<?php


namespace App\Services;
use App\Models\Exam;
use App\Models\CartItem;
use App\Models\LibraryBook;
use App\Models\User;
use App\Models\BookRedemption;
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
    return [
        'books' => LibraryBookResource::collection(
            LibraryBook::where('count', '>', 0)->get()
        ),
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



public function requestBookRedemption(array $bookIds): array
{
    $user = User::findOrFail(auth()->id());

    $cartItems = CartItem::where('user_id', $user->id)
        ->whereIn('library_book_id', $bookIds)
        ->get();

    if ($cartItems->isEmpty()) {
        throw new \Exception('No selected books found in your cart.');
    }

    $books = LibraryBook::whereIn('id', $cartItems->pluck('library_book_id'))
        ->get()
        ->keyBy('id');

    $totalPoints = 0;

    foreach ($cartItems as $item) {

        $book = $books->get($item->library_book_id);

        if (!$book) {
            throw new \Exception('Book not found.');
        }

        if ($book->count <= 0) {
            throw new \Exception("Book '{$book->name}' is out of stock.");
        }

        $totalPoints += $book->price;
    }

    if ($user->points < $totalPoints) {
        throw new \Exception('Not enough points.');
    }

    foreach ($cartItems as $item) {

        $book = $books->get($item->library_book_id);

        $alreadyRequested = BookRedemption::where('user_id', $user->id)
            ->where('library_book_id', $book->id)
            ->where('status', 'pending')
            ->exists();

        if ($alreadyRequested) {
            throw new \Exception("You already have a pending request for '{$book->name}'.");
        }

        BookRedemption::create([
            'user_id' => $user->id,
            'library_book_id' => $book->id,
            'points_spent' => $book->price,
            'status' => 'pending',
        ]);
    }

    CartItem::where('user_id', $user->id)
        ->whereIn('library_book_id', $bookIds)
        ->delete();

return [
    'library_location' => 'البرامكة، بجانب مشفى التوليد',
    'working_hours' => 'من الساعة 10:00 صباحًا حتى 5:00 مساءً',
    'message' => 'تم إرسال طلب تبديل الكتب بنجاح. يرجى مراجعة المكتبة في البرامكة، بجانب مشفى التوليد، خلال أوقات الدوام لإتمام عملية استلام الكتب.'
];
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
}



