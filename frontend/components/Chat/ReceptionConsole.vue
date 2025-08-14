<template>
  <v-container fluid class="pa-0 agent-console">
    <v-row no-gutters class="receiption-chats">
      <!-- LEFT: Chats list -->
      <v-col cols="12" md="3" class="left-col">
        <v-row>
          <v-col>
            <v-text-field
              v-model="filterSearch"
              placeholder="Filter Room Number or Guest name"
              dense
              outlined
              hide-details
              class="flex-grow-1 mr-2"
              clearable
            />
            <v-icon @click="getDataFromApi()">mdi-card-search-outline</v-icon>
          </v-col>
        </v-row>
        <div style="display: none1">
          <v-data-table
            v-if="bookingsListdata"
            dense
            :headers="headers"
            :items="bookingsListdata"
            :loading="loading"
            :options.sync="options"
            :server-items-length="totalRowsCount"
            :footer-props="{
              itemsPerPageOptions: [25, 50, 100, 500, 1000],
            }"
            :item-class="rowClass"
            @click:row="(item, event) => openRoomRow(item, event)"
          >
            <template v-slot:item.room="{ item }">
              {{ item.room_no }}
            </template>
            <template v-slot:item.guest="{ item }">
              {{ caps(item.booking.customer.first_name) }}</template
            ><template v-slot:item.checkin="{ item }">
              {{ $dateFormat.dateWithDayShortName(item.check_in) }} </template
            ><template v-slot:item.checkout="{ item }">
              <div v-if="item.booking_status == 2" style="color: red">---</div>
              <div v-else>
                {{ $dateFormat.dateWithDayShortName(item.check_out) }}
              </div>
            </template>

            <template v-slot:item.chat="{ item }">
              <v-chip
                v-if="unread[String(item.id)]"
                x-small
                color="red"
                text-color="white"
                label
              >
                {{ unread[String(item.id)] }}
              </v-chip>
            </template>
          </v-data-table>
        </div>

        <!-- <div class="px-4 py-3 d-flex align-center justify-space-between">
          <div class="text-subtitle-1 font-weight-medium">My chats</div>
          <div class="caption grey--text">{{ bookingsList.length }}</div>
        </div>

        <v-text-field
          v-model="q"
          dense
          hide-details
          outlined
          class="mx-4 mb-3"
          placeholder="Search guests…"
          prepend-inner-icon="mdi-magnify"
        />

        <v-list dense two-line nav class="py-0">
          <template v-for="r in filteredRooms">
            <v-list-item
              :key="r"
              :class="{ 'active-chat': String(r) === String(activeRoom) }"
              @click="openRoom(r)"
            >
              <v-list-item-avatar size="34">
                <v-avatar color="blue lighten-5">
                  <span class="blue--text text--darken-2">{{
                    initials(r)
                  }}</span>
                </v-avatar>
              </v-list-item-avatar>
              <v-list-item-content>
                <v-list-item-title class="text-truncate"
                  >Room {{ r }}</v-list-item-title
                >
                <v-list-item-subtitle class="text-truncate">
                  {{ lastPreview(r) }}
                </v-list-item-subtitle>
              </v-list-item-content>
              <v-list-item-action>
                <v-chip
                  v-if="unread[String(r)]"
                  x-small
                  color="red"
                  text-color="white"
                  label
                >
                  {{ unread[String(r)] }}
                </v-chip>
              </v-list-item-action>
            </v-list-item>
            <v-divider :key="'d-' + r" inset></v-divider>
          </template>
        </v-list> -->
      </v-col>

      <!-- CENTER: Conversation -->
      <v-col cols="12" md="9" class="center-col">
        <div class="conv-header d-flex align-center px-4">
          <div>
            <div class="text-subtitle-1 font-weight-medium">
              Room Number : {{ activeRoomBooking?.room_no || "—" }}
            </div>
            <div class="caption grey--text">
              <span :class="{ 'green--text': online, 'orange--text': !online }">
                {{ online ? "" : "● Reconnecting…" }}
              </span>
            </div>
          </div>
          <v-spacer></v-spacer>
          Guest Name:
          {{ activeRoomBooking?.booking.customer.title || "—" }}
          {{ activeRoomBooking?.booking.customer.first_name || "—" }}
          {{ activeRoomBooking?.booking.customer.last_name || "—" }}
          <!-- <v-btn icon @click="loadHistory(activeRoom)"
            ><v-icon>mdi-refresh</v-icon></v-btn
          > -->
        </div>

        <div
          ref="scroll"
          class="messages px-4 py-3"
          style="background-color: var(--wa-bg, #e5ddd5)"
          :key="messagesKey"
        >
          <div
            style="text-align: center"
            v-if="
              !messages[String(activeRoom)] ||
              messages[String(activeRoom)].length == 0
            "
          >
            No Chat History is available
          </div>
          <div
            v-for="m in messages[String(activeRoom)] || []"
            :key="m.id"
            class="mb-3 msg"
            :class="m.role === 'reception' ? 'mine' : 'them'"
          >
            <div
              v-if="m.type === 'system'"
              class="caption text-center my-4 grey--text"
            >
              {{ m.text }}
            </div>

            <div v-else class="bubble">
              <div class="caption grey--text text--darken-1 mb-1">
                {{ prettySender(m.sender) }} · {{ time(m.ts) }}
                <!-- <span>✓</span> -->
                <!-- <span v-if="m.role === 'reception'" class="ml-1">
                  · <span v-if="m.seen" title="Seen by guest">✓✓</span
                  ><span v-else>✓</span>
                </span> -->
              </div>

              <div v-if="m.type === 'text'">{{ m.text }}</div>
              <v-card v-else-if="m.type === 'file'" flat class="pa-3 card-msg">
                <div class="subtitle-2 mb-1">Attachment</div>
                <a :href="m.url" target="_blank">{{ m.filename || "file" }}</a>
              </v-card>
              <audio
                v-else-if="m.type === 'audio'"
                :src="m.url"
                controls
              ></audio>
            </div>
          </div>

          <!-- <div v-if="typingNames.length" class="caption grey--text mt-2">
            {{ typingNames.join(", ") }} typing…
          </div> -->
        </div>

        <div class="composer px-3 py-2 d-flex align-center" v-if="activeRoom">
          <v-text-field
            ref="messageInput"
            v-model="draft"
            placeholder="Type a message…"
            dense
            outlined
            hide-details
            class="flex-grow-1 mr-2"
            @keydown.enter.exact.prevent="send"
          />
          <v-btn color="primary" @click="send">Send</v-btn>
        </div>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  name: "AgentConsole",
  props: {
    hotelId: { type: [String, Number], required: true },
    staffName: { type: String, default: "Reception" },
  },
  data: () => ({
    filterSearch: "",
    bookingsListdata: null,
    page: 1,
    perPage: 0,
    currentPage: 1,
    cumulativeIndex: 1,

    action: null,
    headers: [
      { text: "Room", value: "room", sortable: false, filterable: false },
      {
        text: "Guest",
        value: "guest",
        sortable: false,
        filterable: false,
      },
      {
        text: "Check_in",
        value: "checkin",
        sortable: false,
        filterable: false,
      },
      {
        text: "Check Out",
        value: "checkout",
        sortable: false,
        filterable: false,
      },
      {
        text: "Chat Unread",
        value: "chat",
        sortable: false,
        filterable: false,
      },
    ],
    totalRowsCount: 0,
    pagination: {
      current: 1,
      total: 0,
      per_page: 25,
    },
    Model: "Sources",
    options: { page: 1, per_page: 25 },
    loading: false,
    messagesKey: 1,
    // your state
    bookingsList: [],
    activeRoom: null,
    messages: {}, // { "1205": [...] }
    unread: {},
    draft: "",
    typingMap: {}, // { "1205": Set() }
    typingUnsubs: {}, // { "1205": fn }
    ackUnsub: null,
    unsubs: [],
    _t: null,
    _typingTimers: {},

    // ui
    q: "",
    online: false,
    roomTags: {}, // { "1205": ["billing"] }
    tagDraft: "",
    activeRoomBooking: null,
  }),
  computed: {
    me() {
      return `Reception:${this.staffName}`;
    },
    wildcard() {
      return `chat/hotel/${this.hotelId}/room/+/message`;
    },
    typingNames() {
      const set = this.typingMap[String(this.activeRoom)] || new Set();
      return [...set].map(this.prettyUser);
    },
    filteredRooms() {
      if (!this.q) return this.bookingsList;
      const s = this.q.toLowerCase();
      return this.bookingsList.filter((r) =>
        String(r).toLowerCase().includes(s)
      );
    },
  },
  watch: {
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
  },
  async mounted() {
    await this.getDataFromApi();

    setTimeout(() => {
      this.$refs.messageInput.focus();
    }, 3000);
  },

  beforeDestroy() {
    this.unsubs.forEach((fn) => fn && fn());
    Object.values(this.typingUnsubs).forEach((fn) => fn && fn());
    if (this.ackUnsub) this.ackUnsub();
    Object.values(this._typingTimers || {}).forEach((t) => clearTimeout(t));
  },
  methods: {
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },
    rowClass(item) {
      // add classes per row (item is the row's data)
      const classes = [];
      if (String(item.id) === String(this.activeRoom)) {
        classes.push("active-chat");
      }
      if (item.booking_status == 2) {
        classes.push("row-cancelled");
      } else if (new Date(item.check_out) < new Date()) {
        classes.push("row-overdue");
      }
      return classes.join(" ");
    },

    async loadBookingRoomslist() {
      this.bookingsList = [...new Set(this.bookingsListdata.map((e) => e.id))];
      console.log(this.bookingsList);
      this.activeRoom = this.bookingsList[0];

      // connection indicators
      const c = this.$mqtt?.raw;
      if (c) {
        this.online = c.connected;
        c.on("connect", () => {
          this.online = true;
        });
        c.on("reconnect", () => {
          this.online = false;
        });
        c.on("close", () => {
          this.online = false;
        });
      }

      // wildcard messages
      const unsub = this.$mqtt.sub(this.wildcard, (m, topic) => {
        const segs = (topic || "").split("/");
        const bookingId = String(segs[4] || "");
        // this.scrollToEnd();
        // this.$nextTick(this.scrollToEnd);
        if (!m) return;

        // console.log(bookingId);

        this.upsertMessage(bookingId, m);

        if (bookingId !== String(this.activeRoom)) {
          this.$set(this.unread, bookingId, (this.unread[bookingId] || 0) + 1);

          // this.scrollToEnd();
          // this.$nextTick(this.scrollToEnd);
        } else {
          // this.scrollToEnd();
          // this.$nextTick(this.scrollToEnd);
          this.ack(bookingId, m.id);
        }
        setTimeout(() => {
          this.scrollToEnd();
          this.$nextTick(this.scrollToEnd);
        }, 1000 * 2);
      });
      this.unsubs.push(unsub);

      // open current room streams
      await this.openRoom(this.activeRoom);

      this.messagesKey++;
      // this.$nextTick(this.scrollToEnd);
      // this.scrollToEnd();
    },
    async getDataFromApi() {
      //let page = this.pagination.current;
      this.currentPage = this.currentPage ?? 1;

      let { sortBy, sortDesc, page, itemsPerPage } = this.options;
      let sortedBy = sortBy ? sortBy[0] : "";
      let sortedDesc = sortDesc ? sortDesc[0] : "";

      this.perPage = itemsPerPage;
      // if (!page > 0) return false;
      this.loading = true;
      let options = {
        params: {
          page: page,
          //sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: itemsPerPage,
          pagination: true,

          company_id: this.$auth.user.company.id,
          search: this.search,
          type: this.type,
          filterSearch: this.filterSearch,
        },
      };

      this.$axios.get("/chat_messages_bookings", options).then(({ data }) => {
        this.currentPage = page;
        this.bookingsListdata = data.data;
        this.pagination.current = data.current_page;
        this.pagination.total = data.last_page;
        this.loading = false;
        this.totalRowsCount = data.total;

        this.loadBookingRoomslist();
      });
    },
    prettyUser(u) {
      const [rid, name] = String(u || "").split(":");
      return name && rid ? `${name} ${rid}` : name || u; // e.g. "Guest 1205"
    },
    // UI helpers
    initials(r) {
      return "R";
    },
    prettySender(s) {
      if (!s) return "";
      if (String(s).startsWith("Reception:")) return "Me (" + s + " )";

      return "Guest ( " + s + ")";
      const [rid, name] = String(s).split(":");
      return name || `Guest ${rid}`;
    },
    time(ts) {
      return new Date(ts).toLocaleTimeString([], {
        hour: "2-digit",
        minute: "2-digit",
      });
    },
    scrollToEnd() {
      const el = this.$refs.scroll;

      if (el) el.scrollTop = el.scrollHeight;
    },
    lastPreview(r) {
      const list = this.messages[String(r)] || [];
      const last = list[list.length - 1];
      if (!last) return "—";
      return last.type === "text" ? last.text : last.type;
    },

    // Data helpers (your model)
    ensureList(bookingId) {
      const key = String(bookingId);
      if (!this.messages[key]) this.$set(this.messages, key, []);
      return this.messages[key];
    },
    upsertMessage(bookingId, msg) {
      const key = String(bookingId);
      const list = this.ensureList(key);
      const i = list.findIndex((x) => x.id === msg.id);
      if (i === -1) list.push(msg);
      else this.$set(list, i, { ...list[i], ...msg });
    },

    // MQTT topics
    msgTopic(r) {
      return `chat/hotel/${this.hotelId}/room/${String(r)}/message`;
    },
    ackTopic(r) {
      return `chat/hotel/${this.hotelId}/room/${String(r)}/ack`;
    },
    async loadHistory(bookingId) {
      try {
        const q = `?company_id=${this.hotelId}&bookingId=${bookingId}&limit=50`;
        const rows =
          (await this.$axios.get(`/chat_messages_history${q}`)) || [];
        this.messages[bookingId] = rows.data;

        this.$nextTick(this.scrollToEnd);

        setTimeout(() => {
          this.$nextTick(this.scrollToEnd);
        }, 1000 * 2);
        // this.$nextTick(this.scrollToEnd);
      } catch (_) {}
    },
    openRoomRow(item) {
      this.openRoom(item.id);
    },
    async openRoom(bookingOrderId) {
      this.activeRoomBooking = this.bookingsListdata.find(
        (e) => e.id == bookingOrderId
      );

      const key = String(bookingOrderId);
      this.activeRoom = bookingOrderId;
      this.$set(this.unread, key, 0);

      // typing subscribe per room
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
          this.$forceUpdate();
        }
      });

      // ack subscribe for active room
      if (this.ackUnsub) this.ackUnsub();
      const aTopic = this.ackTopic(key);
      this.ackUnsub = this.$mqtt.sub(aTopic, (ack) => {
        const list = this.messages[key] || [];
        list.forEach((x, idx) => {
          if (x.id === ack?.lastSeenId && !x.seen) {
            this.$set(list, idx, { ...x, seen: true });
          }
        });
        this.scrollToEnd();
        this.$nextTick(this.scrollToEnd);
      });

      // optional: history
      await this.loadHistory(bookingOrderId);

      this.$nextTick(this.scrollToEnd);

      setTimeout(() => {
        this.$refs.messageInput.focus();
      }, 500);

      // await this.loadHistory(bookingId);
    },

    async send() {
      const text = this.draft.trim();
      if (!text || !this.activeRoom) return;
      const r = String(this.activeRoom);

      // console.log(this.activeRoomBooking);
      this.activeRoomBooking;
      let m = {
        id: Date.now() + "_" + Math.random().toString(36).slice(2),
        sender: this.me,
        role: "reception",
        type: "text",
        text,
        ts: Date.now(),
        booking_id: this.activeRoomBooking.booking_id,
        booking_room_id: this.activeRoomBooking.id,

        room_id: this.activeRoomBooking.room_id,
        room_number: this.activeRoomBooking.room_no,

        receiption_name: this.me,
      };
      this.$mqtt.pub(this.msgTopic(r), m);
      this.upsertMessage(r, m);
      this.draft = "";
      this.$nextTick(this.scrollToEnd);
      this.ack(r, m.id);

      //store backup
      try {
        // m = {

        //   ...m,
        // };

        await this.$axios.post(`/chat_messages`, m);
      } catch (e) {}
    },

    sendTyping() {
      if (!this.activeRoom) return;
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

    ack(bookingId, id) {
      const r = String(bookingId);
      this.$mqtt.pub(this.ackTopic(r), {
        user: this.me,
        lastSeenId: id,
        ts: Date.now(),
      });
    },

    // Tags right panel
    addTag() {
      const k = String(this.activeRoom);
      const t = this.tagDraft.trim();
      if (!t) return;
      const arr = this.roomTags[k] || [];
      arr.push(t);
      this.$set(this.roomTags, k, arr);
      this.tagDraft = "";
    },
    removeTag(i) {
      const k = String(this.activeRoom);
      const arr = this.roomTags[k] || [];
      arr.splice(i, 1);
      this.$set(this.roomTags, k, arr);
    },
  },
};
</script>
