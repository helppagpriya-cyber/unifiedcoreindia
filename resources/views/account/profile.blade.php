<x-layouts.website title="My profile | Unified Core India">
    <section class="bg-slate-50 px-4 py-12 sm:px-6">
        <div class="mx-auto max-w-4xl">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3"><div><h1 class="text-2xl font-bold text-slate-900">My profile</h1><p class="mt-1 text-sm text-slate-600">Manage your account details and password.</p></div><form method="POST" action="{{ route('logout') }}">@csrf<button class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold hover:bg-white">Sign out</button></form></div>
            @if (session('status'))<div class="mb-5 rounded-lg bg-emerald-50 p-3 text-sm text-emerald-800">{{ session('status') }}</div>@endif
            <div class="grid gap-6 md:grid-cols-2">
                <form method="POST" action="{{ route('profile.update') }}" class="space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">@csrf @method('PUT')<h2 class="text-lg font-semibold">Profile details</h2>
                    <label class="block text-sm font-medium">Full name<input name="name" value="{{ old('name', $user->name) }}" required class="mt-1 w-full rounded-lg border-slate-300" />@error('name')<span class="text-red-600">{{ $message }}</span>@enderror</label>
                    <label class="block text-sm font-medium">Email<input type="email" name="email" value="{{ old('email', $user->email) }}" required class="mt-1 w-full rounded-lg border-slate-300" />@error('email')<span class="text-red-600">{{ $message }}</span>@enderror</label>
                    <label class="block text-sm font-medium">Mobile<input name="mobile" value="{{ old('mobile', $user->mobile) }}" class="mt-1 w-full rounded-lg border-slate-300" />@error('mobile')<span class="text-red-600">{{ $message }}</span>@enderror</label>
                    <p class="text-sm text-slate-500">Role: {{ $user->getRoleNames()->join(', ') ?: 'EGJewel Executive' }} (managed by an administrator)</p><button class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white">Save profile</button>
                </form>
                <form method="POST" action="{{ route('profile.password') }}" class="space-y-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">@csrf @method('PUT')<h2 class="text-lg font-semibold">Update password</h2>
                    <label class="block text-sm font-medium">Current password<input type="password" name="current_password" required autocomplete="current-password" class="mt-1 w-full rounded-lg border-slate-300" />@error('current_password')<span class="text-red-600">{{ $message }}</span>@enderror</label>
                    <label class="block text-sm font-medium">New password<input type="password" name="password" required autocomplete="new-password" class="mt-1 w-full rounded-lg border-slate-300" />@error('password')<span class="text-red-600">{{ $message }}</span>@enderror</label>
                    <label class="block text-sm font-medium">Confirm new password<input type="password" name="password_confirmation" required autocomplete="new-password" class="mt-1 w-full rounded-lg border-slate-300" /></label><button class="rounded-lg bg-cyan-800 px-4 py-2.5 text-sm font-semibold text-white">Update password</button>
                </form>
            </div>
        </div>
    </section>
</x-layouts.website>
