<template>
  <div>
    <v-row class="justify-center">
      <v-col md="8">
        <div
          class="card mt-4 p-4"
          style="text-align: justify;
          text-justify: inter-word;"
        >
          <div class="body">
            <div class="wrapper">
              <div class="project-card-header">Project description</div>
              <p>
                Lorem Ipsum is simply dummy text sof the printing and
                typesettingiure similique doloribus inventore quidem! Lorem
                ipsum dolor sit amet consectetur adipisicing elit. Minima
                placeat voluptas quia culpaiure similique doloribus inventore
                quidem! Lorem ipsum dolor sit amet consectetur adipisicing elit.
                Minima placeat voluptas quia culpaiure similique doloribus
                inventore quidem! Lorem ipsum dolor sit amet consectetur
                adipisicing elit. Minima placeat voluptas quia culpaiure
                similique iure similique doloribus inventore quidem! Lorem ipsum
                dolor sit amet consectetur adipisicing elit. Minima placeat
                voluptas quia culpaiure similiqiure similique doloribus
                inventore quidem! Lorem ipsum dolor sit amet consectetur
                adipisicing elit. Minima placeat voluptas quia culpaiure
                similique doloribus inventore quidem! Lorem ipsum dolor sit amet
                consectetur adipisicing elit. Minima placeat voluptas quia
                culpaiure similique doloribus inventore quidem! Lorem ipsum
                dolor sit amet consectetur adipisicing elit. Minima placeat
                voluptas quia culpa quibusdam vitae unde esse sunt, aliquam
                praesentium doloremque to make a type specimen book.
              </p>
              <br />
              <p>
                It has survived not only five centuries, but also the leap into
                electronic typesetting, remaining essentially unchanged. It was
                popularised in the 1960s with the release of Letraset sheets
                containing Lorem Ipsum passages, and more recently with desktop
                publishing software like Aldus PageMaker including versions of
                Lorem Ipsum.
              </p>
            </div>
          </div>
        </div>
      </v-col>
    </v-row>
  </div>
</template>

<script>
export default {
  layout({ $auth }) {
    let { is_master } = $auth.user;
    return is_master ? "default" : "employee";
  }
};
</script>

<style scoped src="@/assets/dashtem.css"></style>
