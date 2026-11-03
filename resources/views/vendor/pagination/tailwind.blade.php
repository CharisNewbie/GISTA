@if ($paginator->hasPages())
    <nav class="flex items-center justify-between text-xs">
        <div class="flex-1 flex justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="px-3 py-1.5 bg-slate-100 text-slate-400 rounded-lg cursor-not-allowed font-medium">
                    ← Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1.5 bg-white border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 font-medium transition">
                    ← Sebelumnya
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1.5 bg-white border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 font-medium transition">
                    Selanjutnya →
                </a>
            @else
                <span class="px-3 py-1.5 bg-slate-100 text-slate-400 rounded-lg cursor-not-allowed font-medium">
                    Selanjutnya →
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-slate-600">
                    Menampilkan
                    @if ($paginator->firstItem())
                        <span class="font-semibold text-slate-800">{{ $paginator->firstItem() }}</span>
                        -
                        <span class="font-semibold text-slate-800">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    dari
                    <span class="font-semibold text-slate-800">{{ $paginator->total() }}</span>
                    data
                </p>
            </div>

            <div class="flex items-center gap-1">
                @if ($paginator->onFirstPage())
                    <span class="px-3 py-1.5 bg-slate-100 text-slate-400 rounded-lg cursor-not-allowed font-medium">
                        ← Sebelumnya
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1.5 bg-white border border-slate-200 text-slate-700 rounded-lg hover:bg-gista-50 hover:border-gista-200 font-medium transition">
                        ← Sebelumnya
                    </a>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="px-3 py-1.5 text-slate-400">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="px-3 py-1.5 bg-gista-600 text-white rounded-lg font-bold">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="px-3 py-1.5 bg-white border border-slate-200 text-slate-700 rounded-lg hover:bg-gista-50 hover:border-gista-200 font-medium transition">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1.5 bg-white border border-slate-200 text-slate-700 rounded-lg hover:bg-gista-50 hover:border-gista-200 font-medium transition">
                        Selanjutnya →
                    </a>
                @else
                    <span class="px-3 py-1.5 bg-slate-100 text-slate-400 rounded-lg cursor-not-allowed font-medium">
                        Selanjutnya →
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif
