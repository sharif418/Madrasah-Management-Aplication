@props(['title', 'subtitle' => null])

<section class="pt-32 pb-20"
    style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;0.05&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E'), linear-gradient(135deg, #047857 0%, #065f46 50%, #064e3b 100%); background-color: #047857;">
    <div class="container mx-auto px-4 text-center text-white">
        <h1 class="text-4xl md:text-5xl font-bold mb-4" data-aos="fade-up">{{ $title }}</h1>
        @if($subtitle)
            <p class="text-xl opacity-80" data-aos="fade-up" data-aos-delay="100">{{ $subtitle }}</p>
        @endif
        {{ $slot }}
    </div>
</section>