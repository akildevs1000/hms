<template>
  <v-dialog v-model="CustomerChatHistoryDialog" max-width="650px">
    <AssetsIconClose left="640" @click="close" />
    <template v-slot:activator="{ on, attrs }">
      <span v-bind="attrs" v-on="on">
        <v-icon x-small color="primary">mdi-eye</v-icon>
        <AssetsTextLabel color="text-color" label="View" />
      </span>
    </template>

    <v-card>
      <v-alert class="rounded" color="grey lighten-3" dense flat>
        <span>Customer Chat History</span>
      </v-alert>
      <v-card-text>
        <div
          class="px-5"
          ref="chatMessages"
          :style="`overflow-y: scroll; overflow-x: hidden;max-height:500px;`"
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
                <div style="max-width: 300px"
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
                <div style="max-width: 300px">{{ message.message }}</div>
                <small>{{ $dateFormat.hm(message.created_at) }}</small>
              </div>
            </div>
          </div>
        </div>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>
<script>
const today = new Date();
const tomorrow = new Date(today);
tomorrow.setDate(tomorrow.getDate() + 1);
export default {
  props: ["id"],
  data: () => ({
    messages: [],
    previewImages: [],
    CustomerChatHistoryDialog: false,
  }),
  async created() {
    let { data } = await this.$axios.get(`chat-by-customer-id/${this.id}`);

    this.messages = data;
  },
  methods: {
    close() {
      this.CustomerChatHistoryDialog = false;
    },
  },
};
</script>
