<div x-data="releaseNotes()" x-init="init()">
  <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/40" @click="dismiss()"></div>
    <div class="relative bg-white rounded-lg shadow-xl border border-gray-200 max-w-2xl w-full mx-4">
      <div class="px-5 py-4 bg-blue-50 border-b border-blue-100 flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900">What's new in {{ config('app.version') }}</h3>
        <button class="text-gray-400 hover:text-gray-600" @click="dismiss()"><i class="fas fa-times"></i></button>
      </div>
      <div class="px-5 py-4 text-sm text-gray-700 max-h-[60vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-3">
          <div class="text-gray-600" x-show="lastLogin">
            Last login: <span x-text="new Date(lastLogin).toLocaleString()"></span>
          </div>
          <label class="inline-flex items-center text-xs text-gray-600" x-show="lastLogin">
            <input type="checkbox" class="mr-2 rounded border-gray-300" x-model="sinceLastOnly" />
            Show only changes since last login
          </label>
        </div>

        <template x-if="releases.length">
          <div class="space-y-4">
            <template x-for="rel in displayedReleases()" :key="rel.version + rel.date">
              <div class="border border-gray-200 rounded-md">
                <div class="px-4 py-2 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                  <div class="font-medium text-gray-900" x-text="'Version ' + rel.version"></div>
                  <div class="text-xs text-gray-500" x-text="new Date(rel.date).toLocaleDateString()"></div>
                </div>
                <div class="px-4 py-3 prose prose-sm max-w-none" x-html="rel.notesHtml"></div>
              </div>
            </template>
            <div class="text-gray-500" x-show="displayedReleases().length === 0">
              No changes since your last login.
            </div>
          </div>
        </template>

        <template x-if="!releases.length">
          <div class="space-y-3">
            @if(file_exists(base_path('..'.DIRECTORY_SEPARATOR.'docs'.DIRECTORY_SEPARATOR.'CHANGELOG.html')))
              {!! file_get_contents(base_path('..'.DIRECTORY_SEPARATOR.'docs'.DIRECTORY_SEPARATOR.'CHANGELOG.html')) !!}
            @else
              <ul class="list-disc pl-5">
                <!-- <li>Welcome to the latest version of the application.</li>
                <li>UI improvements and bug fixes.</li>
                <li>Performance enhancements.</li> -->
              </ul>
            @endif
          </div>
        </template>
      </div>
      <div class="px-5 py-4 bg-blue-50 border-t border-blue-100 flex items-center justify-between">
        <label class="inline-flex items-center text-sm text-gray-600">
          <input type="checkbox" class="mr-2 rounded border-gray-300" x-model="dontShowAgain" />
          Don't show again for this version
        </label>
        <button class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md" @click="dismiss()">Got it</button>
      </div>
    </div>
  </div>

  <script>
    function releaseNotes() {
      const version = @js(config('app.version'));
      const key = 'release_notes_seen_' + version;
      const lastLoginIso = @js((auth()->check() && isset(Auth::user()->last_login_at)) ? \Carbon\Carbon::parse(Auth::user()->last_login_at)->toIso8601String() : null);
      return {
        open: false,
        dontShowAgain: true,
        releases: [],
        sinceLastOnly: true,
        lastLogin: null,
        async fetchChangelog() {
          try {
            const res = await fetch('/changelog.json', { cache: 'no-cache' });
            if (!res.ok) return;
            const data = await res.json();
            const items = Array.isArray(data.releases) ? data.releases : [];
            this.releases = items
              .filter(r => r.version && r.date && r.notesHtml)
              .sort((a,b) => new Date(b.date) - new Date(a.date));
          } catch (e) {
            // silent fallback to server-rendered HTML
          }
        },
        displayedReleases() {
          if (this.sinceLastOnly && this.lastLogin) {
            return this.releases.filter(r => new Date(r.date) > new Date(this.lastLogin));
          }
          return this.releases;
        },
        init() {
          try {
            const seen = localStorage.getItem(key);
            this.open = !seen;
          } catch (e) { this.open = true; }
          this.lastLogin = lastLoginIso ? new Date(lastLoginIso) : null;
          this.fetchChangelog();
        },
        dismiss() {
          if (this.dontShowAgain) {
            try { localStorage.setItem(key, '1'); } catch (e) {}
          }
          this.open = false;
        }
      }
    }
  </script>
</div>


