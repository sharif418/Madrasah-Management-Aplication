@extends('website.layouts.app')

@section('title', 'কর্মচারী তালিকা - ' . (institution_name() ?? 'মাদরাসা'))

@section('content')
    <section style="background: linear-gradient(135deg, #047857 0%, #065f46 50%, #064e3b 100%);" class=" pt-32 pb-20">
        <div class="container mx-auto px-4 text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4" data-aos="fade-up">কর্মচারী তালিকা</h1>
            <p class="text-xl text-primary-100" data-aos="fade-up" data-aos-delay="100">আমাদের কর্মচারীবৃন্দ</p>
        </div>
    </section>

    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse($staff as $member)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden text-center" data-aos="fade-up"
                        data-aos-delay="{{ $loop->index * 50 }}">
                        <div class="h-48 bg-gray-100 flex items-center justify-center">
                            @if($member->photo)
                                <img src="{{ Storage::url($member->photo) }}" alt="{{ $member->name }}"
                                    class="w-full h-full object-cover">
                            @else
                                <span class="text-5xl text-gray-400">👤</span>
                            @endif
                        </div>
                        <div class="p-4">
                            <h4 class="font-bold text-gray-900">{{ $member->name }}</h4>
                            <p class="text-primary-600 text-sm">{{ $member->designation }}</p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-20">
                        <div class="text-6xl mb-4">👤</div>
                        <h3 class="text-2xl font-bold text-gray-400 mb-2">কর্মচারী তালিকা পাওয়া যায়নি</h3>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection