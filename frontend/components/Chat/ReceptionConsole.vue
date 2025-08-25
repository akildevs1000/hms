<template>
  <v-container fluid class="pa-0 agent-console">
    <v-dialog
      v-model="DialogpreviewImage"
      width="600px"
      max-width="100%"
      style="max-width: 100% !important; margin: 0px !important"
    >
      <v-card>
        <v-card-title dense class="primary white--text background">
          <span>
            Image
            <!-- Download
            <v-icon @click="downloadImage()" outlined dark color="white">
              mdi-download-box
            </v-icon>
             -->
          </span>
          <v-spacer> </v-spacer>
          <v-spacer></v-spacer>

          <v-icon
            @click="DialogpreviewImage = false"
            outlined
            dark
            color="white"
          >
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>
        <v-card-text style="" class="pt-2">
          <img
            :src="previewImageUrl"
            style="width: 100%; border-radius: 10px; text-align: right"
          />
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-row no-gutters class="receiption-chats">
      <!-- LEFT: Chats list -->
      <v-col cols="12" md="3" class="left-col">
        <v-row no-gutters>
          <v-col class="flex-grow-0 pr-2 pt-1">Search </v-col>
          <v-col>
            <v-text-field
              v-model="filterSearch"
              placeholder="Filter Room Number or Guest name"
              dense
              outlined
              hide-details
              clearable
              :loading="loading"
              @click:clear="getDataFromApi"
            />
          </v-col>

          <v-col class="flex-grow-0 pl-2" style="padding-top: 2px">
            <v-btn
              dense
              small
              class="primary"
              color="primary"
              :loading="loading"
              @click="getDataFromApi"
              :disabled="loading"
              >Search
              <v-icon>mdi-card-search-outline</v-icon>
            </v-btn>
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
                {{ unread[String(item.id)] / 2 }}
              </v-chip>
            </template>
          </v-data-table>
        </div>
      </v-col>

      <!-- CENTER: Conversation -->
      <v-col cols="12" md="9" class="center-col">
        <div class="conv-header d-flex align-center px-4">
          <div>
            <div class="text-subtitle-1">
              Room Number :
              <span style="font-weight: bold">
                {{ activeRoomBooking?.room_no || "—" }}</span
              >
            </div>
            <div class="caption grey--text">
              <span :class="{ 'green--text': online, 'orange--text': !online }">
                {{ online ? "" : "● Reconnecting…" }}
              </span>
            </div>
          </div>
          <v-spacer></v-spacer>
          Receiption Name :
          <span style="font-weight: bold">{{ caps(staffName) }}</span>
          <v-spacer></v-spacer>
          Guest Name :
          <span style="font-weight: bold">
            {{ activeRoomBooking?.booking.customer.title || "—" }}
            {{ caps(activeRoomBooking?.booking.customer.first_name) || "—" }}
            {{
              caps(activeRoomBooking?.booking.customer.last_name) || "—"
            }}</span
          >
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
                {{ prettySender(m.sender) }}
                <!-- <span>✓</span> -->
                <!-- <span v-if="m.role === 'reception'" class="ml-1">
                  · <span v-if="m.seen" title="Seen by guest">✓✓</span
                  ><span v-else>✓</span>
                </span> -->
              </div>

              <div v-if="m.type === 'text'">{{ m.text }}</div>
              <v-card
                v-else-if="m.type === 'file'"
                style="text-align: center"
                flat
                class="pa-3 card-msg"
              >
                <img
                  :src="m.url"
                  @click="viewImage(m.id, m.url)"
                  style="
                    margin: auto;
                    width: 100px;
                    border-radius: 10px;
                    text-align: right;
                  "
                />
                <!-- <div class="subtitle-2 mb-1">Attachment</div> -->
                <!-- <v-icon size="18" color="blue">mdi-message-image</v-icon>
                <a :href="m.url" target="_blank">
                  Image
                   {{ m.filename || "file" }}
                </a> -->
              </v-card>
              <audio
                style="height: 28px"
                controlsList="nodownload"
                v-else-if="m.type === 'audio'"
                :src="m.url"
                controls
              ></audio>
              <div
                :style="
                  m.role === 'reception'
                    ? 'text-align:right'
                    : 'text-align:  left'
                "
              >
                <span
                  class="sender"
                  style="padding-top: 5px; color: black; font-size: 10px"
                  ><v-icon size="16">mdi-clock-outline</v-icon>
                  {{ time(m.ts) }}</span
                >
              </div>
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
          <v-icon
            :color="selectedFile ? 'green' : ''"
            left
            @click="$refs.fileInput.click()"
            >mdi-paperclip</v-icon
          >
          <div v-if="selectedFile">
            <!-- File -->
            <v-icon color="red" @click="selectedFile = null"
              >mdi-delete-circle-outline</v-icon
            >
          </div>
          <div>
            <v-icon
              size="25"
              style="color: black; margin-top: 4px"
              @click="startRec"
              v-if="!recording"
              :disabled="recording"
              >mdi-microphone</v-icon
            >
            <span v-else :style="recording ? 'width:100px' : 'width:50px'">
              <v-icon
                class="flex"
                size="25"
                color="red"
                @click="stopRec"
                :disabled="!recording"
                style="color: red; margin-top: 4px"
                >mdi-microphone</v-icon
              >

              <span v-if="recording">{{ seconds }}s</span>
            </span>
          </div>
          <button style="margin-left: 5px" class="send-btn" @click="send">
            Send
          </button>

          <input
            accept="image/*"
            style="display: none"
            type="file"
            ref="fileInput"
            @change="handleFileSelect"
          />

          <!-- <v-btn color="primary" @click="send">Send</v-btn> -->
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
    mediaRecorder: null,
    chunks: [],
    recording: false,
    startTs: null,
    seconds: 0,
    tmr: null,
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
    timezone: "Asia/Kolkata",

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
    selectedFile: null,
    previewImageUrl: null,
    DialogpreviewImage: null,
    previewImageId: null,
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
    this.getChatUnreadmessages();
    // setTimeout(() => {
    //   this.$refs.messageInput.focus();
    // }, 3000);
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
      // console.log(this.bookingsList);
      this.activeRoom = this.bookingsList[0];

      localStorage.setItem("active_booking_room_id", this.activeRoom || "");

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

      // wildcard messages - All Rooms messages listner
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

          // Get existing IDs (or empty array if none)
          let ackMessageIDs = [];
          try {
            ackMessageIDs =
              JSON.parse(localStorage.getItem("ack_messages_ids")) || [];
          } catch (e) {
            ackMessageIDs = [];
          }

          // Add the new ID if not already stored
          if (!ackMessageIDs.includes(m.id)) {
            ackMessageIDs.push(m.id);
          }
          // console.log("ackMessageIDs", ackMessageIDs);

          if (ackMessageIDs != "") {
            // Do something with ackMessageIDs
            localStorage.setItem(
              "ack_messages_ids",
              JSON.stringify(ackMessageIDs)
            );
          } else {
            localStorage.setItem("ack_messages_ids", JSON.stringify([]));
          }

          // Save back to localStorage

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
      const date = new Date(ts);
      const now = new Date();

      // Check if same day
      const isToday =
        date.getDate() === now.getDate() &&
        date.getMonth() === now.getMonth() &&
        date.getFullYear() === now.getFullYear();

      if (isToday) {
        // Only show time
        return date.toLocaleTimeString([], {
          hour: "2-digit",
          minute: "2-digit",
        });
      } else {
        // Show date + time
        return (
          date.toLocaleDateString([], {
            month: "short",
            day: "numeric",
            year: "numeric", // valid values: "numeric" or "2-digit"
          }) +
          " " +
          date.toLocaleTimeString([], {
            hour: "2-digit",
            minute: "2-digit",
          })
        );
      }
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
    async loadHistory(bookingRoomId) {
      if (!bookingRoomId) return false;
      try {
        const q = `?company_id=${this.hotelId}&role=reception&booking_room_id=${bookingRoomId}&limit=50`;
        const rows =
          (await this.$axios.get(`/chat_messages_history${q}`)) || [];
        this.messages[bookingRoomId] = rows.data;

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
    async openRoom(bookingRoomId) {
      // optional: history
      await this.loadHistory(bookingRoomId);

      this.activeRoomBooking = this.bookingsListdata.find(
        (e) => e.id == bookingRoomId
      );

      const key = String(bookingRoomId);
      this.activeRoom = bookingRoomId;

      localStorage.setItem("active_booking_room_id", this.activeRoom || "");

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
      // await this.loadHistory(bookingRoomId);

      this.$nextTick(this.scrollToEnd);

      setTimeout(() => {
        try {
          this.$refs.messageInput.focus();
        } catch (e) {}
      }, 500);

      // await this.loadHistory(bookingId);
    },
    handleFileSelect(event) {
      this.selectedFile = event.target.files[0] || null;
      console.log("Selected file:", this.selectedFile?.name);
    },
    async getChatUnreadmessages() {
      if (this.chatUnreadMessagesStatus) return false;

      this.chatUnreadMessagesStatus = true;

      let company_id = this.$auth.user?.company?.id || 0;
      //console.log("company_id", company_id);
      if (company_id == 0) {
        return false;
      }
      let options = {
        params: {
          company_id: company_id,
        },
      };

      await this.$axios
        .get(`chat_get_unread_messages_group_by_bookingid`, options)
        .then(async ({ data }) => {
          data.forEach((item) => {
            item.booking_room_id;
            this.$set(
              this.unread,
              item.booking_room_id,
              item.unread_count * 2 || 0
            );
          });
        });
    },
    async sendFile() {
      if (!this.selectedFile) {
        //alert("Please select a file first");
        return;
      }

      const file = this.selectedFile;
      // const file = e.target.files && e.target.files[0];

      // console.log(file);
      const r = String(this.activeRoom);
      if (!file) return;
      try {
        const form = new FormData();
        form.append("file", file);
        form.append("sender", this.me);
        form.append("role", "reception");
        form.append("type", "file");
        form.append("ts", Date.now());

        form.append("booking_id", this.activeRoomBooking.booking_id);
        form.append("booking_room_id", this.activeRoomBooking.id);
        form.append("room_id", this.activeRoomBooking.room_id);
        form.append("room_number", this.activeRoomBooking.room_no);
        form.append("company_id", this.hotelId);

        const res = await this.$axios.post("/chat_messages_upload_file", form);

        const url = res && res.data.message.url;
        const id = res.data.message.id;

        const m = {
          id: id, //Date.now() + "_" + Math.random().toString(36).slice(2),
          sender: this.me,
          role: "reception",
          type: "file",
          url: url,
          filename: "image",
          ts: this.getSecondsInTimezone(this.timezone),
          tsDb: Date.now(),
          booking_id: this.activeRoomBooking.booking_id,
          booking_room_id: this.activeRoomBooking.id,

          room_id: this.activeRoomBooking.room_id,
          room_number: this.activeRoomBooking.room_no,

          company_id: this.hotelId,
        };
        this.$mqtt.pub(this.msgTopic(r), m);
        this.upsertMessage(r, m);
        this.draft = "";
        this.$nextTick(this.scrollToEnd);
        this.ack(r, m.id);
        await this.sendTyping();
        this.$nextTick(this.scrollToEnd);
        // this.$mqtt.pub(this.msgTopic, m);
        // this.upsertMessage(this.activeRoomBooking.booking_id, m);
        // this.$nextTick(this.scrollToEnd);
        // this.ack(m.id);
      } catch (err) {
        console.error("Upload failed", err);
      } finally {
        // e.target.value = "";
        this.$refs.fileInput.value = ""; // reset input
      }

      this.selectedFile = null; // reset after upload
      this.$refs.fileInput.value = ""; // reset input

      console.log("selectedFile", this.selectedFile);
    },
    viewImage(messageId, url) {
      this.previewImageId = messageId;
      let $url = process.env.BACKEND_URL;
      this.previewImageUrl = url;

      this.DialogpreviewImage = true;
    },
    downloadImage() {
      let $url = process.env.BACKEND_URL;
      window.open(
        `${$url}chat_download_image?id=${this.previewImageId}`,
        "_blank"
      );
    },
    async send() {
      if (this.selectedFile) {
        this.sendFile();
      }
      const text = this.draft.trim();
      if (!text || !this.activeRoom) return;
      const r = String(this.activeRoom);

      // console.log("activeRoomBooking", this.activeRoomBooking);
      this.activeRoomBooking;
      let m = {
        id: Date.now() + "_" + Math.random().toString(36).slice(2),
        sender: this.me,
        role: "reception",
        type: "text",
        text,
        ts: this.getSecondsInTimezone(this.timezone),
        tsDb: Date.now(),

        booking_id: this.activeRoomBooking.booking_id,
        booking_room_id: this.activeRoomBooking.id,

        room_id: this.activeRoomBooking.room_id,
        room_number: this.activeRoomBooking.room_no,

        receiption_name: this.me,
      };

      // console.log(Date.now());
      // console.log(
      //   new Date().toLocaleString("en-US", {
      //     timeZone: this.timezone,
      //   })
      // );

      this.$mqtt.pub(this.msgTopic(r), m);
      this.upsertMessage(r, m);
      this.draft = "";
      this.$nextTick(this.scrollToEnd);
      this.ack(r, m.id);
      await this.sendTyping();
      //store backup
      try {
        // m = {

        //   ...m,
        // };

        await this.$axios.post(`/chat_messages`, m);
      } catch (e) {}
    },
    getSecondsInTimezone(timeZone) {
      // Current UTC timestamp (ms)
      const now = new Date();

      // Format the time in the target timezone
      const formatter = new Intl.DateTimeFormat("en-US", {
        timeZone,
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: false,
      });

      // Extract date/time parts
      const parts = {};
      formatter.formatToParts(now).forEach(({ type, value }) => {
        parts[type] = value;
      });

      // Build a date string as if it's local time in that timezone
      const localTimeString = `${parts.year}-${parts.month}-${parts.day}T${parts.hour}:${parts.minute}:${parts.second}`;

      console.log(localTimeString);

      // Parse that string as if it's UTC (to get correct epoch seconds for that timezone clock time)
      return Math.floor(new Date(localTimeString).getTime());
    },
    async sendTyping() {
      if (!this.activeRoom) return;
      const r = String(this.activeRoom);
      const tTopic = `chat/hotel/${this.hotelId}/room/${r}/typing`;
      this.$mqtt.pub(tTopic, {
        user: this.me,
        typing: true,
        ts: this.getSecondsInTimezone(this.timezone),
        tsDb: Date.now(),
      });
      clearTimeout(this._t);
      this._t = setTimeout(() => {
        this.$mqtt.pub(tTopic, {
          user: this.me,
          typing: false,
          ts: this.getSecondsInTimezone(this.timezone),
          tsDb: Date.now(),
        });
      }, 1200);
    },

    ack(bookingId, id) {
      const r = String(bookingId);
      this.$mqtt.pub(this.ackTopic(r), {
        user: this.me,
        lastSeenId: id,
        ts: this.getSecondsInTimezone(this.timezone),
        tsDb: Date.now(),
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

    async startRec() {
      // Pick best supported mime for the browser
      const candidates = [
        "audio/webm;codecs=opus",
        "audio/webm",
        "audio/mp4", // iOS Safari will end up giving m4a
      ];
      let mimeType = "";
      for (const c of candidates) {
        if (MediaRecorder.isTypeSupported && MediaRecorder.isTypeSupported(c)) {
          mimeType = c;
          break;
        }
      }

      const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
      this.mediaRecorder = new MediaRecorder(
        stream,
        mimeType ? { mimeType } : {}
      );
      this.chunks = [];
      this.mediaRecorder.ondataavailable = (e) =>
        e.data.size && this.chunks.push(e.data);
      this.mediaRecorder.start();
      this.recording = true;
      this.startTs = Date.now();
      this.seconds = 0;
      this.tmr = setInterval(
        () => (this.seconds = Math.round((Date.now() - this.startTs) / 1000)),
        200
      );
    },

    async stopRec() {
      if (!this.mediaRecorder) return;
      await new Promise((res) => {
        this.mediaRecorder.onstop = res;
        this.mediaRecorder.stop();
      });
      clearInterval(this.tmr);

      // Stop all mic tracks so the browser icon goes away
      if (this.mediaRecorder.stream) {
        this.mediaRecorder.stream.getTracks().forEach((track) => track.stop());
      }
      this.recording = false;
      const r = String(this.activeRoom);

      if (!confirm("Are you sure want to send Audio record?")) {
        return false;
      }
      const blob = new Blob(this.chunks, {
        type: this.mediaRecorder.mimeType || "audio/webm",
      });
      const durationMs = Date.now() - this.startTs;

      // 1) Upload via HTTP
      const form = new FormData();
      // form.append("file", file);
      form.append("sender", this.me);
      form.append("role", "receiption");
      form.append("type", "audio");
      form.append("ts", Date.now());

      form.append("booking_id", this.activeRoomBooking.booking_id);
      form.append("booking_room_id", this.activeRoomBooking.id);
      form.append("room_id", this.activeRoomBooking.room_id);
      form.append("room_number", this.activeRoomBooking.room_no);
      form.append("company_id", this.hotelId);
      form.append("durationMs", durationMs.toString());
      form.append(
        "file",
        blob,
        `voice_${this.bookingRoomId}.${this.fileExt(blob.type)}`
      );
      // const text = this.draft.trim();
      if (!this.activeRoom) return;
      const res = await this.$axios.post("chat_messages_upload_file", form);

      // data => { url, mime, size, durationMs }
      const url = res && res.data.message.url;
      const id = res.data.message.id;

      if (url == "null") {
        alert("File Upload Failed");

        return false;
      }
      const m = {
        id: id, //Date.now() + "_" + Math.random().toString(36).slice(2),
        sender: this.me,
        role: "reception",
        type: "audio",
        url: url,

        filename: "audio",
        ts: this.getSecondsInTimezone(this.timezone),
        tsDb: Date.now(),
        booking_id: this.activeRoomBooking.booking_id,
        booking_room_id: this.activeRoomBooking.id,

        room_id: this.activeRoomBooking.room_id,
        room_number: this.activeRoomBooking.room_no,
        company_id: this.hotelId,
        audio: url,
      };

      this.$mqtt.pub(this.msgTopic(r), m);
      this.upsertMessage(r, m);
      this.draft = "";
      this.$nextTick(this.scrollToEnd);
      this.ack(r, m.id);
      await this.sendTyping();
      this.$nextTick(this.scrollToEnd);
    },

    fileExt(mime) {
      if (
        mime.includes("mp4") ||
        mime.includes("x-m4a") ||
        mime.includes("aac")
      )
        return "m4a";
      if (mime.includes("webm")) return "webm";
      if (mime.includes("ogg")) return "ogg";
      return "dat";
    },
  },
};
</script>
