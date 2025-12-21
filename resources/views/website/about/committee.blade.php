@extends('website.layouts.app')

@section('title', 'পরিচালনা কমিটি - ' . (institution_name() ?? 'মাদরাসা'))

@section('content')
    <section style="background: linear-gradient(135deg, #047857 0%, #065f46 50%, #064e3b 100%);" class=" pt-32 pb-20">
        <div class="container mx-auto px-4 text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4" data-aos="fade-up">
                {{ setting('committee_title', 'পরিচালনা কমিটি') }}
            </h1>
            <p class="text-xl text-primary-100" data-aos="fade-up" data-aos-delay="100">প্রতিষ্ঠান পরিচালনা পর্ষদ</p>
        </div>
    </section>

    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            @if(setting('committee_intro'))
                <div class="max-w-3xl mx-auto text-center mb-12" data-aos="fade-up">
                    <p class="text-gray-600 text-lg">{{ setting('committee_intro') }}</p>
                </div>
            @endif

            @if(setting('committee_members'))
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach(explode("\n", setting('committee_members')) as $member)
                        @php
                            $parts = explode('|', $member);
                            $name = trim($parts[0] ?? '');
                            $position = trim($parts[1] ?? '');
                            $phone = trim($parts[2] ?? '');
                        @endphp
                        @if($name)
                            <div class="bg-white rounded-xl shadow-lg p-6 text-center hover:shadow-xl transition-shadow"
                                data-aos="fade-up">
                                <div class="w-20 h-20 bg-primary-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                                    <span class="text-3xl text-primary-600">👤</span>
                                </div>
                                <h3 class="text-lg font-bold text-gray-800 mb-1">{{ $name }}</h3>
                                @if($position)
                                    <p class="text-primary-600 font-medium mb-2">{{ $position }}</p>
                                @endif
                                @if($phone)
                                    <p class="text-gray-500 text-sm">📞 {{ $phone }}</p>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="text-center py-20" data-aos="fade-up">
                    <div class="text-6xl mb-4">👥</div>
                    <h3 class="text-2xl font-bold text-gray-400 mb-2">কমিটি সদস্যদের তথ্য যুক্ত করুন</h3>
                    <p class="text-gray-500">Admin Panel > ওয়েবসাইট কনটেন্ট > কমিটি ট্যাবে গিয়ে সদস্যদের তথ্য যুক্ত করুন</p>
                </div>
            @endif
        </div>
    </section>
@endsection