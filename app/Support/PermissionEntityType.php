<?php

namespace App\Support;

use App\Models\Access\Group;
use App\Models\Board\Board;
use App\Models\Board\Thread;
use App\Models\Dictionary\Word;
use App\Models\Economy\Company;
use App\Models\Economy\CompanyWorker;
use App\Models\Encyclopedia\Page;
use App\Models\Territory\Territory;
use App\Models\User;
use App\Models\User\Character;
use Illuminate\Database\Eloquent\Model;

enum PermissionEntityType: int
{
    case USER = 0;
    case THREAD = 1;
    case COMPANY = 2;
    case BOARD = 3;
    case GROUP = 4;
    case ENCYCLOPEDIA_PAGE = 5;
    case CHARACTER = 6;
    case COMPANY_WORKER = 8;
    case DICTIONARY_WORD = 9;
    case TERRITORY = 10;

    public function modelClass(): string
    {
        return match ($this) {
            self::USER => User::class,
            self::THREAD => Thread::class,
            self::COMPANY => Company::class,
            self::BOARD => Board::class,
            self::GROUP => Group::class,
            self::ENCYCLOPEDIA_PAGE => Page::class,
            self::CHARACTER => Character::class,
            self::COMPANY_WORKER => CompanyWorker::class,
            self::DICTIONARY_WORD => Word::class,
            self::TERRITORY => Territory::class,
        };
    }

    public static function fromModel(Model $model): ?self
    {
        return match (true) {
            $model instanceof User => self::USER,
            $model instanceof Thread => self::THREAD,
            $model instanceof Company => self::COMPANY,
            $model instanceof Board => self::BOARD,
            $model instanceof Group => self::GROUP,
            $model instanceof Page => self::ENCYCLOPEDIA_PAGE,
            $model instanceof Character => self::CHARACTER,
            $model instanceof CompanyWorker => self::COMPANY_WORKER,
            $model instanceof Word => self::DICTIONARY_WORD,
            $model instanceof Territory => self::TERRITORY,
            default => null,
        };
    }

    public static function fromDatabase(int|string|null $value): ?self
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (! is_numeric($value)) {
            return null;
        }

        return self::tryFrom((int) $value);
    }

    /**
     * @return array<int, class-string<Model>>
     */
    public static function morphMap(): array
    {
        $map = [];

        foreach (self::cases() as $type) {
            $map[$type->value] = $type->modelClass();
        }

        return $map;
    }

    /**
     * @return list<int>
     */
    public static function values(): array
    {
        return array_map(fn (self $type) => $type->value, self::cases());
    }
}
