@extends('website.layouts.app')

@section('title', 'অনলাইন ভর্তি আবেদন - ' . (institution_name() ?? 'মাদরাসা'))

@section('content')
    <!-- Page Header -->
    <x-website.page-header title="অনলাইন ভর্তি আবেদন" subtitle="ভর্তির জন্য আবেদন ফর্ম পূরণ করুন" />

    <!-- Application Form -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto" x-data="{
                            step: 1,
                            form: {
                                // Student Info
                                student_name: '',
                                student_name_en: '',
                                date_of_birth: '',
                                gender: '',
                                blood_group: '',
                                religion: 'ইসলাম',
                                nationality: 'বাংলাদেশী', // Not in DB but kept for checking

                                // Guardian Info
                                father_name: '',
                                father_phone: '',
                                father_occupation: '',
                                mother_name: '',
                                mother_phone: '',

                                // Address
                                present_address: '',
                                permanent_address: '',

                                // Academic
                                class_id: '',
                                previous_institution: '',
                                previous_class: '',

                                // Contact
                                email: '',
                                phone: '',
                            },
                            loading: false,
                            submitted: false,
                            applicationNo: '',
                            errors: {},

                            validateStep1() {
                                this.errors = {};
                                if (!this.form.student_name) this.errors.student_name = 'নাম আবশ্যক';
                                if (!this.form.date_of_birth) this.errors.date_of_birth = 'জন্ম তারিখ আবশ্যক';
                                if (!this.form.gender) this.errors.gender = 'লিঙ্গ নির্বাচন করুন';
                                return Object.keys(this.errors).length === 0;
                            },

                            validateStep2() {
                                this.errors = {};
                                if (!this.form.father_name) this.errors.father_name = 'পিতার নাম আবশ্যক';
                                if (!this.form.father_phone) this.errors.father_phone = 'পিতার ফোন আবশ্যক';
                                if (!this.form.mother_name) this.errors.mother_name = 'মাতার নাম আবশ্যক';
                                return Object.keys(this.errors).length === 0;
                            },

                            validateStep3() {
                                this.errors = {};
                                if (!this.form.present_address) this.errors.present_address = 'বর্তমান ঠিকানা আবশ্যক';
                                if (!this.form.class_id) this.errors.class_id = 'শ্রেণী নির্বাচন করুন';
                                return Object.keys(this.errors).length === 0;
                            },

                            nextStep() {
                                if (this.step === 1 && this.validateStep1()) this.step = 2;
                                else if (this.step === 2 && this.validateStep2()) this.step = 3;
                                else if (this.step === 3 && this.validateStep3()) this.step = 4;
                            },

                            async submitForm() {
                                this.loading = true;
                                this.errors = {};

                                try {
                                    const response = await fetch('{{ route('admission.store') }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify(this.form)
                                    });

                                    const data = await response.json();

                                    if (response.ok && data.success) {
                                        this.submitted = true;
                                        this.applicationNo = data.application_no;
                                        window.scrollTo({ top: 0, behavior: 'smooth' });
                                    } else {
                                        if(data.errors) {
                                            this.errors = data.errors;
                                            // Simple logic to find step with error
                                            if(this.errors.student_name || this.errors.date_of_birth) this.step = 1;
                                            else if(this.errors.father_name) this.step = 2;
                                            else if(this.errors.class_id) this.step = 3;

                                            alert('অনুগ্রহ করে হাইলাইট করা ভুলগুলো সংশোধন করুন।');
                                        } else {
                                            alert(data.message || 'কিছু সমস্যা হয়েছে। আবার চেষ্টা করুন।');
                                        }
                                    }
                                } catch (error) {
                                    console.error(error);
                                    alert('সার্ভার যোগাযোগে সমস্যা। আপনার ইন্টারনেট সংযোগ চেক করুন।');
                                }

                                this.loading = false;
                            },
                         }">

                <!-- Progress Steps -->
                <div class="mb-12" data-aos="fade-up">
                    <div class="flex items-center justify-between relative">
                        <div class="absolute top-5 left-0 right-0 h-1 bg-gray-200 -z-10">
                            <div class="h-full bg-primary-600 transition-all"
                                :style="'width: ' + ((step - 1) / 3 * 100) + '%'"></div>
                        </div>

                        <template x-for="(label, index) in ['ছাত্র তথ্য', 'অভিভাবক', 'শিক্ষা', 'জমা দিন']" :key="index">
                            <div class="flex flex-col items-center">
                                <div :class="step > index ? 'bg-primary-600 text-white' : (step === index + 1 ? 'bg-primary-600 text-white ring-4 ring-primary-200' : 'bg-gray-200 text-gray-500')"
                                    class="w-10 h-10 rounded-full flex items-center justify-center font-bold transition-all"
                                    x-text="index + 1"></div>
                                <span class="mt-2 text-sm font-medium text-gray-600 hidden md:block" x-text="label"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Success Message -->
                <div x-show="submitted" x-cloak class="bg-green-50 border-2 border-green-200 rounded-3xl p-12 text-center"
                    data-aos="zoom-in">
                    <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-green-800 mb-4">আবেদন সফলভাবে গৃহীত হয়েছে!</h2>
                    <p class="text-green-700 mb-6">আপনার তথ্য আমাদের ডাটাবেসে সংরক্ষিত হয়েছে। শীঘ্রই আপনার সাথে যোগাযোগ করা
                        হবে।</p>
                    <p class="text-lg text-green-800 font-medium">আবেদন নম্বর: <span class="font-bold text-2xl"
                            x-text="applicationNo"></span></p>
                    <div class="mt-8 flex justify-center gap-4">
                        <a href="{{ route('home') }}"
                            class="px-8 py-3 bg-white border border-green-600 text-green-600 rounded-full font-bold hover:bg-green-50 transition-colors">হোমে
                            ফিরে যান</a>
                        <button @click="window.print()"
                            class="px-8 py-3 bg-green-600 text-white rounded-full font-bold hover:bg-green-700 transition-colors">
                            প্রিন্ট করুন
                        </button>
                    </div>
                </div>

                <!-- Forms -->
                <div x-show="!submitted" class="bg-white rounded-3xl shadow-xl p-8 md:p-12 mb-20" data-aos="fade-up">

                    <!-- Step 1: Student Info -->
                    <div x-show="step === 1">
                        <h2 class="text-2xl font-bold text-gray-900 mb-8">ছাত্র/ছাত্রীর তথ্য</h2>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">পূর্ণ নাম (বাংলায়) <span
                                        class="text-red-500">*</span></label>
                                <input type="text" x-model="form.student_name"
                                    class="w-full px-4 py-3 border-2 rounded-xl focus:border-primary-500 outline-none"
                                    :class="errors.student_name ? 'border-red-400' : 'border-gray-200'">
                                <p x-show="errors.student_name" class="text-red-500 text-sm mt-1"
                                    x-text="errors.student_name"></p>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium mb-2">নাম (ইংরেজিতে)</label>
                                <input type="text" x-model="form.student_name_en"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 outline-none">
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium mb-2">জন্ম তারিখ <span
                                        class="text-red-500">*</span></label>
                                <input type="date" x-model="form.date_of_birth"
                                    class="w-full px-4 py-3 border-2 rounded-xl focus:border-primary-500 outline-none"
                                    :class="errors.date_of_birth ? 'border-red-400' : 'border-gray-200'">
                                <p x-show="errors.date_of_birth" class="text-red-500 text-sm mt-1"
                                    x-text="errors.date_of_birth"></p>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium mb-2">লিঙ্গ <span
                                        class="text-red-500">*</span></label>
                                <select x-model="form.gender"
                                    class="w-full px-4 py-3 border-2 rounded-xl focus:border-primary-500 outline-none"
                                    :class="errors.gender ? 'border-red-400' : 'border-gray-200'">
                                    <option value="">-- নির্বাচন করুন --</option>
                                    <option value="male">ছেলে</option>
                                    <option value="female">মেয়ে</option>
                                </select>
                                <p x-show="errors.gender" class="text-red-500 text-sm mt-1" x-text="errors.gender"></p>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium mb-2">রক্তের গ্রুপ</label>
                                <select x-model="form.blood_group"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 outline-none">
                                    <option value="">-- নির্বাচন করুন --</option>
                                    <option>A+</option>
                                    <option>A-</option>
                                    <option>B+</option>
                                    <option>B-</option>
                                    <option>AB+</option>
                                    <option>AB-</option>
                                    <option>O+</option>
                                    <option>O-</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium mb-2">ধর্ম</label>
                                <input type="text" x-model="form.religion"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Guardian Info -->
                    <div x-show="step === 2" x-cloak>
                        <h2 class="text-2xl font-bold text-gray-900 mb-8">অভিভাবকের তথ্য</h2>

                        <div class="space-y-8">
                            <div class="bg-blue-50 rounded-2xl p-6">
                                <h3 class="font-bold text-blue-800 mb-4">পিতার তথ্য</h3>
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-gray-700 font-medium mb-2">পিতার নাম <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" x-model="form.father_name"
                                            class="w-full px-4 py-3 border-2 rounded-xl focus:border-primary-500 outline-none"
                                            :class="errors.father_name ? 'border-red-400' : 'border-gray-200'">
                                        <p x-show="errors.father_name" class="text-red-500 text-sm mt-1"
                                            x-text="errors.father_name"></p>
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 font-medium mb-2">মোবাইল নম্বর <span
                                                class="text-red-500">*</span></label>
                                        <input type="tel" x-model="form.father_phone" placeholder="01XXXXXXXXX"
                                            class="w-full px-4 py-3 border-2 rounded-xl focus:border-primary-500 outline-none"
                                            :class="errors.father_phone ? 'border-red-400' : 'border-gray-200'">
                                        <p x-show="errors.father_phone" class="text-red-500 text-sm mt-1"
                                            x-text="errors.father_phone"></p>
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 font-medium mb-2">পেশা</label>
                                        <input type="text" x-model="form.father_occupation"
                                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 outline-none">
                                    </div>
                                </div>
                            </div>

                            <div class="bg-pink-50 rounded-2xl p-6">
                                <h3 class="font-bold text-pink-800 mb-4">মাতার তথ্য</h3>
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-gray-700 font-medium mb-2">মাতার নাম <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" x-model="form.mother_name"
                                            class="w-full px-4 py-3 border-2 rounded-xl focus:border-primary-500 outline-none"
                                            :class="errors.mother_name ? 'border-red-400' : 'border-gray-200'">
                                        <p x-show="errors.mother_name" class="text-red-500 text-sm mt-1"
                                            x-text="errors.mother_name"></p>
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 font-medium mb-2">মোবাইল নম্বর</label>
                                        <input type="tel" x-model="form.mother_phone" placeholder="01XXXXXXXXX"
                                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 outline-none">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Academic Info -->
                    <div x-show="step === 3" x-cloak>
                        <h2 class="text-2xl font-bold text-gray-900 mb-8">ঠিকানা ও শিক্ষাগত তথ্য</h2>

                        <div class="space-y-6">
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">বর্তমান ঠিকানা <span
                                        class="text-red-500">*</span></label>
                                <textarea x-model="form.present_address" rows="3"
                                    class="w-full px-4 py-3 border-2 rounded-xl focus:border-primary-500 outline-none resize-none"
                                    :class="errors.present_address ? 'border-red-400' : 'border-gray-200'"></textarea>
                                <p x-show="errors.present_address" class="text-red-500 text-sm mt-1"
                                    x-text="errors.present_address"></p>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium mb-2">স্থায়ী ঠিকানা</label>
                                <textarea x-model="form.permanent_address" rows="3"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 outline-none resize-none"></textarea>
                            </div>

                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-gray-700 font-medium mb-2">ভর্তি ইচ্ছুক শ্রেণী <span
                                            class="text-red-500">*</span></label>
                                    <select x-model="form.class_id"
                                        class="w-full px-4 py-3 border-2 rounded-xl focus:border-primary-500 outline-none"
                                        :class="errors.class_id ? 'border-red-400' : 'border-gray-200'">
                                        <option value="">-- শ্রেণী নির্বাচন করুন --</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                    <p x-show="errors.class_id" class="text-red-500 text-sm mt-1" x-text="errors.class_id">
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-gray-700 font-medium mb-2">পূর্ববর্তী প্রতিষ্ঠান</label>
                                    <input type="text" x-model="form.previous_institution"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 outline-none">
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-gray-700 font-medium mb-2">ইমেইল</label>
                                    <input type="email" x-model="form.email"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-gray-700 font-medium mb-2">মোবাইল নম্বর</label>
                                    <input type="tel" x-model="form.phone"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 outline-none">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Review & Submit -->
                    <div x-show="step === 4" x-cloak>
                        <h2 class="text-2xl font-bold text-gray-900 mb-8">তথ্য পর্যালোচনা ও জমা</h2>

                        <div class="space-y-6">
                            <div class="bg-gray-50 rounded-2xl p-6">
                                <h3 class="font-bold text-gray-800 mb-4">ছাত্র তথ্য</h3>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div><span class="text-gray-500">নাম:</span> <span class="font-medium"
                                            x-text="form.student_name"></span></div>
                                    <div><span class="text-gray-500">জন্ম তারিখ:</span> <span class="font-medium"
                                            x-text="form.date_of_birth"></span></div>
                                    <div><span class="text-gray-500">লিঙ্গ:</span> <span class="font-medium"
                                            x-text="form.gender === 'male' ? 'ছেলে' : 'মেয়ে'"></span></div>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-2xl p-6">
                                <h3 class="font-bold text-gray-800 mb-4">অভিভাবক তথ্য</h3>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div><span class="text-gray-500">পিতা:</span> <span class="font-medium"
                                            x-text="form.father_name"></span></div>
                                    <div><span class="text-gray-500">ফোন:</span> <span class="font-medium"
                                            x-text="form.father_phone"></span></div>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-2xl p-6">
                                <h3 class="font-bold text-gray-800 mb-4">শিক্ষাগত তথ্য</h3>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div><span class="text-gray-500">শ্রেণী:</span> <span class="font-medium"
                                            x-text="document.querySelector(`option[value='${form.class_id}']`)?.text || form.class_id"></span>
                                    </div>
                                    <div><span class="text-gray-500">ঠিকানা:</span> <span class="font-medium"
                                            x-text="form.present_address"></span></div>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <input type="checkbox" id="terms" class="mt-1 w-5 h-5 text-primary-600 rounded">
                                <label for="terms" class="text-gray-600">
                                    আমি সকল তথ্য সঠিক দিয়েছি এবং প্রতিষ্ঠানের <a href="#"
                                        class="text-primary-600 hover:underline">নিয়মাবলী</a> মেনে চলতে সম্মত আছি।
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex justify-between mt-10 pt-8 border-t border-gray-100">
                        <button x-show="step > 1" @click="step--"
                            class="px-8 py-3 border-2 border-gray-300 text-gray-700 rounded-full font-semibold hover:bg-gray-100 transition-colors">
                            ← পূর্ববর্তী
                        </button>
                        <div x-show="step === 1"></div>

                        <button x-show="step < 4" @click="nextStep()"
                            class="px-8 py-3 bg-primary-600 text-white rounded-full font-semibold hover:bg-primary-700 transition-colors">
                            পরবর্তী →
                        </button>

                        <button x-show="step === 4" @click="submitForm()" :disabled="loading"
                            class="px-8 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-full font-semibold shadow-lg hover:shadow-xl transition-all disabled:opacity-50">
                            <span x-show="!loading">✓ আবেদন জমা দিন</span>
                            <span x-show="loading" class="flex items-center gap-2">
                                <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                জমা হচ্ছে...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection