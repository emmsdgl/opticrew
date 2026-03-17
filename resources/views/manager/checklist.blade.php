<x-layouts.general-manager :title="'Checklist'">
    <div class="flex flex-col gap-6 w-full" x-data="checklistManager()" x-init="init()">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Checklist</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Manage your company cleaning checklist</p>
            </div>
            <div class="flex gap-3" x-show="!checklist">
                <button @click="showCreateModal = true"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                    <i class="fa-solid fa-plus"></i>
                    Create Checklist
                </button>
            </div>
        </div>

        <!-- No Checklist State -->
        <div x-show="!checklist && !loading" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-8 text-center">
            <div class="w-16 h-16 mx-auto bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                <i class="fa-solid fa-clipboard-list text-2xl text-gray-400"></i>
            </div>
            <p class="text-gray-500 dark:text-gray-400 mb-4">No checklist found. Create one to get started.</p>
            <button @click="showCreateModal = true" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                Create your first checklist
            </button>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="flex justify-center py-12">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        </div>

        <!-- Checklist Content -->
        <template x-if="checklist">
            <div class="space-y-6">
                <!-- Important Reminders -->
                <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-xl border border-yellow-200 dark:border-yellow-800 p-4 md:p-5">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-base font-semibold text-yellow-800 dark:text-yellow-200 flex items-center gap-2">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            Important Reminders
                        </h2>
                        <button @click="editReminders()" class="text-sm text-yellow-700 dark:text-yellow-300 hover:underline">
                            <i class="fa-solid fa-pen text-xs"></i> Edit
                        </button>
                    </div>
                    <template x-if="!editingReminders">
                        <p class="text-sm text-yellow-700 dark:text-yellow-300" x-text="checklist.important_reminders || 'No reminders set. Click Edit to add.'"></p>
                    </template>
                    <template x-if="editingReminders">
                        <div class="flex gap-2">
                            <textarea x-model="remindersText" rows="3"
                                class="flex-1 px-3 py-2 border border-yellow-300 dark:border-yellow-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-yellow-500 resize-none"></textarea>
                            <div class="flex flex-col gap-2">
                                <button @click="saveReminders()" class="px-3 py-1 bg-yellow-600 text-white rounded-lg text-sm hover:bg-yellow-700">Save</button>
                                <button @click="editingReminders = false" class="px-3 py-1 border border-yellow-400 text-yellow-700 dark:text-yellow-300 rounded-lg text-sm hover:bg-yellow-100 dark:hover:bg-yellow-900/40">Cancel</button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Add Category Button -->
                <div class="flex justify-end">
                    <button @click="showAddCategoryModal = true"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                        <i class="fa-solid fa-folder-plus"></i>
                        Add Category
                    </button>
                </div>

                <!-- Categories -->
                <div class="space-y-4">
                    <template x-for="category in checklist.categories" :key="category.id">
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                            <!-- Category Header -->
                            <div class="p-4 md:p-5 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between cursor-pointer"
                                 @click="toggleCategory(category.id)">
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-chevron-right text-gray-400 transition-transform duration-200"
                                       :class="{ 'rotate-90': expandedCategories.includes(category.id) }"></i>
                                    <h3 class="text-base font-semibold text-gray-900 dark:text-white" x-text="category.name"></h3>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-full"
                                          x-text="(category.items || []).length + ' items'"></span>
                                </div>
                                <div class="flex items-center gap-2" @click.stop>
                                    <button @click="openEditCategory(category)" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors text-gray-500 hover:text-blue-600">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </button>
                                    <button @click="confirmDeleteCategory(category)" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors text-gray-500 hover:text-red-600">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                    <button @click="openAddItem(category)" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors text-gray-500 hover:text-green-600">
                                        <i class="fa-solid fa-plus text-xs"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Category Items (Expandable) -->
                            <div x-show="expandedCategories.includes(category.id)" x-collapse>
                                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                                    <template x-for="item in category.items || []" :key="item.id">
                                        <div class="p-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                                    <i class="fa-solid fa-check text-blue-600 dark:text-blue-400 text-xs"></i>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="item.name"></p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400" x-text="'Qty: ' + (item.quantity || '1')"></p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <button @click="openEditItem(category, item)" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors text-gray-500 hover:text-blue-600">
                                                    <i class="fa-solid fa-pen text-xs"></i>
                                                </button>
                                                <button @click="confirmDeleteItem(item)" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors text-gray-500 hover:text-red-600">
                                                    <i class="fa-solid fa-trash text-xs"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="!category.items || category.items.length === 0">
                                        <div class="p-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                            No items in this category. <button @click="openAddItem(category)" class="text-blue-600 dark:text-blue-400 hover:underline">Add one</button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Empty categories state -->
                    <template x-if="checklist && (!checklist.categories || checklist.categories.length === 0)">
                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-8 text-center">
                            <p class="text-gray-500 dark:text-gray-400">No categories yet. Add your first category to start building your checklist.</p>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        <!-- Create Checklist Modal -->
        <div x-show="showCreateModal" x-transition class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="showCreateModal = false"></div>
            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md" @click.stop>
                    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Create Checklist</h3>
                        <button @click="showCreateModal = false" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"><i class="fa-solid fa-xmark text-gray-500"></i></button>
                    </div>
                    <div class="p-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Checklist Name</label>
                            <input type="text" x-model="createForm.name" placeholder="e.g. Company Cleaning Checklist"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Important Reminders (Optional)</label>
                            <textarea x-model="createForm.important_reminders" rows="3" placeholder="Add any important reminders..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 p-4 border-t border-gray-200 dark:border-gray-700">
                        <button @click="showCreateModal = false" class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">Cancel</button>
                        <button @click="createChecklist()" :disabled="submitting" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium disabled:opacity-50">
                            <span x-text="submitting ? 'Creating...' : 'Create'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Category Modal -->
        <div x-show="showAddCategoryModal" x-transition class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="showAddCategoryModal = false"></div>
            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md" @click.stop>
                    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Add Category</h3>
                        <button @click="showAddCategoryModal = false" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"><i class="fa-solid fa-xmark text-gray-500"></i></button>
                    </div>
                    <div class="p-4 space-y-4">
                        <!-- Predefined Categories -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quick Select</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach($predefinedCategories ?? [] as $cat)
                                    <button @click="categoryForm.name = '{{ $cat }}'"
                                            :class="{ 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border-blue-300 dark:border-blue-700': categoryForm.name === '{{ $cat }}' }"
                                            class="px-3 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 transition-colors">
                                        {{ $cat }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Or Custom Name</label>
                            <input type="text" x-model="categoryForm.name" placeholder="Category name"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 p-4 border-t border-gray-200 dark:border-gray-700">
                        <button @click="showAddCategoryModal = false" class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">Cancel</button>
                        <button @click="addCategory()" :disabled="submitting || !categoryForm.name" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium disabled:opacity-50">
                            <span x-text="submitting ? 'Adding...' : 'Add Category'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Category Modal -->
        <div x-show="showEditCategoryModal" x-transition class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="showEditCategoryModal = false"></div>
            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md" @click.stop>
                    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Category</h3>
                        <button @click="showEditCategoryModal = false" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"><i class="fa-solid fa-xmark text-gray-500"></i></button>
                    </div>
                    <div class="p-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category Name</label>
                        <input type="text" x-model="editCategoryForm.name"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div class="flex items-center justify-end gap-3 p-4 border-t border-gray-200 dark:border-gray-700">
                        <button @click="showEditCategoryModal = false" class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">Cancel</button>
                        <button @click="updateCategory()" :disabled="submitting" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium disabled:opacity-50">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add/Edit Item Modal -->
        <div x-show="showItemModal" x-transition class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="showItemModal = false"></div>
            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md" @click.stop>
                    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="itemForm.id ? 'Edit Item' : 'Add Item'"></h3>
                        <button @click="showItemModal = false" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"><i class="fa-solid fa-xmark text-gray-500"></i></button>
                    </div>
                    <div class="p-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Item Name</label>
                            <input type="text" x-model="itemForm.name" placeholder="e.g. Mop floors"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quantity</label>
                            <input type="text" x-model="itemForm.quantity" placeholder="1"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 p-4 border-t border-gray-200 dark:border-gray-700">
                        <button @click="showItemModal = false" class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">Cancel</button>
                        <button @click="saveItem()" :disabled="submitting || !itemForm.name" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium disabled:opacity-50">
                            <span x-text="submitting ? 'Saving...' : (itemForm.id ? 'Update' : 'Add Item')"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div x-show="showDeleteModal" x-transition class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="showDeleteModal = false"></div>
            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-sm" @click.stop>
                    <div class="p-6 text-center">
                        <div class="w-12 h-12 mx-auto bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mb-4">
                            <i class="fa-solid fa-trash text-red-600 dark:text-red-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Confirm Delete</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6" x-text="deleteMessage"></p>
                        <div class="flex gap-3 justify-center">
                            <button @click="showDeleteModal = false" class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">Cancel</button>
                            <button @click="executeDelete()" :disabled="submitting" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium disabled:opacity-50">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast Notification -->
        <div x-show="toast.show" x-transition
             class="fixed bottom-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-white text-sm font-medium"
             :class="toast.type === 'success' ? 'bg-green-600' : 'bg-red-600'"
             x-text="toast.message" style="display: none;"></div>
    </div>

    @push('scripts')
    <script>
        function checklistManager() {
            return {
                checklist: @json($checklistData ?? null),
                loading: false,
                submitting: false,
                expandedCategories: [],
                editingReminders: false,
                remindersText: '',

                // Modals
                showCreateModal: false,
                showAddCategoryModal: false,
                showEditCategoryModal: false,
                showItemModal: false,
                showDeleteModal: false,

                // Forms
                createForm: { name: '{{ Auth::user()->name }} Checklist', important_reminders: '' },
                categoryForm: { name: '' },
                editCategoryForm: { id: null, name: '' },
                itemForm: { id: null, category_id: null, name: '', quantity: '1' },
                deleteTarget: { type: null, id: null },
                deleteMessage: '',

                // Toast
                toast: { show: false, message: '', type: 'success' },

                csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

                init() {},

                showToast(message, type = 'success') {
                    this.toast = { show: true, message, type };
                    setTimeout(() => this.toast.show = false, 3000);
                },

                toggleCategory(id) {
                    const idx = this.expandedCategories.indexOf(id);
                    if (idx > -1) this.expandedCategories.splice(idx, 1);
                    else this.expandedCategories.push(id);
                },

                editReminders() {
                    this.remindersText = this.checklist.important_reminders || '';
                    this.editingReminders = true;
                },

                async saveReminders() {
                    this.submitting = true;
                    try {
                        const res = await fetch(`/manager/checklist/${this.checklist.id}`, {
                            method: 'PUT',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                            body: JSON.stringify({ important_reminders: this.remindersText })
                        });
                        if (res.ok) {
                            this.checklist.important_reminders = this.remindersText;
                            this.editingReminders = false;
                            this.showToast('Reminders updated');
                        }
                    } catch (e) { this.showToast('Failed to update reminders', 'error'); }
                    this.submitting = false;
                },

                async createChecklist() {
                    if (!this.createForm.name) return;
                    this.submitting = true;
                    try {
                        const res = await fetch('/manager/checklist', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                            body: JSON.stringify(this.createForm)
                        });
                        const data = await res.json();
                        if (res.ok) {
                            this.checklist = { ...data.checklist, categories: data.checklist.categories || [] };
                            this.showCreateModal = false;
                            this.showToast('Checklist created!');
                        }
                    } catch (e) { this.showToast('Failed to create checklist', 'error'); }
                    this.submitting = false;
                },

                async addCategory() {
                    if (!this.categoryForm.name) return;
                    this.submitting = true;
                    try {
                        const res = await fetch(`/manager/checklist/${this.checklist.id}/categories`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                            body: JSON.stringify(this.categoryForm)
                        });
                        const data = await res.json();
                        if (res.ok) {
                            this.checklist.categories.push({ ...data.category, items: data.category.items || [] });
                            this.showAddCategoryModal = false;
                            this.categoryForm.name = '';
                            this.showToast('Category added!');
                        }
                    } catch (e) { this.showToast('Failed to add category', 'error'); }
                    this.submitting = false;
                },

                openEditCategory(category) {
                    this.editCategoryForm = { id: category.id, name: category.name };
                    this.showEditCategoryModal = true;
                },

                async updateCategory() {
                    this.submitting = true;
                    try {
                        const res = await fetch(`/manager/checklist/categories/${this.editCategoryForm.id}`, {
                            method: 'PUT',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                            body: JSON.stringify({ name: this.editCategoryForm.name })
                        });
                        if (res.ok) {
                            const cat = this.checklist.categories.find(c => c.id === this.editCategoryForm.id);
                            if (cat) cat.name = this.editCategoryForm.name;
                            this.showEditCategoryModal = false;
                            this.showToast('Category updated!');
                        }
                    } catch (e) { this.showToast('Failed to update category', 'error'); }
                    this.submitting = false;
                },

                confirmDeleteCategory(category) {
                    this.deleteTarget = { type: 'category', id: category.id };
                    this.deleteMessage = `Delete "${category.name}" and all its items?`;
                    this.showDeleteModal = true;
                },

                confirmDeleteItem(item) {
                    this.deleteTarget = { type: 'item', id: item.id };
                    this.deleteMessage = `Delete "${item.name}"?`;
                    this.showDeleteModal = true;
                },

                async executeDelete() {
                    this.submitting = true;
                    const { type, id } = this.deleteTarget;
                    const url = type === 'category'
                        ? `/manager/checklist/categories/${id}`
                        : `/manager/checklist/items/${id}`;
                    try {
                        const res = await fetch(url, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' }
                        });
                        if (res.ok) {
                            if (type === 'category') {
                                this.checklist.categories = this.checklist.categories.filter(c => c.id !== id);
                            } else {
                                this.checklist.categories.forEach(cat => {
                                    cat.items = (cat.items || []).filter(i => i.id !== id);
                                });
                            }
                            this.showDeleteModal = false;
                            this.showToast(`${type === 'category' ? 'Category' : 'Item'} deleted!`);
                        }
                    } catch (e) { this.showToast('Failed to delete', 'error'); }
                    this.submitting = false;
                },

                openAddItem(category) {
                    this.itemForm = { id: null, category_id: category.id, name: '', quantity: '1' };
                    this.showItemModal = true;
                },

                openEditItem(category, item) {
                    this.itemForm = { id: item.id, category_id: category.id, name: item.name, quantity: item.quantity || '1' };
                    this.showItemModal = true;
                },

                async saveItem() {
                    if (!this.itemForm.name) return;
                    this.submitting = true;
                    const isEdit = !!this.itemForm.id;
                    const url = isEdit
                        ? `/manager/checklist/items/${this.itemForm.id}`
                        : `/manager/checklist/categories/${this.itemForm.category_id}/items`;
                    const method = isEdit ? 'PUT' : 'POST';
                    try {
                        const res = await fetch(url, {
                            method,
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                            body: JSON.stringify({ name: this.itemForm.name, quantity: this.itemForm.quantity })
                        });
                        const data = await res.json();
                        if (res.ok) {
                            const cat = this.checklist.categories.find(c => c.id === this.itemForm.category_id);
                            if (cat) {
                                if (isEdit) {
                                    const idx = cat.items.findIndex(i => i.id === this.itemForm.id);
                                    if (idx > -1) cat.items[idx] = data.item;
                                } else {
                                    cat.items = cat.items || [];
                                    cat.items.push(data.item);
                                }
                            }
                            this.showItemModal = false;
                            this.showToast(isEdit ? 'Item updated!' : 'Item added!');
                        }
                    } catch (e) { this.showToast('Failed to save item', 'error'); }
                    this.submitting = false;
                }
            };
        }
    </script>
    @endpush
</x-layouts.general-manager>
