{{-- Prayer Times Widget --}}
<div x-data="prayerTimes(@js($prayerData ?? null))" x-init="initWidget()" class="bg-white/10 backdrop-blur-md rounded-2xl p-6 text-white border border-white/10 shadow-lg">
    <div class="flex items-start justify-between mb-6">
        <div class="flex items-center gap-3">
            <div class="relative">
                <div class="w-12 h-12 bg-gradient-to-br from-gold-400 to-gold-600 rounded-xl flex items-center justify-center shadow-lg transform rotate-3">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full animate-ping"></div>
                <div class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full border-2 border-primary-800"></div>
            </div>
            <div>
                <h3 class="font-bold text-xl leading-none mb-1">নামাজের সময়</h3>
                <p class="text-gold-200 text-sm font-medium" x-text="hijriDate || 'লোড হচ্ছে...'"></p>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-2xl font-bold tracking-wider" x-text="currentTime"></span>
                    <span class="text-xs bg-white/20 px-1.5 py-0.5 rounded" x-text="currentAmPm"></span>
                </div>
            </div>
        </div>
        
        <!-- Next Prayer Countdown -->
        <div x-show="nextPrayerTime" class="text-right">
            <p class="text-xs text-gray-300 mb-1">পরবর্তী: <span x-text="prayerNames[nextPrayerName]"></span></p>
            <div class="inline-block bg-black/30 rounded-lg px-3 py-1 border border-white/10">
                <span class="font-mono font-bold text-gold-400" x-text="timeRemaining">--:--:--</span>
            </div>
        </div>
    </div>

    <!-- Prayer Times Grid -->
    <div class="grid grid-cols-5 gap-2 text-center mb-2">
        <template x-for="(time, key) in displayTimes" :key="key">
            <div class="relative group">
                <div class="rounded-xl p-2 transition-all duration-300 border border-transparent"
                     :class="currentPrayer === key ? 'bg-gold-500/20 border-gold-500/50 shadow-[0_0_15px_rgba(234,179,8,0.2)]' : 'bg-white/5 hover:bg-white/10'">
                    
                    <p class="text-[10px] uppercase tracking-wider mb-1" 
                       :class="currentPrayer === key ? 'text-gold-300 font-bold' : 'text-gray-400'"
                       x-text="prayerNames[key]"></p>
                    
                    <p class="font-bold text-sm md:text-base whitespace-nowrap" 
                       :class="currentPrayer === key ? 'text-white scale-110' : 'text-gray-200'"
                       x-text="toBengaliTime(time)"></p>
                       
                    <!-- Active Indicator -->
                    <div x-show="currentPrayer === key" 
                         class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-8 h-1 bg-gold-500 rounded-full shadow-[0_0_8px_rgba(234,179,8,0.6)]"></div>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
    function prayerTimes(serverData) {
        return {
            hijriDate: '',
            currentTime: '',
            currentAmPm: '',
            currentPrayer: null,
            nextPrayerName: null,
            nextPrayerTime: null,
            timeRemaining: '',
            
            // Raw times in 24h format for calculation
            rawTimes: {},
            
            // Display times in 12h format
            displayTimes: {
                Fajr: '--:--',
                Dhuhr: '--:--',
                Asr: '--:--',
                Maghrib: '--:--',
                Isha: '--:--'
            },
            
            prayerNames: {
                Fajr: 'ফজর',
                Dhuhr: 'যোহর',
                Asr: 'আসর',
                Maghrib: 'মাগরিব',
                Isha: 'ইশা'
            },

            initWidget() {
                // Start clock
                this.updateClock();
                setInterval(() => this.updateClock(), 1000);
                
                // Use Server Data if available
                if (serverData) {
                    this.processData(serverData);
                } else {
                    // Fallback to client fetch if server fetch failed completely
                    this.fetchData();
                }
            },

            updateClock() {
                const now = new Date();
                
                // Format display time
                let hours = now.getHours();
                const minutes = now.getMinutes();
                const seconds = now.getSeconds();
                const ampm = hours >= 12 ? 'PM' : 'AM';
                
                hours = hours % 12;
                hours = hours ? hours : 12; 
                
                this.currentTime = `${this.toBengaliNumber(hours)}:${this.toBengaliNumber(minutes.toString().padStart(2, '0'))}:${this.toBengaliNumber(seconds.toString().padStart(2, '0'))}`;
                this.currentAmPm = ampm;

                // Update countdown
                if (this.nextPrayerTime) {
                    this.updateCountdown(now);
                }
            },

            updateCountdown(now) {
                const diff = this.nextPrayerTime - now;
                
                if (diff <= 0) {
                    this.calculateNextPrayer(); // Refresh
                    return;
                }

                const h = Math.floor(diff / (1000 * 60 * 60));
                const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const s = Math.floor((diff % (1000 * 60)) / 1000);

                this.timeRemaining = `${this.toBengaliNumber(h)}:${this.toBengaliNumber(m.toString().padStart(2, '0'))}:${this.toBengaliNumber(s.toString().padStart(2, '0'))}`;
            },

            processData(data) {
                const timings = data.timings;
                const hijri = data.date.hijri;
                
                // Hijri Date
                const monthName = hijri.month.en === 'Ramadan' ? 'রমজান' : (hijri.month.bn || hijri.month.en);
                this.hijriDate = `${this.toBengaliNumber(hijri.day)} ${monthName} ${this.toBengaliNumber(hijri.year)}`;

                // Times
                this.rawTimes = {
                    Fajr: timings.Fajr,
                    Dhuhr: timings.Dhuhr,
                    Asr: timings.Asr,
                    Maghrib: timings.Maghrib,
                    Isha: timings.Isha
                };

                this.displayTimes = {
                    Fajr: timings.Fajr,
                    Dhuhr: timings.Dhuhr,
                    Asr: timings.Asr,
                    Maghrib: timings.Maghrib,
                    Isha: timings.Isha
                };
                
                this.calculateNextPrayer();
            },

            async fetchData() {
                try {
                    const lat = 23.8103;
                    const lng = 90.4125;
                    const date = new Date();
                    const url = `https://api.aladhan.com/v1/timings/${date.getDate()}-${date.getMonth() + 1}-${date.getFullYear()}?latitude=${lat}&longitude=${lng}&method=1&school=1`;
                    
                    const response = await fetch(url);
                    const data = await response.json();
                    
                    if (data.code === 200) {
                        this.processData(data.data);
                    }
                } catch (error) {
                    console.error('Fetch failed', error);
                    this.hijriDate = 'তারিখ লোড হয়নি';
                }
            },

            calculateNextPrayer() {
                const now = new Date();
                let foundNext = false;
                let current = null;

                for (const [key, time] of Object.entries(this.rawTimes)) {
                    if(!time) continue;
                    
                    const [h, m] = time.split(':');
                    const pTime = new Date();
                    pTime.setHours(parseInt(h), parseInt(m), 0);

                    if (now < pTime) {
                        this.nextPrayerName = key;
                        this.nextPrayerTime = pTime;
                        foundNext = true;
                        break;
                    }
                    current = key;
                }

                if (!foundNext && this.rawTimes.Fajr) {
                    this.nextPrayerName = 'Fajr';
                    const [h, m] = this.rawTimes.Fajr.split(':');
                    const pTime = new Date();
                    pTime.setDate(pTime.getDate() + 1); 
                    pTime.setHours(parseInt(h), parseInt(m), 0);
                    this.nextPrayerTime = pTime;
                    current = 'Isha';
                }

                this.currentPrayer = current;
            },

            toBengaliNumber(num) {
                if(num === undefined || num === null) return '';
                const bengaliNums = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
                return String(num).replace(/[0-9]/g, d => bengaliNums[d]);
            },

            toBengaliTime(time24) {
                if (!time24 || time24 === '--:--') return time24;
                const [h, m] = time24.split(':');
                let hour = parseInt(h);
                // convert to 12h
                hour = hour % 12 || 12; 
                return this.toBengaliNumber(hour) + ':' + this.toBengaliNumber(m);
            }
        }
    }
</script>