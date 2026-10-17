<?php

namespace App\Enums;

enum ProductType: string
{
    case Game = 'game';
    case Account = 'account';
    case GameAccount = 'game_account';
    case Joki = 'joki';

    public function label(): string
    {
        return match ($this) {
            self::Game => 'Top-Up Game',
            self::Account => 'Akun Premium',
            self::GameAccount => 'Jual Beli Akun Game',
            self::Joki => 'Jasa Joki Rank',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Game => 'indigo',
            self::Account => 'cyan',
            self::GameAccount => 'amber',
            self::Joki => 'emerald',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Game => '🎮',
            self::Account => '🍿',
            self::GameAccount => '👑',
            self::Joki => '⚔️',
        };
    }

    public function requiresPlayerId(): bool
    {
        return $this === self::Game;
    }

    public function isAccountStock(): bool
    {
        return in_array($this, [self::Account, self::GameAccount], true);
    }
}
