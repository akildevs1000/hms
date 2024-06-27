<template>
  <div>
    <v-dialog persistent v-model="dialog" max-width="1050">
      <template v-slot:activator="{ on, attrs }">
        <v-btn v-bind="attrs" v-on="on" block dark class="primary pt-4 pb-4">
          <v-icon small dark>mdi-plus</v-icon> ID
        </v-btn>
      </template>
      <v-card style="overflow: hidden">
        <v-row>
          <v-col cols="12" class="text-right">
            <v-icon @click="close" color="blue"
              >mdi-close-circle-outline</v-icon
            >
          </v-col>
          <v-col cols="7" class="text-center pl-5 mb-1">
            <VideoBox
              @frontSrc="handlFrontSrc"
              @backSrc="handlBackSrc"
              @close="close"
            />
          </v-col>
          <v-col cols="5" class="text-right pr-5">
            <img
              style="border: 1px solid black"
              v-if="frontSrcPreview"
              :src="frontSrcPreview"
              alt="Front Image"
            />
            <br />
            <br />
            <img
              style="border: 1px solid black"
              v-if="backSrcPreview"
              :src="backSrcPreview"
              alt="Front Image"
            />
            <br />
            <v-btn
              v-if="frontSrcPreview && backSrcPreview"
              class="grey white--text"
              @click="clear"
              >Clear</v-btn
            >

            <v-btn
              v-if="frontSrcPreview && backSrcPreview"
              class="blue white--text"
              @click="submit"
              >Submit</v-btn
            >
          </v-col>
        </v-row>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
export default {
  data: () => ({
    dialog: false,
    frontSrcPreview: null,
    backSrcPreview: null,
  }),
  methods: {
    handlFrontSrc(e) {
      this.frontSrcPreview = e;
    },
    handlBackSrc(e) {
      this.backSrcPreview = e;
    },
    submit() {
      this.dialog = false;
      this.$emit("front_pic", this.frontSrcPreview);
      this.$emit("back_pic", this.backSrcPreview);
    },
    clear() {
      this.frontSrcPreview = null;
      this.backSrcPreview = null;
      this.$emit("front_pic", null);
      this.$emit("back_pic", null);
    },

    close() {
      this.dialog = false;
      this.frontSrcPreview = null;
      this.backSrcPreview = null;
      this.$emit("front_pic", null);
      this.$emit("back_pic", null);
    },
  },
};
</script>
