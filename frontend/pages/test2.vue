<template>
  <div>
    <check-in />
  </div>
</template>

<script>
import CheckIn from "../components/booking/CheckIn.vue";
export default {
  components: { CheckIn },
  data() {
    return {
      x: 0,
      y: 0
    };
  },
  mounted() {
    document.addEventListener("mousemove", this.updateMouseLocation);
  },

  methods: {
    updateMouseLocation(event) {
      this.x = event.clientX;
      this.y = event.clientY;
    },

    uploadImage() {
      const input = this.$refs.fileInput;
      const file = input.files[0];
      console.log(file);

      const reader = new FileReader();
      reader.readAsDataURL(file);
      reader.onload = e => {
        const dataURL = e.target.result;
        let payload = {
          image: dataURL,
          booking_id: 293
        };
        console.log(dataURL);
        this.$axios
          .post("/store_document", payload)
          .then(({ data }) => {
            this.loading = false;
            if (!data.status) {
              this.errors = data.errors;
              this.subLoad = false;
            }
          })
          .catch(e => console.log(e));
      };
    }
  }
};
</script>
