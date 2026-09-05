<x-layouts.admin :title="$faq->exists ? 'Edit FAQ' : 'Add FAQ'" breadcrumb="Content / FAQs / Form">
    <form method="POST" action="{{ $faq->exists ? route('admin.faqs.update', $faq) : route('admin.faqs.store') }}" class="max-w-3xl space-y-6">
        @csrf
        @if($faq->exists) @method('PUT') @endif
        <x-card class="space-y-4">
            <div>
                <x-label for="question" required>Question</x-label>
                <x-input name="question" id="question" :value="old('question', $faq->question)" required />
            </div>
            <div>
                <x-label for="answer" required>Answer</x-label>
                <x-wysiwyg name="answer" id="answer" profile="full" :value="old('answer', $faq->answer)" :required="true" :rows="8" />
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <x-label for="category">Category</x-label>
                    <x-input name="category" id="category" :value="old('category', $faq->category)" placeholder="Booking, Services, …" />
                </div>
                <div>
                    <x-label for="display_order">Display order</x-label>
                    <x-input type="number" name="display_order" id="display_order" :value="old('display_order', $faq->display_order ?? 0)" />
                </div>
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $faq->is_active ?? true))>
                Active
            </label>
        </x-card>
        <div class="flex justify-between">
            <a href="{{ route('admin.faqs.index') }}" class="btn-secondary">Cancel</a>
            <x-button type="submit">Save FAQ</x-button>
        </div>
    </form>
</x-layouts.admin>
