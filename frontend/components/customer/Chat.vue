<template>
  <v-container
    fluid
    style="
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 0;
      min-height: 522px;
      max-height: 522px;
    "
  >
    <!-- Chat Messages Container -->
    <v-card
      style="flex: 1; overflow-y: auto; padding: 16px; min-height: 50vh"
      elevation="0"
    >
      <v-container
        v-if="messages.length === 0"
        style="
          display: flex;
          align-items: center;
          justify-content: center;
          min-height: inherit;
        "
      >
        <p class="text-center">No messages yet</p>
      </v-container>

      <v-container v-else style="display: flex; flex-direction: column">
        <div
          v-for="(message, index) in messages"
          :key="index"
          class="text-color"
        >
          <div
            v-if="message.sender_id == id"
            style="display: flex; justify-content: flex-end"
            class="pb-1"
          >
            <div
              style="
                font-size: 13px;
                display: inline-block;
                line-height: 1.1;
                margin-right: 10px;
                margin-top: 15px;
              "
              class="text-right"
            >
              <audio
                v-if="message.voice_note"
                controls
                style="width: 200px; height: 32px; margin-top: -6px"
              >
                <source :src="message.voice_note" />
              </audio>

              <div>
                <v-avatar
                  class="mr-1 my-2"
                  v-for="(chat_photo, index) in message.chat_photos"
                  :key="index"
                  style="
                    border: 5px solid #eaeaea;
                    border-radius: 5px !important;
                  "
                  tile
                  size="30"
                >
                  <ThumbnailImageView
                    :key="chat_photo.photo_path"
                    :src="chat_photo.photo_path"
                  />
                </v-avatar>
              </div>
              <div
                :class="` ${
                  message && message.chat_photos.length > 0 ? 'pr-2' : ''
                }`"
              >
                {{ message.message }}
              </div>
              <small
                :class="` ${
                  message && message.chat_photos.length > 0 ? 'pr-2' : ''
                }`"
              >
                {{ $dateFormat.hm(message.created_at) }}</small
              >
            </div>

            <v-avatar
              :class="`purple lighten-1 ${
                message && message.chat_photos.length > 0 ? 'mt-5' : 'mt-2'
              }`"
              size="30"
            >
              <v-icon color="white" style="align-self: flex-start" small
                >mdi-account</v-icon
              >
            </v-avatar>
          </div>

          <div style="display: flex" v-else class="pb-1">
            <v-avatar class="grey lighten-1" size="30">
              <v-icon color="white" small>mdi-account</v-icon>
            </v-avatar>
            <div
              style="
                font-size: 13px;
                display: inline-block;
                line-height: 1.1;
                margin-left: 10px;
              "
              class="mt-3"
            >
              <audio
                v-if="message.voice_note"
                controls
                style="width: 200px; height: 32px; margin-top: -12px"
              >
                <source :src="message.voice_note" />
              </audio>
              <div>
                <v-avatar
                  v-for="(chat_photo, index) in message.chat_photos"
                  :key="index"
                  style="
                    border: 5px solid #eaeaea;
                    border-radius: 5px !important;
                  "
                  tile
                  size="30"
                >
                  <ThumbnailImageView
                    :key="chat_photo.photo_path"
                    :src="chat_photo.photo_path"
                  />
                </v-avatar>
              </div>
              <div>{{ message.message }}</div>
              <small>{{ $dateFormat.hm(message.created_at) }}</small>
            </div>
          </div>
        </div>
      </v-container>
    </v-card>

    <!-- Input Field at Bottom -->
    <div style="position: sticky; bottom: 60px; background: white">
      <div v-if="previewImages.length" class="mb-1">
        <v-row no-gutters>
          <v-col
            cols="2"
            v-for="(previewImage, index) in previewImages"
            :key="index"
          >
            <div style="position: relative">
              <v-icon
                class="my-icon"
                style="position: absolute; z-index: 1; left: 30px; top: -6px"
                right
                color="red"
                small
                @click="removePreviewImage(index)"
              >
                mdi-close
              </v-icon>

              <v-avatar
                style="border: 1px solid purple; border-radius: 10px !important"
                tile
                size="50"
              >
                <v-img :src="previewImage"></v-img>
              </v-avatar>
            </div>
          </v-col>
        </v-row>
      </div>

      <div style="display: flex">
        <div style="width: 100%">
          <v-text-field
            @paste="handlePaste"
            outlined
            dense
            hide-details
            v-model="newMessage"
            label="Type your message..."
            @keyup.enter="sendMessage"
            style="width: 100%"
          >
            <template v-slot:append>
              <span>
                <UploadMultiplePhotos
                  @files-selected="handleMultipleFileSelection($event)"
                />
              </span>
              <span style="margin-right: 18px !important; margin-top: -4px">
                <WidgetsVoice
                  @voice-note="handleVoiceNote($event, `${Date.now()}.mp3`)"
              /></span>
            </template>
          </v-text-field>
        </div>
        <div class="ml-2">
          <v-icon
            color="primary"
            @click="sendMessage"
            style="cursor: pointer; flex-grow: 1; margin-top: 3px"
          >
            mdi-send
          </v-icon>
        </div>
      </div>
    </div>
  </v-container>
</template>
<script>
import Pusher from "pusher-js";
export default {
  props: ["id"],
  data: () => ({
    newMessage: "",
    messages: [],
    previewImages: [],
    voice: null,
  }),
  async created() {
    await this.getMessages();
    await this.checkForNewMessageCount();
  },
  methods: {
    handleVoiceNote(e, name) {
      this.voice = {
        voice_note: e,
        voice_note_name: name,
      };
    },
    handleMultipleFileSelection(e) {
      this.previewImages = e;
    },
    removePreviewImage(index) {
      this.previewImages.splice(index, 1);
    },
    handlePaste(event) {
      if (this.previewImages.length >= 3) {
        alert(`Only 3 photos allowed`);
        return;
      }
      const items = (event.clipboardData || window.clipboardData).items;
      for (let index in items) {
        const item = items[index];
        if (item.kind === "file") {
          const file = item.getAsFile();
          const reader = new FileReader();
          reader.onload = (e) => {
            this.previewImages.push(e.target.result); // Add the preview image to the array
          };
          reader.readAsDataURL(file);
        }
      }
      // Prevent the default paste action
      event.preventDefault();
    },
    async getMessages() {
      let { data } = await this.$axios.get(`chat-by-customer-id/${this.id}`);
      this.messages = data;
      this.newMessage = "";
      this.previewImages = [];
    },
    async sendMessage() {
      try {
        let payload = {
          message: this.newMessage,
          sender_id: this.$auth.user.id,
          receiver_id: this.id,
          chat_photos: this.previewImages,
          company_id: this.$auth.user.company_id,

          ...this.voice,
        };

        await this.$axios.post(`chat`, payload);
        await this.getMessages();
      } catch (error) {
        console.log(error);
      }
    },

    async checkForNewMessageCount() {
      Pusher.logToConsole = true;
      const pusher = new Pusher("b042e1aebc809a6212dd", {
        cluster: "ap2",
      });

      // Subscribe to the channel and bind to the event
      const channel = pusher.subscribe(
        "my-channel" + this.$auth.user.company_id
      );
      channel.bind("my-event", async (data) => {
        await this.getMessages();
      });
    },
  },
};
</script>
