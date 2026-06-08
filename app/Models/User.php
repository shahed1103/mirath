<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens , HasFactory, Notifiable , HasRoles;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
        'name',
        'nick_name',
        'email',
        'password',
        'nationality_id',
        'age',
        'photo',
        'points'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    public function nationality(): BelongsTo{
        return $this->belongsTo(Nationality::class);
    }

    public function readingProgress(): HasMany{
        return $this->hasMany(ReadingProgress::class);
    }

    public function reviewChapters(){
        return $this->belongsToMany(Chapter::class,'chapter_user');
    }

    public function exams(): HasMany{
        return $this->hasMany(Exam::class);
    }

    public function chapterProgress(): HasMany {
        return $this->hasMany(UserChapterProgress::class);
    }

    public function questionHistory(): HasMany {
        return $this->hasMany(UserQuestionHistory::class);
    }

    public function summaries(): HasMany{
    return $this->hasMany(Summary::class);
    }

    public function progresses(){
    return $this->hasMany(ContentProgress::class);
    }
    
}
