<div class="mt-3">
    @if ($paginator->hasPages())
        <nav class="flex justify-center" role="navigation"
            aria-label="Pagination Navigation">
            <span>
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <a
                        class="flex items-center justify-center h-10 px-4 leading-tight text-gray-500 bg-white border border-gray-300 rounded-l-lg cursor-default hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
                        rel="prev">
                        <svg class="w-2.5 h-2.5" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round"
                                stroke-linejoin="round" stroke-width="2"
                                d="M5 1 1 5l4 4" />
                        </svg>
                    </a>
                @else
                    <a
                        class="flex items-center justify-center h-10 px-4 ml-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
                        href="{{ $paginator->previousPageUrl() }}"
                        rel="prev">
                        <svg class="w-2.5 h-2.5" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round"
                                stroke-linejoin="round" stroke-width="2"
                                d="M5 1 1 5l4 4" />
                        </svg>
                    </a>
                @endif
            </span>

            <ul class="inline-flex h-10 -space-x-px text-base">
                {{-- Goto Page Link --}}
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <li>
                            <a
                                class="flex items-center justify-center h-10 px-4 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                                {{ $element }}
                            </a>
                        </li>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <a
                                    class="z-10 flex items-center justify-center h-10 px-4 leading-tight text-blue-600 border border-blue-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white">{{ $page }}
                                </a>
                            @else
                                <a
                                    class="flex items-center justify-center h-10 px-4 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
                                    href="{{ $url }}">{{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </ul>

            <span>
                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a
                        class="flex items-center justify-center h-10 px-4 leading-tight text-gray-500 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
                        href="{{ $paginator->nextPageUrl() }}"
                        rel="next">
                        <svg class="w-3 h-3" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round"
                                stroke-linejoin="round" stroke-width="2"
                                d="m1 9 4-4-4-4" />
                        </svg>
                    </a>
                @else
                    <a
                        class="flex items-center justify-center h-10 px-4 leading-tight text-gray-500 bg-white border border-gray-300 rounded-r-lg cursor-default hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
                        rel="next">
                        <svg class="w-3 h-3" aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 6 10">
                            <path stroke="currentColor" stroke-linecap="round"
                                stroke-linejoin="round" stroke-width="2"
                                d="m1 9 4-4-4-4" />
                        </svg>
                    </a>
                @endif
            </span>
        </nav>
    @endif
</div>