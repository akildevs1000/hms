<template>
  <div class="flex gap-6">
    <!-- LEFT: Chat Window -->
    <div class="flex-1">
      <div v-if="activeRoom" class="mb-2 text-sm text-gray-600">
        Hotel {{ hotelId }} · Room {{ activeRoom }}
      </div>

      <div
        v-if="activeRoom"
        ref="list"
        class="border rounded p-3"
        style="height: 420px; overflow: auto"
      >
        <div
          v-for="m in messages[String(activeRoom)] || []"
          :key="m.id"
          class="mb-2"
          :style="bubble(m)"
        >
          <div class="text-xs opacity-60">
            {{ m.sender }} · {{ time(m.ts) }}
            <span v-if="m.role === 'reception'">
              · <span v-if="m.seen" title="Seen by guest">✓✓</span
              ><span v-else>✓</span>
            </span>
          </div>

          <div v-if="m.type === 'text'">{{ m.text }}</div>
          <a v-else-if="m.type === 'file'" :href="m.url" target="_blank">{{
            m.filename || "file"
          }}</a>
          <audio v-else-if="m.type === 'audio'" :src="m.url" controls></audio>
        </div>

        <div v-if="typingNames.length" class="text-xs opacity-60 mt-2">
          {{ typingNames.join(", ") }} typing…
        </div>
      </div>

      <div v-if="activeRoom" class="mt-3 flex gap-2">
        <input
          v-model="draft"
          @keydown="onKey"
          @input="sendTyping"
          class="flex-1 border rounded px-3 py-2"
          placeholder="Reply to room…"
        />
        <button class="px-3 py-2 rounded bg-blue-600 text-white" @click="send">
          Send
        </button>
      </div>
    </div>

    <!-- RIGHT: Guests / Rooms List -->
    <aside class="w-64">
      <div class="font-semibold mb-2">Guests</div>

      <div
        v-for="r in roomList"
        :key="r"
        class="flex justify-between items-center mb-2"
      >
        <button
          class="px-3 py-2 rounded border w-full text-left"
          :class="{ 'bg-blue-50': String(r) === String(activeRoom) }"
          @click="openRoom(r)"
        >
          Guest {{ r }}
        </button>
        <span
          v-if="unread[String(r)]"
          class="ml-2 text-xs bg-red-600 text-white rounded px-2"
        >
          {{ unread[String(r)] }}
        </span>
      </div>
    </aside>
  </div>
</template>

