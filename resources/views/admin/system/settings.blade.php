<x-layouts.admin title="Cache & Settings" breadcrumb="System / Cache & Settings">
    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <x-card class="text-sm">
            <div class="text-graphite-500">Environment</div>
            <div class="mt-1 font-semibold">{{ $appEnv }} · debug {{ $appDebug ? 'on' : 'off' }}</div>
        </x-card>
        <x-card class="text-sm">
            <div class="text-graphite-500">Cache driver</div>
            <div class="mt-1 font-semibold">{{ $cacheDriver }}</div>
        </x-card>
        <x-card class="text-sm">
            <div class="text-graphite-500">Queue connection</div>
            <div class="mt-1 font-semibold">{{ $queueConnection }}</div>
        </x-card>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <form method="POST" action="{{ route('admin.system.settings.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <x-card class="space-y-4">
                <h3 class="font-semibold">SEO defaults</h3>
                <div>
                    <x-label for="meta_title">Site meta title</x-label>
                    <x-input name="meta_title" id="meta_title" :value="old('meta_title', $seo['meta_title'] ?? '')" />
                </div>
                <div>
                    <x-label for="meta_description">Site meta description</x-label>
                    <textarea name="meta_description" id="meta_description" rows="3" class="form-input">{{ old('meta_description', $seo['meta_description'] ?? '') }}</textarea>
                </div>
                <p class="text-xs text-graphite-500">Also see <a href="{{ route('admin.seo.index') }}" class="text-blue-600 hover:underline">Marketing → SEO</a> for sitemap links.</p>
            </x-card>

            <x-card class="space-y-4">
                <h3 class="font-semibold">Email identity</h3>
                <div>
                    <x-label for="mail_from_name">From name</x-label>
                    <x-input name="mail_from_name" id="mail_from_name" :value="old('mail_from_name', $email['mail_from_name'] ?? '')" />
                </div>
                <div>
                    <x-label for="mail_from_address">From address</x-label>
                    <x-input type="email" name="mail_from_address" id="mail_from_address" :value="old('mail_from_address', $email['mail_from_address'] ?? '')" />
                </div>
                <x-form-hint>Provider credentials stay in <code>.env</code> (<code>MAIL_*</code>).</x-form-hint>
            </x-card>

            <x-card class="space-y-4">
                <h3 class="font-semibold">Analytics</h3>
                <div>
                    <x-label for="ga_measurement_id">GA4 measurement ID</x-label>
                    <x-input name="ga_measurement_id" id="ga_measurement_id" :value="old('ga_measurement_id', $analytics['ga_measurement_id'] ?? '')" placeholder="G-XXXXXXXX" />
                </div>
                <div>
                    <x-label for="gtm_container_id">GTM container ID</x-label>
                    <x-input name="gtm_container_id" id="gtm_container_id" :value="old('gtm_container_id', $analytics['gtm_container_id'] ?? '')" placeholder="GTM-XXXXXXX" />
                </div>
            </x-card>

            <x-button type="submit">Save settings</x-button>
        </form>

        <x-card>
            <h3 class="font-semibold">Clear caches</h3>
            <p class="mt-2 text-sm text-graphite-500">Use carefully on production. Config/route clear does not rebuild caches automatically.</p>
            <div class="mt-4 grid gap-2 sm:grid-cols-2">
                @foreach(['application' => 'App cache', 'views' => 'Views', 'config' => 'Config', 'routes' => 'Routes', 'all' => 'Everything'] as $type => $label)
                    <form method="POST" action="{{ route('admin.system.cache.clear') }}">
                        @csrf
                        <input type="hidden" name="type" value="{{ $type }}">
                        <x-button type="submit" variant="secondary" class="w-full">{{ $label }}</x-button>
                    </form>
                @endforeach
            </div>
        </x-card>
    </div>
</x-layouts.admin>
