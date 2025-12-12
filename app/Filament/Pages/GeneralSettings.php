<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class GeneralSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'সেটিংস';

    protected static ?string $navigationLabel = 'সাধারণ সেটিংস';

    protected static ?string $title = 'সাধারণ সেটিংস';

    protected static ?int $navigationSort = 0;

    protected static string $view = 'filament.pages.general-settings';

    // Form data
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->getSettings());
    }

    protected function getSettings(): array
    {
        return [
            // Institution Info
            'institution_name' => Setting::getValue('institution_name', ''),
            'institution_name_en' => Setting::getValue('institution_name_en', ''),
            'institution_slogan' => Setting::getValue('institution_slogan', ''),
            'institution_type' => Setting::getValue('institution_type', 'madrasah'),
            'eiin' => Setting::getValue('eiin', ''),
            'established_year' => Setting::getValue('established_year', ''),

            // Contact Info
            'address' => Setting::getValue('address', ''),
            'phone' => Setting::getValue('phone', ''),
            'phone2' => Setting::getValue('phone2', ''),
            'email' => Setting::getValue('email', ''),
            'website' => Setting::getValue('website', ''),

            // Social Links
            'facebook' => Setting::getValue('facebook', ''),
            'youtube' => Setting::getValue('youtube', ''),
            'twitter' => Setting::getValue('twitter', ''),

            // Logo & Favicon
            'logo' => Setting::getValue('logo', ''),
            'logo_dark' => Setting::getValue('logo_dark', ''),
            'favicon' => Setting::getValue('favicon', ''),

            // Academic Settings
            'current_academic_year' => Setting::getValue('current_academic_year', ''),
            'passing_marks' => Setting::getValue('passing_marks', '33'),
            'attendance_required_percent' => Setting::getValue('attendance_required_percent', '75'),
            'late_fee_percent' => Setting::getValue('late_fee_percent', '5'),
            'late_fee_days' => Setting::getValue('late_fee_days', '10'),

            // SMS Settings
            'sms_enabled' => Setting::getValue('sms_enabled', '0'),
            'sms_gateway' => Setting::getValue('sms_gateway', ''),
            'sms_api_key' => Setting::getValue('sms_api_key', ''),
            'sms_sender_id' => Setting::getValue('sms_sender_id', ''),

            // Email Settings
            'mail_driver' => Setting::getValue('mail_driver', 'smtp'),
            'mail_host' => Setting::getValue('mail_host', ''),
            'mail_port' => Setting::getValue('mail_port', '587'),
            'mail_username' => Setting::getValue('mail_username', ''),
            'mail_password' => Setting::getValue('mail_password', ''),
            'mail_from_address' => Setting::getValue('mail_from_address', ''),
            'mail_from_name' => Setting::getValue('mail_from_name', ''),

            // Payment Settings
            'bkash_enabled' => Setting::getValue('bkash_enabled', '0'),
            'bkash_app_key' => Setting::getValue('bkash_app_key', ''),
            'bkash_app_secret' => Setting::getValue('bkash_app_secret', ''),
            'nagad_enabled' => Setting::getValue('nagad_enabled', '0'),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Settings')
                    ->tabs([
                        // ==========================================
                        // TAB 1: INSTITUTION INFO
                        // ==========================================
                        Forms\Components\Tabs\Tab::make('প্রতিষ্ঠান')
                            ->icon('heroicon-o-building-office')
                            ->schema([
                                Forms\Components\Section::make('প্রতিষ্ঠানের তথ্য')
                                    ->description('মাদরাসার মূল তথ্য')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('institution_name')
                                                    ->label('প্রতিষ্ঠানের নাম (বাংলা)')
                                                    ->required()
                                                    ->placeholder('জামিয়া ইসলামিয়া মাদরাসা'),

                                                Forms\Components\TextInput::make('institution_name_en')
                                                    ->label('প্রতিষ্ঠানের নাম (English)')
                                                    ->placeholder('Jamia Islamia Madrasah'),
                                            ]),

                                        Forms\Components\TextInput::make('institution_slogan')
                                            ->label('স্লোগান/মটো')
                                            ->placeholder('জ্ঞানই শক্তি'),

                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\Select::make('institution_type')
                                                    ->label('প্রতিষ্ঠানের ধরণ')
                                                    ->options([
                                                        'madrasah' => 'মাদরাসা',
                                                        'hifz' => 'হিফজখানা',
                                                        'nurani' => 'নূরানী মাদরাসা',
                                                        'qawmi' => 'কওমী মাদরাসা',
                                                        'alia' => 'আলিয়া মাদরাসা',
                                                    ])
                                                    ->native(false),

                                                Forms\Components\TextInput::make('eiin')
                                                    ->label('EIIN নম্বর'),

                                                Forms\Components\TextInput::make('established_year')
                                                    ->label('প্রতিষ্ঠার সাল')
                                                    ->numeric()
                                                    ->placeholder('1990'),
                                            ]),
                                    ]),

                                Forms\Components\Section::make('যোগাযোগের তথ্য')
                                    ->schema([
                                        Forms\Components\Textarea::make('address')
                                            ->label('ঠিকানা')
                                            ->rows(2),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('phone')
                                                    ->label('ফোন-১')
                                                    ->tel()
                                                    ->prefix('+880'),

                                                Forms\Components\TextInput::make('phone2')
                                                    ->label('ফোন-২')
                                                    ->tel()
                                                    ->prefix('+880'),
                                            ]),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('email')
                                                    ->label('ইমেইল')
                                                    ->email(),

                                                Forms\Components\TextInput::make('website')
                                                    ->label('ওয়েবসাইট')
                                                    ->url()
                                                    ->prefix('https://'),
                                            ]),
                                    ]),

                                Forms\Components\Section::make('সোশ্যাল মিডিয়া')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('facebook')
                                                    ->label('Facebook')
                                                    ->url()
                                                    ->prefix('https://'),

                                                Forms\Components\TextInput::make('youtube')
                                                    ->label('YouTube')
                                                    ->url()
                                                    ->prefix('https://'),

                                                Forms\Components\TextInput::make('twitter')
                                                    ->label('X (Twitter)')
                                                    ->url()
                                                    ->prefix('https://'),
                                            ]),
                                    ])
                                    ->collapsed(),
                            ]),

                        // ==========================================
                        // TAB 2: LOGO & BRANDING
                        // ==========================================
                        Forms\Components\Tabs\Tab::make('লোগো')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Forms\Components\Section::make('লোগো ও ফেভিকন')
                                    ->description('প্রতিষ্ঠানের লোগো আপলোড করুন')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\FileUpload::make('logo')
                                                    ->label('লোগো (Light)')
                                                    ->image()
                                                    ->disk('public')
                                                    ->directory('settings')
                                                    ->visibility('public')
                                                    ->imageResizeMode('cover')
                                                    ->maxSize(512)
                                                    ->helperText('PNG/JPG, Max 500KB'),

                                                Forms\Components\FileUpload::make('logo_dark')
                                                    ->label('লোগো (Dark)')
                                                    ->image()
                                                    ->disk('public')
                                                    ->directory('settings')
                                                    ->visibility('public')
                                                    ->maxSize(512)
                                                    ->helperText('অন্ধকার ব্যাকগ্রাউন্ডের জন্য'),

                                                Forms\Components\FileUpload::make('favicon')
                                                    ->label('ফেভিকন')
                                                    ->image()
                                                    ->disk('public')
                                                    ->directory('settings')
                                                    ->visibility('public')
                                                    ->maxSize(64)
                                                    ->helperText('ICO/PNG, 32x32px'),
                                            ]),
                                    ]),
                            ]),

                        // ==========================================
                        // TAB 3: ACADEMIC SETTINGS
                        // ==========================================
                        Forms\Components\Tabs\Tab::make('শিক্ষা')
                            ->icon('heroicon-o-academic-cap')
                            ->schema([
                                Forms\Components\Section::make('একাডেমিক সেটিংস')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('passing_marks')
                                                    ->label('পাস মার্ক (%)')
                                                    ->numeric()
                                                    ->suffix('%')
                                                    ->default(33),

                                                Forms\Components\TextInput::make('attendance_required_percent')
                                                    ->label('উপস্থিতি আবশ্যক (%)')
                                                    ->numeric()
                                                    ->suffix('%')
                                                    ->default(75)
                                                    ->helperText('পরীক্ষায় বসতে ন্যূনতম'),
                                            ]),
                                    ]),

                                Forms\Components\Section::make('ফি সেটিংস')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('late_fee_percent')
                                                    ->label('বিলম্ব ফি (%)')
                                                    ->numeric()
                                                    ->suffix('%')
                                                    ->default(5)
                                                    ->helperText('বিলম্বে পরিশোধে অতিরিক্ত চার্জ'),

                                                Forms\Components\TextInput::make('late_fee_days')
                                                    ->label('বিলম্ব দিন')
                                                    ->numeric()
                                                    ->suffix('দিন')
                                                    ->default(10)
                                                    ->helperText('এত দিন পর বিলম্ব ফি যোগ হবে'),
                                            ]),
                                    ]),
                            ]),

                        // ==========================================
                        // TAB 4: SMS SETTINGS
                        // ==========================================
                        Forms\Components\Tabs\Tab::make('SMS')
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->schema([
                                Forms\Components\Section::make('SMS গেটওয়ে কনফিগারেশন')
                                    ->description('SMS পাঠানোর জন্য গেটওয়ে সেটআপ করুন')
                                    ->schema([
                                        Forms\Components\Toggle::make('sms_enabled')
                                            ->label('SMS সক্রিয়')
                                            ->live(),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Select::make('sms_gateway')
                                                    ->label('গেটওয়ে')
                                                    ->options([
                                                        'ssl' => 'SSL Wireless',
                                                        'bulksms' => 'BulkSMS BD',
                                                        'twilio' => 'Twilio',
                                                        'greenweb' => 'Green Web BD',
                                                    ])
                                                    ->native(false),

                                                Forms\Components\TextInput::make('sms_sender_id')
                                                    ->label('Sender ID'),
                                            ]),

                                        Forms\Components\TextInput::make('sms_api_key')
                                            ->label('API Key')
                                            ->password()
                                            ->revealable(),
                                    ]),
                            ]),

                        // ==========================================
                        // TAB 5: EMAIL SETTINGS
                        // ==========================================
                        Forms\Components\Tabs\Tab::make('ইমেইল')
                            ->icon('heroicon-o-envelope')
                            ->schema([
                                Forms\Components\Section::make('SMTP কনফিগারেশন')
                                    ->description('ইমেইল পাঠানোর জন্য SMTP সেটআপ করুন')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\Select::make('mail_driver')
                                                    ->label('Driver')
                                                    ->options([
                                                        'smtp' => 'SMTP',
                                                        'mailgun' => 'Mailgun',
                                                        'ses' => 'Amazon SES',
                                                    ])
                                                    ->native(false),

                                                Forms\Components\TextInput::make('mail_host')
                                                    ->label('Host')
                                                    ->placeholder('smtp.gmail.com'),

                                                Forms\Components\TextInput::make('mail_port')
                                                    ->label('Port')
                                                    ->numeric()
                                                    ->placeholder('587'),
                                            ]),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('mail_username')
                                                    ->label('Username'),

                                                Forms\Components\TextInput::make('mail_password')
                                                    ->label('Password')
                                                    ->password()
                                                    ->revealable(),
                                            ]),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('mail_from_address')
                                                    ->label('From Address')
                                                    ->email(),

                                                Forms\Components\TextInput::make('mail_from_name')
                                                    ->label('From Name'),
                                            ]),
                                    ]),
                            ]),

                        // ==========================================
                        // TAB 6: PAYMENT SETTINGS
                        // ==========================================
                        Forms\Components\Tabs\Tab::make('পেমেন্ট')
                            ->icon('heroicon-o-credit-card')
                            ->schema([
                                Forms\Components\Section::make('bKash কনফিগারেশন')
                                    ->schema([
                                        Forms\Components\Toggle::make('bkash_enabled')
                                            ->label('bKash সক্রিয়')
                                            ->live(),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('bkash_app_key')
                                                    ->label('App Key')
                                                    ->password()
                                                    ->revealable(),

                                                Forms\Components\TextInput::make('bkash_app_secret')
                                                    ->label('App Secret')
                                                    ->password()
                                                    ->revealable(),
                                            ]),
                                    ]),

                                Forms\Components\Section::make('Nagad কনফিগারেশন')
                                    ->schema([
                                        Forms\Components\Toggle::make('nagad_enabled')
                                            ->label('Nagad সক্রিয়'),
                                    ])
                                    ->collapsed(),
                            ]),

                        // ==========================================
                        // TAB 7: BACKUP
                        // ==========================================
                        Forms\Components\Tabs\Tab::make('ব্যাকআপ')
                            ->icon('heroicon-o-arrow-down-tray')
                            ->schema([
                                Forms\Components\Section::make('ডাটাবেস ব্যাকআপ')
                                    ->description('ডাটাবেস ব্যাকআপ ও রিস্টোর')
                                    ->schema([
                                        Forms\Components\Placeholder::make('backup_info')
                                            ->label('')
                                            ->content('ব্যাকআপ নিতে নিচের বাটনে ক্লিক করুন। ব্যাকআপ ফাইল storage/app/backups ফোল্ডারে সেভ হবে।'),

                                        Forms\Components\Actions::make([
                                            Forms\Components\Actions\Action::make('backup_database')
                                                ->label('ডাটাবেস ব্যাকআপ')
                                                ->icon('heroicon-o-arrow-down-tray')
                                                ->color('success')
                                                ->action(function () {
                                                    // Run backup command
                                                    try {
                                                        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
                                                        $path = storage_path('app/backups/' . $filename);

                                                        if (!file_exists(storage_path('app/backups'))) {
                                                            mkdir(storage_path('app/backups'), 0755, true);
                                                        }

                                                        $command = sprintf(
                                                            'mysqldump -u%s -p%s %s > %s',
                                                            config('database.connections.mysql.username'),
                                                            config('database.connections.mysql.password'),
                                                            config('database.connections.mysql.database'),
                                                            $path
                                                        );

                                                        exec($command);

                                                        Notification::make()
                                                            ->success()
                                                            ->title('ব্যাকআপ সফল!')
                                                            ->body('ফাইল: ' . $filename)
                                                            ->send();
                                                    } catch (\Exception $e) {
                                                        Notification::make()
                                                            ->danger()
                                                            ->title('ব্যাকআপ ব্যর্থ!')
                                                            ->body($e->getMessage())
                                                            ->send();
                                                    }
                                                }),

                                            Forms\Components\Actions\Action::make('clear_cache')
                                                ->label('ক্যাশ ক্লিয়ার')
                                                ->icon('heroicon-o-trash')
                                                ->color('warning')
                                                ->action(function () {
                                                    Artisan::call('cache:clear');
                                                    Artisan::call('config:clear');
                                                    Artisan::call('view:clear');

                                                    Notification::make()
                                                        ->success()
                                                        ->title('ক্যাশ ক্লিয়ার হয়েছে!')
                                                        ->send();
                                                }),
                                        ]),
                                    ]),
                            ]),
                    ])
                    ->persistTabInQueryString()
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            if ($value !== null && $value !== '') {
                Setting::setValue($key, is_array($value) ? json_encode($value) : $value, $this->getGroup($key));
            }
        }

        Notification::make()
            ->success()
            ->title('সেটিংস সেভ হয়েছে!')
            ->send();
    }

    protected function getGroup(string $key): string
    {
        $groups = [
            'sms' => ['sms_enabled', 'sms_gateway', 'sms_api_key', 'sms_sender_id'],
            'email' => ['mail_driver', 'mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_from_address', 'mail_from_name'],
            'payment' => ['bkash_enabled', 'bkash_app_key', 'bkash_app_secret', 'nagad_enabled'],
            'academic' => ['passing_marks', 'attendance_required_percent', 'late_fee_percent', 'late_fee_days', 'current_academic_year'],
        ];

        foreach ($groups as $group => $keys) {
            if (in_array($key, $keys)) {
                return $group;
            }
        }

        return 'general';
    }
}
