@extends('website.layouts.app')

@section('title', 'ইতিহাস - ' . (institution_name() ?? 'মাদরাসা'))

@section('content')
    <section style="background: linear-gradient(135deg, #047857 0%, #065f46 50%, #064e3b 100%);" class=" pt-32 pb-20">
        <div class="container mx-auto px-4 text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4" data-aos="fade-up">
                {{ setting('history_title', 'প্রতিষ্ঠানের ইতিহাস') }}
            </h1>
            <p class="text-xl text-primary-100" data-aos="fade-up" data-aos-delay="100">আমাদের গৌরবোজ্জ্বল অতীত</p>
        </div>
    </section>

    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto prose prose-lg" data-aos="fade-up">
                @if(setting('history_content'))
                    {!! setting('history_content') !!}
                @else
                    <h2 class="text-primary-700">প্রতিষ্ঠানের সংক্ষিপ্ত ইতিহাস</h2>
                    <p class="text-gray-600 leading-relaxed">
                        আমাদের প্রতিষ্ঠানটি দ্বীনি শিক্ষার প্রসারের মহান উদ্দেশ্যে প্রতিষ্ঠিত হয়েছে।
                        স্থানীয় আলেম ও মুসলিম সমাজের সম্মিলিত প্রচেষ্টায় এই প্রতিষ্ঠানটি যাত্রা শুরু করে।
                    </p>
                    <p class="text-gray-600 leading-relaxed">
                        প্রতিষ্ঠালগ্ন থেকেই কুরআন-সুন্নাহর আলোকে শিক্ষাদানের লক্ষ্যে কাজ করে যাচ্ছে।
                        এখানে হাজার হাজার ছাত্র কুরআন হিফজ করে হাফেজে কুরআন হয়েছে।
                    </p>
                    <p class="text-gray-500 text-sm italic mt-8">
                        (এই কনটেন্ট পরিবর্তন করতে Admin Panel > ওয়েবসাইট কনটেন্ট > ইতিহাস ট্যাবে যান)
                    </p>
                @endif
            </div>
        </div>
    </section>
@endsection