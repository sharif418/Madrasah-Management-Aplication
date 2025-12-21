@extends('website.layouts.app')

@section('title', 'প্রতিষ্ঠান পরিচিতি - ' . (institution_name() ?? 'মাদরাসা'))

@section('content')
    <!-- Page Header -->
    <section style="background: linear-gradient(135deg, #047857 0%, #065f46 50%, #064e3b 100%);" class=" pt-32 pb-20">
        <div class="container mx-auto px-4 text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4" data-aos="fade-up">
                {{ setting('about_welcome_title', 'প্রতিষ্ঠান পরিচিতি') }}
            </h1>
            <p class="text-xl text-primary-100" data-aos="fade-up" data-aos-delay="100">আমাদের সম্পর্কে জানুন</p>
            <!-- Breadcrumb -->
            <nav class="mt-6" data-aos="fade-up" data-aos-delay="200">
                <ol class="flex items-center justify-center gap-2 text-primary-200">
                    <li><a href="{{ route('home') }}" class="hover:text-white">হোম</a></li>
                    <li>/</li>
                    <li class="text-white">পরিচিতি</li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Content -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto prose prose-lg">
                <div data-aos="fade-up">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">
                        {{ institution_name() ?? 'মাদরাসা নাম' }} সম্পর্কে
                    </h2>

                    @if(setting('about_welcome_text'))
                        <p class="text-gray-600 leading-relaxed">
                            {{ setting('about_welcome_text') }}
                        </p>
                    @else
                        <p class="text-gray-600 leading-relaxed">
                            আমাদের প্রতিষ্ঠান কুরআন ও সুন্নাহর আলোকে দ্বীনি ও আধুনিক শিক্ষার সমন্বয়ে একটি আদর্শ শিক্ষা
                            প্রতিষ্ঠান।
                            এখানে ছাত্ররা হিফজুল কুরআন, ইলমে দ্বীন এবং আধুনিক শিক্ষার মাধ্যমে নিজেদের গড়ে তোলার সুযোগ পায়।
                        </p>
                    @endif

                    @if(setting('about_introduction'))
                        <div class="mt-6">
                            {!! setting('about_introduction') !!}
                        </div>
                    @endif
                </div>

                <!-- Features Grid -->
                <div class="grid md:grid-cols-2 gap-6 mt-12" data-aos="fade-up">
                    @php
                        $features = setting('about_features') ? explode("\n", setting('about_features')) : [];
                        $defaultFeatures = [
                            ['title' => 'হিফজ বিভাগ', 'desc' => 'কুরআন মাজীদ সম্পূর্ণ মুখস্থ করার সুবর্ণ সুযোগ', 'color' => 'primary'],
                            ['title' => 'অভিজ্ঞ শিক্ষকমণ্ডলী', 'desc' => 'দেশের বরেণ্য আলেমদের তত্ত্বাবধানে শিক্ষাদান', 'color' => 'gold'],
                            ['title' => 'আধুনিক সুযোগ-সুবিধা', 'desc' => 'শীতাতপ নিয়ন্ত্রিত শ্রেণীকক্ষ ও আবাসিক ব্যবস্থা', 'color' => 'blue'],
                            ['title' => 'সরকারি স্বীকৃতি', 'desc' => 'বাংলাদেশ মাদরাসা শিক্ষা বোর্ড অনুমোদিত', 'color' => 'purple'],
                        ];
                        $colors = ['primary', 'gold', 'blue', 'purple', 'green', 'red'];
                    @endphp

                    @if(count($features) > 0)
                        @foreach($features as $index => $feature)
                            @if(trim($feature))
                                @php $color = $colors[$index % count($colors)]; @endphp
                                <div class="bg-{{ $color }}-50 rounded-2xl p-6">
                                    <div
                                        class="w-12 h-12 bg-{{ $color }}-600 text-white rounded-xl flex items-center justify-center mb-4">
                                        <span class="text-xl">✓</span>
                                    </div>
                                    <h3 class="font-bold text-gray-900 mb-2">{{ trim($feature) }}</h3>
                                </div>
                            @endif
                        @endforeach
                    @else
                        @foreach($defaultFeatures as $feature)
                            <div class="bg-{{ $feature['color'] }}-50 rounded-2xl p-6">
                                <div
                                    class="w-12 h-12 bg-{{ $feature['color'] }}-600 text-white rounded-xl flex items-center justify-center mb-4">
                                    <span class="text-xl">✓</span>
                                </div>
                                <h3 class="font-bold text-gray-900 mb-2">{{ $feature['title'] }}</h3>
                                <p class="text-gray-600 text-sm">{{ $feature['desc'] }}</p>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Links -->
    <section class="py-12 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('about.history') }}"
                    class="px-6 py-3 bg-white rounded-full shadow hover:shadow-lg transition-shadow font-medium text-gray-700 hover:text-primary-600">
                    ইতিহাস
                </a>
                <a href="{{ route('about.mission') }}"
                    class="px-6 py-3 bg-white rounded-full shadow hover:shadow-lg transition-shadow font-medium text-gray-700 hover:text-primary-600">
                    লক্ষ্য ও উদ্দেশ্য
                </a>
                <a href="{{ route('about.committee') }}"
                    class="px-6 py-3 bg-white rounded-full shadow hover:shadow-lg transition-shadow font-medium text-gray-700 hover:text-primary-600">
                    পরিচালনা কমিটি
                </a>
                <a href="{{ route('about.teachers') }}"
                    class="px-6 py-3 bg-white rounded-full shadow hover:shadow-lg transition-shadow font-medium text-gray-700 hover:text-primary-600">
                    শিক্ষক তালিকা
                </a>
            </div>
        </div>
    </section>
@endsection