<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\LoginActivity;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class KasController extends Controller
{
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! auth()->attempt(['email' => $credentials['username'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            return back()->withErrors(['username' => 'Username atau password salah.'])->withInput($request->only('username'));
        }

        $request->session()->regenerate();
        $request->session()->put('login_at', now()->toDateTimeString());
        LoginActivity::create([
            'user_id' => auth()->id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'Berhasil',
            'logged_in_at' => now(),
        ]);

        return redirect()->intended(route('dashboard'));
    }

    public function dashboard(): View
    {
        $successful = Transaction::query()->where('status', 'Sukses');
        $pemasukan = (clone $successful)->where('tipe', 'masuk')->sum('nominal');
        $pengeluaran = (clone $successful)->where('tipe', 'keluar')->sum('nominal');
        $weeks = collect(range(4, 0))->map(function (int $offset) use ($successful) {
            $start = now()->startOfWeek()->subWeeks($offset);
            $end = $start->copy()->endOfWeek();

            return [
                'label' => 'Minggu ' . (5 - $offset),
                'masuk' => (clone $successful)->where('tipe', 'masuk')->whereBetween('tanggal', [$start, $end])->sum('nominal'),
                'keluar' => (clone $successful)->where('tipe', 'keluar')->whereBetween('tanggal', [$start, $end])->sum('nominal'),
            ];
        });
        $maxWeekValue = max(1, $weeks->flatMap(fn ($week) => [$week['masuk'], $week['keluar']])->max());
        $weeks = $weeks->map(fn ($week) => [
            ...$week,
            'masukHeight' => round(($week['masuk'] / $maxWeekValue) * 100),
            'keluarHeight' => round(($week['keluar'] / $maxWeekValue) * 100),
        ]);

        $accounts = Transaction::query()
            ->select('rekening_id', DB::raw("SUM(CASE WHEN tipe = 'masuk' AND status = 'Sukses' THEN nominal ELSE 0 END) - SUM(CASE WHEN tipe = 'keluar' AND status = 'Sukses' THEN nominal ELSE 0 END) AS saldo"))
            ->whereNotNull('rekening_id')
            ->groupBy('rekening_id')
            ->orderByDesc('saldo')
            ->get();
        $recentTransactions = Transaction::query()->latest('tanggal')->latest('id')->limit(5)->get();
        $activeProjects = Project::query()->where('status', 'aktif')->latest()->get()->map(function (Project $project) {
            $spent = Transaction::query()->where('proyek_id', $project->id)->where('tipe', 'keluar')->where('status', 'Sukses')->sum('nominal');
            $project->spent = $spent;
            $project->progress = $project->pagu_anggaran > 0 ? min(100, round(($spent / $project->pagu_anggaran) * 100)) : 0;
            return $project;
        });

        $totalProjectBudget = $activeProjects->sum('pagu_anggaran');
        $totalProjectSpent = $activeProjects->sum('spent');
        $avgProgress = $totalProjectBudget > 0 ? round(($totalProjectSpent / $totalProjectBudget) * 100) : 0;

        return view('dashboard', [
            'saldoKonsolidasi' => $pemasukan - $pengeluaran,
            'pemasukan' => $pemasukan,
            'pengeluaran' => $pengeluaran,
            'jumlahTransaksiMasuk' => (clone $successful)->where('tipe', 'masuk')->count(),
            'jumlahPosKeluar' => (clone $successful)->where('tipe', 'keluar')->count(),
            'weeks' => $weeks,
            'accounts' => $accounts,
            'recentTransactions' => $recentTransactions,
            'activeProjects' => $activeProjects,
            'totalProjectBudget' => $totalProjectBudget,
            'totalProjectSpent' => $totalProjectSpent,
            'avgProgress' => $avgProgress,
        ]);
    }

    public function transactions(Request $request): View
    {
        $query = Transaction::query()->with('project');
        $query->when($request->filled('rekening_id'), fn ($builder) => $builder->where('rekening_id', $request->string('rekening_id')));
        $query->when($request->filled('kategori'), fn ($builder) => $builder->where('kategori', $request->string('kategori')));
        $query->when($request->filled('status'), fn ($builder) => $builder->where('status', $request->string('status')));

        $transactions = $query->latest('tanggal')->latest('id')->paginate(15)->withQueryString();

        return view('transaksi.index', [
            'transactions' => $transactions,
            'transaksiData' => $transactions->getCollection()->map(fn (Transaction $t) => [
                'id' => $t->id,
                'tipe' => $t->tipe,
                'nominal' => $t->nominal,
                'tanggal' => $t->tanggal->format('Y-m-d'),
                'tanggal_formatted' => $t->tanggal->format('d M Y'),
                'kategori' => $t->kategori,
                'proyek_id' => $t->proyek_id,
                'proyek_nama' => $t->project?->nama_proyek ?? 'Non-Proyek',
                'deskripsi' => $t->deskripsi,
                'rekening_id' => $t->rekening_id,
                'status' => $t->status,
                'verifikasi_langsung' => $t->verifikasi_langsung,
                'bukti' => $t->bukti ? asset('storage/' . $t->bukti) : null,
            ])->values(),
            'accountOptions' => Transaction::query()->whereNotNull('rekening_id')->distinct()->orderBy('rekening_id')->pluck('rekening_id'),
            'categoryOptions' => Transaction::query()->whereNotNull('kategori')->distinct()->orderBy('kategori')->pluck('kategori'),
            'projects' => Project::query()->where('status', 'aktif')->orderBy('nama_proyek')->get(),
        ]);
    }

    public function createTransaction(): View
    {
        return view('transaksi.create', [
            'projects' => Project::query()->where('status', 'aktif')->orderBy('nama_proyek')->get(),
        ]);
    }

    public function storeTransaction(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tipe' => ['required', 'in:masuk,keluar'],
            'nominal' => ['required', 'integer', 'min:1'],
            'tanggal' => ['required', 'date'],
            'kategori' => ['required', 'in:operasional,proyek,gaji,pajak,pendapatan,bunga_bank,injeksi_modal,pinjaman'],
            'proyek_id' => ['nullable', 'integer', 'exists:projects,id'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'rekening_id' => ['nullable', 'string', 'max:100'],
            'verifikasi_langsung' => ['required', 'boolean'],
            'bukti' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        if ($request->hasFile('bukti')) {
            $data['bukti'] = $request->file('bukti')->store('bukti-transaksi', 'public');
        }

        $data['status'] = $request->boolean('verifikasi_langsung') ? 'Sukses' : 'Pending';
        Transaction::create($data);

        return redirect()->route('transaksi')->with('success', 'Transaksi berhasil disimpan.');
    }

    public function updateTransaction(Request $request, Transaction $transaction): RedirectResponse
    {
        $data = $request->validate([
            'tipe' => ['required', 'in:masuk,keluar'],
            'nominal' => ['required', 'integer', 'min:1'],
            'tanggal' => ['required', 'date'],
            'kategori' => ['required', 'in:operasional,proyek,gaji,pajak,pendapatan,bunga_bank,injeksi_modal,pinjaman'],
            'proyek_id' => ['nullable', 'integer', 'exists:projects,id'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'rekening_id' => ['nullable', 'string', 'max:100'],
            'verifikasi_langsung' => ['required', 'boolean'],
            'bukti' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        if ($request->hasFile('bukti')) {
            if ($transaction->bukti) {
                Storage::disk('public')->delete($transaction->bukti);
            }
            $data['bukti'] = $request->file('bukti')->store('bukti-transaksi', 'public');
        }

        $data['status'] = $request->boolean('verifikasi_langsung') ? 'Sukses' : 'Pending';
        $transaction->update($data);

        return redirect()->route('transaksi')->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroyTransactions(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:transactions,id'],
        ]);

        $transactions = Transaction::query()->whereIn('id', $request->input('ids'))->get();

        foreach ($transactions as $transaction) {
            if ($transaction->bukti) {
                Storage::disk('public')->delete($transaction->bukti);
            }
        }

        Transaction::query()->whereIn('id', $request->input('ids'))->delete();

        return redirect()->route('transaksi')->with('success', count($request->input('ids')) . ' transaksi berhasil dihapus.');
    }

    public function projects(): View
    {
        $projects = Project::query()->latest()->get()->map(function (Project $project) {
            $project->spent = Transaction::query()->where('proyek_id', $project->id)->where('tipe', 'keluar')->where('status', 'Sukses')->sum('nominal');
            $project->progress = $project->pagu_anggaran > 0 ? min(100, round(($project->spent / $project->pagu_anggaran) * 100)) : 0;
            return $project;
        });
        $totalBudget = $projects->sum('pagu_anggaran');
        $totalSpent = $projects->sum('spent');

        return view('proyek.index', [
            'projects' => $projects,
            'proyekData' => $projects->map(fn (Project $p) => [
                'id' => $p->id,
                'nama_proyek' => $p->nama_proyek,
                'kategori_proyek' => $p->kategori_proyek,
                'deskripsi' => $p->deskripsi,
                'pagu_anggaran' => $p->pagu_anggaran,
                'tanggal_mulai' => $p->tanggal_mulai?->format('Y-m-d'),
                'tanggal_selesai' => $p->tanggal_selesai?->format('Y-m-d'),
                'project_lead_id' => $p->project_lead_id,
                'departemen' => $p->departemen ?? [],
            ])->values(),
            'users' => User::query()->orderBy('name')->get(['id', 'name']),
            'totalBudget' => $totalBudget,
            'totalSpent' => $totalSpent,
            'remainingBudget' => max(0, $totalBudget - $totalSpent),
            'persentaseRealisasi' => $totalBudget > 0 ? round(($totalSpent / $totalBudget) * 100, 1) . '%' : '0%',
        ]);
    }

    public function reports(): View
    {
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();
        $periodTransactions = Transaction::query()->whereBetween('tanggal', [$start, $end])->where('status', 'Sukses')->get();
        $maxFlow = max(1, $periodTransactions->groupBy(fn ($transaction) => $transaction->tanggal->format('Y-m-d'))->map(fn ($items) => abs($items->sum(fn ($item) => $item->tipe === 'masuk' ? $item->nominal : -$item->nominal)))->max() ?: 1);
        $flux = $periodTransactions->groupBy(fn ($transaction) => $transaction->tanggal->format('Y-m-d'))->sortKeys()->map(function ($items, $date) use ($maxFlow) {
            $value = $items->sum(fn ($item) => $item->tipe === 'masuk' ? $item->nominal : -$item->nominal);
            return ['label' => Carbon::parse($date)->format('d M'), 'value' => $value, 'height' => round((abs($value) / $maxFlow) * 100)];
        })->values();
        $totalOut = max(1, $periodTransactions->where('tipe', 'keluar')->sum('nominal'));
        $composition = $periodTransactions->where('tipe', 'keluar')->groupBy('kategori')->map(function ($items, $category) use ($totalOut) {
            $amount = $items->sum('nominal');
            return ['name' => $category, 'amount' => $amount, 'percent' => round(($amount / $totalOut) * 100, 1)];
        })->sortByDesc('amount')->values();
        $recap = $periodTransactions->groupBy('rekening_id')->map(function ($items, $account) {
            $income = $items->where('tipe', 'masuk')->sum('nominal');
            $expense = $items->where('tipe', 'keluar')->sum('nominal');
            return ['account' => $account ?: 'Tidak ditentukan', 'initial' => 0, 'income' => $income, 'expense' => $expense, 'final' => $income - $expense];
        })->values();

        return view('laporan.index', compact('start', 'end', 'flux', 'composition', 'recap'));
    }

    public function storeProject(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_proyek' => ['required', 'string', 'max:255'],
            'kategori_proyek' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
            'pagu_anggaran' => ['required', 'integer', 'min:0'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'project_lead_id' => ['nullable', 'integer'],
            'departemen' => ['nullable', 'array'],
            'departemen.*' => ['string', 'max:100'],
        ]);

        Project::create($data);

        return redirect()->route('proyek')->with('success', 'Proyek berhasil dibuat.');
    }

    public function updateProject(Request $request, Project $project): RedirectResponse
    {
        $data = $request->validate([
            'nama_proyek' => ['required', 'string', 'max:255'],
            'kategori_proyek' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
            'pagu_anggaran' => ['required', 'integer', 'min:0'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'project_lead_id' => ['nullable', 'integer'],
            'departemen' => ['nullable', 'array'],
            'departemen.*' => ['string', 'max:100'],
        ]);

        $project->update($data);

        return redirect()->route('proyek')->with('success', 'Proyek berhasil diperbarui.');
    }

    public function destroyProjects(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:projects,id'],
        ]);

        Project::query()->whereIn('id', $request->input('ids'))->delete();

        return redirect()->route('proyek')->with('success', count($request->input('ids')) . ' proyek berhasil dihapus.');
    }

    public function settings(): View
    {
        return view('settings', ['settings' => Setting::query()->firstOrCreate([])]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'bahasa' => ['required', 'in:id,en'],
            'mata_uang' => ['required', 'in:idr,usd'],
            'format_tanggal' => ['required', 'in:dd/mm/yyyy,yyyy-mm-dd'],
            'mode_tampilan' => ['required', 'in:light,dark'],
            'notif_email' => ['nullable', 'boolean'],
            'notif_sistem' => ['nullable', 'boolean'],
        ]);

        $data['notif_email'] = $request->boolean('notif_email');
        $data['notif_sistem'] = $request->boolean('notif_sistem');
        Setting::query()->firstOrCreate([])->update($data);

        return redirect()->route('settings')->with('success', 'Preferensi berhasil disimpan.');
    }

    public function profile(): View
    {
        $user = Auth::user();
        $loginHistory = LoginActivity::query()->where('user_id', $user->id)->latest('logged_in_at')->limit(10)->get();

        return view('profile', ['account' => $user, 'user' => $user, 'loginHistory' => $loginHistory]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($request->hasFile('foto_profil')) {
            $request->validate([
                'foto_profil' => ['required', 'image', 'max:5120'],
            ]);

            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }

            $path = $request->file('foto_profil')->store('foto-profil', 'public');
            $user->update(['foto' => $path]);

            return redirect()->route('profile')->with('success', 'Foto profil berhasil diperbarui.');
        }

        if ($request->filled('current_password')) {
            $request->validate([
                'current_password' => ['required', 'current_password'],
                'new_password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);

            $user->update(['password' => Hash::make($request->string('new_password')->toString())]);

            return redirect()->route('profile')->with('success', 'Kata sandi berhasil diperbarui.');
        }

        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'telepon' => ['nullable', 'string', 'max:50'],
            'nip' => ['nullable', 'string', 'max:100'],
            'departemen' => ['nullable', 'string', 'max:150'],
        ]);

        $user->update($data);

        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui.');
    }

    public function search(Request $request): View
    {
        $query = trim($request->input('q', ''));

        $transactions = collect();
        $projects = collect();

        if ($query !== '') {
            $transactions = Transaction::query()
                ->where('deskripsi', 'like', "%{$query}%")
                ->orWhere('kategori', 'like', "%{$query}%")
                ->orWhere('rekening_id', 'like', "%{$query}%")
                ->latest('tanggal')
                ->get();

            $projects = Project::query()
                ->where('nama_proyek', 'like', "%{$query}%")
                ->orWhere('kategori_proyek', 'like', "%{$query}%")
                ->orWhere('deskripsi', 'like', "%{$query}%")
                ->latest()
                ->get();
        }

        return view('search', compact('query', 'transactions', 'projects'));
    }

    public function exportExcel(Request $request)
    {
        $transactions = Transaction::query()->with('project')->latest('tanggal')->get();
        $filename = 'laporan-kas-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, ['ID', 'Tanggal', 'Tipe', 'Kategori', 'Proyek', 'Rekening', 'Status', 'Nominal', 'Deskripsi']);

            foreach ($transactions as $t) {
                fputcsv($file, [
                    $t->id,
                    $t->tanggal->format('Y-m-d'),
                    ucfirst($t->tipe),
                    ucfirst($t->kategori),
                    $t->project?->nama_proyek ?? 'Non-Proyek',
                    $t->rekening_id ?? '-',
                    $t->status,
                    $t->nominal,
                    $t->deskripsi,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request): View
    {
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();
        $transactions = Transaction::query()->whereBetween('tanggal', [$start, $end])->where('status', 'Sukses')->latest('tanggal')->get();
        $totalMasuk = $transactions->where('tipe', 'masuk')->sum('nominal');
        $totalKeluar = $transactions->where('tipe', 'keluar')->sum('nominal');

        return view('laporan.pdf', compact('start', 'end', 'transactions', 'totalMasuk', 'totalKeluar'));
    }

    public function logout(Request $request): RedirectResponse
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
