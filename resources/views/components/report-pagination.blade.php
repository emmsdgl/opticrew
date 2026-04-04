<template x-if="totalPages > 1">
    <div class="flex items-center justify-between mt-4 px-2">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Showing <span x-text="(page - 1) * perPage + 1"></span>-<span x-text="Math.min(page * perPage, total)"></span> of <span x-text="total"></span>
        </p>
        <div class="flex items-center gap-1">
            <button @click="page = Math.max(1, page - 1)" :disabled="page === 1"
                class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed text-gray-700 dark:text-gray-300">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </button>
            <template x-for="p in totalPages" :key="p">
                <button @click="page = p"
                    class="px-3 py-1.5 text-sm rounded-lg transition-colors"
                    :class="page === p ? 'bg-blue-600 text-white' : 'border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'"
                    x-text="p"></button>
            </template>
            <button @click="page = Math.min(totalPages, page + 1)" :disabled="page === totalPages"
                class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed text-gray-700 dark:text-gray-300">
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </button>
        </div>
    </div>
</template>
