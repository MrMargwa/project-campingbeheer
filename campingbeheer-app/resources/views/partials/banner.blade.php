@props(['title' => '', 'image' => '/images/default-image.png'])

<section class="-mt-10 mb-10 relative overflow-hidden" style="width:100vw;margin-left:calc(-50vw + 50%)">
    <div class="h-56 md:h-64 relative flex items-center justify-center overflow-hidden">
        <img src="{{ asset($image) }}" alt="" class="absolute inset-0 w-full h-full object-cover" style="object-position: 50% 60%">
        <div class="absolute inset-0 bg-accent/55"></div>
        <h1 class="relative z-10 text-4xl md:text-5xl font-bold text-white tracking-tight text-center px-4"
            @if(isset($i18nKey)) data-i18n="{{ $i18nKey }}" @endif>
            {{ $title }}
        </h1>
    </div>
</section>
