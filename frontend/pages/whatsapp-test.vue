<template>
  <div>
    <v-card>
      <v-card-title
        style="background-color: rgb(95, 175, 163)"
        class="text-h5 white--text mb-5"
      >
        Send test Message to whatsapp
      </v-card-title>

      <v-card-text>
        <v-text-field
          dense
          outlined
          placeholder="number"
          v-model="instance_id"
        />
        <v-text-field
          dense
          outlined
          placeholder="number"
          v-model="access_token"
        />
        <v-text-field dense outlined placeholder="number" v-model="number" />
        <v-textarea dense outlined placeholder="message" v-model="message" />
      </v-card-text>

      <v-divider></v-divider>

      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn style="background-color: rgb(95, 175, 163)" dark @click="send">
          Send
        </v-btn>
      </v-card-actions>
    </v-card>
  </div>
</template>

<script>
export default {
  data: () => ({
    instance_id: "",
    access_token: "",
    number: "",
    message: "",
  }),
  methods: {
    send() {
      // https://ezwhat.com/api/send.php?number=923108559858&type=text&message=test%20message&instance_id=64466B01B7926&access_token=a27e1f9ca2347bb766f332b8863ebe9f
      this.$axios
        .get(
          `https://ezwhat.com/api/send.php?number=${this.number}&type=text&message=${this.message}&instance_id=${this.instance_id}&access_token=${this.access_token}`
        )
        .then(({ data }) => console.log(data));
    },
  },
};
</script>
