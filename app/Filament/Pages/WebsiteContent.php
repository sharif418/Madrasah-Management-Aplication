<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use App\Filament\Pages\BasePage;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;

class WebsiteContent extends BasePage implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';
    protected static ?string $navigationGroup = 'ওয়েবসাইট';
    protected static ?string $navigationLabel = 'ওয়েবসাইট কনটেন্ট';
    protected static ?string $title = 'ওয়েবসাইট কনটেন্ট ব্যবস্থাপনা';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.website-content';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->getSettings());
    }

    protected function getSettings(): array
    {
        return [
            // Home Page
            'home_welcome_title' => Setting::getValue('home_welcome_title', ''),
            'home_welcome_text' => Setting::getValue('home_welcome_text', ''),
            'home_features' => Setting::getValue('home_features', ''),
            'home_mission_title' => Setting::getValue('home_mission_title', ''),
            'home_mission_text' => Setting::getValue('home_mission_text', ''),
            'home_why_choose_title' => Setting::getValue('home_why_choose_title', ''),
            'home_why_choose_items' => Setting::getValue('home_why_choose_items', ''),

            // About Page
            'about_welcome_title' => Setting::getValue('about_welcome_title', ''),
            'about_welcome_text' => Setting::getValue('about_welcome_text', ''),
            'about_introduction' => Setting::getValue('about_introduction', ''),
            'about_features' => Setting::getValue('about_features', ''),

            // History Page
            'history_title' => Setting::getValue('history_title', ''),
            'history_content' => Setting::getValue('history_content', ''),

            // Mission Page
            'mission_title' => Setting::getValue('mission_title', ''),
            'mission_content' => Setting::getValue('mission_content', ''),
            'vision_title' => Setting::getValue('vision_title', ''),
            'vision_content' => Setting::getValue('vision_content', ''),
            'values_content' => Setting::getValue('values_content', ''),

            // Committee Page
            'committee_title' => Setting::getValue('committee_title', ''),
            'committee_intro' => Setting::getValue('committee_intro', ''),
            'committee_members' => Setting::getValue('committee_members', ''),

            // Admission Page
            'admission_title' => Setting::getValue('admission_title', ''),
            'admission_intro' => Setting::getValue('admission_intro', ''),
            'admission_process' => Setting::getValue('admission_process', ''),
            'admission_documents' => Setting::getValue('admission_documents', ''),

            // Eligibility Page
            'eligibility_title' => Setting::getValue('eligibility_title', ''),
            'eligibility_content' => Setting::getValue('eligibility_content', ''),

            // Donate Page
            'donate_title' => Setting::getValue('donate_title', ''),
            'donate_intro' => Setting::getValue('donate_intro', ''),
            'donate_bank_name' => Setting::getValue('donate_bank_name', ''),
            'donate_account_name' => Setting::getValue('donate_account_name', ''),
            'donate_account_number' => Setting::getValue('donate_account_number', ''),
            'donate_branch' => Setting::getValue('donate_branch', ''),
            'donate_routing' => Setting::getValue('donate_routing', ''),
            'donate_bkash' => Setting::getValue('donate_bkash', ''),
            'donate_nagad' => Setting::getValue('donate_nagad', ''),
            'donate_rocket' => Setting::getValue('donate_rocket', ''),

            // Portal Page
            'portal_title' => Setting::getValue('portal_title', ''),
            'portal_intro' => Setting::getValue('portal_intro', ''),

            // Footer
            'footer_about' => Setting::getValue('footer_about', ''),
            'footer_copyright' => Setting::getValue('footer_copyright', ''),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('WebsiteContent')
                    ->tabs([
                        // ==========================================
                        // TAB 1: HOME PAGE
                        // ==========================================
                        Forms\Components\Tabs\Tab::make('হোম পেজ')
                            ->icon('heroicon-o-home')
                            ->schema([
                                Forms\Components\Section::make('স্বাগতম সেকশন')
                                    ->description('হোম পেজের "আমাদের সম্পর্কে" অংশ')
                                    ->schema([
                                        Forms\Components\Textarea::make('home_welcome_text')
                                            ->label('বিবরণ')
                                            ->rows(4)
                                            ->helperText('হোম পেজে "আমাদের সম্পর্কে" অংশে দেখানো বিবরণ')
                                            ->placeholder('আমাদের প্রতিষ্ঠান কুরআন ও সুন্নাহর আলোকে দ্বীনি ও আধুনিক শিক্ষার সমন্বয়ে একটি আদর্শ শিক্ষা প্রতিষ্ঠান।'),
                                        Forms\Components\Textarea::make('home_features')
                                            ->label('বৈশিষ্ট্য তালিকা (চেকমার্ক সহ)')
                                            ->rows(4)
                                            ->helperText('প্রতি লাইনে একটি করে বৈশিষ্ট্য')
                                            ->placeholder("হিফজ বিভাগ\nকিতাব বিভাগ\nযোগ্য শিক্ষকমণ্ডলী\nআধুনিক সুযোগ-সুবিধা"),
                                    ]),

                                Forms\Components\Section::make('আমাদের লক্ষ্য (সবুজ বক্স)')
                                    ->description('হোম পেজের ডান পাশে সবুজ বক্সে যা দেখাবে')
                                    ->schema([
                                        Forms\Components\TextInput::make('home_mission_title')
                                            ->label('শিরোনাম')
                                            ->placeholder('আমাদের লক্ষ্য'),
                                        Forms\Components\Textarea::make('home_mission_text')
                                            ->label('লক্ষ্য বিবরণ')
                                            ->rows(3)
                                            ->placeholder('কুরআন-সুন্নাহর আলোকে আদর্শ মানুষ তৈরি করা এবং দ্বীন ও দুনিয়া উভয় ক্ষেত্রে সফল মুসলিম গড়ে তোলা।'),
                                    ]),
                            ]),

                        // ==========================================
                        // TAB 2: ABOUT PAGE
                        // ==========================================
                        Forms\Components\Tabs\Tab::make('পরিচিতি')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\Section::make('পরিচিতি পেজ')
                                    ->schema([
                                        Forms\Components\TextInput::make('about_welcome_title')
                                            ->label('শিরোনাম')
                                            ->placeholder('আমাদের সম্পর্কে'),
                                        Forms\Components\Textarea::make('about_welcome_text')
                                            ->label('সংক্ষিপ্ত বিবরণ')
                                            ->rows(3),
                                        Forms\Components\RichEditor::make('about_introduction')
                                            ->label('বিস্তারিত পরিচিতি')
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'underline',
                                                'bulletList',
                                                'orderedList',
                                                'h2',
                                                'h3',
                                            ]),
                                        Forms\Components\Textarea::make('about_features')
                                            ->label('বিশেষ বৈশিষ্ট্য')
                                            ->rows(4)
                                            ->helperText('প্রতি লাইনে একটি করে বৈশিষ্ট্য'),
                                    ]),
                            ]),

                        // ==========================================
                        // TAB 3: HISTORY PAGE
                        // ==========================================
                        Forms\Components\Tabs\Tab::make('ইতিহাস')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                Forms\Components\Section::make('প্রতিষ্ঠানের ইতিহাস')
                                    ->schema([
                                        Forms\Components\TextInput::make('history_title')
                                            ->label('শিরোনাম')
                                            ->placeholder('প্রতিষ্ঠানের ইতিহাস'),
                                        Forms\Components\RichEditor::make('history_content')
                                            ->label('ইতিহাস বিবরণ')
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'underline',
                                                'bulletList',
                                                'orderedList',
                                                'h2',
                                                'h3',
                                            ])
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // ==========================================
                        // TAB 4: MISSION & VISION
                        // ==========================================
                        Forms\Components\Tabs\Tab::make('মিশন ও ভিশন')
                            ->icon('heroicon-o-light-bulb')
                            ->schema([
                                Forms\Components\Section::make('আমাদের মিশন')
                                    ->schema([
                                        Forms\Components\TextInput::make('mission_title')
                                            ->label('শিরোনাম')
                                            ->placeholder('আমাদের মিশন'),
                                        Forms\Components\RichEditor::make('mission_content')
                                            ->label('মিশন বিবরণ')
                                            ->toolbarButtons(['bold', 'italic', 'bulletList']),
                                    ]),
                                Forms\Components\Section::make('আমাদের ভিশন')
                                    ->schema([
                                        Forms\Components\TextInput::make('vision_title')
                                            ->label('শিরোনাম')
                                            ->placeholder('আমাদের ভিশন'),
                                        Forms\Components\RichEditor::make('vision_content')
                                            ->label('ভিশন বিবরণ')
                                            ->toolbarButtons(['bold', 'italic', 'bulletList']),
                                    ]),
                                Forms\Components\Section::make('আমাদের মূল্যবোধ')
                                    ->schema([
                                        Forms\Components\Textarea::make('values_content')
                                            ->label('মূল্যবোধ তালিকা')
                                            ->rows(5)
                                            ->helperText('প্রতি লাইনে একটি করে মূল্যবোধ'),
                                    ])->collapsed(),
                            ]),

                        // ==========================================
                        // TAB 5: COMMITTEE
                        // ==========================================
                        Forms\Components\Tabs\Tab::make('কমিটি')
                            ->icon('heroicon-o-user-group')
                            ->schema([
                                Forms\Components\Section::make('পরিচালনা কমিটি')
                                    ->schema([
                                        Forms\Components\TextInput::make('committee_title')
                                            ->label('শিরোনাম')
                                            ->placeholder('পরিচালনা কমিটি'),
                                        Forms\Components\Textarea::make('committee_intro')
                                            ->label('পরিচিতি')
                                            ->rows(2),
                                        Forms\Components\Textarea::make('committee_members')
                                            ->label('সদস্য তালিকা')
                                            ->rows(10)
                                            ->helperText('ফরম্যাট: নাম | পদবী | ফোন (প্রতি লাইনে একজন)')
                                            ->placeholder("মোঃ আব্দুল্লাহ | সভাপতি | 01712345678\nমোঃ করিম | সহ-সভাপতি | 01812345678"),
                                    ]),
                            ]),

                        // ==========================================
                        // TAB 6: ADMISSION
                        // ==========================================
                        Forms\Components\Tabs\Tab::make('ভর্তি তথ্য')
                            ->icon('heroicon-o-academic-cap')
                            ->schema([
                                Forms\Components\Section::make('ভর্তি পেজ')
                                    ->schema([
                                        Forms\Components\TextInput::make('admission_title')
                                            ->label('শিরোনাম')
                                            ->placeholder('ভর্তি তথ্য'),
                                        Forms\Components\RichEditor::make('admission_intro')
                                            ->label('ভর্তি প্রক্রিয়া')
                                            ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList']),
                                        Forms\Components\Textarea::make('admission_process')
                                            ->label('ধাপসমূহ')
                                            ->rows(5)
                                            ->helperText('প্রতি লাইনে একটি ধাপ')
                                            ->placeholder("অনলাইনে আবেদন করুন\nভর্তি পরীক্ষা দিন\nফলাফল দেখুন"),
                                        Forms\Components\Textarea::make('admission_documents')
                                            ->label('প্রয়োজনীয় কাগজপত্র')
                                            ->rows(5)
                                            ->helperText('প্রতি লাইনে একটি'),
                                    ]),
                            ]),

                        // ==========================================
                        // TAB 7: ELIGIBILITY
                        // ==========================================
                        Forms\Components\Tabs\Tab::make('যোগ্যতা')
                            ->icon('heroicon-o-check-badge')
                            ->schema([
                                Forms\Components\Section::make('ভর্তি যোগ্যতা')
                                    ->schema([
                                        Forms\Components\TextInput::make('eligibility_title')
                                            ->label('শিরোনাম')
                                            ->placeholder('ভর্তির যোগ্যতা'),
                                        Forms\Components\RichEditor::make('eligibility_content')
                                            ->label('যোগ্যতার বিবরণ')
                                            ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList', 'h2', 'h3'])
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // ==========================================
                        // TAB 8: DONATE
                        // ==========================================
                        Forms\Components\Tabs\Tab::make('দান/অনুদান')
                            ->icon('heroicon-o-gift')
                            ->schema([
                                Forms\Components\Section::make('দান পেজ')
                                    ->schema([
                                        Forms\Components\TextInput::make('donate_title')
                                            ->label('শিরোনাম')
                                            ->placeholder('দান করুন'),
                                        Forms\Components\Textarea::make('donate_intro')
                                            ->label('দানের গুরুত্ব')
                                            ->rows(3),
                                    ]),
                                Forms\Components\Section::make('ব্যাংক অ্যাকাউন্ট')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('donate_bank_name')
                                                    ->label('ব্যাংকের নাম')
                                                    ->placeholder('ইসলামী ব্যাংক বাংলাদেশ'),
                                                Forms\Components\TextInput::make('donate_account_name')
                                                    ->label('একাউন্ট নাম'),
                                                Forms\Components\TextInput::make('donate_account_number')
                                                    ->label('একাউন্ট নম্বর'),
                                                Forms\Components\TextInput::make('donate_branch')
                                                    ->label('শাখা'),
                                                Forms\Components\TextInput::make('donate_routing')
                                                    ->label('রাউটিং নম্বর'),
                                            ]),
                                    ]),
                                Forms\Components\Section::make('মোবাইল ব্যাংকিং')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('donate_bkash')
                                                    ->label('বিকাশ নম্বর')
                                                    ->tel(),
                                                Forms\Components\TextInput::make('donate_nagad')
                                                    ->label('নগদ নম্বর')
                                                    ->tel(),
                                                Forms\Components\TextInput::make('donate_rocket')
                                                    ->label('রকেট নম্বর')
                                                    ->tel(),
                                            ]),
                                    ]),
                            ]),

                        // ==========================================
                        // TAB 9: FOOTER
                        // ==========================================
                        Forms\Components\Tabs\Tab::make('ফুটার')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\Section::make('ফুটার সেকশন')
                                    ->schema([
                                        Forms\Components\Textarea::make('footer_about')
                                            ->label('প্রতিষ্ঠান সম্পর্কে (সংক্ষিপ্ত)')
                                            ->rows(3)
                                            ->placeholder('ফুটারে দেখানো সংক্ষিপ্ত বিবরণ'),
                                        Forms\Components\TextInput::make('footer_copyright')
                                            ->label('কপিরাইট টেক্সট')
                                            ->placeholder('© 2024 সর্বস্বত্ব সংরক্ষিত'),
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
            if ($value !== null) {
                Setting::setValue($key, is_array($value) ? json_encode($value) : $value, 'website');
            }
        }

        Notification::make()
            ->success()
            ->title('ওয়েবসাইট কনটেন্ট সেভ হয়েছে!')
            ->send();
    }
}
