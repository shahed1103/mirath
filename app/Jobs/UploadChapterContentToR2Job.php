<?php

namespace App\Jobs;

use App\Models\ChapterContent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class UploadChapterContentToR2Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public int $chapterContentId,
        public string $temporaryPath,
        public string $folder,
        public ?string $oldPath = null,
    ) {
    }

    public function handle(): void
    {
        $content = ChapterContent::find($this->chapterContentId);

        //Content no longer exists
        if (!$content) {
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

            $r2Path = trim($this->folder, '/') . '/' . $filename;

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
            $content->url = $r2Path;
            $content->upload_status = 'uploaded';
            $content->save();

            //Verify database update
            $content->refresh();

            if (
                $content->url !== $r2Path ||
                $content->upload_status !== 'uploaded'
            ) {
                throw new RuntimeException(
                    "Chapter content database update failed."
                );
            }

            //Delete old file only after everything succeeded
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
        ChapterContent::where(
            'id',
            $this->chapterContentId
        )->update([
            'upload_status' => 'failed',
        ]);

        report($exception);
    }
}