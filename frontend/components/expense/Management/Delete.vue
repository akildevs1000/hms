<template>
  <div @click="submit">
    <v-icon color="black" small> mdi-delete </v-icon>
    Delete
  </div>
</template>
<script>
export default {
  props: ["id", "endpoint"],
  methods: {
    async submit() {
      try {
        await this.$axios.delete(this.endpoint + "/" + this.id);
        this.$emit("response", "Record has been delete");
      } catch ({ response }) {
        console.log(response);
        // console.error("Error adding income:", response.data.message);
      }
    },
  },
};
</script>
