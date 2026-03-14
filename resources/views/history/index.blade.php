<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-gray-900 dark:text-gray-100 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            閲覧履歴（最近見たトピック）
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-[#1e1f20] shadow-sm sm:rounded-lg overflow-hidden border border-gray-200 dark:border-gray-800">
                <div class="p-6">
                    @if($viewedTopics->isEmpty())
                        <div class="flex flex-col items-center justify-center py-12 px-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400 text-sm font-bold">まだ閲覧履歴はありません。</p>
                            <p class="text-gray-400 dark:text-gray-500 text-xs mt-2">トピックを見ると、ここに履歴が残ります。</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($viewedTopics as $topic)
                                <div class="border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden hover:shadow-md transition-shadow bg-white dark:bg-[#131314] flex flex-col h-full relative group">
                                    
                                    <a href="{{ route('topics.show', $topic) }}" class="block aspect-video bg-gray-100 dark:bg-gray-800 overflow-hidden relative">
                                        @if($topic->thumbnail_path)
                                            <img src="{{ asset('storage/' . $topic->thumbnail_path) }}" alt="{{ $topic->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400 group-hover:scale-105 transition-transform duration-300">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="absolute top-2 left-2">
                                            <span class="inline-block px-2 py-1 text-[10px] font-bold rounded bg-black/60 text-white backdrop-blur-sm">{{ $topic->category }}</span>
                                        </div>
                                    </a>

                                    <div class="p-4 flex flex-col flex-grow">
                                        <div class="flex items-center gap-2 mb-2">
                                            @if($topic->user->avatar)
                                                <img class="h-6 w-6 rounded-full object-cover border border-gray-200 dark:border-gray-700" src="{{ asset('storage/' . $topic->user->avatar) }}" alt="Avatar" />
                                            @else
                                                <div class="h-6 w-6 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center border border-gray-300 dark:border-gray-600">
                                                    <svg class="h-3 w-3 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                                </div>
                                            @endif
                                            <span class="text-[11px] font-bold text-gray-700 dark:text-gray-300 truncate">{{ $topic->user->name }}</span>
                                            
                                            <span class="text-[10px] text-blue-500 dark:text-blue-400 font-bold ml-auto bg-blue-50 dark:bg-blue-900/20 px-1.5 py-0.5 rounded">
                                                {{ \Carbon\Carbon::parse($topic->pivot->last_viewed_at)->diffForHumans() }} に閲覧
                                            </span>
                                        </div>

                                        <h3 class="font-bold text-sm text-gray-900 dark:text-gray-100 line-clamp-2 leading-tight flex-grow mt-1">
                                            <a href="{{ route('topics.show', $topic) }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                                {{ $topic->title }}
                                            </a>
                                        </h3>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8">
                            {{ $viewedTopics->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>