<?php

namespace app\Data;

class BookProgress
{
    /**
     * رقم الفصل الحالي داخل Collection
     */
    public int $chapterIndex = 0;

    /**
     * الصفحة الحالية التي سنبدأ منها
     */
    public int $currentPage = 0;

    /**
     * هل انتهى الكتاب بالكامل؟
     */
    public bool $finished = false;


    public function finish(): void
{
    $this->finished = true;
}

    public function __construct(int $startPage)
    {
        $this->currentPage = $startPage;
    }


}
