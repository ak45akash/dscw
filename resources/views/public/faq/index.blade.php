<x-layouts.public :seo-title="$seoTitle" :seo-description="$seoDescription">
    <x-page-hero
        badge="Help Centre"
        title="Frequently Asked Questions"
        subtitle="Everything you need to know about our services, booking process, pricing, and policies."
    />

    <x-breadcrumbs :items="[['label' => 'Home', 'url' => route('home')], ['label' => 'FAQ']]" />

    <section class="py-16 sm:py-20">
        <div class="container-site max-w-4xl">
            <p class="mb-12 text-lg leading-relaxed text-graphite-600 dark:text-graphite-300">
                We've compiled answers to the most common questions our customers ask. If you don't find what you're looking for, please don't hesitate to <a href="{{ route('contact.index') }}" class="font-medium text-brand-600 hover:underline">contact us</a> — our team is always happy to help.
            </p>

            @foreach($faqGroups as $category => $faqs)
                <div class="mb-12">
                    <h2 class="mb-6 text-xl font-bold text-graphite-900 dark:text-white">{{ $category }}</h2>
                    <div class="space-y-4">
                        @foreach($faqs as $faq)
                            <details class="card p-5 group">
                                <summary class="cursor-pointer list-none font-medium text-graphite-900 dark:text-white">
                                    <span class="flex items-center justify-between gap-4">
                                        {{ $faq->question }}
                                        <svg class="h-5 w-5 shrink-0 text-graphite-400 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </span>
                                </summary>
                                <p class="mt-4 text-sm leading-relaxed text-graphite-600 dark:text-graphite-300">{{ $faq->answer }}</p>
                            </details>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="bg-graphite-50 py-16 dark:bg-graphite-900/50 sm:py-20">
        <div class="container-site text-center">
            <h2 class="text-2xl font-bold text-graphite-900 dark:text-white">Still have questions?</h2>
            <p class="mx-auto mt-3 max-w-xl text-graphite-600 dark:text-graphite-300">Our friendly team is ready to assist you with service recommendations, pricing, and booking enquiries.</p>
            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <x-button href="{{ route('contact.index') }}">Contact Us</x-button>
                <x-button href="{{ route('booking.index') }}" variant="secondary">Book a Service</x-button>
            </div>
        </div>
    </section>
</x-layouts.public>
