<?php


namespace App\Services;
use App\Models\Exam;
use App\Models\CartItem;
use App\Models\LibraryBook;
use App\Models\User;
use Carbon\Carbon;
use App\Models\BookRedemption;
use Illuminate\Support\Facades\DB;

use App\Http\Resources\LibraryBookResource;
use App\Http\Resources\BookRedemptionResource;

class LibraryAdminService {


public function storeLibraryBook( $request): array
{
    $book = LibraryBook::create([
        'name' => $request->name,
        'author' => $request->author,
        'price' => $request->price,
        'count' => $request->count
    ]);

    return [
        'book' => $book,
        'message' => 'Book added successfully'
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
}
