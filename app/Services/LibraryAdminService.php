<?php


namespace App\Services;
use App\Models\Exam;
use App\Models\CartItem;
use App\Models\LibraryBook;
use App\Models\User;
use Carbon\Carbon;
use App\Models\BookRedemption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\LibraryBookResource;
use App\Http\Resources\BookRedemptionResource;
use Illuminate\Http\Request;
use App\Services\NotificationManager;

class LibraryAdminService {

    public function __construct(NotificationManager  $notificationManager){
        $this->notificationManager = $notificationManager;
    }


public function getAllLibraryBooks(): array
{
    return [
        'books' => LibraryBookResource::collection(
            LibraryBook::where('count', '>', 0)->get()
        ),
        'message' => 'Library books retrieved successfully',
    ];
}



public function storeLibraryBook($request): array
{
    $photo = null;

    if ($request->hasFile('photo')) {
        $photo = Storage::disk('r2')->putFile(
            'covers',
            $request->file('photo')
        );
    }

    $book = LibraryBook::create([
        'name'   => $request->name,
        'author' => $request->author,
        'price'  => $request->price,
        'count'  => $request->count,
        'photo'  => $photo,
    ]);

    return [
        'book' => $book,
        'message' => 'Book added successfully',
    ];
}


public function getAllBookRedemptions(): array
{
    $redemptions = BookRedemption::with(['user', 'book'])
        ->latest()
        ->get();

    return [
        'redemptions' => BookRedemptionResource::collection($redemptions),
        'message' => 'Book redemptions retrieved successfully.'
    ];
}



public function getMostRedeemedBooks(): array
{
    $books = BookRedemption::select(
            'library_book_id',
            DB::raw('COUNT(*) as redemption_count')
        )
        ->with('book:id,name')
        ->groupBy('library_book_id')
        ->orderByDesc('redemption_count')
        ->get();

    return [
        'books' => $books,
        'message' => 'Books redemption count retrieved successfully.'
    ];
}



public function getMonthlyRedeemedPoints(): array
{
    $points = BookRedemption::where(
            'created_at',
            '>=',
            Carbon::now()->subMonth()
        )
        ->sum('points_spent');

    return [
        'points' => $points,
        'message' => 'Monthly redeemed points retrieved successfully.'
    ];
}


public function getBookRedemptionStatistics(): array
{
    return [
        'total_redemptions' => BookRedemption::count(),

        'books_redeemed' => BookRedemption::distinct('library_book_id')->count(),

        'students_redeemed' => BookRedemption::distinct('user_id')->count(),

        'points_spent' => BookRedemption::sum('points_spent'),

        'message' => 'Statistics retrieved successfully.'
    ];
}




public function confirmBookRedemption(int $redemptionId): array
{
    return DB::transaction(function () use ($redemptionId) {

        $redemption = BookRedemption::where('id', $redemptionId)
            ->lockForUpdate()
            ->first();

        if (!$redemption) {
            throw new \Exception('Redemption request not found.');
        }

        if ($redemption->status === 'done') {
            throw new \Exception('This redemption request has already been confirmed.');
        }

        $user = User::where('id', $redemption->user_id)
            ->lockForUpdate()
            ->first();

        $book = LibraryBook::where('id', $redemption->library_book_id)
            ->lockForUpdate()
            ->first();

        if (!$book) {
            throw new \Exception('Book not found.');
        }

        if ($book->count <= 0) {
            throw new \Exception("Book '{$book->name}' is out of stock.");
        }

        if ($user->points < $redemption->points_spent) {
            throw new \Exception('The user no longer has enough points.');
        }

        $user->decrement('points', $redemption->points_spent);

        $book->decrement('count');

        $redemption->update([
            'status' => 'done',
        ]);


        $notificationRequest = new Request([
            'userId' => $redemption->user_id,
            'title' => "موافقة على طلب التبديل الكتاب",
            'body' => "يرجى مراجعة المكتبة في البرامكة، بجانب مشفى التوليد، خلال أوقات الدوام لإتمام عملية استلام الكتب من الساعة 10:00 صباحًا حتى 5:00 مساءً{$book->name}",
            'type' => 'confirm_book_redemption',
            'data' => []
        ]);

        $this->notificationManager->sendNotification($notificationRequest);

        return [
            'message' => 'Book redemption confirmed successfully.'
        ];
    });
}


public function getCompletedBookRedemptions(): array
{
$redemptions = BookRedemption::with([
    'user:id,name,email',
    'libraryBook:id,name,author,photo,price'
])
->where('status', 'done')
->latest()
->get();

    return [
        'book_redemptions' => $redemptions,
        'total' => $redemptions->count(),
    ];
}
}
