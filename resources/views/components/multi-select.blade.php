@props([
    'placeholder' => 'Select options...',
    'options' => [],
    'selected' => [],
    'wireModel' => null,
    'searchable' => false,
])

@php
    $componentId = 'multi-select-' . uniqid();
@endphp

<div x-data="{
    open: false,
    selected: @entangle($wireModel).live,
    search: '',
    options: {{ json_encode($options) }},
    get filteredOptions() {
        if (!this.search.trim()) return this.options;
        const searchTerm = this.search.toLowerCase().trim();
        return this.options.filter(option => 
            option.label.toLowerCase().includes(searchTerm)
        );
    },
    get selectedLabels() {
        if (!this.selected || this.selected.length === 0) return [];
        return this.options.filter(option => 
            this.selected.includes(option.value.toString())
        ).map(option => option.label);
    },
    toggleOption(value) {
        const stringValue = value.toString();
        if (!this.selected) this.selected = [];
        if (this.selected.includes(stringValue)) {
            this.selected = this.selected.filter(item => item !== stringValue);
        } else {
            this.selected = [...this.selected, stringValue];
        }
    },
    removeOption(value) {
        const stringValue = value.toString();
        this.selected = this.selected.filter(item => item !== stringValue);
    },
    isSelected(value) {
        return this.selected && this.selected.includes(value.toString());
    },
    clearSearch() {
        this.search = '';
    },
    init() {
        // Ensure selected is always an array
        if (!Array.isArray(this.selected)) {
            this.selected = [];
        }
    }
}" 
@click.away="open = false" 
@keydown.escape="open = false"
class="relative w-full">
    <!-- Trigger Button -->
    <button
        type="button"
        @click="open = !open"
        class="relative w-full bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-600 rounded-lg shadow-sm pl-3 text-left cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 text-sm transition-all duration-200 hover:border-zinc-400 dark:hover:border-zinc-500"
        :class="{ 
            'ring-2 ring-indigo-500 border-indigo-500 dark:ring-indigo-400 dark:border-indigo-400': open,
            'shadow-md': open,
            'pr-16': selected && selected.length > 0,
            'pr-10': !selected || selected.length === 0,
            'py-2.5': true
        }"
    >
        <span class="block truncate">
            <template x-if="!selected || selected.length === 0">
                <span class="text-zinc-500 dark:text-zinc-400">{{ $placeholder }}</span>
            </template>
            <template x-if="selected && selected.length === 1">
                <span class="text-zinc-900 dark:text-zinc-100 font-medium" x-text="selectedLabels[0]"></span>
            </template>
            <template x-if="selected && selected.length > 1">
                <span class="text-zinc-900 dark:text-zinc-100 font-medium" x-text="`${selected.length} items selected`"></span>
            </template>
        </span>
        
        <!-- Clear Button (when selections exist) -->
        <template x-if="selected && selected.length > 0">
            <button
                type="button"
                @click.stop="selected = []; open = false"
                class="absolute inset-y-0 right-8 flex items-center pr-1 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors duration-150"
                title="Clear selections"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </template>
        
        <!-- Dropdown Arrow -->
        <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
            <svg class="h-4 w-4 text-zinc-400 dark:text-zinc-500 transition-transform duration-200" :class="{ 'rotate-180': open }" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </span>
    </button>

    <!-- Selected Items Display -->
    <template x-if="selected && selected.length > 0">
        <div class="mt-2 flex flex-wrap gap-2">
            <template x-for="(value, index) in selected" :key="value">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-200 transition-colors duration-200">
                    <span x-text="selectedLabels[index]"></span>
                    <button 
                        type="button"
                        @click="removeOption(value)"
                        class="inline-flex items-center justify-center w-4 h-4 rounded-full hover:bg-indigo-200 dark:hover:bg-indigo-800 focus:outline-none focus:bg-indigo-200 dark:focus:bg-indigo-800 transition-colors duration-150"
                        tabindex="-1"
                    >
                        <svg class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </span>
            </template>
        </div>
    </template>

    <!-- Dropdown -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute z-50 mt-2 w-full bg-white dark:bg-zinc-800 shadow-xl max-h-72 rounded-lg text-base ring-1 ring-black ring-opacity-5 dark:ring-zinc-600 overflow-hidden focus:outline-none sm:text-sm border border-zinc-200 dark:border-zinc-600"
        style="display: none;"
        @keydown.arrow-down.prevent="$focus.wrap().next()"
        @keydown.arrow-up.prevent="$focus.wrap().previous()"
    >
        @if($searchable)
            <!-- Search Input -->
            <div class="sticky top-0 z-20 bg-white dark:bg-zinc-800 border-b border-zinc-100 dark:border-zinc-700 p-3 shadow-sm">
                <div class="relative">
                    <input
                        type="text"
                        x-model="search"
                        x-ref="searchInput"
                        placeholder="Search options..."
                        class="w-full pl-9 pr-8 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-400 dark:focus:border-indigo-400 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 text-sm placeholder-zinc-400 dark:placeholder-zinc-500"
                        @keydown.escape="search = ''; open = false"
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-zinc-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <template x-if="search">
                        <button
                            type="button"
                            @click="clearSearch()"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </template>
                </div>
            </div>
        @endif

        <!-- Options List -->
        <div class="py-2 overflow-y-auto max-h-60"
             style="scrollbar-width: thin; scrollbar-color: rgba(156, 163, 175, 0.5) transparent;">
            <template x-for="option in filteredOptions" :key="option.value">
                <div
                    @click="toggleOption(option.value); $el.blur()"
                    @mousedown.prevent
                    class="cursor-pointer select-none relative py-3 pl-4 pr-12 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150"
                    :class="{ 
                        'bg-indigo-50 dark:bg-indigo-900/20 border-l-2 border-indigo-500': isSelected(option.value),
                        'text-indigo-600 dark:text-indigo-400': isSelected(option.value),
                        'text-zinc-900 dark:text-zinc-100': !isSelected(option.value)
                    }"
                    tabindex="0"
                    @keydown.enter="toggleOption(option.value)"
                    @keydown.space.prevent="toggleOption(option.value)"
                >
                    <div class="flex items-center">
                        <!-- Checkbox -->
                        <div class="flex-shrink-0 mr-3">
                            <div class="w-4 h-4 rounded border-2 border-zinc-300 dark:border-zinc-600 flex items-center justify-center transition-all duration-150"
                                 :class="{ 
                                     'bg-indigo-600 dark:bg-indigo-500 border-indigo-600 dark:border-indigo-500': isSelected(option.value),
                                     'bg-white dark:bg-zinc-800': !isSelected(option.value)
                                 }">
                                <template x-if="isSelected(option.value)">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </template>
                            </div>
                        </div>
                        <span class="block truncate font-medium" x-text="option.label"></span>
                    </div>
                </div>
            </template>

            <!-- No Results Message -->
            <template x-if="filteredOptions.length === 0">
                <div class="py-6 px-4 text-center">
                    <div class="text-zinc-500 dark:text-zinc-400 text-sm">
                        <div class="mb-2">
                            <svg class="mx-auto h-8 w-8 text-zinc-300 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.137 0-4.146.832-5.657 2.343" />
                            </svg>
                        </div>
                        No options match your search
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>