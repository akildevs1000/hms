<template>
  <span>
    <v-file-input
      hide-details
      class=""
      prepend-icon=""
      append-icon="mdi-upload"
      v-model="files"
      color="primary"
      counter
      multiple
      placeholder="Select your files"
      :show-size="1000"
    >
      <template v-slot:selection="{ index, text }">
        <v-chip v-if="index < 2" label small color="primary">
          {{ text }}
        </v-chip>

        <span
          v-else-if="index === 2"
          class="text-overline grey--text text--darken-3 mx-2"
        >
          +{{ files.length - 2 }} File(s)
        </span>
      </template>
    </v-file-input>
  </span>
</template>

<script>
export default {
  data() {
    return {
      files: [], // Holds the uploaded files
    };
  },
  methods: {
    // Handle file input
    handleFiles(newFiles) {
      this.files = [...newFiles]; // Update files array
    },
  },
};
</script>
