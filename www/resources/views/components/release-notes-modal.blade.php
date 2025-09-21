<div x-data="releaseNotes()" x-init="init()">
  <style>[x-cloak]{display:none!important}</style>
  <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/40" @click="dismiss()"></div>
    <div class="relative bg-white rounded-lg shadow-xl border border-gray-200 max-w-2xl w-full mx-4">
      <div class="px-5 py-4 bg-blue-50 border-b border-blue-100 flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900" x-text="`What's new in ${version || appVersion}${releaseDate ? ' (' + formatDate(releaseDate) + ')' : ''}`"></h3>
        <button class="text-gray-400 hover:text-gray-600" @click="dismiss()"><i class="fas fa-times"></i></button>
      </div>
      <div class="px-5 py-4 text-sm text-gray-700 max-h-[60vh] overflow-y-auto">
        <div x-show="notesHtml" x-html="buildList(notesHtml)"></div>
        <div x-show="!notesHtml" class="text-gray-500">No notes available.</div>
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
      const appVersion = @js(config('app.version'));
      const key = (v) => 'release_notes_seen_' + (v || appVersion);
      return {
        appVersion,
        open: false,
        version: null,
        releaseDate: null,
        notesHtml: '',
        dontShowAgain: true,
        async init() {
          // Fetch latest release
          try {
            const res = await fetch('{{ route('changelog.feed') }}', { cache: 'no-cache' });
            if (res.ok) {
              const data = await res.json();
              const latest = Array.isArray(data.releases) && data.releases.length ? data.releases[0] : null;
              if (latest) {
                this.version = latest.version || this.appVersion;
                this.releaseDate = latest.date || null;
                this.notesHtml = latest.notesHtml || '';
              }
            }
          } catch (e) {}
          // Respect seen flag
          const v = this.version || this.appVersion;
          let seen = false;
          try { seen = !!localStorage.getItem(key(v)); } catch (e) {}
          this.open = !seen && !!this.notesHtml;
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
        dismiss() {
          const v = this.version || this.appVersion;
          if (this.dontShowAgain) {
            try { localStorage.setItem(key(v), '1'); } catch (e) {}
          }
          this.open = false;
        }
      }
    }
  </script>
</div>


