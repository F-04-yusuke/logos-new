<div class="flex items-center gap-2" {{ $attributes }}>
    {{-- 
       【修正】LOGOSの新しいロゴ（パターンC：構造的モノリス）
       黄色のアクセントをなくし、純粋な青のグラデーション（濃淡）だけで
       強固で洗練された3Dの「L」を表現しました。Laravelのようなソリッドな質感を意識しています。
    --}}
    <svg viewBox="0 0 110 100" xmlns="http://www.w3.org/2000/svg" class="h-8 w-auto">
        <path d="M 15 30 L 30 15 H 55 L 40 30 Z" class="fill-blue-400 dark:fill-blue-300"/>
        <path d="M 40 65 L 55 50 H 95 L 80 65 Z" class="fill-blue-400 dark:fill-blue-300"/>
        
        <path d="M 15 30 H 40 V 65 H 80 V 90 H 15 Z" class="fill-blue-600 dark:fill-blue-500"/>
        
        <path d="M 40 30 L 55 15 V 50 L 40 65 Z" class="fill-blue-800 dark:fill-blue-700"/>
        <path d="M 80 65 L 95 50 V 75 L 80 90 Z" class="fill-blue-800 dark:fill-blue-700"/>
    </svg>

    <span class="text-2xl font-black tracking-widest text-gray-900 dark:text-white mt-1">LOGOS</span>
</div>