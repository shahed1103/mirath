<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Chapter;
use App\Models\ChapterContent;
use App\Models\Classification;
use Illuminate\Support\Facades\Storage;

class StorageCleanupService
{

    public function deleteClassificationFiles(Classification $classification): void {
        $classification->load('books.chapters.contents');

        foreach ($classification->books as $book) {
            $this->deleteBookFiles($book);
        }
    }


    public function deleteBookFiles(Book $book): void {
        if ($book->photo) {
            Storage::disk('r2')->delete($book->photo);
        }

        foreach ($book->chapters as $chapter) {
            $this->deleteChapterFiles($chapter);
        }
    }


    public function deleteChapterFiles(Chapter $chapter): void {
        foreach ($chapter->contents as $content) {
            $this->deleteChapterContentFiles($content);
        }
    }


    public function deleteChapterContentFiles(ChapterContent $content): void {
        if ($content->type === 'video') {
            return;
        }

        if ($content->url) {
            Storage::disk('r2')->delete($content->url);
        }
    }

}