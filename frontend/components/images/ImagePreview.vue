<template>
  <div>
    <v-img :src="src" class="grey darken-4" v-if="isImg"></v-img>
    <embed v-else-if="isPdf" :src="src" width="100%" height="1000px" />
  </div>
</template>

<script>
export default {
  props: ["docObj"],
  data() {
    return {
      imgView: false,
      isImg: false,
      isPdf: false,
      src: false
    };
  },
  watch: {
    docObj() {
      this.docView();
    }
  },
  mounted() {
    this.docView();
  },

  methods: {
    docView() {
      console.log(this.docObj);
      if (this.docObj.fileExtension == "pdf") {
        this.isPdf = true;
        this.isImg = false;
      } else {
        this.isPdf = false;
        this.isImg = true;
      }
      this.src = this.docObj.file;

      console.log(this.isPdf + "isPdf");
      console.log(this.isImg + "isImg");
      console.log(this.src);
    }
  }
};
</script>

<style scoped></style>
