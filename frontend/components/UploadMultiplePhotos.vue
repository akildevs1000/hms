<template>
  <span>
    <div @click="triggerFileInput">
      <v-icon
        style="padding: 7px; top: -3px; right: 1px; position: absolute"
        color="primary"
        >mdi-camera</v-icon
      >
    </div>
    <div>
      <input
        type="file"
        multiple
        :ref="`fileInput`"
        style="display: none"
        @change="handleFileInputChange"
      />
    </div>
  </span>
</template>

<script>
export default {
  props: {
    label: {
      default: "",
      type: String,
    },
    color: {
      default: "primary",
      type: String,
    },

    name: {
      default: "file",
      type: String,
    },
    defaultAttachments: {
      default: () => [],
      type: Array,
    },
  },
  data() {
    return {
      isFileSelect: false,
      selectedFiles: [],
      currentComponent: null,
      newDialogKey: 1,
      defaultItems: [],
    };
  },
  created() {
    this.defaultItems = this.defaultAttachments;
  },
  methods: {
    deleteItem(index) {
      this.selectedFiles.splice(index, 1);
    },
    triggerFileInput() {
      this.$refs[`fileInput`].click();
    },

    handleFileInputChange(event) {
      const files = event.target.files;
      if (files.length > 3) {
        alert(`Only 3 photos allowed`);
        return;
      }

      let images = [];

      for (let i = 0; i < files.length; i++) {
        let file = files[i];
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = (e) => {
          const img = new Image();
          img.src = e.target.result;
          img.onload = () => {
            const canvas = document.createElement("canvas");
            const ctx = canvas.getContext("2d");
            canvas.width = img.width;
            canvas.height = img.height;
            ctx.drawImage(img, 0, 0);

            canvas.toBlob(
              (blob) => {
                const compressedFile = new File([blob], file.name, {
                  type: "image/jpeg", // Change to the desired image type if needed
                  lastModified: Date.now(),
                });

                const compressedReader = new FileReader();
                compressedReader.readAsDataURL(compressedFile);
                compressedReader.onload = (compressedEvent) => {
                  images.push(compressedEvent.target.result);
                };
              },
              "image/jpeg",
              0.3
            ); // Adjust the image quality (0.7 is a good balance)
          };
        };
      }

      this.$emit("files-selected", images);
    },
  },
};
</script>
