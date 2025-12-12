<?php

namespace App\Filament\Parent\Pages;

use App\Models\Notice;
use Filament\Pages\Page;

class NoticesPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationLabel = 'নোটিশ';

    protected static ?string $title = 'নোটিশ ও ঘোষণা';

    protected static ?string $slug = 'notices';

    protected static string $view = 'filament.parent.pages.notices';

    protected static ?int $navigationSort = 6;

    public function getNotices()
    {
        return Notice::where('is_published', true)
            ->orderBy('publish_date', 'desc')
            ->take(20)
            ->get();
    }
}
