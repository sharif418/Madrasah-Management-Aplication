@extends('website.layouts.app')

@section('title', 'ফলাফল অনুসন্ধান - ' . (institution_name() ?? 'মাদরাসা'))

@section('content')
    <!-- Page Header -->
    <section class="pt-32 pb-20" style="background: linear-gradient(135deg, #047857 0%, #065f46 50%, #064e3b 100%);">
        <div class="container mx-auto px-4 text-center text-white">
            <h1 class="text-4xl md:text-5xl font-bold mb-4" data-aos="fade-up">ফলাফল অনুসন্ধান</h1>
            <p class="text-xl opacity-80" data-aos="fade-up" data-aos-delay="100">রোল নম্বর দিয়ে আপনার ফলাফল দেখুন</p>
            <nav class="mt-6" data-aos="fade-up" data-aos-delay="200">
                <ol class="flex items-center justify-center gap-2 text-primary-200">
                    <li><a href="{{ route('home') }}" class="hover:text-white">হোম</a></li>
                    <li>/</li>
                    <li class="text-white">ফলাফল</li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Result Search Form -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-2xl mx-auto" x-data="{ 
                            roll: '', 
                            examType: '', 
                            year: '2025',
                            searching: false,
                            result: null,
                            error: null,
                            async searchResult() {
                                if (!this.roll || !this.examType) {
                                    this.error = 'রোল নম্বর এবং পরীক্ষার ধরণ দিন';
                                    return;
                                }
                                this.searching = true;
                                this.error = null;
                                this.result = null;

                                // Simulate API call - replace with actual endpoint
                                await new Promise(resolve => setTimeout(resolve, 1500));

                                // Mock result - replace with actual API
                                if (this.roll === '101') {
                                    this.result = {
                                        name: 'মোহাম্মদ আব্দুল্লাহ',
                                        roll: '101',
                                        class: 'হিফজ - ১ম বর্ষ',
                                        exam: this.examType === 'annual' ? 'বার্ষিক পরীক্ষা' : 'সাময়িক পরীক্ষা',
                                        year: this.year,
                <div class=" max-w-4xl mx-auto" x-data="{
                    roll: '',
                    exam_id: '',
                    year: '{{ date('Y') }}',
                    loading: false,
                    searched: false,
                    success: false,
                    error: '',
                    student: {},
                    result: {},
                    marks: [],

                    async searchResult() {
                        if(!this.roll || !this.exam_id) {
                            this.error = 'অনুগ্রহ করে সব তথ্য প্রদান করুন';
                            return;
                        }

                        this.loading = true;
                        this.error = '';
                        this.searched = false;
                        this.success = false;

                        try {
                            // Construct URL params
                            const params = new URLSearchParams({
                                roll: this.roll,
                                exam_id: this.exam_id,
                                year: this.year
                            });

                            const response = await fetch('{{ route('results') }}/search?' + params.toString(), {
                                method: 'GET',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });

                            const data = await response.json();

                            if(response.ok && data.success) {
                                this.success = true;
                                this.student = data.data.student;
                                this.result = data.data.result;
                                this.marks = data.data.marks;
                            } else {
                                this.success = false;
                                this.error = data.message || 'ফলাফল পাওয়া যায়নি';
                            }
                        } catch(err) {
                            console.error(err);
                            this.error = 'সার্ভার সমস্যা। আবার চেষ্টা করুন।';
                        }

                        this.searched = true;
                        this.loading = false;
                    },

                    printResult() {
                        const printContent = document.getElementById('result-card').innerHTML;
                        const originalContent = document.body.innerHTML;
                        document.body.innerHTML = printContent;
                        window.print();
                        document.body.innerHTML = originalContent;
                        window.location.reload(); // Reload to restore event listeners
                    }
                }">

                <!-- Search Box -->
                <div class="bg-white rounded-3xl shadow-xl p-8 md:p-12 mb-12" data-aos="fade-up">
                    <div class="grid md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">পরীক্ষা নির্বাচন করুন</label>
                            <select x-model="exam_id"
                                class="w-full px-4 py-3 border-2 border-gray-100 rounded-xl focus:border-primary-500 outline-none transition-colors">
                                <option value="">-- পরীক্ষা --</option>
                                @foreach($exams as $exam)
                                    <option value="{{ $exam->id }}">{{ $exam->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">সাল</label>
                            <select x-model="year"
                                class="w-full px-4 py-3 border-2 border-gray-100 rounded-xl focus:border-primary-500 outline-none transition-colors">
                                <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                                <option value="{{ date('Y') - 1 }}">{{ date('Y') - 1 }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-bold mb-2">রোল নম্বর</label>
                            <input type="number" x-model="roll" placeholder="রোল নম্বর লিখুন"
                                class="w-full px-4 py-3 border-2 border-gray-100 rounded-xl focus:border-primary-500 outline-none transition-colors">
                        </div>
                    </div>

                    <div class="mt-8 text-center">
                        <p x-show="error" class="text-red-500 mb-4 font-medium" x-text="error"></p>

                        <button @click="searchResult()" :disabled="loading"
                            class="px-10 py-3 bg-primary-600 text-white rounded-full font-bold hover:bg-primary-700 transition-all transform hover:scale-105 shadow-lg disabled:opacity-50 flex items-center gap-2 mx-auto">
                            <span x-show="!loading">ফলাফল খুঁজুন</span>
                            <span x-show="loading" class="flex items-center gap-2">
                                <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                খোঁজা হচ্ছে...
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Result Display -->
                <div x-show="searched && success" x-cloak id="result-card"
                    class="bg-white rounded-3xl shadow-xl overflow-hidden border-t-8 border-primary-600" data-aos="zoom-in">
                    <!-- Header -->
                    <div class="bg-primary-50 p-8 text-center border-b border-primary-100">
                        <div class="w-20 h-20 mx-auto mb-4">
                            <img src="{{ asset('storage/' . (institution_logo() ?? 'logo.png')) }}" alt="Logo"
                                class="w-full h-full object-contain">
                        </div>
                        <h2 class="text-2xl font-bold text-primary-900 mb-1">{{ institution_name() }}</h2>
                        <h3 class="text-lg text-primary-700 font-medium" x-text="result.exam?.name || 'পরীক্ষার ফলাফল'">
                        </h3>
                    </div>

                    <!-- Student Info -->
                    <div class="p-8 bg-white">
                        <div
                            class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8 p-6 bg-gray-50 rounded-2xl border border-gray-100">
                            <div>
                                <span class="block text-gray-500 text-sm">নাম</span>
                                <span class="block font-bold text-gray-900 text-lg"
                                    x-text="student.name_bn || student.name"></span>
                            </div>
                            <div>
                                <span class="block text-gray-500 text-sm">রোল নম্বর</span>
                                <span class="block font-bold text-gray-900 text-lg" x-text="student.roll_no"></span>
                            </div>
                            <div>
                                <span class="block text-gray-500 text-sm">শ্রেণী</span>
                                <span class="block font-bold text-gray-900 text-lg"
                                    x-text="student.class?.name || 'N/A'"></span>
                            </div>
                            <div>
                                <span class="block text-gray-500 text-sm">বিভাগ</span>
                                <span class="block font-bold text-gray-900 text-lg">সাধারণ</span>
                            </div>
                        </div>

                        <!-- Marks Table -->
                        <div class="overflow-x-auto mb-8">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-gray-100 text-left">
                                        <th class="px-4 py-3 rounded-l-lg">বিষয়</th>
                                        <th class="px-4 py-3 text-center">পূর্ণমান</th>
                                        <th class="px-4 py-3 text-center">প্রাপ্ত নম্বর</th>
                                        <th class="px-4 py-3 text-center rounded-r-lg">গ্রেড</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="mark in marks" :key="mark.id">
                                        <tr>
                                            <td class="px-4 py-3 font-medium" x-text="mark.subject?.name || 'Subject'"></td>
                                            <td class="px-4 py-3 text-center text-gray-500" x-text="mark.full_marks"></td>
                                            <td class="px-4 py-3 text-center font-bold" x-text="mark.total_marks"></td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="px-2 py-1 rounded text-xs font-bold" :class="{
                                                            'bg-green-100 text-green-700': ['A+', 'A', 'A-'].includes(mark.grade?.name),
                                                            'bg-yellow-100 text-yellow-700': ['B', 'C', 'D'].includes(mark.grade?.name),
                                                            'bg-red-100 text-red-700': mark.grade?.name === 'F'
                                                        }" x-text="mark.grade?.name || '-'">
                                                </span>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <!-- Summary -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-primary-50 p-4 rounded-xl text-center">
                                <span class="block text-primary-600 text-sm font-bold uppercase">মোট নম্বর</span>
                                <span class="block text-3xl font-extrabold text-primary-900"
                                    x-text="result.total_marks"></span>
                            </div>
                            <div class="bg-blue-50 p-4 rounded-xl text-center">
                                <span class="block text-blue-600 text-sm font-bold uppercase">জিপিএ (GPA)</span>
                                <span class="block text-3xl font-extrabold text-blue-900" x-text="result.gpa"></span>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-xl text-center">
                                <span class="block text-purple-600 text-sm font-bold uppercase">ফলাফল</span>
                                <span class="block text-2xl font-extrabold"
                                    :class="result.result_status === 'pass' ? 'text-green-600' : 'text-red-600'"
                                    x-text="result.result_status === 'pass' ? 'পাস' : 'ফেল'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="bg-gray-50 p-6 flex justify-center gap-4 border-t border-gray-100 print:hidden">
                        <button @click="printResult()"
                            class="px-6 py-2 bg-gray-900 text-white rounded-lg font-bold hover:bg-gray-800 transition-colors flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                </path>
                            </svg>
                            প্রিন্ট করুন
                        </button>
                        <button @click="searched = false; success = false;"
                            class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg font-bold hover:bg-gray-100 transition-colors">
                            নতুন অনুসন্ধান
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Print Styles -->
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #result-card,
            #result-card * {
                visibility: visible;
            }

            #result-card {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
                border: none;
            }

            .print\:hidden {
                display: none !important;
            }
        }
    </style>
@endsection