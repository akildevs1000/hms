<template>
  <div class="text-center">
    <v-dialog persistent v-model="dialog" max-width="900px">
      <template v-slot:activator="{ on, attrs }">
        <v-btn v-bind="attrs" v-on="on" block dark class="primary pt-4 pb-4">
          <v-icon small dark>mdi-file</v-icon>
        </v-btn>
      </template>
      <v-card>
        <v-toolbar dense flat>
          <v-spacer></v-spacer>
          <v-icon @click="dialog = false" color="primary">mdi-close</v-icon>
        </v-toolbar>

        <v-container>
          <v-row>
            <v-col cols="6">
              <div class="text-center">Front</div>
              <img style="width: 100%;" v-if="frontSrc" :src="frontSrc" alt="Front Image" />
            </v-col>
            <v-col cols="6">
              <div class="text-center">Back</div>
              <img style="width: 100%;" v-if="backSrc" :src="backSrc" alt="Back Image" />
            </v-col>
          </v-row>
        </v-container>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
export default {
  props: ["frontSrc", "backSrc"],
  data: () => ({
    dialog: false,
    videoStream: null,
  }),
};
</script>
