<template>
  <span>
    <div style="display: flex; align-items: center; cursor: pointer">
      <div @click="triggerFileInput" style="height: 25px; width: 25px">
        <svg
          data-v-7d68c7d9=""
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 512 512"
          class="icon align-text-top"
        >
          <path
            data-v-7d68c7d9=""
            fill="#2196f3 "
            d="M447.6 270.8c-8.8 0-15.9 7.1-15.9 15.9v142.7H80.4V286.8c0-8.8-7.1-15.9-15.9-15.9s-15.9 7.1-15.9 15.9v158.6c0 8.8 7.1 15.9 15.9 15.9h383.1c8.8 0 15.9-7.1 15.9-15.9V286.8c0-8.8-7.1-16-15.9-16z"
          ></path>
          <path
            data-v-7d68c7d9=""
            fill="#2196f3 "
            d="M267.3 183.6c-.4-.4-.8-.7-1.2-1.1-.2-.1-.4-.3-.5-.4-.2-.2-.5-.4-.7-.5-.2-.1-.4-.3-.7-.4-.2-.1-.4-.3-.7-.4-.2-.1-.5-.2-.7-.3-.2-.1-.5-.2-.7-.3-.2-.1-.5-.2-.7-.3-.3-.1-.5-.2-.8-.3-.2-.1-.5-.1-.7-.2-.3-.1-.5-.1-.8-.2-.3-.1-.6-.1-.8-.1-.2 0-.5-.1-.7-.1-.5-.1-1-.1-1.6-.1s-1 0-1.6.1c-.2 0-.5.1-.7.1-.3 0-.6.1-.8.1-.3.1-.5.1-.8.2-.2.1-.5.1-.7.2-.3.1-.5.2-.8.3-.2.1-.5.2-.7.3-.2.1-.5.2-.7.3-.2.1-.5.2-.7.3-.2.1-.5.3-.7.4-.2.1-.4.3-.7.4-.3.2-.5.4-.7.5-.2.1-.4.3-.5.4-.4.3-.8.7-1.2 1.1l-95 95c-6.2 6.2-6.2 16.3 0 22.5 6.2 6.2 16.3 6.2 22.5 0L240 233.3v212c0 8.8 7.1 15.9 15.9 15.9s15.9-7.1 15.9-15.9v-212l67.8 67.8c6.2 6.2 16.3 6.2 22.5 0 6.2-6.2 6.2-16.3 0-22.5l-94.8-95z"
            transform="translate(0, -100)"
          ></path>
        </svg>
      </div>
      <div @click="triggerFileInput" class="mx-2">{{ label }}</div>
      <div>
        <span v-if="isFileSelect">
          <span v-for="(file, index) in selectedFiles" :key="index">
            <PreviewUploadPic
              :label="`${name}-${index + 1}`"
              icon="mdi-paperclip"
              :src="file.preview"
            />
            <v-icon class="mr-2" color="primary" @click="deleteItem(index)"
              >mdi-close</v-icon
            >
          </span>
        </span>
        <span v-for="(file, index) in defaultItems" :key="index">
          <PreviewUploadPic
            :label="file.slug"
            icon="mdi-paperclip"
            :src="file.attachment"
          />
        </span>
        <input
          type="file"
          multiple
          :ref="`fileInput`"
          style="display: none"
          @change="handleFileInputChange"
        />
      </div>
    </div>
  </span>
</template>

<script>
export default {
  props: {
    label: {
      default: "Upload attachments",
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
      this.defaultItems = [];
      const files = event.target.files;
      if (files.length > 0) {
        this.isFileSelect = true;
        for (let i = 0; i < files.length; i++) {
          this.processFile(
            files[i],
            `${this.name}-${
              files.length > 1 ? i + 1 : this.selectedFiles.length + 1
            }`
          );
        }
      } else {
        this.isFileSelect = false;
      }
    },

    processFile(file, picName) {
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
                const compressedDataUrl = compressedEvent.target.result;

                // Update the preview and emit events
                const fileSizeInKB = (compressedFile.size / 1024).toFixed(2);
                const imageSize = `(${fileSizeInKB} KB)`;
                this.selectedFiles.push({
                  name: picName,
                  preview: compressedDataUrl,
                  imageSize,
                  key: this.newDialogKey++,
                });

                this.currentComponent = "ViewFile"; // Set the component name to render

                this.$emit("files-selected", this.selectedFiles);
              };
            },
            "image/jpeg",
            0.3
          ); // Adjust the image quality (0.7 is a good balance)
        };
      };
    },
  },
};
</script>
