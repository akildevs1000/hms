<template>
  <v-container fill-height>
    <v-card class="mx-auto" width="1000">
      <v-row no-gutters>
        <v-col>
          <v-card
            flat
            style="min-height: 500px; width: 100%"
            class="d-flex justify-center align-center pa-0 ma-0"
          >
            <v-card-text>
              <div class="text-center">
                <img style="width: 250px" :src="logo" alt="logo" />
              </div>
              <v-container>
                <div>
                  Hi there! It seems like you haven't subscribed to the telegram
                  channel yet. Please click the following link or scan the QR
                  code to subscribe by sending a "hi" message.
                </div>
                <br />
                <div class="text-center">
                  <a
                    small
                    link
                    color="primary"
                    href="https://t.me/akil_dev_user_bot"
                    target="_blank"
                  >
                    https://t.me/akil_dev_user_bot
                  </a>
                </div>
                <br />
                <div class="text-center">OR</div>
                <br />
                <v-card outlined class="mx-auto" max-width="200">
                  <v-img
                    style="width: 100%"
                    src="/telegram-channel-link.png"
                  ></v-img>
                </v-card>
                <br />
                <div class="text-right">
                  <a small link color="primary" @click="getUpdates">
                    Back to login
                  </a>
                </div>
              </v-container>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" md="6" class="primary">
          <v-card
            dark
            flat
            style="min-height: 500px; width: 100%"
            class="white--text d-flex justify-center align-center primary pa-0 ma-0"
          >
            <v-card-text class="white--text">
              <h5>MyHotel2Cloud THE RIGHT SOLUTION FOR YOU</h5>
              <p>
                Make it simple, easy and accessible anywhere, anytime. Save
                time, stay compliant and reduce labor costs by streamlining how
                you collect hours worked and time-off accruals.
              </p>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-card>
  </v-container>
</template>

<script>
export default {
  layout: "login",
  data: () => ({
    logo: "/logo1.png",
  }),
  created() {},
  methods: {
    async getUpdates() {
      const botToken = "7356807670:AAGtb_m3juvOpUGZCBaMXK73oO7A0-iUPOg";
      let url = `https://api.telegram.org/bot${botToken}/getUpdates`;
      try {
        const { data } = await this.$axios.get(url);
        let result = data.result.filter((e) => e.message);
        let lastItem = result[result.length - 1]; // Get the last item from the filtered array
        this.updateChatIdForUser(lastItem?.message?.chat?.id);
      } catch (error) {
        console.error("Error sending message:", error);
      }
    },
    async updateChatIdForUser(telegram_chat_id) {
      let user_id = this.$auth.user.id;
      let url = `/update-telegram-chat-id/${user_id}`;
      try {
        await this.$axios.post(url, {
          telegram_chat_id,
        });
        this.$auth.logout();
        this.$router.push(`/login`);
      } catch (error) {
        console.error("Error sending message:", error);
      }
    },
  },
};
</script>
