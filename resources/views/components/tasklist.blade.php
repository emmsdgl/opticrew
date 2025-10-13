<div x-data="{
    tasks: [
        { id: 1, title: 'Finish user onboarding', category: 'Marketing', date: 'Tomorrow', comments: 1, avatar: 'https://i.pravatar.cc/30?img=1', done: false },
        { id: 2, title: 'Solve the Dribbble prioritisation issue with the team', category: '', date: 'Jan 8, 2027', comments: 2, avatar: 'https://i.pravatar.cc/30?img=2', done: true },
        { id: 3, title: 'Change license and remove products', category: 'Marketing', date: 'Feb 12, 2027', comments: 1, avatar: 'https://i.pravatar.cc/30?img=3', done: true },
    ]
}" class="p-6 w-full">
    
    <h2 class="text-base font-sans font-bold mb-6 text-gray-800 dark:text-gray-100">Task List</h2>

    <template x-for="task in tasks" :key="task.id">
        <div 
            class="flex justify-between items-center p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 
                   rounded-xl shadow-sm hover:shadow-md transition cursor-pointer group mb-3">

            <!-- Left: Checkbox + Title -->
            <div class="flex items-center gap-3">
                <!-- Interactive Checkbox -->
                <input type="checkbox" 
                       x-model="task.done" 
                       class="w-5 h-5 text-base text-blue-600 bg-transparent border-gray-400 dark:border-gray-600 rounded focus:ring-blue-500">

                <p class="font-medium text-gray-800 dark:text-gray-100"
                   :class="{ 'line-through text-gray-400 dark:text-gray-500': task.done }">
                    <span x-text="task.title"></span>
                </p>
            </div>

            <!-- Right: Meta Info -->
            <div class="flex items-center gap-3 text-sm text-gray-500 dark:text-gray-400">
                <template x-if="task.category">
                    <span class="bg-blue-100 dark:bg-blue-700 text-blue-700 dark:text-blue-100 px-2 py-1 rounded" 
                          x-text="task.category"></span>
                </template>

                <div class="flex items-center gap-1">
                    <i class="fa-regular fa-calendar"></i>
                    <span x-text="task.date"></span>
                </div>

                <div class="flex items-center gap-1">
                    <i class="fa-regular fa-comment"></i>
                    <span x-text="task.comments"></span>
                </div>

                <img :src="task.avatar" class="rounded-full w-7 h-7 border border-gray-300 dark:border-gray-600">
            </div>
        </div>
    </template>
</div>
