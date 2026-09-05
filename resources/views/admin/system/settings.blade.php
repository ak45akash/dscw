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

            <x-card class="space-y-4">
                <h3 class="font-semibold">SMS (Msg91 / Twilio)</h3>
                <p class="text-xs text-graphite-500">Media disk: <code>{{ $mediaDisk }}</code> (<code>MEDIA_DISK</code>). Reminders run hourly via scheduler.</p>
                <div class="flex flex-wrap gap-4">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="sms_enabled" value="1" @checked(old('sms_enabled', $sms['enabled'] ?? false))>
                        Enable SMS
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="sms_confirmations_enabled" value="1" @checked(old('sms_confirmations_enabled', $sms['confirmations_enabled'] ?? false))>
                        Booking confirmations
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="sms_reminders_enabled" value="1" @checked(old('sms_reminders_enabled', $sms['reminders_enabled'] ?? false))>
                        Reminders
                    </label>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <x-label for="sms_provider">Provider</x-label>
                        <select name="sms_provider" id="sms_provider" class="form-input">
                            @foreach(['null' => 'None', 'msg91' => 'MSG91', 'twilio' => 'Twilio'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('sms_provider', $sms['provider'] ?? 'null') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-label for="sms_reminder_hours_before">Reminder hours before</x-label>
                        <x-input type="number" min="1" max="168" name="sms_reminder_hours_before" id="sms_reminder_hours_before" :value="old('sms_reminder_hours_before', $sms['reminder_hours_before'] ?? 24)" />
                    </div>
                    <div>
                        <x-label for="sms_api_key">API key / Auth token</x-label>
                        <x-input name="sms_api_key" id="sms_api_key" :value="old('sms_api_key', $sms['api_key'] ?? '')" />
                    </div>
                    <div>
                        <x-label for="sms_account_sid">Twilio Account SID</x-label>
                        <x-input name="sms_account_sid" id="sms_account_sid" :value="old('sms_account_sid', $sms['account_sid'] ?? '')" />
                    </div>
                    <div>
                        <x-label for="sms_sender_id">MSG91 sender ID</x-label>
                        <x-input name="sms_sender_id" id="sms_sender_id" :value="old('sms_sender_id', $sms['sender_id'] ?? 'DSCW')" />
                    </div>
                    <div>
                        <x-label for="sms_from_number">Twilio from number</x-label>
                        <x-input name="sms_from_number" id="sms_from_number" :value="old('sms_from_number', $sms['from_number'] ?? '')" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-label for="sms_template_id">MSG91 template ID (optional)</x-label>
                        <x-input name="sms_template_id" id="sms_template_id" :value="old('sms_template_id', $sms['template_id'] ?? '')" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-label for="sms_confirmation_template">Confirmation template</x-label>
                        <textarea name="sms_confirmation_template" id="sms_confirmation_template" rows="2" class="form-input">{{ old('sms_confirmation_template', $sms['confirmation_template'] ?? '') }}</textarea>
                    </div>
                    <div class="sm:col-span-2">
                        <x-label for="sms_reminder_template">Reminder template</x-label>
                        <textarea name="sms_reminder_template" id="sms_reminder_template" rows="2" class="form-input">{{ old('sms_reminder_template', $sms['reminder_template'] ?? '') }}</textarea>
                        <x-form-hint>Placeholders: {name} {reference} {date} {time} {service} {location}</x-form-hint>
                    </div>
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
