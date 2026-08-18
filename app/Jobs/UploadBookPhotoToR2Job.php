<?php

namespace App\Jobs;

use App\Models\Book;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class UploadBookPhotoToR2Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public int $bookId,
        public string $temporaryPath,
        public ?string $oldPath = null,
    ) {
    }

    public function handle(): void
    {
        $book = Book::find($this->bookId);

        //Book no longer exists
        if (!$book) {
            Storage::disk('local')->delete($this->temporaryPath);
            return;
        }

        //Check temporary file
        if (!Storage::disk('local')->exists($this->temporaryPath)) {
            throw new RuntimeException(
                "Temporary file not found: {$this->temporaryPath}"
            );
        }

        //Open temporary file
        $stream = Storage::disk('local')
            ->readStream($this->temporaryPath);

        if ($stream === false) {
            throw new RuntimeException(
                "Unable to read temporary file: {$this->temporaryPath}"
            );
        }

        try {

            //Generate R2 path
            $extension = pathinfo(
                $this->temporaryPath,
                PATHINFO_EXTENSION
            );

            $filename = uniqid('', true);

            if (!empty($extension)) {
                $filename .= '.' . $extension;
            }

            $r2Path = 'covers/' . $filename;

            //Upload to R2
            Storage::disk('r2')->writeStream(
                $r2Path,
                $stream
            );

            //Make sure upload really exists
            if (!Storage::disk('r2')->exists($r2Path)) {
                throw new RuntimeException(
                    "File was not found on R2 after upload: {$r2Path}"
                );
            }

            //Update database
            $book->photo = $r2Path;
            $book->photo_upload_status = 'uploaded';
            $book->save();

            //Verify database update
            $book->refresh();

            if (
                $book->photo !== $r2Path ||
                $book->photo_upload_status !== 'uploaded'
            ) {
                throw new RuntimeException(
                    "Book photo database update failed."
                );
            }

            //Delete old photo only after everything succeeded
            if (
                !empty($this->oldPath) &&
                $this->oldPath !== $r2Path
            ) {
                if (Storage::disk('r2')->exists($this->oldPath)) {
                    Storage::disk('r2')->delete($this->oldPath);
                }
            }

        } finally {

            //Close stream
            if (is_resource($stream)) {
                fclose($stream);
            }

            //Delete temporary local file
            if (
                Storage::disk('local')
                    ->exists($this->temporaryPath)
            ) {
                Storage::disk('local')->delete(
                    $this->temporaryPath
                );
            }
        }
    }

    public function failed(Throwable $exception): void
    {
        Book::where(
            'id',
            $this->bookId
        )->update([
            'photo_upload_status' => 'failed',
        ]);

        report($exception);
    }
}