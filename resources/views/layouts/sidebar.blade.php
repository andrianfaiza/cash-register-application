<div>
                {{-- Logo --}}
                <div class="flex items-center gap-3 px-6 py-6">
                    <div class="w-10 h-10 rounded-lg bg-blue-600 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h11a2 2 0 012 2v1h1a2 2 0 012 2v7a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 13h2" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-lg font-bold leading-tight">KAS</p>
                        <p class="text-[10px] font-semibold tracking-wide text-orange-500">PT. WINNER NUSANTARA JAYA</p>
                    </div>
                </div>

                {{-- Nav --}}
                <nav class="mt-4 px-3 space-y-1">
                    
                    <a href="{{ route('dashboard')}}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium 
                    {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('transaksi')}}" class="flex items-center justify-between px-4 py-2.5 rounded-lg 
                    {{ request()->routeIs('transaksi') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }} text-sm font-medium transition-colors">
                        <span class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4M16 17H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                            Transaksi
                        </span>
                    </a>
                    <a href="{{ route('proyek')}}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg 
                    {{ request()->routeIs('proyek') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }} text-sm font-medium transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Proyek
                    </a>
                    <a href="{{ route('laporan')}}" class="flex items-center gap-3 px-4 py-2.5 rounded-lg 
                    {{ request()->routeIs('laporan') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }} text-sm font-medium transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6m-9 0h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        Laporan
                    </a>
                </nav>
            </div>

            {{-- User footer --}}
            <div class="flex items-center gap-3 px-6 py-3 border-t border-slate-800">
                @include('layouts.profile')
            </div>