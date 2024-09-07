<template>
  <v-hover v-slot:default="{ hover }">
    <div class="hover-div">
      Hover over this div
      <v-icon small v-if="hover" color="primary">mdi-pencil</v-icon>
    </div>
  </v-hover>
</template>

<script>
export default {
  name: "HoverPencilIcon",
};
</script>
