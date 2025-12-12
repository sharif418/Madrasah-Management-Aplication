@extends('website.layouts.app')

@section('title', 'পরিচালনা কমিটি - ' . (institution_name() ?? 'মাদরাসা'))

@section('content')
    <section style="background: linear-gradient(135deg, #047857 0%, #065f46 50%, #064e3b 100%);" class=" pt-32 pb-20">
        <div class="container mx-auto px-4 text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4" data-aos="fade-up">পরিচালনা কমিটি</h1>
            <p class="text-xl text-primary-100" data-aos="fade-up" data-aos-delay="100">প্রতিষ্ঠান পরিচালনা পর্ষদ</p>
        </div>
    </section>

    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center py-20" data-aos="fade-up">
                <div class="text-6xl mb-4">👥</div>
                <h3 class="text-2xl font-bold text-gray-400 mb-2">শীঘ্রই আপডেট করা হবে</h3>
                <p class="text-gray-500">পরিচালনা কমিটির সদস্যদের তথ্য সংযুক্ত করা হচ্ছে</p>
            </div>
        </div>
    </section>
@endsection