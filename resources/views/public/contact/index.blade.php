<x-layouts.public :seo-title="$seoTitle" :seo-description="$seoDescription">
    <x-page-hero
        badge="Get in Touch"
        title="Contact Diamond Steam Car Wash"
        subtitle="Have a question about our services, need a custom quote, or want to discuss paint protection options? We're here to help."
    />

    <x-breadcrumbs :items="[['label' => 'Home', 'url' => route('home')], ['label' => 'Contact']]" />

    <section class="py-16 sm:py-20">
        <div class="container-site">
            <div class="grid gap-12 lg:grid-cols-2">
                <div>
                    <h2 class="text-2xl font-bold text-graphite-900 dark:text-white">Send us a message</h2>
                    <p class="mt-3 text-graphite-600 dark:text-graphite-300">Fill out the form and our team will get back to you within 24 hours on business days. For urgent enquiries, please call us directly.</p>

                    @if(session('success'))
                        <x-alert type="success" class="mt-6">{{ session('success') }}</x-alert>
                    @endif

                    <form method="POST" action="{{ route('contact.store') }}" class="mt-8 space-y-5">
                        @csrf
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <x-label for="name" required>Full Name</x-label>
                                <x-input name="name" id="name" :value="old('name')" required />
                                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <x-label for="phone">Phone</x-label>
                                <x-input name="phone" id="phone" :value="old('phone')" />
                            </div>
                        </div>
                        <div>
                            <x-label for="email" required>Email</x-label>
                            <x-input type="email" name="email" id="email" :value="old('email')" required />
                            @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <x-label for="subject">Subject</x-label>
                            <x-input name="subject" id="subject" :value="old('subject')" placeholder="e.g. Ceramic coating enquiry" />
                        </div>
                        <div>
                            <x-label for="message" required>Message</x-label>
                            <textarea name="message" id="message" rows="6" class="form-input" required>{{ old('message') }}</textarea>
                            @error('message')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <x-button type="submit">Send Message</x-button>
                    </form>
                </div>

                <div class="space-y-6">
                    <x-card>
                        <h3 class="text-lg font-semibold">Contact Information</h3>
                        <ul class="mt-4 space-y-4 text-sm">
                            @if(!empty($business['phone']))
                                <li>
                                    <p class="font-medium text-graphite-500">Phone</p>
                                    <a href="tel:{{ $business['phone'] }}" class="text-brand-700 hover:underline dark:text-brand-300">{{ $business['phone'] }}</a>
                                </li>
                            @endif
                            @if(!empty($business['email']))
                                <li>
                                    <p class="font-medium text-graphite-500">Email</p>
                                    <a href="mailto:{{ $business['email'] }}" class="text-brand-700 hover:underline dark:text-brand-300">{{ $business['email'] }}</a>
                                </li>
                            @endif
                            @if(!empty($business['whatsapp_number']))
                                <li>
                                    <p class="font-medium text-graphite-500">WhatsApp</p>
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $business['whatsapp_number']) }}" class="text-brand-700 hover:underline dark:text-brand-300" target="_blank" rel="noopener">{{ $business['whatsapp_number'] }}</a>
                                </li>
                            @endif
                        </ul>
                    </x-card>

                    @if($locations->isNotEmpty())
                        <div class="grid gap-4 sm:grid-cols-1">
                            @foreach($locations as $location)
                                <x-card class="h-full">
                                    <h3 class="text-lg font-semibold">{{ $location->name }}</h3>
                                    <p class="mt-3 text-sm leading-relaxed text-graphite-600 dark:text-graphite-300">{{ $location->fullAddress() }}</p>
                                    @if($location->phone)
                                        <a href="tel:{{ $location->phone }}" class="mt-3 inline-block text-sm font-medium text-brand-700 hover:underline dark:text-brand-300">{{ $location->phone }}</a>
                                    @endif
                                </x-card>
                            @endforeach
                        </div>
                    @elseif(!empty($business['address']))
                        <x-card>
                            <h3 class="text-lg font-semibold">Address</h3>
                            <p class="mt-3 text-sm text-graphite-700 dark:text-graphite-200">{{ $business['address'] }}</p>
                        </x-card>
                    @endif

                    <x-card>
                        <h3 class="text-lg font-semibold">Working Hours</h3>
                        <ul class="mt-4 space-y-2 text-sm text-graphite-600 dark:text-graphite-300">
                            <li class="flex justify-between gap-4"><span>Monday – Saturday</span><span class="text-right">8:00 AM – 8:00 PM</span></li>
                            <li class="flex justify-between gap-4"><span>Sunday</span><span class="text-right">9:00 AM – 6:00 PM</span></li>
                        </ul>
                    </x-card>
                </div>
            </div>
        </div>
    </section>

    @if($faqs->isNotEmpty())
    <section class="bg-graphite-50 py-16 dark:bg-graphite-900 sm:py-20">
        <div class="container-site max-w-3xl">
            <x-section-heading title="Common Questions" class="mb-8" />
            <div class="space-y-4">
                @foreach($faqs as $faq)
                    <details class="card p-5">
                        <summary class="cursor-pointer font-medium text-graphite-900 dark:text-white">{{ $faq->question }}</summary>
                        <p class="mt-3 text-sm text-graphite-600 dark:text-graphite-300">{{ $faq->answer }}</p>
                    </details>
                @endforeach
            </div>
            <p class="mt-6 text-center text-sm text-graphite-500">
                <a href="{{ route('faq.index') }}" class="font-medium text-brand-600 hover:underline">View all FAQs →</a>
            </p>
        </div>
    </section>
    @endif
</x-layouts.public>
