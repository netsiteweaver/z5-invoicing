<div x-data="releaseNotes()" x-init="init()">
  <style>[x-cloak]{display:none!important}</style>
  <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/40" @click="dismiss()"></div>
    <div class="relative bg-white rounded-lg shadow-xl border border-gray-200 max-w-2xl w-full mx-4">
      <div class="px-5 py-4 bg-blue-50 border-b border-blue-100 flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <h3 class="text-lg font-semibold text-gray-900" x-text="getCurrentRelease() ? `What's new in ${getCurrentRelease().version}${getCurrentRelease().date ? ' (' + formatDate(getCurrentRelease().date) + ')' : ''}` : 'What\'s New'"></h3>
          <div x-show="unseenReleases.length > 1" class="flex items-center space-x-2 text-sm text-gray-600">
            <span x-text="`${currentReleaseIndex + 1} of ${unseenReleases.length}`"></span>
          </div>
        </div>
        <button class="text-gray-400 hover:text-gray-600" @click="dismiss()"><i class="fas fa-times"></i></button>
      </div>
      <div class="px-5 py-4 text-sm text-gray-700 max-h-[60vh] overflow-y-auto">
        <div x-show="getCurrentRelease()" x-html="buildList(getCurrentRelease().notesHtml)"></div>
        <div x-show="!getCurrentRelease()" class="text-gray-500">No notes available.</div>
      </div>
      <div class="px-5 py-4 bg-blue-50 border-t border-blue-100 flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <label class="inline-flex items-center text-sm text-gray-600">
            <input type="checkbox" class="mr-2 rounded border-gray-300" x-model="dontShowAgain" />
            <span x-text="unseenReleases.length > 1 ? 'Mark all as seen' : 'Don\'t show again'"></span>
          </label>
        </div>
        <div class="flex items-center space-x-2">
          <!-- Navigation buttons -->
          <div x-show="unseenReleases.length > 1" class="flex items-center space-x-1">
            <button class="p-2 text-gray-400 hover:text-gray-600 disabled:opacity-50" 
                    @click="prevRelease()" 
                    :disabled="currentReleaseIndex === 0"
                    title="Previous release">
              <i class="fas fa-chevron-left"></i>
            </button>
            <button class="p-2 text-gray-400 hover:text-gray-600 disabled:opacity-50" 
                    @click="nextRelease()" 
                    :disabled="currentReleaseIndex === unseenReleases.length - 1"
                    title="Next release">
              <i class="fas fa-chevron-right"></i>
            </button>
          </div>
          <button class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md" 
                  @click="unseenReleases.length > 1 && currentReleaseIndex < unseenReleases.length - 1 ? nextRelease() : dismiss()">
            <span x-text="unseenReleases.length > 1 && currentReleaseIndex < unseenReleases.length - 1 ? 'Next' : 'Got it'"></span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
    function releaseNotes() {
      const appVersion = @js(config('app.version'));
      const lastSeenKey = 'release_notes_last_seen_version';
      const key = (v) => 'release_notes_seen_' + (v || appVersion);
      return {
        appVersion,
        open: false,
        releases: [],
        unseenReleases: [],
        dontShowAgain: true,
        currentReleaseIndex: 0,
        async init() {
          // Fetch all releases
          try {
            const res = await fetch('{{ route('changelog.feed') }}', { cache: 'no-cache' });
            if (res.ok) {
              const data = await res.json();
              this.releases = Array.isArray(data.releases) ? data.releases : [];
            }
          } catch (e) {}
          
          // Get last seen version
          let lastSeenVersion = null;
          try { 
            lastSeenVersion = localStorage.getItem(lastSeenKey); 
          } catch (e) {}
          
          // Find unseen releases (newer than last seen version)
          this.unseenReleases = this.releases.filter(release => {
            if (!lastSeenVersion) return true; // Show all if no last seen version
            return this.isVersionNewer(release.version, lastSeenVersion);
          });
          
          // Open modal if there are unseen releases
          this.open = this.unseenReleases.length > 0;
        },
        isVersionNewer(version1, version2) {
          // Remove 'v' prefix for comparison
          const v1 = version1.replace(/^v/, '');
          const v2 = version2.replace(/^v/, '');
          
          // Split version strings and compare numerically
          const parts1 = v1.split('.').map(Number);
          const parts2 = v2.split('.').map(Number);
          
          for (let i = 0; i < Math.max(parts1.length, parts2.length); i++) {
            const part1 = parts1[i] || 0;
            const part2 = parts2[i] || 0;
            if (part1 > part2) return true;
            if (part1 < part2) return false;
          }
          return false; // Versions are equal
        },
        getCurrentRelease() {
          return this.unseenReleases[this.currentReleaseIndex] || null;
        },
        formatDate(iso) {
          try {
            const d = new Date(iso);
            // Force YYYY-MM-DD
            const yyyy = d.getUTCFullYear();
            const mm = String(d.getUTCMonth() + 1).padStart(2, '0');
            const dd = String(d.getUTCDate()).padStart(2, '0');
            return `${yyyy}-${mm}-${dd}`;
          } catch (e) { return ''; }
        },
        buildList(html) {
          // If content already has a list, return as is
          if (/(<ul\b|<ol\b)/i.test(html)) return html;
          // Split on <br> inserted by nl2br; handle various forms
          const parts = html.split(/<br\s*\/?>(?:\n)?/i)
            .map(s => s.replace(/^[-*]\s*/, '').trim())
            .filter(Boolean);
          if (!parts.length) return html;
          const lis = parts.map(line => `<li>${line}</li>`).join('');
          return `<ul class="list-disc ml-6 space-y-1">${lis}</ul>`;
        },
        nextRelease() {
          if (this.currentReleaseIndex < this.unseenReleases.length - 1) {
            this.currentReleaseIndex++;
          }
        },
        prevRelease() {
          if (this.currentReleaseIndex > 0) {
            this.currentReleaseIndex--;
          }
        },
        dismiss() {
          // Mark all unseen releases as seen
          if (this.dontShowAgain) {
            try {
              // Update last seen version to the latest unseen release
              if (this.unseenReleases.length > 0) {
                const latestVersion = this.unseenReleases[0].version;
                localStorage.setItem('release_notes_last_seen_version', latestVersion);
              }
            } catch (e) {}
          }
          this.open = false;
        }
      }
    }
  </script>
</div>


