<x-layouts.admin title="Business Settings" breadcrumb="Business / Settings">
    <form method="POST" action="{{ route('admin.settings.business.update') }}" class="max-w-3xl space-y-8">
        @csrf
        @method('PUT')

        <x-card>
            <h3 class="text-lg font-semibold">General</h3>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-label for="business_name" required>Business Name</x-label>
                    <x-input name="business_name" id="business_name" :value="$settings['business_name'] ?? ''" required />
                </div>
                <div class="sm:col-span-2">
                    <x-label for="tagline">Tagline</x-label>
                    <x-input name="tagline" id="tagline" :value="$settings['tagline'] ?? ''" />
                </div>
                <div>
                    <x-label for="phone">Phone</x-label>
                    <x-input name="phone" id="phone" :value="$settings['phone'] ?? ''" />
                </div>
                <div>
                    <x-label for="email">Email</x-label>
                    <x-input type="email" name="email" id="email" :value="$settings['email'] ?? ''" />
                </div>
                <div class="sm:col-span-2">
                    <x-label for="address">Address</x-label>
                    <textarea name="address" id="address" rows="3" class="form-input">{{ old('address', $settings['address'] ?? '') }}</textarea>
                </div>
                <div>
                    <x-label for="currency" required>Currency</x-label>
                    <x-input name="currency" id="currency" :value="$settings['currency'] ?? 'INR'" required />
                </div>
                <div>
                    <x-label for="timezone" required>Timezone</x-label>
                    <x-input name="timezone" id="timezone" :value="$settings['timezone'] ?? 'Asia/Kolkata'" required />
                </div>
            </div>
        </x-card>

        <x-card>
            <h3 class="text-lg font-semibold">Social & Contact</h3>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <x-label for="whatsapp_number">WhatsApp</x-label>
                    <x-input name="whatsapp_number" id="whatsapp_number" :value="$settings['whatsapp_number'] ?? ''" />
                </div>
                <div>
                    <x-label for="facebook_url">Facebook URL</x-label>
                    <x-input type="url" name="facebook_url" id="facebook_url" :value="$settings['facebook_url'] ?? ''" />
                </div>
                <div>
                    <x-label for="instagram_url">Instagram URL</x-label>
                    <x-input type="url" name="instagram_url" id="instagram_url" :value="$settings['instagram_url'] ?? ''" />
                </div>
                <div>
                    <x-label for="youtube_url">YouTube URL</x-label>
                    <x-input type="url" name="youtube_url" id="youtube_url" :value="$settings['youtube_url'] ?? ''" />
                </div>
            </div>
        </x-card>

        <x-card>
            <h3 class="text-lg font-semibold">Theme</h3>
            <div class="mt-4">
                <x-label for="theme_mode" required>Dark Mode Control</x-label>
                <select name="theme_mode" id="theme_mode" class="form-input mt-1.5">
                    @foreach(['system' => 'User / system preference', 'enabled' => 'Enabled (user can toggle)', 'disabled' => 'Disabled (light only)'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('theme_mode', $settings['mode'] ?? 'system') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </x-card>

        <div class="flex justify-end">
            <x-button type="submit">Save Settings</x-button>
        </div>
    </form>
</x-layouts.admin>
