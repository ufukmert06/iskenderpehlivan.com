<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $publishedPosts = Post::where('status', 'published')->count();
        $draftPosts = Post::where('status', 'draft')->count();
        $totalPosts = Post::count();
        $totalCategories = Category::count();
        $totalTags = Tag::count();
        $totalUsers = User::count();

        return [
            Stat::make('Toplam Yazı', $totalPosts)
                ->description('Tüm yazılar')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary')
                ->chart([
                    $totalPosts > 7 ? $totalPosts - 6 : 0,
                    $totalPosts > 6 ? $totalPosts - 5 : 0,
                    $totalPosts > 5 ? $totalPosts - 4 : 0,
                    $totalPosts > 4 ? $totalPosts - 3 : 0,
                    $totalPosts > 3 ? $totalPosts - 2 : 0,
                    $totalPosts > 2 ? $totalPosts - 1 : 0,
                    $totalPosts,
                ]),

            Stat::make('Yayınlanan', $publishedPosts)
                ->description('Aktif yazılar')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([
                    $publishedPosts > 7 ? $publishedPosts - 6 : 0,
                    $publishedPosts > 6 ? $publishedPosts - 5 : 0,
                    $publishedPosts > 5 ? $publishedPosts - 4 : 0,
                    $publishedPosts > 4 ? $publishedPosts - 3 : 0,
                    $publishedPosts > 3 ? $publishedPosts - 2 : 0,
                    $publishedPosts > 2 ? $publishedPosts - 1 : 0,
                    $publishedPosts,
                ]),

            Stat::make('Taslak', $draftPosts)
                ->description('Bekleyen yazılar')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Kategoriler', $totalCategories)
                ->description('Toplam kategori')
                ->descriptionIcon('heroicon-m-folder')
                ->color('info'),

            Stat::make('Etiketler', $totalTags)
                ->description('Toplam etiket')
                ->descriptionIcon('heroicon-m-tag')
                ->color('success'),

            Stat::make('Kullanıcılar', $totalUsers)
                ->description('Kayıtlı kullanıcı')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
        ];
    }
}
