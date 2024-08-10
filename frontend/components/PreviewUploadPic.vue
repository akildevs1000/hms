<template>
  <v-dialog v-model="dialog" height="700" width="700">
    <template v-slot:activator="{ on, attrs }">
      <!-- <v-icon color="primary" v-bind="attrs" v-on="on">mdi-attachment</v-icon> -->
      <v-icon color="primary" v-bind="attrs" v-on="on">{{ icon }}</v-icon>
      {{ label }}
    </template>

    <v-card>
      <v-toolbar flat dense
        >{{ label }} <v-spacer></v-spacer>
        <v-icon @click="dialog = false" color="primary"
          >mdi-close-circle-outline</v-icon
        >
      </v-toolbar>
      
      <v-img :src="`${src}`" :alt="altText"></v-img>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  props: {
    label: {
      type: String,
      required: true,
      default: "View Photo",
    },
    icon: {
      type: String,
      required: true,
      default: "mdi-camera-outline",
    },
    src: {
      type: String,
      required: true,
      default: "",
    },
    altText: {
      type: String,
      default: "Image",
    },
  },
  data: () => ({
    BACKEND_ABSOLUTE_URL: process.env.BACKEND_ABSOLUTE_URL,
    dialog: false,
  }),
};
</script>
