<template>
  <v-row no-gutter>
    <v-col style="font-size: 12px">
      <v-icon left color="blue">mdi-chat</v-icon> Chat
    </v-col>
    <v-col class="text-right" style="font-size: 12px">
      <span
        class="blue--text"
        style="cursor: pointer"
        @click="$router.push(`/chat`)"
        >View All</span
      >
    </v-col>
    <v-col cols="12">
      <v-simple-table dense>
        <template v-slot:default>
          <tbody>
            <tr v-for="(item, index) in items" :key="index">
              <td style="font-size: 11px">
                {{ item?.lattest_room?.room_no }}
              </td>
              <td style="font-size: 11px">
                {{ $dateFormat.hm(item.created_at) }}
              </td>
              <td style="font-size: 11px">
                <WidgetsReadMore :text="item.message" :textLength="10" />
              </td>
              <td style="font-size: 11px">{{ item.service }}</td>
              <td style="font-size: 11px">
                <WidgetsChatResponseDialog :item="item" />
              </td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>
    </v-col>
  </v-row>
</template>

<script>
import Pusher from "pusher-js";

export default {
  data() {
    return {
      items: [],
      intervalId: null,
      messageCount: 0, // To track the number of messages
    };
  },
  async created() {
    await this.getTopThreeMessages();
    await this.checkForNewMessageCount();
  },
  methods: {
    async getTopThreeMessages() {
      let payload = {
        params: {
          company_id: this.$auth.user.company_id,
        },
      };
      try {
        let { data } = await this.$axios.get(`lattest-three-chat`, payload);
        this.items = data;
      } catch (error) {
        console.error("Failed to fetch messages:", error);
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
        await this.getTopThreeMessages();
      });
    },
  },
};
</script>
