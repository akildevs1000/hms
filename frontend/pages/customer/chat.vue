<template>
  <v-container>
    <style>
      .chat-container {
        display: flex;
        flex-direction: column;
        height: 8vh;
      }
    </style>
    <v-app-bar app color="" flat>
      <v-toolbar-title>Chat</v-toolbar-title>
    </v-app-bar>

    <v-container fluid class="mt-10">
      <div
        ref="chatMessages"
        :style="`overflow-y: scroll; overflow-x: hidden; 
        min-height: ${previewImages.length == 0 ? '700px' : '650px'};
        max-height: ${previewImages.length == 0 ? '700px' : '650px'}`"
      >
        <div
          v-if="!messages.length"
          style="align-items: center; justify-content: center; height: 75vh"
          class="d-flex"
        >
          No message found
        </div>
        <div
          v-for="(message, index) in messages"
          :key="index"
          class="text-color"
        >
          <div
            v-if="message.sender_id == sender_id"
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
                  size="50"
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
              size="40"
            >
              <v-icon color="white" style="align-self: flex-start"
                >mdi-account</v-icon
              >
            </v-avatar>
          </div>

          <div style="display: flex" v-else class="pb-1">
            <v-avatar class="grey lighten-1" size="40">
              <v-icon color="white">mdi-account</v-icon>
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
              <div>
                <v-avatar
                  v-for="(chat_photo, index) in message.chat_photos"
                  :key="index"
                  style="
                    border: 1px solid purple;
                    border-radius: 10px !important;
                  "
                  tile
                  size="50"
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
      </div>
    </v-container>
    <div style="position: relative" v-if="previewImages.length">
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
    <div class="chat-container">
      <div class="input-area">
        <div
          :style="`display: flex; padding-top: ${
            messages.length == 0 ? '45' : '30'
          }px;`"
        >
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
                <UploadMultiplePhotos
                  @files-selected="handleMultipleFileSelection($event)"
                /> </template
            ></v-text-field>
          </div>
          <div class="ml-2">
            <v-icon
              color="primary"
              @click="sendMessage"
              style="cursor: pointer; flex-grow: 1; margin-top: 2px"
            >
              mdi-send
            </v-icon>
          </div>
        </div>
      </div>
    </div>
  </v-container>
</template>

<script>
export default {
  layout: "qrcode",
  data: () => ({
    id: "",
    pageValid: false,
    newMessage: "",
    messages: [],
    sender_id: 0,
    receiver_id: 4,
    company_id: 0,
    previewImages: [],
    attachments: [],
  }),
  auth: false,
  mounted() {},
  async created() {
    this.sender_id = 3;
    this.company_id = 3;
    await this.getMessages();

    this.previewImages = [];

    setInterval(async () => {
      await this.getMessages();
    }, 5000);
  },
  methods: {
    handleMultipleFileSelection(e) {
      this.previewImages = e;
    },
    removePreviewImage(index) {
      this.previewImages.splice(index, 1);
    },
    async getMessages() {
      let payload = {
        params: {
          sender_id: this.sender_id,
          receiver_id: this.receiver_id,
          company_id: this.company_id,
        },
      };
      let { data } = await this.$axios.get(`chat`, payload);
      this.messages = data;
      this.scrollToBottom(); // Scroll to the bottom after fetching messages
    },

    scrollToBottom() {
      // Scroll to the bottom of the chat messages container
      const chatContainer = this.$refs.chatMessages;
      chatContainer.scrollTop = chatContainer.scrollHeight;
    },
    async sendMessage() {
      let payload = {
        message: this.newMessage,
        sender_id: this.sender_id,
        receiver_id: this.receiver_id,
        chat_photos: this.previewImages,
        company_id: this.company_id,
      };

      await this.$axios.post(`chat`, payload);
      await this.getMessages();
      this.newMessage = "";
      this.previewImages = [];
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
  },
};
</script>