<script>
export default {
  props: {
    hotelId: { type: [String, Number], required: true },
    staffName: { type: String, default: "Reception" },
  },
  data: () => ({
    roomList: [],
    activeRoom: null,
    messages: {}, // { "1205": [ { id, role, ... }, ... ] }
    unread: {}, // { "1205": 2 }
    draft: "",
    typingMap: {}, // { "1205": Set(users) }
    unsubs: [], // wildcard + typing unsubs pushed here too
    typingUnsubs: {}, // { "1205": fn }
    ackUnsub: null, // current room ack unsub
    _t: null, // typing debounce timer
    _typingTimers: {}, // per-guest auto-clear timers for typing state
  }),
  computed: {
    wildcard() {
      return `chat/hotel/${this.hotelId}/room/+/message`;
    },
    me() {
      return `Reception:${this.staffName}`;
    },
    typingNames() {
      const set = this.typingMap[String(this.activeRoom)] || new Set();
      return [...set].map(this.prettyUser);
    },
  },
  async mounted() {
    // Load room list (replace with API if needed)
    this.roomList = [1205, 1202, 1203];
    this.activeRoom = this.roomList[0];

    // Subscribe to all incoming room messages (wildcard)
    const wildcardUnsub = this.$mqtt.sub(this.wildcard, (m, topic) => {
      const segs = (topic || "").split("/");
      const roomId = String(segs[4] || ""); // chat/hotel/{2}/room/{4}/message
      if (!m) return;

      this.upsertMessage(roomId, m);

      if (roomId !== String(this.activeRoom)) {
        this.$set(this.unread, roomId, (this.unread[roomId] || 0) + 1);
      } else {
        this.$nextTick(this.scrollToEnd);
        this.ack(roomId, m.id);
      }
    });
    this.unsubs.push(wildcardUnsub);

    // Open initial room (typing + ack)
    this.openRoom(this.activeRoom);
  },
  beforeDestroy() {
    // Clean all general unsubs
    this.unsubs.forEach((fn) => fn && fn());
    this.unsubs = [];

    // Per-room typing unsubs
    Object.values(this.typingUnsubs).forEach((fn) => fn && fn());
    this.typingUnsubs = {};

    // Ack unsub
    if (this.ackUnsub) this.ackUnsub();
    this.ackUnsub = null;

    // Clear typing timers
    Object.values(this._typingTimers || {}).forEach((t) => clearTimeout(t));
  },
  methods: {
    prettyUser(u) {
      const [rid, name] = String(u || "").split(":");
      return name && rid ? `${name} ${rid}` : name || u; // "Guest 1205"
    },

    // Optional: load last N history items when entering a room
    async loadHistory(roomId) {
      try {
        const q = `?hotelId=${this.hotelId}&roomId=${roomId}&limit=50`;
        const rows = (await this.$axios.$get(`/api/chat/history${q}`)) || [];
        this.$set(this.messages, String(roomId), rows);
        this.$nextTick(this.scrollToEnd);
      } catch (_) {}
    },

    ensureList(roomId) {
      const key = String(roomId);
      if (!this.messages[key]) this.$set(this.messages, key, []);
      return this.messages[key];
    },
    upsertMessage(roomId, msg) {
      const key = String(roomId);
      const list = this.ensureList(key);
      const i = list.findIndex((x) => x.id === msg.id);
      if (i === -1) list.push(msg);
      else this.$set(list, i, { ...list[i], ...msg }); // merge updates (e.g., seen)
    },

    openRoom(roomId) {
      const key = String(roomId);
      this.activeRoom = roomId;
      this.$set(this.unread, key, 0);

      // Typing subscribe (single per room)
      const tTopic = `chat/hotel/${this.hotelId}/room/${key}/typing`;
      if (this.typingUnsubs[key]) this.typingUnsubs[key]();

      this.typingUnsubs[key] = this.$mqtt.sub(tTopic, (t) => {
        if (!this.typingMap[key]) this.$set(this.typingMap, key, new Set());
        const set = this.typingMap[key];

        const timerKey = `${key}:${t?.user || ""}`;
        clearTimeout(this._typingTimers?.[timerKey]);
        this._typingTimers = this._typingTimers || {};

        if (t && t.typing) {
          set.add(t.user);
          this._typingTimers[timerKey] = setTimeout(() => {
            set.delete(t.user);
            this.$forceUpdate();
          }, 3000);
        } else {
          set.delete(t && t.user);
        }
        this.$forceUpdate();
      });
      this.unsubs.push(this.typingUnsubs[key]);

      // ACK subscribe for active room (mark seen)
      if (this.ackUnsub) this.ackUnsub();
      const aTopic = `chat/hotel/${this.hotelId}/room/${key}/ack`;
      this.ackUnsub = this.$mqtt.sub(aTopic, (ack) => {
        const list = this.messages[key] || [];
        list.forEach((x, idx) => {
          if (x.id === ack?.lastSeenId && !x.seen) {
            this.$set(list, idx, { ...x, seen: true });
          }
        });
      });

      // Optional history load:
      // this.loadHistory(roomId);

      this.$nextTick(this.scrollToEnd);
    },

    msgTopic(r) {
      return `chat/hotel/${this.hotelId}/room/${String(r)}/message`;
    },
    ackTopic(r) {
      return `chat/hotel/${this.hotelId}/room/${String(r)}/ack`;
    },

    bubble(m) {
      const mine = m.role === "reception";
      return {
        maxWidth: "85%",
        marginLeft: mine ? "auto" : "0",
        background: mine ? "#e8f5e9" : "#f5f5f5",
        borderRadius: "14px",
        padding: "8px 12px",
      };
    },
    time(ts) {
      return new Date(ts).toLocaleTimeString();
    },
    scrollToEnd() {
      const el = this.$refs.list;
      if (el) el.scrollTop = el.scrollHeight;
    },

    send() {
      const text = this.draft.trim();
      if (!text) return;
      const r = String(this.activeRoom);
      const m = {
        id: Date.now() + "_" + Math.random().toString(36).slice(2),
        sender: this.me,
        role: "reception",
        type: "text",
        text,
        ts: Date.now(),
      };
      this.$mqtt.pub(this.msgTopic(r), m);
      this.upsertMessage(r, m);
      this.draft = "";
      this.$nextTick(this.scrollToEnd);
      this.ack(r, m.id);
    },

    onKey(e) {
      if (e.key === "Enter" && !e.shiftKey) {
        e.preventDefault();
        this.send();
      }
    },

    sendTyping() {
      const r = String(this.activeRoom);
      const tTopic = `chat/hotel/${this.hotelId}/room/${r}/typing`;
      this.$mqtt.pub(tTopic, { user: this.me, typing: true, ts: Date.now() });
      clearTimeout(this._t);
      this._t = setTimeout(() => {
        this.$mqtt.pub(tTopic, {
          user: this.me,
          typing: false,
          ts: Date.now(),
        });
      }, 1200);
    },

    ack(roomId, id) {
      const r = String(roomId);
      this.$mqtt.pub(this.ackTopic(r), {
        user: this.me,
        lastSeenId: id,
        ts: Date.now(),
      });
    },
  },
};
</script>
